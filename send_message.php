<?php
session_start();
include 'db_connect.php';

$sender_id = $_SESSION['user_id'] ?? null; // or 0 for guests
$sender_role = $_SESSION['role'] ?? 'guest';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';
$type = $_POST['type'] ?? 'feedback';

if ($subject && $message) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_role, subject, message, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $sender_id, $sender_role, $subject, $message, $type);
    $stmt->execute();
    $stmt->close();
    $_SESSION['message_sent'] = "Thank you! Your message has been sent.";
} else {
    $_SESSION['message_sent'] = "Please fill in all fields.";
}

header("Location: contactForm.php");
exit;
?>
