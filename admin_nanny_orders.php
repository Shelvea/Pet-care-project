<?php
session_start();
include 'db_connect.php';
    
    if (isset($_POST['order_id']) && $_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $order_id = intval($_POST['order_id']);

        $stmt = $conn->prepare("DELETE FROM nanny_orders WHERE id = ?;");
        $stmt->bind_param("i", $order_id);

        if($stmt->execute()){
            $_SESSION['message'] = "delete successful";   
             $_SESSION['message_type'] = "green";  // success message         
           
        }else{
            $_SESSION['message'] = "error cannot delete nanny orders.";
            $_SESSION['message_type'] = "red";    // error message
          
        }

        header("Location: admin_nanny_orders.php");
        exit;
    }

      


?>

<?php
// Fetch nanny orders
$sql = "SELECT no.id, no.order_date, no.status, no.notes, 
       p.name, p.gender, p.type, p.breed, 
       cd.client_name AS customer_name, cd.email, cd.phone_number, cd.address, 
       nd.nanny_name, nd.email AS nanny_email, nd.phone_number AS nanny_phone,
       GROUP_CONCAT(bs.service_type SEPARATOR ', ') AS services
FROM nanny_orders no
JOIN client_data cd ON no.customer_id = cd.id
LEFT JOIN nanny_data nd ON no.nanny_id = nd.id
JOIN pets_info p ON no.pet_id = p.id
LEFT JOIN nanny_order_services nos ON no.id = nos.order_id
LEFT JOIN branch_services bs ON nos.service_id = bs.id
GROUP BY no.id
ORDER BY no.order_date ASC";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Service Orders</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminNavbarStyle.css">
    <link rel="stylesheet" href="footerStyle.css">
    <style>
     h2{
            text-align: center;
            color:chocolate;
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
            margin-left: 10px;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid brown;
            border-collapse: separate; /* Must use 'separate' instead of 'collapse' */
            border-spacing: 0;          /* Prevent spacing gaps */
            overflow: hidden;

        }
        .status-pending {
    background-color: #f0ad4e; /* orange-yellow */
    color: white;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
}

.status-in-progress {
    background-color: #5bc0de; /* light blue */
    color: white;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
}

.status-completed {
    background-color: #5cb85c; /* green */
    color: white;
    padding: 4px 8px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
}

.delete-btn {
    background-color: #337ab7; /* Bootstrap blue */
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.delete-btn:hover {
    background-color: #286090;
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
    h2{
        text-align: center;
        color:chocolate;
    
    }

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
    padding-top: 60px;    /* prevent navbar overlap */
    padding-bottom: 120px; /* 👈 match or exceed footer height */
    overflow-y: auto;     /* allow vertical scrolling */
  }

  html, body {
    height: auto;         /* let content decide height */
    min-height: 100%;     /* ensure page stretches */
      /* prevent sideways scroll */
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
     .order-table {
        margin-bottom: 130px; /* leave enough space for the footer */
        
    }

}
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
<h2>Manage Nanny Service Orders</h2>
<!-- Show message above table -->
<?php
if (isset($_SESSION['message'])) {
    $color = $_SESSION['message_type'] ?? 'green'; // default green
    echo "<div style='text-align:center; margin:10px; color:$color; font-weight:bold;'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message'], $_SESSION['message_type'] );
} 
?>
<table class="order-table" border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Order ID</th>
        <th>Order Date</th>
        <th>Status</th>
        <th>Notes</th>
        <th>Service type</th>
        <th>Pet info</th>
        <th>Customer info</th>
        <th>Nanny info</th>
        <th>Actions</th>
    </tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['order_date'] ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td><?= htmlspecialchars($row['notes']) ?></td>
    <td><?= htmlspecialchars($row['services']) ?></td>
    <td><?= 'Pet name: '. htmlspecialchars($row['name']).'<br>gender: '. htmlspecialchars($row['gender']). '<br>type: '. htmlspecialchars($row['type']) . '<br>breed: '.htmlspecialchars($row['breed']) ?></td>
    <td><?= 'Customer name: '. htmlspecialchars($row['customer_name']) . '<br>email: ' . htmlspecialchars($row['email']) . '<br>phone: '.htmlspecialchars($row['phone_number']) . '<br>Address: ' . htmlspecialchars($row['address']) ?></td>
    <td><?= 'Nanny name: '. htmlspecialchars($row['nanny_name']) . '<br>email: ' . htmlspecialchars($row['nanny_email']) . '<br>phone: '.htmlspecialchars($row['nanny_phone']) ?></td>
    <td>
        <form method="POST" action="" style="display:inline;">
            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
            <button type="submit"  class="delete-btn">Delete</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
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