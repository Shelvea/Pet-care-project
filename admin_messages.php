<?php
include 'db_connect.php';
session_start();

$result = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['messageid'])) {
    $messageid = intval($_POST['messageid']);

    if ($messageid > 0) {
        $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->bind_param("i", $messageid);

        if ($stmt->execute()) {
            $_SESSION['status_message'] = [
            'text' => "message deleted.",
            'type' => 'success'
          ];
        } else {
            $_SESSION['status_message'] = [
            'text' => "Error deleting message: " . $stmt->error,
            'type' => 'error'
          ];
        }

        $stmt->close();
    }

    header("Location: admin_messages.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="adminNavbarStyle.css">
<link rel="stylesheet" href="footerStyle.css">
<title>Admin Messages</title>
<style>
  h2{
    text-align: center;
    color:chocolate;
  }
table {
  margin: 10px auto;
  border-collapse: separate; /* IMPORTANT: don't use collapse */
  border-spacing: 0;
  width: 95%;
  border: 1px solid brown;
  border-radius: 10px;
  overflow: hidden; /* helps visually clip rounded borders */
}

thead tr:first-child th:first-child {
  border-top-left-radius: 10px;
}

thead tr:first-child th:last-child {
  border-top-right-radius: 10px;
}

tbody tr:last-child td:first-child {
  border-bottom-left-radius: 10px;
}

tbody tr:last-child td:last-child {
  border-bottom-right-radius: 10px;
}




  /* Header & cell styles */
th, td {
  border: 1px solid brown;
  text-align: center;
  color: brown;
  padding: 20px 5px;
  background-color: bisque;
}
th {
  color: chocolate;
}


  textarea{
    border: 1px solid brown;
    border-radius: 10px;
    
  }
  .reply-container{
    background-color: chocolate;
    color: white;
    border-radius: 10px;
    padding: 5px 5px;
    font-weight: bold;
    margin-top: 5px;
    border: 1px solid burlywood;
  }
  .reply-container:hover{
    background-color: wheat;
    color:brown;
  }
  .option-container{
    background-color: chocolate;
    color: white;
    border-radius: 10px;
    padding: 5px 5px;
    font-weight: bold;
    margin-top: 5px;
    border: 1px solid burlywood;
  }
  .option-container:hover{
    background-color: wheat;
    color:brown;
  }
  .update-container{
    background-color: chocolate;
    color: white;
    border-radius: 10px;
    padding: 5px 5px;
    font-weight: bold;
    margin-top: 5px;
 
    border: 1px solid burlywood;
  }
  .update-container:hover{
    background-color: wheat;
    color:brown;
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
 /* Hamburger Button */
    .menu-toggle {
    display: none;  /* hidden on desktop */
    
    }
    
/* Mobile Responsive */
@media screen and (max-width: 480px) {
    .AdminNavbar {
        flex-direction: column;
        display: none; /* hidden by default */
        position: fixed;     /* also fixed */
        top: 50px;           /* push below menu-toggle */
        left: 0;
        right: 0;
        background: chocolate;
        z-index: 1000;
    }
    .AdminNavbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 330px;
            text-decoration: none;
            font-weight: bold;
            flex:1;/* 🔥 Make all items take equal space */
    }
    .AdminNavbar.active {
        display: flex; /* shown when toggled */
    }
    .menu-toggle {
    display: block; /* show only on mobile */
    position: fixed;     /* 🧲 stick to top */
    top: 0;              /* align top */
    left: 0;             /* align left */
    width: 100%;         /* full width bar */
    cursor: pointer;
    font-size: 22px;
    text-decoration: none;
    font-weight: bold;
    padding: 10px;
    background: chocolate;
    z-index: 1100;       /* higher than navbar */
    color: white;
    text-align: left;
    }
    body {
        padding-top: 50px; /* prevent overlap */
    }
     footer {/* override footer style for mobile screen */
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background-color: chocolate;
        color: white;
        padding: 15px 10px;
        text-align: center;
        z-index: 1000;
    }
    .footer-container {
        max-width: 100%;   /* override any width limit */
        padding: 0 10px;
    }
    .footer-links {
        display: block;
        margin-top: 10px;
    }
    .footer-links a {
        display: inline-block;
        margin: 5px 8px;
    }
}
</style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>
    <h2>Admin Messages</h2>

    <?php if(isset($_SESSION['status_message']) && is_array($_SESSION['status_message'])): 
    $msg = $_SESSION['status_message'];
    ?>
  <div class="status-message <?= htmlspecialchars($msg['type']) ?>">
    <?= htmlspecialchars($msg['text']) ?>
  </div>
  <?php unset($_SESSION['status_message']); ?>
<?php endif; ?>

    <table>
  <thead>
    <tr>
      <th>Type</th>
      <th>Subject</th>
      <th>Sender Role</th>
      <th>Message</th>
      <th>Status</th>
      <th>Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['type']) ?></td>
        <td><?= htmlspecialchars($row['subject']) ?></td>
        <td><?= htmlspecialchars($row['sender_role']) ?></td>
        <td>
            <?= nl2br(htmlspecialchars($row['message'])) ?>
            <!-- Reply Form -->
        <?php if ($row['type'] === 'question'): ?>
        <form method="POST" action="send_reply.php" style="margin-top:10px;">
        <input type="hidden" name="message_id" value="<?= $row['id'] ?>">
        <textarea name="reply" rows="5" cols="40" required placeholder="Type your reply..."></textarea><br>
        <button type="submit" class="reply-container">Reply</button>
        </form>
        <?php endif; ?>        
        </td>
        <td>
        <form method="POST" action="update_status.php">
          <input type="hidden" name="message_id" value="<?= $row['id'] ?>">
          <select name="new_status" class="option-container">
              <option value="read" <?= $row['status'] == 'read' ? 'selected' : '' ?>>Read</option>
              <option value="archived" <?= $row['status'] == 'archived' ? 'selected' : '' ?>>Archived</option>
          </select>
          <button class="update-container" type="submit">Update</button>
        </form>
      </td>
        <td><?= $row['created_at'] ?></td>
        <td>
          <form method="post" action="" style="display:inline;">
        <input type="hidden" name="messageid" value="<?= htmlspecialchars($row['id']) ?>">
        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this message?');">
            Delete
        </button>
      </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
    <?php include 'footer.php'; ?>

      <script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.AdminNavbar').classList.toggle('active');
    });
  });
</script>
</body>
</html>