<?php
session_start();
include 'db_connect.php';

// ✅ Check if logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit;
}
$customer_id = $_SESSION['customer_id'];

// ✅ Get order ID from URL
$orderId = intval($_GET['id'] ?? 0);

// ✅ Check if the order belongs to this user
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
$stmt->bind_param('ii', $orderId, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: order-history.php');
    exit;
}


// ✅ Fetch order items (with product images)
$stmt = $conn->prepare("
    SELECT oi.quantity, oi.price, p.name, p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param('i', $orderId);
$stmt->execute();
$items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order #<?= htmlspecialchars($order['id']) ?> - Pet Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="navBarStyle.css">
    <link rel="stylesheet" href="footerStyle.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
        }
        h1 {
            text-align: center;
            color: chocolate;
        }
        p {
            font-size: 1.1em;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: chocolate;
            color: #fff;
        }
        td {
            background-color: #f9f9f9;
        }
        img.product-image {
            width: 60px;
            height: auto;
            border-radius: 6px;
        }
        a.back-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: chocolate;
            font-weight: bold;
        }
        a.back-link i {
            margin-right: 5px;
        }
        /* Hamburger Button */
    .menu-toggle {
      display: none;  /* hidden on desktop */
    }

    /* Mobile Responsive */
    @media screen and (max-width: 480px) {
      .navbar {
        flex-direction: column;
        display: none; /* hidden by default */
        position: fixed;     /* also fixed */
        top: 50px;           /* push below menu-toggle */
        left: 0;
        right: 0;
        background: chocolate;
        z-index: 1000;
      }

      .navbar.active {
        display: flex; /* shown when toggled */
      }

      .menu-toggle {
        display: block; /* show only on mobile */
        position: fixed;     /* 🧲 stick to top */
        top: 0;
        left: 0;
        width: 100%;
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
    }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h1>📦 Order #<?= htmlspecialchars($order['id']) ?></h1>

    <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
    <p><strong>Total:</strong> $<?= number_format($order['total_amount'], 2) ?></p>

    <h2>🛒 Items</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($item = $items->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                </td>
                <td><?= $item['quantity'] ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td>$<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="order-history.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back to My Orders
    </a>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const toggleBtn = document.querySelector('.menu-toggle');
  const navbar   = document.querySelector('.navbar');

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      navbar.classList.toggle('active');
    });
  }
});
</script>

</body>
</html>
