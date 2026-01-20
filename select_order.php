<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connect.php';

$nanny_id = $_SESSION['customer_id'] ?? 0; //id from users

if (!$nanny_id) {
    die("Unauthorized access. Please log in as a nanny.");
}

$stmt2 = $conn->prepare("SELECT id FROM nanny_data WHERE user_id = ?");
$stmt2->bind_param("i",$nanny_id);
$stmt2->execute();

// bind result column(s) to variable(s)
$stmt2->bind_result($nanny_data_id);
$stmt2->fetch();
$stmt2->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {

    $order_id = intval($_POST['order_id']);

    // Use transaction to ensure atomic update
    $conn->begin_transaction();

    try {
    $stmt = $conn->prepare("UPDATE nanny_orders SET nanny_id = ?, status = 'accepted' WHERE id = ? AND nanny_id IS NULL");
    $stmt->bind_param("ii", $nanny_data_id, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
            throw new Exception("Order already accepted by another nanny or does not exist.");
    }

    $stmt->close();

    $conn->commit();

    $_SESSION['success'][] = "Order #$order_id has been accepted successfully!";
        header("Location: nannyCareRecord.php");
        exit;
    
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['fail'][] = $e->getMessage();
        header("Location: nannyAvailableOrders.php");
        exit;
    }
    

}

?>
