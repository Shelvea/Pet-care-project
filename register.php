<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Page</title>
    
<style>
     body
        {
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');   
        }
        
h1{
    text-align: center;
    color:chocolate;
    margin-top: 40px;
    font-size: 40px;
    
}
.radio-container{
    
    text-align: center;
}
.required{
    color:red;
}
.container{
    text-align: center;
    margin: 0 auto; /* horizontally center container itself */
    width: fit-content; /* shrink-wrap content */
    background-color: rgba(127, 255, 0, 0.7);
    padding: 15px 25px;
    border: 1px solid chocolate;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    color: chocolate;
    font-family: 'Trebuchet MS', sans-serif;
    font-size: 20px;
}
label{
    color:crimson;
    font-weight: bold;
}
.button-container button{
    background-color:chocolate;
    color:white;
    font-weight: bold;
    font-family: 'Trebuchet MS', sans-serif;
    border-radius: 5px;
    padding: 10px 10px;
    border: 1px solid chocolate;
    
}
.button-container button:hover{
    background-color: yellow;
    color:blueviolet;
}

</style>
<title>寵物托育系統!</title>
</head>
<body>
    <h1>註冊頁面</h1>
    <div class="container">
        <form action="identify.php" method="post">
        <label>請選擇您的身份:<span class="required">*</span></label>
        <div class="radio-container">
        <input type="radio" name="identity" value = "Nanny" id="Nanny" required>
        <label for="Nanny">保母</label><br>
        <input type="radio" name="identity" value= "Client" id="Client">
        <label for="Client">顧客</label>
        </div><br>
        <div class="button-container">
            <button type="submit" >確認</button>
            <button type="button" onclick="window.location.href='index.php';">返回</button>
        </div>
        </form>
    </div>


</body>
</html>