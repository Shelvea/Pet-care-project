<?php
session_start();
require 'db_connect.php';

$id = $_GET['id'];
$client_id = $_SESSION['customer_id'];//id from users

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id=?");
$stmt2->bind_param("i", $client_id);
$stmt2->execute();
$stmt2->bind_result($user_id);//id from client_data
$stmt2->fetch();
$stmt2->close();

// Get pet info
$stmt = $conn->prepare("SELECT * FROM pets_info WHERE id = ? AND client_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Pet not found or unauthorized.");
}
$pet = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Edit Pet</title>
    <style>
    body{
        background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');  
    }
  
    form {
            display: grid;
            grid-template-columns: 200px 300px; /* labels column + inputs column */
            row-gap: 15px; /* space between rows */
            column-gap: 20px; /* space between columns */
            background-color: rgba(127, 255, 0, 0.7);
            margin: 50px auto;
            padding: 20px;
            border: 1px solid chocolate;
            border-radius: 8px;
            width: fit-content;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
            label {
            text-align: right; /* align label text to right */
            font-weight: bold;
            color: crimson;
            align-self: center; /* vertically center label with input */
        }
        input, textarea {
            width: 100%; /* make inputs take full column width */
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
            top: 20px;
            left: 20px;
        }
        h2{
            text-align: center;
            color:chocolate;
        }
        .button-container{
            grid-column: span 2; /* span both columns */
            text-align: center;
            margin-top: 10px;
        }
        .button-container button {
            margin: 0 5px;
            background-color: chocolate;
            color: wheat;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .button-container button:hover {
            background-color: yellow;
            color: blueviolet;
        }

/* Mobile Responsive */
@media screen and (max-width: 480px) {
     .form-container {
        width: 280px; /* narrower on mobile */
        margin: 20px auto;
    }

    form {
        grid-template-columns: 90px 180px; /* smaller labels + inputs */
        row-gap: 10px;
        column-gap: 10px;
        font-size: 14px;
    }

    input, textarea, select {
        width: 100%; /* take full input column width */
        padding: 3px;
        border-radius: 3px;
    }
}

    </style>
</head>
<body>
<h2>Edit Pet</h2>

<div class="form-container">
<form action="edit_pet_submit.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $pet['id'] ?>">

    <label>Pet Name:</label><input type="text" name="name" value="<?= htmlspecialchars($pet['name']) ?>">
    <label>Gender:</label>
    <select name="gender">
        <option value="Male" <?= $pet['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $pet['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
    </select>

    <label>Type:</label>
     <select name="type">
        <option value="Cat" <?= $pet['type'] == 'Cat' ? 'selected' : '' ?>>Cat</option>
        <option value="Dog" <?= $pet['type'] == 'Dog' ? 'selected' : '' ?>>Dog</option>
    </select>

    
    <label>Breed:</label><input type="text" name="breed" value="<?= htmlspecialchars($pet['breed']) ?>">
    <label>Age:</label><input type="number" name="age" value="<?= htmlspecialchars($pet['age']) ?>">
    <label>New Picture (optional):</label><input type="file" name="picture">
    <label>Characteristic:</label><textarea name="note"><?= htmlspecialchars($pet['note']) ?></textarea>

    <div class="button-container">
    <button type="submit">Save</button>
    </div>
</form>
</div>

<div class="top-left">
<button  onclick="window.location.href='clientPage.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>

</body>
</html>
