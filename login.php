<?php
  session_start();
  if (isset($_SESSION['error'])) {
    echo "<p style='color:red; text-align:center;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']); // Clear error after displaying
  }
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
<style>
        body
        {
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');   
        }
        
        h1{
            font-size: 40px;
            color: brown;
            text-align: center;    
        }
        p{
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            color: chocolate;
            font-weight: bold;
            text-align: center;

        }
        #bold{
            font-weight: bold;
            color:chocolate;
        }
        .error
        {
            color:red;
        }
        label .error{
            margin-left: 4px; /* space between label text and asterisk */
        }
        .button{
            color:white;
            background-color: brown;
        }
    
        form div{
            display: flex;
            justify-content: center;
            align-items: center; /* Align label and input vertically */
            margin-bottom: 15px;
            font-weight: bold;
              
        }
        form {
        text-align: center;
        
        }
        
        label {
          text-align: right; /* keep label right-aligned */
        }
        .output-container{
          text-align: center; /* center text inside the container */
          margin: 0 auto; /* horizontally center container itself */
          width: 300px; /* shrink-wrap content */
          background-color: rgba(127, 255, 0, 0.7);
   
          padding: 20px 30px;
          

          border: 1px solid chocolate;/* Adds a 1-pixel solid border with a chocolate brown color.*/
          border-radius: 8px;/* Rounds the corners of the container by 8 pixels.*/
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
      
          color: chocolate;
          font-family: 'Trebuchet MS', sans-serif;
          /*Uses Trebuchet MS font if available, otherwise falls back to a generic sans-serif font. */
        }
        .button-container{
          text-align: center;
          gap: 10px;
        }
        .button-container button{
          background-color: white;
          color: chocolate;
          font-weight: bold;
          border: 2px solid chocolate;
          padding: 5px 5px;
          border-radius: 8px;
          cursor: pointer;
          font-size: 15px;
          
        }
        .button-container button:hover{
           background-color:blueviolet;
          color:aqua;
        }
         .password-wrapper {
          display: flex;
          align-items: center;
          gap: 5px;
        }

    .password-wrapper input {
      width: 200px; /* fixed width */
      padding-right: 40px; /* space for eye */
      box-sizing: border-box;
    } 


    .password-wrapper label {
        white-space: nowrap;
      }
  
    .toggle-password {
         
    cursor: pointer;
            
}

.toggle-password:hover {
    color: chocolate;     /* change color on hover */
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
        .input{
        width: 210px;
      }
         /* Mobile Responsive */
    @media screen and (max-width: 480px) {
      .input{
        width: 210px;
      }
    }
</style>

<title>寵物托育系統!</title>
</head>

<body>

<h1>登入頁面</h1>


<p><span class="error">* 必填項目</span></p>

<div class="output-container">
<form action="1verifyLogin.php" method="post" enctype="multipart/form-data">
<!-- enctype="multipart/form-data" is required for file uploads -->
  
  <div>
  <label for="username">用戶名 : <span class="error">*</span></label>
  <input class="input" type="text" name="username" id="username" maxlength="20" required>
  </div>
  <br>
  
  
  <div  class="password-wrapper">
  <label for="password">密碼 : <span class="error">*</span></label>
  <input type="password" name="password" id="password" maxlength="16" required>
  <span id="togglePassword" class="toggle-password">👁️</span>
  </div>


   <script>
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    togglePassword.addEventListener("click", function () {
        // toggle type
        const type = password.type === "password" ? "text" : "password";
        password.type = type;

        // toggle icon (👁️ to 🙈)
        this.textContent = type === "password" ? "👁️" : "🙈";
    });
    </script>

<div>
  <label for="role">身份類別 : <span class="error">*</span></label>
  <select name="role" id="role" required>
    <option value="client">客戶</option>
    <option value="nanny">保姆</option>
    <option value="admin">管理員</option>
  </select>
</div>


  <br><br>
<div class="button-container">
<button type="submit" class="button">確認</button>
<button type="reset" class="button">重填</button>
</div>
</form>
</div>

<p style="text-align: center;">
    <a href="forgotPassword.php" style="color: chocolate;">忘記密碼？</a>
</p>

   <div class="top-left">
        <button onclick="window.location.href='index.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
    </div>
</body>
</html>