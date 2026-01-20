<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body{
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');   
            text-align: center;
            font-family: 'Trebuchet MS', sans-serif;
        }
        button{
            background-color:chocolate;
            color:wheat;
            font-weight: bold;
            font-family: 'Trebuchet MS', sans-serif;
            border-radius: 5px;
            padding: 7px 7px;
            border: 1px solid chocolate;
            cursor: pointer;
            margin-top: 250px;
            font-size: 20px;
        }
        button:hover{
            background-color: yellow;
            color:blueviolet;
        }
        p{
            font-size: 20px;
            font-weight: bold;
            color: brown;
        }
    </style>
</head>

<body>

<?php
require 'db_connect.php';

$username = $_POST['username'];
$answer = $_POST['answer'];
$newPass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

// Check nanny_data
$sql = "SELECT secret_answer FROM nanny_data WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($answer, $row['secret_answer'])) {
        $sql = "UPDATE nanny_data SET pass=? WHERE username=?";//must the same name with column name in database
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $newPass, $username);
        $stmt->execute();
        echo "<p class='success'>✅ Password reset successfully for nanny!</p>";
        echo "<div><button type='button' onclick=\"window.location.href='login.php';\">返回</button></div>";
        exit();

    } else {
        echo "<p class='error'>❌ Wrong secret answer!</p>";
        echo "<div><button type='button' onclick=\"window.location.href='login.php';\">返回</button></div>";
        exit();
    }
}

// Check client_data
$sql = "SELECT secret_answer FROM client_data WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($answer, $row['secret_answer'])) {
        $sql = "UPDATE client_data SET pass=? WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $newPass, $username);
        $stmt->execute();
        echo "<p class='success'>✅ Password reset successfully for client!</p>";
        echo "<div><button type='button' onclick=\"window.location.href='login.php';\">返回</button></div>";
        exit();
    
    } else {
        echo "<p class='error'>❌ Wrong secret answer!</p>";        
        echo "<div><button type='button' onclick=\"window.location.href='login.php';\">返回</button></div>";
        exit();
    }
}

echo "<p class='error'>❌ Username not found!</p>";
echo "<div><button type='button' onclick=\"window.location.href='login.php';\">返回</button></div>";
?>


</body>
</html>