<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_id = intval($_POST['message_id']);
    $new_status = $_POST['new_status'];

    // Validate
    $allowed = ['read', 'archived'];
    if (!in_array($new_status, $allowed)) {
        die("Invalid status.");
    }

    $stmt = $conn->prepare("UPDATE messages SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $message_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: admin_messages.php");
exit;
