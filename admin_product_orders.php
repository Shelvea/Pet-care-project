<?php
session_start();
include 'db_connect.php';


// Fetch product orders info
$sql = "SELECT 
    o.id AS order_id,
    o.customer_name,
    o.phone,
    o.station,
    o.payment_method,
    o.address AS delivery_address,
    o.total_amount AS total_fee,
    o.order_date,
    o.customer_type,
    o.status,
    CONCAT('<ul>', GROUP_CONCAT(CONCAT('<li>', p.name, ' (Qty: ', oi.quantity, ', Price: ', oi.price, ')</li>') SEPARATOR ''), '</ul>') AS products_info
FROM orders o
JOIN users u ON o.customer_id = u.id
JOIN order_items oi ON o.id = oi.order_id
JOIN products p ON oi.product_id = p.id
GROUP BY o.id
ORDER BY o.order_date DESC";

$result = $conn->query($sql);

?>

<?php
    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])){
        $orderid = intval($_POST['order_id']);

        $stmt2 = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt2->bind_param("i", $orderid);
        $stmt2->execute();
        $stmt2->close();

        $stmt3 = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt3->bind_param("i", $orderid);
        $stmt3->execute();
        $stmt3->close();

         // Optional: give feedback and redirect to prevent resubmission
        $_SESSION['success'] = "Order #$orderid deleted successfully.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }


?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Product Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminNavbarStyle.css">
    <link rel="stylesheet" href="footerStyle.css">

    <style>
        h2{
            text-align: center;
            color:chocolate;
            margin: 20px 0;
        }


        table th, table td{
            color:brown;
            border: 1px solid chocolate;
            font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;

        }
        table{
            text-align: center;
            background-color:bisque;
            border-radius: 15px;
            margin: 0 auto; /* center the table */
            border: 1px solid brown;
            border-collapse: separate; /* Must use 'separate' instead of 'collapse' */
            border-spacing: 0;          /* Prevent spacing gaps */
            overflow: hidden;
            margin-left: 10px;
            margin-right: 10px;
            margin-bottom: 10px;

        }
        
        .status-pending {
    background-color: #f0ad4e; /* orange-yellow */
    color: white;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
}

.status-shipped {
    background-color: #5bc0de; /* light blue */
    color: white;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
}

.status-delivered {
    background-color: #5cb85c; /* green */
    color: white;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
}

.update-btn {
    background-color: #337ab7; /* Bootstrap blue */
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.update-btn:hover {
    background-color: #286090;
}

.delete-btn {
    background-color: crimson;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.delete-btn:hover {
    background-color: darkred;
}

.status-select {
    padding: 6px 10px;
    border: 2px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    font-weight: bold;
    color: #333;
    cursor: pointer;
    transition: border-color 0.2s ease-in-out;
}

.status-select:focus {
    border-color: #337ab7; /* highlight on focus */
    outline: none;
}

table th:first-child {
    border-top-left-radius: 15px;
}
table th:last-child {
    border-top-right-radius: 15px;
}
table tr:last-child td:first-child {
    border-bottom-left-radius: 15px;
}
table tr:last-child td:last-child {
    border-bottom-right-radius: 15px;
}


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
            padding: 14px 400px;
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
        padding-top: 60px; /* prevent overlap */
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

@media screen and (min-width: 481px) and (max-width: 920px){
   
     .table-container {
        max-height: calc(100vh - 120px); /* viewport minus navbar+footer */
        overflow-y: auto;               /* vertical scroll */
        margin: 10px;
    }

    .table-container table {
        width: 100%;
        border-collapse: separate;
    }
    /* Sticky headers only inside this scrollable container */
    .table-container thead th {
        position: sticky;
        top: 0;
        background-color: bisque; /* same as header */
        z-index: 5;
    }
   

}

    </style>
</head>
<body>
      <?php include 'admin_navbar.php'; ?>

    <div class="page-content">
    <h2>Product Orders</h2>

   <?php
    if (isset($_SESSION['success'])) {
    echo '<div style="color: green; border: 1px solid green; padding: 8px; margin: 10px 0; border-radius: 5px; background: #eaffea; text-align:center;">'
         . htmlspecialchars($_SESSION['success']) . 
         '</div>';
    unset($_SESSION['success']); // clear it after showing
    }
    ?>

    <div class="table-container">
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Station</th>
            <th>Payment</th>
            <th>Delivery Address</th>
            <th>Product Info</th>
            <th>Total Fee</th>
            <th>Order Date</th>
            <th>Customer Type</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['station']) ?></td>
            <td><?= htmlspecialchars($row['payment_method']) ?></td>
            <td><?= htmlspecialchars($row['delivery_address']) ?></td>
            <td><?= $row['products_info'] ?></td>
            <td><?= number_format($row['total_fee'], 2) ?></td>
            <td><?= $row['order_date'] ?></td>
            <td><?= ucfirst($row['customer_type']) ?></td>
            <td><span class="status-<?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
            <td>
                <form method="POST" action="update_product_order_status.php" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                    <select name="status" class="status-select">
                        <option <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option <?= $row['status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option <?= $row['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                    </select>
                    <button type="submit" class="update-btn">Update</button>
                </form><br><br>
                <form method="POST" action="" style="display:inline;"
                    onsubmit="return confirm('Are you sure you want to delete this order?');">
                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    </div>
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