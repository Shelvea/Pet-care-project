<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping site</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="footerStyle.css">
    <link rel="stylesheet" href="style-responsive.css"> <!-- Responsive & background for mobile screen -->
    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        body {
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');
            background-size: cover;
            background-position: center;
            font-family: 'Trebuchet MS', Arial, sans-serif;
            background-repeat: no-repeat;     /* 👈 Prevent tiling */
        }
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Vertically center content */
            align-items: center; /* Horizontally center content */
            text-align: center;
        }
        h1 {
            color: chocolate;
            font-weight: bold;
            margin-bottom: 30px;
            
        }
        .enter-container {
            padding: 13px 13px;
            color: mediumspringgreen;
            background-color: white;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 10px;
            font-family: 'Fredoka One', cursive;
            border: 2px solid mediumspringgreen;
            transition: 0.3s ease;
        }
        .enter-container:hover {
            color: palegreen;
            font-size: 22px;
        }


/* Only enlarge the text inside span, not the button */
.enter-container .hover-text {
    display: inline-block;
    transition: font-size 0.3s ease;
}

.enter-container:hover .hover-text {
    font-size: 22px; /* 👈 Increase only text size */
}

         .back-container{
            padding: 13px 13px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            
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
<div class="content">
    <h1>🐾 “Welcome to Pet Haven – Your one-stop shop for all your pet needs!”</h1>
    <button onclick="window.location.href='shopping.php'" class="enter-container"><span class="hover-text">🏡 Enter Shop</span></button>
</div>

<div class="top-left">
    <?php
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'client') {
        $backPage = 'clientPage.php';
    } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'nanny') {
        $backPage = 'nannyMainPage.php';
    } else {
        $backPage = '#'; // fallback
    }

    ?>
<button onclick="window.location.href='<?= $backPage ?>'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
