<?php
session_start();
require 'db_connect.php';

$client_id = $_SESSION['customer_id'] ?? 0;

if (!$client_id) die("Unauthorized");

$stmt = $conn->prepare("SELECT username, name, email, phone, address, date_of_birth, picture_path FROM users WHERE role = 'client' AND id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff8f0;
        }
        .profile-container {
            width: 50%;
            margin: auto;
            margin-top: 40px;
            padding: 25px;
            border: 2px solid chocolate;
            border-radius: 15px;
            background-color: #fffaf5;
        }
        h2 {
            text-align: center;
            color: chocolate;
        }
        .profile-item {
            margin: 12px 0;
        }
        img {
            width: 130px;
            border-radius: 12px;
            display: block;
            margin-top: 10px;
        }
        .edit-btn {
            display: block;
            margin: 20px auto 0;
            padding: 10px 20px;
            background-color: chocolate;
            color: white;
            border: none;
            border-radius: 10px;
            text-align: center;
            font-size: 16px;
            text-decoration: none;
        }
        .edit-btn:hover {
            background-color: peru;
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
        @media screen and (max-width: 480px) {
            .back-container{
            padding: 8px 8px;
            border-radius: 8px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            
        }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>👤 Your Profile</h2>
    
    <div class="profile-item"><strong>Username:</strong> <?= htmlspecialchars($client['username']) ?></div>
    <div class="profile-item"><strong>Name:</strong> <?= htmlspecialchars($client['name']) ?></div>
    <div class="profile-item"><strong>Email:</strong> <?= htmlspecialchars($client['email']) ?></div>
    <div class="profile-item"><strong>Phone:</strong> <?= htmlspecialchars($client['phone']) ?></div>
    <div class="profile-item"><strong>Address:</strong> <?= htmlspecialchars($client['address']) ?></div>
    <div class="profile-item"><strong>Date of Birth:</strong> <?= htmlspecialchars($client['date_of_birth']) ?></div>

    <div class="profile-item"><strong>Picture:</strong><br>
        <?php if ($client['picture_path']): ?>
            <img src="<?= htmlspecialchars($client['picture_path']) ?>" alt="Profile Picture">
        <?php else: ?>
            <p>No picture uploaded.</p>
        <?php endif; ?>
    </div>

    <a href="editClient.php" class="edit-btn">✏️ Edit Profile</a>
</div>

<div class="top-left">
<button  onclick="window.location.href='clientPage.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>
</body>
</html>
