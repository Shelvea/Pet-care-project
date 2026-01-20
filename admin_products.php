<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Products</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="adminNavbarStyle.css">
<link rel="stylesheet" href="footerStyle.css">

<style>
    .admin-products-container {
    max-width: 10000px;
    margin: 80px auto; /* leave space for fixed navbar */
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    border-radius: 12px;
}

.admin-products-container h1 {
    color: chocolate;
    margin-bottom: 20px;
    text-align: center;
}

.add-btn {
    display: inline-block;
    background: chocolate;
    color: white;
    padding: 10px 15px;
    border-radius: 6px;
    text-decoration: none;
    margin-bottom: 15px;
    transition: background 0.3s;
}

.add-btn:hover {
    background: #d2691e;
}

.product-table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px auto;
  border-collapse: separate; /* IMPORTANT: don't use collapse */
  border-spacing: 0;
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
.product-table th,
.product-table td {
    padding: 12px 10px;
    border: 1px solid coral;
    text-align: center;
}

.product-table img {
    border-radius: 8px;
}


.action-btn{
    padding: 6px 12px;
    margin-right: 5px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    font-size: 8px;
}
.delete-btn{
    background-color: #e74c3c; /* Red */
    color: white;
}
.delete-btn:hover {
    background-color: #c0392b;
}
.edit-btn {
    background-color: #3498db; /* Blue */
    color: white;
}

.edit-btn:hover {
    background-color: #2980b9;
}
th{
    color:chocolate;
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
            padding: 14px 300px;
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
    .product-table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px auto;
    border-collapse: separate; /* IMPORTANT: don't use collapse */
    border-spacing: 0;
    border: 1px solid brown;
    border-radius: 10px;
    
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
}
</style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

<div class="admin-products-container">
    <h1>Manage Products</h1>
    <a href="add_product.php" class="add-btn"><i class="fa fa-plus"></i> Add New Product</a>

    <table class="product-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Image</th>
                <th>Stock</th>
                <th>Rating</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
           <?php
            $sql = "SELECT * FROM products ORDER BY id ASC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>$" . number_format($row['price'], 2) . "</td>";
                    $image = htmlspecialchars($row['image']);
                    echo "<td><img src='$image' width='50'></td>";
                    echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['rating']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                    echo "<td>
                        <form action='delete_product.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' class='action-btn delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</button>
                        </form>
                        <form action='edit_product.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <button type='submit' class='action-btn edit-btn'>Edit</button>
                        </form>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No products found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
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