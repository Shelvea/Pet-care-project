<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body
        {
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
           
            
        }
        h1{
            text-align: center;
            color: chocolate;
            font-weight: bold;
            font-family: 'Trebuchet MS', sans-serif;
        }
        .dropdown {
            position: absolute; /* Take it out of normal flow */
            top: 10px;          /* Distance from top edge */
            left: 10px;         /* Distance from left edge */
        }


        /* Dropdown button */
        .dropbtn {
            background-color: chocolate;
            color: white;
            padding: 12px 16px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Dropdown content (hidden by default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 220px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            border-radius: 5px;
            z-index: 1;
        }

        /* Dropdown links */
        .dropdown-content a {
            
            display: block;
            padding: 8px 12px;
            background-color: white;
            color: chocolate;
            text-decoration: none;
            text-align: left;
            
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Show dropdown on hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .dropbtn {
            background-color: #cc6600;
        }
                /* Mobile Responsive */
    @media screen and (max-width: 480px) {
        h1{
            margin-top: 140px;
        }

    }
        </style>
<title>寵物托育系統!</title>
</head>
<body>
    <h1>
        保母登入成功
    </h1>
    
<div class="dropdown">
    <button class="dropbtn">
    <i class="fa-solid fa-house"></i> 選單
    </button>
    <div class="dropdown-content">
        <a href="welcomePage.php"><i class="fa-solid fa-cart-shopping"></i> 購物網站</a>
        <a href="nannyAvailableOrders.php"><i class="fa-solid fa-list-check"></i> Nanny Available Orders</a><!--nanny take care records-->
        <a href="contactForm.php"><i class="fa-solid fa-envelope"></i> 聯絡表格</a>
        <a href="nanny_messages.php">
        <i class="fa-solid fa-message"></i> My Messages
        </a>
        <a href="nannyCareRecord.php"><i class="fa-solid fa-book-open"></i> Nanny Care Records</a>
        <a href="viewNanny.php"><i class="fa-solid fa-user"></i> 查看個人資料</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> 登出</a>
    </div>
</div>
    
</body>    
</html>