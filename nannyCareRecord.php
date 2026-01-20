<?php
session_start();

if (!empty($_SESSION['success'])) {
    foreach ($_SESSION['success'] as $msg) {
        echo "<div style='color:green; padding:5px; margin:5px 0; border:1px solid green; text-align:center;'>{$msg}</div>";
    }
    unset($_SESSION['success']);
}

if (!empty($_SESSION['fail'])) {
    foreach ($_SESSION['fail'] as $msg) {
        echo "<div style='color:red; padding:5px; margin:5px 0; border:1px solid red; text-align:center;'>{$msg}</div>";
    }
    unset($_SESSION['fail']);
}

require 'db_connect.php';

$nanny_id = $_SESSION['customer_id'] ?? 0;//id from users
if (!$nanny_id) die("Unauthorized");

$stmt2 = $conn->prepare("SELECT id FROM nanny_data WHERE user_id = ?");
$stmt2->bind_param("i",$nanny_id);
$stmt2->execute();

// bind result column(s) to variable(s)
$stmt2->bind_result($nanny_data_id);
$stmt2->fetch();
$stmt2->close();

$stmt = $conn->prepare("
    SELECT no.id AS order_id, no.customer_id, no.nanny_id, no.order_date, no.status, no.notes, no.pet_id, no.pickup_time, no.branch_dropoff_time, no.branch_id, no.pickup_address, no.pickup_method, no.auto_message_sent, no.return_method, no.return_address, no.return_pickup_time, no.return_time,
        p.name AS pet_name, c.client_name, bs.service_type, bs.fixed_price, bs.unit, sb.city, sb.address
    FROM nanny_orders no
    JOIN pets_info p ON no.pet_id = p.id
    JOIN client_data c ON no.customer_id = c.id
    LEFT JOIN shop_branches sb ON no.branch_id = sb.id
    LEFT JOIN nanny_order_services nos ON no.id = nos.order_id
    LEFT JOIN branch_services bs ON nos.service_id = bs.id
    WHERE no.nanny_id = ?
    ORDER BY no.order_date ASC, no.id ASC
");

$stmt->bind_param("i", $nanny_data_id);
$stmt->execute();
$result = $stmt->get_result();

// Group orders by order_date and order_id
$ordersByDate = [];
while ($row = $result->fetch_assoc()) {
    $date = $row['order_date'];
    $orderId = $row['order_id'];
    if (!isset($ordersByDate[$date][$orderId])) {
        $ordersByDate[$date][$orderId] = [
            'customer_id' => $row['customer_id'],
            'pet_id' => $row['pet_id'],
            'pet_name' => $row['pet_name'],
            'pickup_address' => $row['pickup_address'],
            'pickup_method' => $row['pickup_method'],
            'pickup_time' => $row['pickup_time'],
            'branch_dropoff_time' => $row['branch_dropoff_time'],
            'city' => $row['city'],
            'address' => $row['address'],
            'status' => $row['status'],
            'notes' => $row['notes'],
            'auto_message_sent' => $row['auto_message_sent'],
            'return_method' => $row['return_method'],
            'return_address' => $row['return_address'],
            'return_pickup_time' => $row['return_pickup_time'],
            'return_time' => $row['return_time'],
            'services' => []
        ];
    }
    $ordersByDate[$date][$orderId]['services'][] = [
        'service_type' => $row['service_type'],
        'price' => $row['fixed_price'],
        'unit' => $row['unit']
    ];
}



// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $status = $_POST['status'] ?? '';
    $order_id = intval($_POST['order_id']);

    $stmt = $conn->prepare("
        UPDATE nanny_orders 
        SET status = ? 
        WHERE id = ? AND nanny_id = ?
    ");
    $stmt->bind_param("sii", $status, $order_id, $nanny_data_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'][] = "Order #$order_id status updated successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Take Care Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <style>
        table { width: 90%; margin: auto; border-collapse: collapse;}
        th, td { padding: 10px; border: 1px solid chocolate; text-align: center; }
        th { background-color: chocolate; color: white; }
        .select-btn {
            padding: 6px 12px;
            background-color: chocolate;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .select-btn:hover {
            background-color: peru;
        }
        .back-container{
            padding: 13px 13px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            
        }
        .back-container:hover{
            color:chocolate;
            background-color: bisque;
        }
        .top-left {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .set-status label {
            display: block;
    
        }
        .button-style{
            background-color:chocolate;
            color:white;
            border-radius:5px;
            border:1px solid burlywood;
            padding:5px 5px;
        }

          /* Mobile Responsive */
@media screen and (max-width: 480px) {
     .back-container{
            padding: 8px 8px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            
        }

        .top-left {
        position: fixed;
        top: 10px;
        left: 10px;
    
    }
    h2{
        margin-top: 60px;
    }
}
    </style>
</head>
<body>
    <?php if (isset($_GET['msg'])): ?>
    <p class='success' style="text-align: center;"><?= htmlspecialchars($_GET['msg']) ?></p>
    <?php endif; ?>
    
    <?php if (isset($_GET['updated'])): ?>
    <p style="color:green; text-align:center;">Order status updated successfully.</p>
    <?php endif; ?>

<h2 style="text-align:center; color:crimson; " >🐾 Your Take Care Records</h2>

<?php if (!empty($ordersByDate)): ?>
    <?php foreach ($ordersByDate as $order_date => $orders): ?>
        <h3 class="date-header">📅 Date: <?= htmlspecialchars($order_date) ?></h3>
<table>
    <tr>
        <th>Client Details</th>
        <th>Status</th>
        <th>Notes</th>
        <th>Pet Details</th>
        <th>Pickup</th>
        <th>Branch Dropoff</th>
        <th>Service Type(s)</th>
        <th>Shop Address</th>
        <th>Return</th>
        <th>Action</th>
    </tr>
     <?php foreach ($orders as $orderId => $order): ?>

    <tr>
        
        <td>
            <form action="client_details.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $orderId ?>">
                <input type="hidden" name="from" value="nannyCareRecord.php">
                <input type="submit" value="View Client Details" class="select-btn">
            </form>
        </td>
        <td><?= htmlspecialchars($order['status']) ?></td>
        <td><?= htmlspecialchars($order['notes']) ?></td>

        <td>
            <form action="pet_details.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $orderId ?>">
                <input type="hidden" name="from" value="nannyCareRecord.php">
                <input type="submit" value="View Pet Details" class="select-btn">
            </form>
        </td>
        <td>
        <?= "<span style='color:chocolate; '>Pickup method:</span> " . htmlspecialchars($order['pickup_method'] ?? '') . "<br>" . 
        "<span style='color:chocolate; '>Pickup address:</span> " . htmlspecialchars($order['pickup_address'] ?? '') . "<br>" .
        "<span style='color:chocolate; '>Pickup time:</span> " . htmlspecialchars($order['pickup_time'] ?? '') ?>
        </td>
        <td><?= "<span style='color:chocolate; '>Branch dropoff time:</span> ".htmlspecialchars($order['branch_dropoff_time']) ?? " " ?></td>
    <td>
    <?= implode('<br>', array_map(fn($s) => "• {$s['service_type']} ({$s['price']} {$s['unit']})", $order['services'])) ?>
    </td>
        <td><?= htmlspecialchars($order['city'] . ', ' . $order['address']) ?></td>
        <td>
        <?= "<span style='color:chocolate; '>Return method:</span> " . htmlspecialchars($order['return_method'] ?? '') . "<br>" . 
        "<span style='color:chocolate; '>Return address:</span> " . htmlspecialchars($order['return_address'] ?? '') . "<br>" .
        "<span style='color:chocolate; '>Return pickup time:</span> " . htmlspecialchars($order['return_pickup_time'] ?? '') . "<br>" .
        "<span style='color:chocolate; '>Return time:</span> " . htmlspecialchars($order['return_time'] ?? '') ?>
        </td>
        <td>
            <!-- Set Status -->
            <form method="POST" action="" class="set-status">
                <input type="hidden" name="order_id" value="<?= $orderId ?>">    
                <label><input type="radio" name="status" value="pet has arrived" required> Pet arrived</label>
                <label><input type="radio" name="status" value="in service"> In service</label>
                <label><input type="radio" name="status" value="service completed"> Service completed</label><br>
                <input type="submit" class="button-style" value="Set Status" class="confirm">
            </form>
            <br>
        <!-- Send Automatic Message -->
        <?php if (!empty($order['auto_message_sent']) && $order['auto_message_sent'] == 1): ?>
        <button class="select-btn" style="background-color: green;" disabled>Message Sent</button>
        <?php else: ?>
            <button class="select-btn send-message-btn" 
                data-order="<?= $orderId ?>" 
                data-pet="<?= $order['pet_id'] ?>" 
                data-customer="<?= $order['customer_id'] ?>">
                Send Automatic Message
            </button>
        <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endforeach; ?>
<?php else: ?>
<p style="color:chocolate; text-align:center;">No available records at the moment.</p>
<?php endif; ?>
    

<div class="top-left">
<button  onclick="window.location.href='nannyMainPage.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>

<script>
document.querySelectorAll(".send-message-btn").forEach(btn => {
    btn.addEventListener("click", function() {
        let orderId = this.dataset.order;
        let petId = this.dataset.pet;
        let customerId = this.dataset.customer;
        let button = this;

        fetch("automatic_message.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `order_id=${orderId}&pet_id=${petId}&customer_id=${customerId}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                button.textContent = "Message Sent";
                button.style.backgroundColor = "green";
                button.disabled = true;
            } else {
                alert("Failed to send message");
            }
        })
        .catch(err => console.error(err));
    });
});
</script>

</body>
</html>