<?php
session_start();
require 'db_connect.php';

$client_id = $_SESSION['customer_id'];

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id=?");
$stmt2->bind_param("i", $client_id);
$stmt2->execute();
$stmt2->bind_result($user_id);//id from client_data
$stmt2->fetch();
$stmt2->close();

$name = $_POST['name'];
$gender = $_POST['gender'];
$type = $_POST['type'];
$breed = $_POST['breed'] ?? '';
$age = $_POST['age'] ?? null;
$note = $_POST['note'] ?? '';
$picture_path = '';

// Handle image upload
if (!empty($_FILES['picture']['name'])) {
    $upload_dir = "uploads/";
    $unique_name = uniqid() . "_" . basename($_FILES["picture"]["name"]);
    $target_file = $upload_dir . $unique_name;
    move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
    $picture_path = $unique_name;
}

$stmt = $conn->prepare("INSERT INTO pets_info (client_id, name, gender, type, breed, age, picture_path, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssiss", $user_id, $name, $gender, $type, $breed, $age, $picture_path, $note);

if ($stmt->execute()) {
    header("Location: my_pets.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}