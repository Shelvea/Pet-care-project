<?php
session_start();
include 'db_connect.php';

// Get order ID from query
$order_id = intval($_GET['order_id'] ?? 0);
$date = $_GET['date'] ?? ''; // sanitize date
$safeDate = str_replace([':', ' '], ['-', '_'], $date);

if (!$order_id || !$date) {
    die("Invoice not found.");
}
// Ensure user is logged in
$client_id = $_SESSION['customer_id'] ?? null;//id from users

if (!$client_id) {
    die("Access denied.");
}

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id = ?");
$stmt2->bind_param("i",$client_id);
$stmt2->execute();

// bind result column(s) to variable(s)
$stmt2->bind_result($client_data_id);
$stmt2->fetch();
$stmt2->close();

// Check if the logged-in user owns this invoice
$stmt = $conn->prepare("
    SELECT customer_id
    FROM nanny_orders
    WHERE id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->bind_result($order_client_id);
$stmt->fetch();
$stmt->close();

if (($client_id && $client_data_id != $order_client_id)) {
    die("You are not allowed to access this invoice.");
}

// File path
$filePath = __DIR__ . "/protected_invoices/invoice_{$order_id}_{$safeDate}.pdf";
if (!file_exists($filePath)) {
    die("Invoice not found.");
}

// Force download
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"invoice_{$order_id}_{$safeDate}.pdf\"");
header("Content-Length: " . filesize($filePath));
readfile($filePath);
exit; 