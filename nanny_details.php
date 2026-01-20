<?php
session_start();
require 'db_connect.php';

//$client_id = $_SESSION['client_id']; // assuming session stores logged-in client
$order_id = $_POST['order_id'] ?? null;

$stmt = $conn->prepare("SELECT nd.nanny_name, nd.email, nd.phone_number, nd.address, nd.picture_path
FROM nanny_orders no
JOIN nanny_data nd ON no.nanny_id = nd.id
WHERE no.id = ?"
);

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <title>My Pets</title>
    <style>
        body {
            font-family: Arial;
            background-color: #fffaf0;
            color: chocolate;
            text-align: center;
        }
        .cust-card {
            display: inline-block;
            width: 300px;
            border: 2px solid chocolate;
            border-radius: 12px;
            padding: 15px;
            margin: 20px;
            background-color: #fff5e6;
        }
        .cust-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cust-card h3 {
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

<h1>保母細節</h1>

<?php if (!empty($row)): ?>
    <div class="cust-card">
        <?php if (!empty($row['picture_path'])): ?>
            <img src="<?= htmlspecialchars($row['picture_path']) ?>" alt="Nanny Image">
        <?php else: ?>
            <img src="https://via.placeholder.com/300x200?text=No+Image" alt="No Image">
        <?php endif; ?>

        <h3><?= htmlspecialchars($row['nanny_name']) ?></h3>
        <p>Email: <?= $row['email'] ?></p>
        <p>Phone: <?= $row['phone_number'] ?></p>
        <p>Address: <?= $row['address'] ?></p>
    </div>
<?php else: ?>
     <p>尚無托育員</p>
<?php endif; ?>

<div class="top-left">
    <?php
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'client') {
        $backPage = 'careRecords.php';
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'nanny') {
        $backPage = 'nannyCareRecord.php';
    } else {
        $backPage = '#'; // fallback
    }

    ?>
<button onclick="window.location.href='<?= $backPage ?>'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>

</body>
</html>
