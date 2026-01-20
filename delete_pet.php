<?php
session_start();
require 'db_connect.php';

$id = $_GET['id'];
$client_id = $_SESSION['customer_id'];//id from users

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id=?");
$stmt2->bind_param("i", $client_id);
$stmt2->execute();
$stmt2->bind_result($user_id);//id from client_data
$stmt2->fetch();
$stmt2->close();

$stmt = $conn->prepare("DELETE FROM pets_info WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    header("Location: my_pets.php");
    exit;
} else {
    echo "Delete failed.";
}
