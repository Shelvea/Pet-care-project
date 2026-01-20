<?php
session_start();
if (!isset($_SESSION['client_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Your Pet</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial;
            background-color: #fffce0;
            color: chocolate;
        }
        form {
            width: 60%;
            margin: auto;
            background-color: #fff3e0;
            padding: 20px;
            border: 2px solid chocolate;
            border-radius: 10px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            padding: 10px;
            background-color: chocolate;
            color: white;
            border: none;
            border-radius: 6px;
        }
        button:hover {
            background-color: darkorange;
        }
         .back-container{
            padding: 13px 13px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-weight: bold;
            text-align: center;
            
        }
        .back-container:hover{
            color:chocolate;
            background-color: bisque;
        }
        .top-left {
            position: absolute;
            top: 1px;
            left: 10px;
        }
        @media screen and (max-width: 480px) {
            .back-container{
            padding: 8px 8px;
            border-radius: 8px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-weight: bold;
            text-align: center;
            
        }
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Add Your Pet</h2>

<form action="add_pet_submit.php" method="POST" enctype="multipart/form-data">
    <label for="name">Pet Name:</label>
    <input type="text" name="name" required>

    <label for="gender">Gender:</label>
    <select name="gender" required>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>

    <label for="type">Type:</label>
     <select name="type" required>
        <option value="Cat">Cat</option>
        <option value="Dog">Dog</option>
    </select>

    <label for="breed">Breed (optional):</label>
    <input type="text" name="breed">

    <label for="age">Age (optional):</label>
    <input type="number" name="age" min="0">

    <label for="picture">Picture (optional):</label>
    <input type="file" name="picture" accept="image/*">

    <label for="note">Characteristic (optional):</label>
    <textarea name="note" rows="4"></textarea>

    <button type="submit">Add Pet</button>
</form>

<div class="top-left">
<button  onclick="window.location.href='clientPage.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>

</body>
</html>
