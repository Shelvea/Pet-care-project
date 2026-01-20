<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<style>
        body
        {
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            color: chocolate;
            font-weight: bold;
        }
        form
        {
            text-align: center;
        }
        h1, p{
            font-size: 40px;
            color: brown;
            text-align: center;          
        }        
        .button{
          font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
          color:chocolate;
          background-color: white;
          font-weight: bold;
        }
        h5
        {
            font-size: 30px;
            color: brown;
            justify-content: center;
            display:flex;
            text-align: center;
            line-height: 50px;
            
        } 
       
        .output-container{
          text-align: center; /* center text inside the container */
          margin: 0 auto; /* horizontally center container itself */
          width: fit-content; /* shrink-wrap content */
          background-color: chartreuse;
          padding: 15px 25px;
          border: 1px solid chocolate;
          border-radius: 8px;
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
          color: chocolate;
          font-family: 'Trebuchet MS', sans-serif;
        }
        .output-container p{
          margin: 10px 0;/* space between lines */
        }
        .return-button{
          text-align: center;
          margin-top: 60px;/* Push the button further down */
        }
        .return-button button{
          background-color: white;
          color: chocolate;
          font-weight: bold;
          border: 2px solid chocolate;
          padding: 10px 20px;
          border-radius: 8px;
          cursor: pointer;
          font-size: 16px;

        }
        .return-button button:hover{
          background-color:blueviolet;
          color:aqua;
        }
      
    </style>
</head>

<body>
<h1>顧客資訊</h1>

<div class="output-container">
<?php

require 'db_connect.php';

//Clean input
function test_input($data){
  return htmlspecialchars(stripslashes(trim($data)));
}

$ClientName = $Email = $Phone = $Address = $DateOfBirth = $uniqueName = $userName = $Password = $secretQuestion = $secretAnswer = "";


if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // if everything is ok, try to upload file
    //ClientName
    $ClientName = test_input($_POST["ClientName"]);
    
    $Email = test_input($_POST["emailAddress"]);
    
    $Phone = test_input($_POST["phone"]);    
    
    $Address = test_input($_POST["Address"]);
    
    $DateOfBirth = test_input($_POST["dateOfBirth"]);
   
    $userName = test_input($_POST["username"]);

    $Password = test_input($_POST["password"]);

    // Hash the password before saving
    $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);

    $secretQuestion = test_input($_POST["secret_question"]);

    $secretAnswer = test_input($_POST["secret_answer"]);
    // Hash the secret answer before saving
    $hashedSecretAnswer = password_hash($secretAnswer, PASSWORD_DEFAULT);

        // Check if email, phone, or username already exists in nanny_data
$checkDuplicate = $conn->prepare("SELECT id, email, phone_number, username FROM client_data WHERE email = ? OR phone_number = ? OR username = ?");
$checkDuplicate->bind_param("sss", $Email, $Phone, $userName);
$checkDuplicate->execute();
$checkResult = $checkDuplicate->get_result();

if ($checkResult->num_rows > 0) {

    echo '<div class="output-container">';
    // There is at least one duplicate, get details
    $duplicate = $checkResult->fetch_assoc();
    
    if ($duplicate['email'] === $Email) {
        echo "⚠️ 電子郵件已經被使用，請更換再試一次。<br>";
    }
    if ($duplicate['phone_number'] === $Phone) {
        echo "⚠️ 電話號碼已經被使用，請更換再試一次。<br>";
    }
    if ($duplicate['username'] === $userName) {
        echo "⚠️ 用戶名已經被使用，請更換再試一次。<br>";
    }

    echo '<div class="return-button">
    <button onclick="window.location.href=\'client_register.php\';">返回</button>
    </div>';
    
    echo '</div>';



    // Stop further execution
    exit;

} else {

    // Handle image upload
    $target_dir = "uploads/client_profile/";
    $uniqueName = uniqid() . "_" . basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $uniqueName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  // Check file type
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if ($check === false){
    echo "檔案不是圖片.";
    $uploadOk = 0;
  }

  // Allow only certain formats
  if (!in_array($imageFileType,["jpg", "jpeg", "png", "gif"])){
    echo "抱歉, 只允許 JPG, JPEG, PNG 和 GIF 檔案格式.";
    $uploadOk = 0;
  }

  if ($uploadOk == 1){
    if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
      echo "圖片 ".htmlspecialchars(basename($_FILES["fileToUpload"]["name"])). " 已成功上傳. <br>";
    
      // Insert data into database with picture path
      $stmt = $conn->prepare("INSERT INTO client_data (client_name, email, phone_number, address, date_of_birth, picture_path, username, pass, secret_question, secret_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssssssss", $ClientName, $Email, $Phone, $Address, $DateOfBirth, $target_file, $userName, $hashedPassword, $secretQuestion, $hashedSecretAnswer);

      if ($stmt->execute()){
        echo "資料已成功儲存到資料庫! <br><br>";
      }else{
        echo "儲存失敗: " . $stmt->error;
      }

      $stmt->close();

      // Show image preview
      echo "<img src='$target_file' width='180' height='180' alt='Client Image' /><br><br>";

    }else{
      echo "抱歉, 圖片上傳失敗. ";
    }
  }
}
}

$conn->close();
   
 echo "顧客名字: ".$ClientName ."<br>";
 
 echo "電子郵件: ".$Email ."<br>";
 
 echo "電話: ".$Phone."<br>";

 echo "地址: ".$Address."<br>";
 
 echo "生日日期: " . $DateOfBirth . "<br>";

 echo "用戶名: " . $userName . "<br>";
?>
</div>
<h5>註冊成功</h5>
<p>請返回再重新登入</p>

<div class="return-button">
<button onclick="window.location.href='index.php';">返回</button>
</div>

</body>
</html>