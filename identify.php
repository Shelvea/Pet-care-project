<!DOCTYPE HTML>
<html>
<head>
    <style>
    
    </style>
<title>寵物托育系統!</title>
</head>
<body>
<?php 
$identity = "";



if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Get selected identity
    $identity = $_POST["identity"];

    if($identity === "Nanny" ){//value is Nanny

        header("Location: nanny_register.php");
        exit();
    }elseif($identity === "Client"){//value is Client
        header("Location: client_register.php");
        exit();
    }
}

?>
</body>
</html>