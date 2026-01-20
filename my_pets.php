<?php
session_start();
require 'db_connect.php';

$client_id = $_SESSION['customer_id']; // assuming session stores logged-in client

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id=?");
$stmt2->bind_param("i", $client_id);
$stmt2->execute();
$stmt2->bind_result($user_id);//id from client_data
$stmt2->fetch();
$stmt2->close();

$stmt = $conn->prepare("SELECT * FROM pets_info WHERE client_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>My Pets</title>
    <style>
        body {
            font-family: Arial;
            background-color: #fffaf0;
            color: chocolate;
            text-align: center;
        }
        .pet-card {
            display: inline-block;
            width: 300px;
            border: 2px solid chocolate;
            border-radius: 12px;
            padding: 15px;
            margin: 20px;
            background-color: #fff5e6;
        }
        .pet-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .pet-card h3 {
            margin: 10px 0 5px;
        }
        .btn-group {
            margin-top: 10px;
        }
        .btn-group a {
            margin: 5px;
            padding: 8px 12px;
            background-color: chocolate;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
        .btn-group a:hover {
            background-color: darkorange;
        }
        .back-container{
            padding: 13px 13px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-weight: bold;
            text-align: center;
            
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

<h1>我的寵物</h1>

<?php while ($row = $result->fetch_assoc()): ?>
    <div class="pet-card">
        <?php if (!empty($row['picture_path'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['picture_path']) ?>" alt="Pet Image">
        <?php else: ?>
            <img src="https://via.placeholder.com/300x200?text=No+Image" alt="No Image">
        <?php endif; ?>

        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <p>性別: <?= $row['gender'] ?></p>
        <p>種類: <?= $row['type'] ?></p>
        <p>品種: <?= $row['breed'] ?></p>
        <p>年齡: <?= $row['age'] ?></p>
        <p>特質: <?= nl2br(htmlspecialchars($row['note'])) ?></p>

        <div class="btn-group">
            <a href="edit_pet.php?id=<?= $row['id'] ?>">編輯</a>
            <a href="delete_pet.php?id=<?= $row['id'] ?>" onclick="return confirm('確定要刪除這隻寵物嗎？')">刪除</a>
        </div>
    </div>
<?php endwhile; ?>

<div class="top-left">
<button  onclick="window.location.href='clientPage.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>

</body>
</html>
