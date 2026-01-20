<?php
session_start();
require 'db_connect.php';

$id = $_POST['id'];
$client_id = $_SESSION['customer_id'];//id from users

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id=?");
$stmt2->bind_param("i", $client_id);
$stmt2->execute();
$stmt2->bind_result($user_id);//id from client_data
$stmt2->fetch();
$stmt2->close();

$name = $_POST['name'];
$gender = $_POST['gender'];
$type = $_POST['type'];
$breed = $_POST['breed'];
$age = $_POST['age'];
$note = $_POST['note'];
$picture_path = '';

// Optional picture upload
if (!empty($_FILES['picture']['name'])) {
    $upload_dir = "uploads/";
    $unique_name = uniqid() . "_" . basename($_FILES["picture"]["name"]);
    $target_file = $upload_dir . $unique_name;
    move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file);
    $picture_path = $unique_name;

    $stmt = $conn->prepare("UPDATE pets_info SET name=?, gender=?, type=?, breed=?, age=?, note=?, picture_path=? WHERE id=? AND client_id=?");
    $stmt->bind_param("ssssissii", $name, $gender, $type, $breed, $age, $note, $picture_path, $id, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE pets_info SET name=?, gender=?, type=?, breed=?, age=?, note=? WHERE id=? AND client_id=?");
    $stmt->bind_param("ssssisii", $name, $gender, $type, $breed, $age, $note, $id, $user_id);
}

if ($stmt->execute()) {
    header("Location: my_pets.php");
    exit;
} else {
    echo "Update failed: " . $stmt->error;
}
