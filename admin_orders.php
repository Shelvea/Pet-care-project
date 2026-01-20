<!DOCTYPE html>
<html lang="en">

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Orders</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="adminNavbarStyle.css">
<link rel="stylesheet" href="footerStyle.css">


<style>
     

       
        h2 {
            color: #333;
            margin-bottom: 40px;
            text-align: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .card-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            color: #333;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            font-size: 22px;
            margin-bottom: 10px;
            font-family: 'Segoe UI', sans-serif;
        }

        .card p {
            font-size: 15px;
            color: #555;
            font-family: 'Segoe UI', sans-serif;
        }

        .card.product {
            background-color: #ffefd5;
        }

        .card.nanny {
            background-color: #e0ffe0;
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
            padding: 14px 130px;
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
        padding-top: 50px; /* prevent overlap */
    }
}
    </style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="container">
        <h2>Manage Orders</h2>
        <div class="card-container">
            <a href="admin_product_orders.php" class="card product">
                <h3>🛍️ Product Orders</h3>
                <p>View and update physical product orders.</p>
            </a>

            <a href="admin_nanny_orders.php" class="card nanny">
                <h3>🧑‍🍼 Nanny Service Orders</h3>
                <p>Manage bookings for nanny services.</p>
            </a>
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