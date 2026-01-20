<!DOCTYPE HTML>
<html>

<head>
    
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<style>


html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-family: 'Trebuchet MS', sans-serif;
    background-image: url('https://img.freepik.com/free-vector/cat-lovr-pattern-background-design_53876-100662.jpg');
}

/* Desktop layout (default) */
h1 {
    text-align: center;
    margin-top: 50px;
    color: darkolivegreen;
}

.button-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 50px;
}

.button-container button {
    border-radius: 10px;
    font-size: 18px;
    background-color: white;
    color: olivedrab;
    font-weight: bold;
    border: 1px solid olivedrab;
    padding: 5px 10px;
    cursor: pointer;
    transition: background-color 0.3s, color 0.3s;
}

.button-container button:hover {
    background-color: chartreuse;
    color: darkblue;
}



/* --- Mobile screens --- */
@media screen and (max-width: 480px) {
    body {
        display: flex;
        flex-direction: column;
        justify-content: flex-start; /* start from top */
        align-items: center;
        height: 100vh;
    }

    h1 {
        font-size: 28px;          /* bigger header on mobile */
        margin-top: 15vh;          /* slightly above middle */
        text-align: center;
    }

    .button-container {
        flex-direction: row;    /* keep side by side */
        gap: 15px;              /* spacing between buttons */
        margin-top: 30px;
        justify-content: center;
        align-items: center;
    }

    .button-container button {
        font-size: 18px;           /* bigger buttons for touch */
        padding: 12px 24px;        /* bigger padding */
        width: auto;            /* no fixed width */
        max-width: none;        /* allow natural sizing */
    }
}
</style>

<title>寵物托育系統!</title>
</head>

<body>

<h1>歡迎來到寵物托育系統!</h1>

<div class="button-container">
    <button onclick="window.location.href='register.php';">註冊</button>
    <button onclick="window.location.href='login.php';">登入</button>
</div>

</body>
</html>
