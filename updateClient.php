<?php
session_start();
require 'db_connect.php';

$client_id = $_SESSION['customer_id'] ?? 0;//id from users
if (!$client_id) die("Unauthorized");

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$name = test_input($_POST['client_name'] ?? '');
$phone = test_input($_POST['phone_number'] ?? '');
$address = test_input($_POST['address'] ?? '');
$dob = test_input($_POST['date_of_birth'] ?? '');
$picturePath = null;

// Handle image upload
if (!empty($_FILES['profile_picture']['name'])) {
    $targetDir = "uploads/client_profile/";
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
        $picturePath = $targetFile;
    }
}

// Get user_id from client_data
$stmt = $conn->prepare("SELECT id FROM client_data WHERE user_id=?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if ($picturePath) {
    $stmt = $conn->prepare("UPDATE client_data SET client_name=?, phone_number=?, address=?, date_of_birth=?, picture_path=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $phone, $address, $dob, $picturePath, $user_id);
    $stmt2 = $conn->prepare("UPDATE users SET name=?, phone=?, address=?, date_of_birth=?, picture_path=? WHERE id=?");
    $stmt2->bind_param("sssssi", $name, $phone, $address, $dob, $picturePath, $client_id);
} else {
    $stmt = $conn->prepare("UPDATE client_data SET client_name=?, phone_number=?, address=?, date_of_birth=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $phone, $address, $dob, $user_id);
    $stmt2 = $conn->prepare("UPDATE users SET name=?, phone=?, address=?, date_of_birth=? WHERE id=?");
    $stmt2->bind_param("ssssi", $name, $phone, $address, $dob, $client_id);
}

// Execute both statements
$success1 = $stmt->execute();
$success2 = $stmt2->execute();

if ($success1 && $success2) {
    header("Location: clientPage.php?success=1");
    exit;
} else {
    echo "❌ Failed to update: " . $stmt->error . " | " . $stmt2->error;
}

$stmt->close();
$stmt2->close();
$conn->close();
?>
