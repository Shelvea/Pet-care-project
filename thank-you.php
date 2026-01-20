<?php
$orderId = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : null;
if (!$orderId) {
    header('Location: shopping.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Thank You</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        h1 {
            color: #4CAF50;
        }
        p {
            font-size: 18px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff6600;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background-color: #ff3300;
        }
    </style>
</head>
<body>
    <h1>🎉 Thank You for Your Order!</h1>
    <p>Your order <strong>#<?= $orderId ?></strong> has been placed successfully.</p>
    <a href="shopping.php">🛍️ Continue Shopping</a>
</body>
</html>
