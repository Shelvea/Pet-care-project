<?php
session_start();
?>

<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <style>
        body{
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');   
            font-family: 'Trebuchet MS', sans-serif;
        }
        form{
            text-align:center;
            
        }
        .container{
            text-align:center;
            background-color:rgba(127, 255, 0, 0.9);
            padding: 20px 40px;
            margin: 0 auto; 
            max-width: 300px;
            border-radius: 10px;
            border: 1px solid chocolate;  
        }
        h1{
            color:chocolate;
            text-align:center;

        }
        .message-container{
            margin-top: 10px;
            text-align:left;
            
        }
        .type-container{
            margin-top: 10px;
            text-align:left;
        }
        label{
            color:brown;
            font-weight:bold;
        }
        button{
            margin-top:20px;
            background-color: chocolate;
            color:white;
            font-weight: bold;
            border-radius: 10px;
            padding: 5px 5px;
            
        }
        button:hover{
            color: #6B8E23;
            background-color:#F0E68C;

        }
        .subject-container{
            text-align:left;
        }
        .button-container{
            text-align:center;
        }
        option{
            background-color: beige;
        }
        .select-container{
            background-color: beige;
            color: #6B8E23;
            font-weight:bold;
            border-radius: 10px;
            padding: 5px 5px;
            border: 2px solid #FFF8DC;
        }
        .select-container:hover{
            background-color: #FFF8DC;
            color: #6B8E23;
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
            
        }
        .back-container:hover{
            color:chocolate;
            background-color: bisque;
        }
        .top-left {
            position: absolute;
            top: 20px;
            left: 20px;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
        }
          /* Mobile Responsive */
    @media screen and (max-width: 480px) {
        .back-container{
            padding: 8px 8px;
            border-radius: 8px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            
        }
          .top-left {
            position: absolute;
            top: 10px;
            left: 20px;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
        }
    }
    </style>
    <title>Contact Form</title>
</head>

<html>
    <body>
        <h1>Contact Form</h1>
        <div class="container">
<form action="send_message.php" method="POST">
<div class="subject-container">  
<label>Subject:</label>
  <input type="text" name="subject" required style="width: 100%; margin-top:5px;border-radius:5px;border:1px solid brown;"><br>
    </div>

  <div class="message-container">
  <label >Message:</label><br><br>
  <textarea name="message" rows="10" cols="30" style="border-radius:10px; border:1px solid brown;" required></textarea><br>
    </div>

    <div class="type-container">
  <label>Type:</label>
  <select name="type" class="select-container">
    <option value="feedback">Feedback</option>
    <option value="question">Question</option>
    <option value="report">Report</option>
    <option value="other">Other</option>
  </select>
    </div>

<div class="button-container">
  <button type="submit">Send Message</button>
    </div>

</form>
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
</body>
</html>