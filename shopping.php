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
    body {
      margin: 0;
      font-family: Arial, sans-serif;

      /* flexbox layout */
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .content {
      flex: 1; /* take remaining space */
      padding-top: 60px;   /* match navbar height */
      text-align: center;
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

  <div class="content">
    <img src="https://www.padoniavets.com/sites/default/files/field/image/cats-and-dogs.jpg" alt="cat and dog" width="300" height="300">
    <!-- content-->
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
