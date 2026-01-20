<?php
session_start();
include 'db_connect.php';

// Ensure client is logged in
$client_id = $_SESSION['customer_id'] ?? null;//id from users
if (!$client_id) {
    die("Please log in as client.");
}

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id = ?");
$stmt2->bind_param("i",$client_id);
$stmt2->execute();

// bind result column(s) to variable(s)
$stmt2->bind_result($client_data_id);
$stmt2->fetch();
$stmt2->close();

$stmt = $conn->prepare("
    SELECT n.id, n.sender_id, nanny.nanny_name AS sender_name, n.subject, n.message, n.receiver_id, no.order_date
    FROM notifications n
    JOIN nanny_data nanny ON nanny.id = n.sender_id
    JOIN client_data cd ON cd.id = n.receiver_id
    JOIN nanny_orders no ON no.id = n.order_id
    WHERE n.receiver_id = ?
    ORDER BY no.order_date DESC, n.id DESC
");

$stmt->bind_param("i", $client_data_id);
$stmt->execute();

$result = $stmt->get_result();

$stmt->close();

// Group notifications by order_date
$notifsByDate = [];
while ($notif = $result->fetch_assoc()) {
    $date = $notif['order_date'];
    $notifsByDate[$date][] = $notif;
}

?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notifid'])) {
    $notifid = intval($_POST['notifid']);

    if ($notifid > 0) {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->bind_param("i", $notifid);

        if ($stmt->execute()) {
            $_SESSION['status_message'] = [
            'text' => "notification deleted.",
            'type' => 'success'
          ];
        } else {
            $_SESSION['status_message'] = [
            'text' => "Error deleting notification: " . $stmt->error,
            'type' => 'error'
          ];
        }

        $stmt->close();
    }

    header("Location: notifications.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Your Notifications</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
        <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .notif-container {
            max-width: 800px;
            margin: auto;
        }
        .notif-card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .notif-header {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .notif-subject {
            font-size: 16px;
            margin-bottom: 10px;
            color: #007BFF;
        }
        .notif-message {
            margin-bottom: 12px;
            line-height: 1.5;
        }
        .notif-actions a {
            display: inline-block;
            padding: 8px 14px;
            margin: 5px 5px 0 0;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            background-color: #007BFF;
            color: white;
            transition: background-color 0.3s;
        }
        .notif-actions a:hover {
            background-color: #0056b3;
        }
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
        .status-message {
            padding: 12px 20px;
            margin: 15px auto;
            width: 80%;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

/* Success */
.status-message.success {
  background-color: #d4edda; /* light green */
  color: #155724;             /* dark green */
  border: 1px solid #c3e6cb;
}

/* Error */
.status-message.error {
  background-color: #f8d7da; /* light red */
  color: #721c24;             /* dark red */
  border: 1px solid #f5c6cb;
}
button.delete-btn {
  background-color: #e74c3c; /* red */
  color: white;
  border: none;
  border-radius: 5px;
  padding: 6px 12px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
}

button.delete-btn:hover {
  background-color: #c0392b; /* darker red on hover */
}

        </style>
    </head>
    <body>
        <h2>Your Notifications</h2>

        <?php if(isset($_SESSION['status_message'])): 
        $msg = $_SESSION['status_message'];
        ?>
        <div class="status-message <?= $msg['type'] ?>">
        <?= htmlspecialchars($msg['text']) ?>
        </div>
        <?php unset($_SESSION['status_message']); ?>
        <?php endif; ?>

    <div class="notif-container">
        <?php if (!empty($notifsByDate)): ?>
            <?php foreach ($notifsByDate as $date => $notifs): ?>
                <h3 style="color:crimson;">📅 Order Date: <?= htmlspecialchars($date) ?></h3>
                <?php foreach ($notifs as $notif): ?>
       
                <div class="notif-card">
                    <div class="notif-header">From: <?= htmlspecialchars($notif['sender_name']) ?></div>
                    <div class="notif-subject"><?= htmlspecialchars($notif['subject']) ?></div>
                    <div class="notif-message">
                        <?= $notif['message'] ?> <!-- Allow HTML for clickable links -->
                    </div>
                    <div class="notif-actions">
                        <?php
                        // Extract links from message and make them buttons
                        preg_match_all('/href=[\'"]([^\'"]+)[\'"]/', $notif['message'], $matches);
                        foreach ($matches[1] as $link):
                            // Button text: pick last part of URL or default
                            $btnText = ucfirst(pathinfo(parse_url($link, PHP_URL_PATH), PATHINFO_FILENAME)) ?: 'Open Link';
                        ?>
                            <a href="<?= htmlspecialchars($link) ?>" target="_blank"><?= htmlspecialchars($btnText) ?></a>
                        <?php endforeach; ?>
                        <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="notifid" value="<?= htmlspecialchars($notif['id']) ?>">
                        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this notification?');">
                        Delete
                        </button>
                        </form>
                    </div>
                </div>
           <?php endforeach; ?>
           <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;">No notifications found.</p>
        <?php endif; ?>
    </div>

    <div class="top-left">
<button  onclick="window.location.href='clientPage.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>
    </body>
</html>