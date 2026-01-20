<?php
include 'db_connect.php';

$username = 'chiki';
$password = 'gtrdqws7';

// Hash the password securely
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$role = 'admin';
$status = 'active';

$sql = "INSERT INTO admins (username, pass, role, status) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $username, $password_hash, $role, $status);

if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}
?>
