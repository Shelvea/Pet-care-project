<!DOCTYPE HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');   
            font-family: 'Trebuchet MS', sans-serif;
        }
        h1 {
            text-align: center;
            color: chocolate;
        }
        .required {
            color: red;
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
        small {
            color: crimson;
            font-size: 12px;
        }
        .button-container {
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
        
        .password-wrapper {
    position: relative; /* parent relative for eye position */
    display: flex;
    align-items: center;
}

.password-wrapper input[type="password"] {
    width: 100%;
    padding-right: 40px; /* space for the eye icon */
}

.toggle-password {
    position: absolute;
    right: 10px;          /* position eye on right */
    cursor: pointer;
    user-select: none;    /* prevent text selection */
    font-size: 18px;
    color: #555;          /* eye icon color */
}

.toggle-password:hover {
    color: chocolate;     /* change color on hover */
}

select {
  font-family: 'Trebuchet MS', sans-serif; /* Change font */
  font-size: 16px;                         /* Change size */
  color: chocolate;                        /* Text color */
  background-color: #f5f5dc;                /* Background color */
  border: 2px solid chocolate;             /* Border color */
  border-radius: 8px;                      /* Rounded corners */
  padding: 5px 10px;
}

option {
  font-family: 'Trebuchet MS', sans-serif;
  font-size: 15px;
  color: darkslategray;
  background-color: #fff8dc; /* Option background */
}

 /* Mobile Responsive */
    @media screen and (max-width: 480px) {
        form {
        grid-template-columns: 120px 200px; /* narrower columns */
        row-gap: 8px;
        column-gap: 10px;
        padding: 10px 30px;
    }

    label {
        font-size: 13px;
    }

    input, textarea, select {
        font-size: 13px;
        padding: 4px;
    }

    small {
        font-size: 10px;
    }

    .button-container button {
        font-size: 13px;
        padding: 6px 10px;
    }

    h1 {
        font-size: 20px;
    }
  

    }

</style>
<title>寵物托育系統!</title>
</head>


<body>
    <h1>顧客註冊頁面</h1>
    <form action="uploadClient.php" method="post" enctype="multipart/form-data">
        <label for="fileToUpload">請上傳個人照片:<span class="required">*</span></label>
        <input type="file" name="fileToUpload" id="fileToUpload" accept="image/*" required>

        <label for="ClientName">顧客名字:<span class="required">*</span></label>
        <input type="text" name="ClientName" id="ClientName" required>

        <label for="phone">電話:<span class="required">*</span></label>
        <div>
            <input type="tel" id="phone" name="phone" placeholder="0912-345-678" 
                   pattern="[0-9]{4}-[0-9]{3}-[0-9]{3}" required>
            <small>Format: 0912-345-678</small>
        </div>

        <label for="dateOfBirth">出生日期:<span class="required">*</span></label>
        <input type="date" id="dateOfBirth" name="dateOfBirth" required>

        <label for="emailAddress">電子郵件:<span class="required">*</span></label>
        <input type="email" id="emailAddress" name="emailAddress" required>

        <label for="Address">地址:<span class="required">*</span></label>
        <input type="text" id="Address" name="Address" required>

        <label for="username">用戶名:<span class="required">*<span></label>
        <input type="text" id="username" name="username" maxlength="20" required>        
        
        <label for="password">密碼:<span class="required">*</span></label>
       
        
<div class="password-wrapper">
    <input type="password" id="password" name="password" minlength="8" maxlength="16" required>
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


<label for="secret_question">密碼提示問題 : <span class="error">*</span></label>
  <select name="secret_question" id="secret_question" required>
    <option value="" disabled selected>-- 請選擇一個問題 --</option>
    <option value="你的第一隻寵物名字是什麼？">你的第一隻寵物名字是什麼？</option>
    <option value="你母親的出生地是？">你母親的出生地是？</option>
    <option value="你就讀的第一所學校是？">你就讀的第一所學校是？</option>
    <option value="你最喜歡的食物是？">你最喜歡的食物是？</option>
    <option value="你父親的名字是？">你父親的名字是？</option>
  </select>




  <label for="secret_answer">密碼提示答案 : <span class="error">*</span></label>
  <input type="text" name="secret_answer" id="secret_answer" maxlength="50" required>



        <div class="button-container">
            <button type="submit">確認</button>
            <button type="button" onclick="window.location.href='register.php';">返回</button>
        </div>
    </form>
</body>
</html>
