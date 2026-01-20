<?php
session_start();
include 'db_connect.php';

// ✅ Use customer_id directly from session
if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];//id of users table
} else {
    header('Location: login.php');
    exit;
}

// ✅ Query using customer_id
$stmt = $conn->prepare("SELECT id, total_amount, status, order_date FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->bind_param('i', $customer_id);//id of users table
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pet Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="navBarStyle.css">
    <link rel="stylesheet" href="footerStyle.css">
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        .page-wrapper {
            flex: 1; /* this takes all available space */
            display: flex;
            flex-direction: column;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

/* add more styles if needed */


        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
        background-color: #f9f9f9;
        }
        a {
        color: #ff6600;
        text-decoration: none;
        }
        a:hover {
        text-decoration: underline;
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
     .navbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 150px;
            text-decoration: none;
            font-weight: bold;
            flex:1;/* 🔥 Make all items take equal space */
      }

      .dropdown-content a {
            color: chocolate;
            padding: 12px 142px;
            text-decoration: none;
            display: flex;/* align icon & text in a row */
            align-items: center; /* vertically center them */
            white-space: nowrap; /* keep text in one line */
        }

       .dropbtn {
            background-color: chocolate;
            color: white;
            padding: 14px 150px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            flex:1;/* 🔥 Make all items take equal space */
            text-align: center;
        }
        
    .navbar.active {
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
}
    
    </style>
</head>
<body>
<div class="page-wrapper">
<?php include 'navbar.php'; ?>

<h1>📦 My Orders</h1>
    
    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows === 0): ?>
        <tr>
        <td colspan="5" style="text-align:center;">You have no orders yet.</td>
        </tr>
        <?php endif; ?>

        <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($order['id']) ?></td>
                <td><?= htmlspecialchars($order['order_date']) ?></td>
                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                <td><?= htmlspecialchars($order['status']) ?></td>
                <td><a href="order-details.php?id=<?= $order['id'] ?>">View</a></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>

 <script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.navbar').classList.toggle('active');
    });
  });
</script>

</body>
</html>
