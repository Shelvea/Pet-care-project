<?php
session_start();
include 'db_connect.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;

header('Content-Type: application/json');

// Ensure nanny is logged in
$nanny_id = $_SESSION['customer_id'] ?? null; // from users table
if (!$nanny_id) {
    echo json_encode(["success" => false, "error" => "Please log in as nanny."]);
    exit;
}

// Get nanny_data id
$stmt2 = $conn->prepare("SELECT id FROM nanny_data WHERE user_id = ?");
$stmt2->bind_param("i", $nanny_id);
$stmt2->execute();
$stmt2->bind_result($nanny_data_id);
$stmt2->fetch();
$stmt2->close();

$order_id    = intval($_POST['order_id'] ?? 0);
$pet_id      = intval($_POST['pet_id'] ?? 0);
$customer_id = intval($_POST['customer_id'] ?? 0);

if (!$order_id || !$pet_id || !$customer_id) {
    echo json_encode(["success" => false, "error" => "Invalid input."]);
    exit;
}

// Fetch all completed services for this pet
$stmt = $conn->prepare("
   SELECT no.id, no.customer_id, c.client_name, c.email AS client_email, 
          p.name AS pet_name, bs.service_type, bs.fixed_price, bs.unit, no.order_date
   FROM nanny_orders no
   JOIN client_data c ON no.customer_id = c.id
   JOIN pets_info p ON no.pet_id = p.id
   JOIN nanny_order_services nos ON no.id = nos.order_id
   JOIN branch_services bs ON nos.service_id = bs.id
   WHERE no.customer_id = ? 
     AND no.pet_id = ? 
     AND no.status = 'service completed'
   ORDER BY no.order_date ASC
");
$stmt->bind_param("ii", $customer_id, $pet_id);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (!$rows) {  
    echo json_encode(["success" => false, "error" => "No completed services found for this pet."]);
    exit;
}

// Group services by order_date
$ordersByDate = [];
foreach ($rows as $row) {
    $ordersByDate[$row['order_date']][] = $row;
}

// Loop through each date to create a separate invoice
foreach ($ordersByDate as $date => $services) {
    $first = $services[0];
    $order_id_for_date = $first['id'];

    $safeDate = str_replace([':', ' '], ['-', '_'], $date);
    
    $total = 0;
    $html_items = '';

    foreach ($services as $s) {
        $lineTotal = $s['fixed_price'];
        $total += $lineTotal;
        $html_items .= "
            <tr class='item'>
                <td>{$s['service_type']}</td>
                <td>{$s['fixed_price']} {$s['unit']}</td>
                <td>{$lineTotal}</td>
            </tr>
        ";
    }

    // ===== Styled HTML for the PDF =====
    $html = "
    <html>
    <head>
    <style>
    body { font-family: Arial, sans-serif; font-size: 14px; }
    h1 { text-align: center; color: #333; }
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 20px;
        border: 1px solid #eee;
        background: #fff;
    }
    .logo { text-align: center; margin-bottom: 20px; }
    .logo img { max-width: 150px; }
    table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
    table td { padding: 5px; vertical-align: top; }
    table tr.heading th { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
    table tr.item td { border-bottom: 1px solid #eee; }
    .total { font-weight: bold; border-top: 2px solid #333; }
    .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #777; }
    </style>
    </head>
    <body>
    <div class='invoice-box'>
        <div class='logo'>
            <img src='https://yourdomain.com/images/logo.png' alt='Company Logo'>
        </div>
        <h1>Invoice</h1>
        <p><strong>Client Name:</strong> {$first['client_name']}<br>
           <strong>Email:</strong> {$first['client_email']}<br>
           <strong>Pet Name:</strong> {$first['pet_name']}<br>
           <strong>Order Date:</strong> {$date}</p>
        
        <table>
            <tr class='heading'>
                <th>Service</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
            $html_items
            <tr>
                <td colspan='2' class='total'>Grand Total</td>
                <td class='total'>{$total}</td>
            </tr>
        </table>
        
        <div class='footer'>
            Thank you for trusting us with your pet care.<br>
            This is an electronically generated invoice.
        </div>
    </div>
    </body>
    </html>
    ";

    // ===== Generate PDF =====
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Save PDF securely
    $invoiceDir = __DIR__ . '/protected_invoices';
    if (!is_dir($invoiceDir)) {
        mkdir($invoiceDir, 0770, true);
    }
    $invoiceFile = $invoiceDir . "/invoice_{$order_id_for_date}_{$safeDate}.pdf";
    file_put_contents($invoiceFile, $dompdf->output());

    // Secure link
    $secureLink = "download_invoice.php?order_id={$order_id_for_date}&date={$safeDate}";

    // Prepare message text
    $message_text = "Your ordered service on <b>{$date}</b> has been completed.<br>
    <a style='display:none;' href='{$secureLink}'>Download Invoice</a><br>
    <a style='display:none;' href='return_method_form.php?order_id={$order_id_for_date}'>Select Return Method</a>";

    $subject = "Service Completed & Bill Payment - {$date}";

    // Insert into messages table
    $stmt = $conn->prepare("INSERT INTO notifications (sender_id, subject, message, receiver_id, order_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $nanny_data_id, $subject, $message_text, $customer_id, $order_id_for_date);
    $stmt->execute();
    $stmt->close();

    // ✅ Mark as sent for this order
    $stmt = $conn->prepare("UPDATE nanny_orders SET auto_message_sent = 1 WHERE id = ?");
    $stmt->bind_param("i", $order_id_for_date);
    $stmt->execute();
    $stmt->close();
}

// ✅ JSON response for AJAX
echo json_encode([
    "success" => true,
    "order_id" => $order_id,
    "message"  => "Invoices sent and automatic message created."
]);
exit;
