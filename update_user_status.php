<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $new_status = $_POST['new_status'] === 'suspended' ? 'suspended' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: admin_users.php");
    exit;
}
?>
