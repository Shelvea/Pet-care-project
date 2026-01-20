<?php
session_start();
require 'db_connect.php';

$nanny_id = $_SESSION['customer_id'] ?? 0;
if (!$nanny_id) die("Unauthorized");

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$name = test_input($_POST['nanny_name'] ?? '');
$phone = test_input($_POST['phone_number'] ?? '');
$address = test_input($_POST['address'] ?? '');
$dob = test_input($_POST['date_of_birth'] ?? '');
$work_at_branch = test_input($_POST['work_at_branch'] ?? '');
$picturePath = null;

// Handle image upload
if (!empty($_FILES['profile_picture']['name'])) {
    $targetDir = "uploads/nanny_profile/";
    $fileName = basename($_FILES["profile_picture"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
        $picturePath = $targetFile;
    }
}

// Get user_id from nanny_data
$stmt = $conn->prepare("SELECT id FROM nanny_data WHERE user_id=?");
$stmt->bind_param("i", $nanny_id);
$stmt->execute();
$stmt->bind_result($user_id);//id from nanny_data
$stmt->fetch();
$stmt->close();

if ($picturePath) {
    $stmt = $conn->prepare("UPDATE nanny_data SET nanny_name=?, phone_number=?, address=?, date_of_birth=?, picture_path=? , work_at_branch=? WHERE id=?");
    $stmt->bind_param("sssssii", $name, $phone, $address, $dob, $picturePath, $work_at_branch, $user_id);

    $stmt2 = $conn->prepare("UPDATE users SET name=?, phone=?, address=?, date_of_birth=?, picture_path=?, work_at_branch=? WHERE id=?");
    $stmt2->bind_param("sssssii", $name, $phone, $address, $dob, $picturePath, $work_at_branch, $nanny_id);
} else {
    $stmt = $conn->prepare("UPDATE nanny_data SET nanny_name=?, phone_number=?, address=?, date_of_birth=?, work_at_branch=? WHERE id=?");
    $stmt->bind_param("ssssii", $name, $phone, $address, $dob, $work_at_branch, $user_id);

    $stmt2 = $conn->prepare("UPDATE users SET name=?, phone=?, address=?, date_of_birth=?, work_at_branch=? WHERE id=?");
    $stmt2->bind_param("ssssii", $name, $phone, $address, $dob, $work_at_branch, $nanny_id);
}

// Execute both statements
$success1 = $stmt->execute();
$success2 = $stmt2->execute();

if ($success1 && $success2) {
    header("Location: nannyMainPage.php?success=1");
    exit;
} else {
    echo "❌ Failed to update: " . $stmt->error . " | " . $stmt2->error;
}

$stmt->close();
$stmt2->close();
$conn->close();
?>
