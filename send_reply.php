<?php
session_start();
include 'db_connect.php';

$admin_id = $_SESSION['admin_id'];
$message_id = $_POST['message_id'];
$reply = $_POST['reply'];

$stmt = $conn->prepare("INSERT INTO replies (message_id, admin_id, reply) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $message_id, $admin_id, $reply);
$stmt->execute();
$stmt->close();

header("Location: admin_messages.php");
exit;
