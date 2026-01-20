<!DOCTYPE html>
<html>
<head>
    <title>🔒 Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');   
            font-family: Arial, sans-serif;
            background-color: #fff8f0;
            text-align: center;
            margin-top: 50px;
        }
        .container {
            display: inline-block;
            background: rgba(255, 228, 196, 0.9); /* light cream */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
        }
        input, button, select {
            margin: 10px;
            padding: 8px;
            width: 250px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        button {
            background-color: chocolate;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        #secret-question {
            margin-top: 15px;
            color: darkred;
            font-weight: bold;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .container-field {
            display: flex;
            align-items: center; /* vertically center label and input */
            justify-content: center; /* center whole pair horizontally */
            margin: 10px 0; 
        }

    .container-field label {
        width: 120px; /* fixed width for labels so inputs align neatly */
        text-align: right;
        margin-right: 10px; /* space between label and input */
        font-family: Arial, sans-serif;
        color: crimson;
        font-weight: bold;
        font-size: 20px;
    }

    .container-field input {
        flex: 1; /* allow input to take remaining space */
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
.back-container{
            padding: 8px 16px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            display: inline-block;  /* prevents stretching */
            width: auto; 
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

        @media screen and (max-width: 480px) {
            .top-left {
            position: absolute;
            top: 1px;
            left: 1px;
            }

            .container {
            margin-top: 10px;
        }

        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔒 Forgot Your Password?</h2>
        <form id="forgotForm" method="POST" action="resetPasswordHandler.php">
            <input type="text" name="username" id="username" placeholder="Enter your username" required>
            <div id="secret-question"></div>
            
            <div class="container-field">
            <label for="answer" id="secretAnswer" style="display:none;">秘密答案: </label>
            <input type="text" name="answer" id="answer" placeholder="Enter secret answer" required style="display:none;">
            </div>

            <div class="container-field">
            <label for="new_password" id="newPassword" style="display:none;">新密碼: </label>
            
            <div class="password-wrapper">
    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" minlength="8" maxlength="16" required style="display:none;" >
    <span id="togglePassword" class="toggle-password" style="display:none;">👁️</span>
            </div>
            
            </div>

            <div class="container-field">
            <button type="submit" style="display:none;">Reset Password</button>
            </div>
        </form>
    </div>

    <script>
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("new_password");

    togglePassword.addEventListener("click", function () {
        // toggle type
        const type = password.type === "password" ? "text" : "password";
        password.type = type;

        // toggle icon (👁️ to 🙈)
        this.textContent = type === "password" ? "👁️" : "🙈";
    });
</script>

    <script>
        const usernameInput = document.getElementById("username");
        const secretQuestionDiv = document.getElementById("secret-question");
        const answerInput = document.getElementById("answer");
        const newPasswordInput = document.getElementById("new_password");
        const submitBtn = document.querySelector("button");

        //for label
        const labelSecretAnswer = document.getElementById("secretAnswer");
        const labelSecretPassword = document.getElementById("newPassword");


        usernameInput.addEventListener("blur", function () {
            const username = usernameInput.value.trim();
            if (username.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "getSecretQuestion.php", true);
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (this.status == 200) {//200 means request successful, checks if the server responded successfully (page found, no error, valid data).
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            secretQuestionDiv.textContent = "❓ " + response.secret_question;
                            answerInput.style.display = "block";
                            newPasswordInput.style.display = "block";
                            togglePassword.style.display = "block";
                            submitBtn.style.display = "block";
                            labelSecretAnswer.style.display = "block";
                            labelSecretPassword.style.display = "block";
                        } else {
                            secretQuestionDiv.innerHTML = "<span class='error'>" + response.error + "</span>";
                            answerInput.style.display = "none";
                            newPasswordInput.style.display = "none";
                            togglePassword.style.display = "none";
                            submitBtn.style.display = "none";
                            labelSecretAnswer.style.display = "none";
                            labelSecretPassword.style.display = "none";
                        }
                    }
                };
                xhr.send("username=" + encodeURIComponent(username));
            }
        });
    </script>

      <div class="top-left">
        <button onclick="window.location.href='login.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
    </div>

</body>
</html>
