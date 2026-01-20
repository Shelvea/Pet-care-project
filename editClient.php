<?php
session_start();
require 'db_connect.php';

$client_id = $_SESSION['customer_id'] ?? 0;

if (!$client_id) {
    die("Unauthorized access.");
}

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
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        form {
            width: 400px;
            margin: auto;
            background: #fff8f0;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px chocolate;
        }
        label {
            display: block;
            margin-top: 15px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        input[type="submit"] {
                        background-color: chocolate;
                        color: white;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #d2691e;
        }
        img {
            margin-top: 10px;
            width: 120px;
            border-radius: 10px;
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
    </style>
</head>
<body>

<h2 style="text-align:center; color:chocolate;">✏️ Edit Your Profile</h2>

<form action="updateClient.php" method="POST" enctype="multipart/form-data">
    <label>Username (not editable)</label>
    <input type="text" value="<?= htmlspecialchars($client['username']) ?>" readonly>

    <label>Name</label>
    <input type="text" name="client_name" value="<?= htmlspecialchars($client['name']) ?>" required>

    <label>Email (not editable)</label>
    <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" readonly>

    <label>Phone Number</label>
    <input type="text" name="phone_number" value="<?= htmlspecialchars($client['phone']) ?>">

    <label>Address</label>
    <textarea name="address"><?= htmlspecialchars($client['address']) ?></textarea>

    <label>Date of Birth</label>
    <input type="date" name="date_of_birth" value="<?= htmlspecialchars($client['date_of_birth']) ?>">

    <label>Current Picture</label><br>
    <?php if ($client['picture_path']): ?>
        <img src="<?= htmlspecialchars($client['picture_path']) ?>" alt="Profile Picture"><!-- client_profile?-->
    <?php else: ?>
        <p>No picture uploaded.</p>
    <?php endif; ?>

    <label>Change Picture</label>
    <input type="file" name="profile_picture" accept="image/*">
    
    <input type="submit" value="Update Profile">
</form>

<div class="top-left">
<button  onclick="window.location.href='viewClient.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>
</body>
</html>

