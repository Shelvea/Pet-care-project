<!DOCTYPE html>
<html lang="en">

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="adminNavbarStyle.css">
<link rel="stylesheet" href="footerStyle.css">
<style>
    h1{
        text-align: center;
        color:crimson;
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
    <h1>Welcome Admin👋</h1>
    <?php include 'footer.php'; ?>

    <!-- Small script to toggle menu -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.AdminNavbar').classList.toggle('active');
    });
  });
</script>
</body>

</html>