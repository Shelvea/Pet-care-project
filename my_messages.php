<?php
session_start();
include 'db_connect.php';

$sender_id = $_SESSION['client_id'] ?? 0;//id from users
$role = $_SESSION['role'] ?? null;

if (!$sender_id || !$role) {
    echo "Unauthorized access.";
    exit;
}

$stmt = $conn->prepare("SELECT id, subject, message, status, created_at FROM messages WHERE sender_id = ? AND sender_role = ? ORDER BY created_at DESC");
$stmt->bind_param("is", $sender_id, $role);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .message-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; border-radius: 8px; background-color: #f9f9f9; }
        .status { font-weight: bold; color: green; }
     .back-container{
            padding: 13px 13px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            
        }
        .back-container:hover{
            color:chocolate;
            background-color: bisque;
        }
        .top-left {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        h2{
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <h2>My Sent Messages</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="message-box">
            <h3><?= htmlspecialchars($row['subject']) ?></h3>
            <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
            <p><span class="status">Status: <?= htmlspecialchars($row['status']) ?></span></p>
            <small>Sent on: <?= $row['created_at'] ?></small>
    
        <?php
    // Fetch and display admin replies
    $replyStmt = $conn->prepare("SELECT reply, created_at FROM replies WHERE message_id = ?");
    $replyStmt->bind_param("i", $row['id']);
    $replyStmt->execute();
    $replyResult = $replyStmt->get_result();

    while ($reply = $replyResult->fetch_assoc()) {
        echo "<div style='margin-top:10px; padding:8px; background:#eef; border-left:4px solid #007bff;'>
                <strong>Admin Reply:</strong><br>" . nl2br(htmlspecialchars($reply['reply'])) . "
                <br><small><em>Replied on: " . $reply['created_at'] . "</em></small>
              </div>";
    }
    $replyStmt->close();
    ?>
        </div>
        <?php endwhile; ?>

<div class="top-left">
<button  onclick="window.location.href='clientPage.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>

</body>
</html>
