<?php
// Database settings
$servername = "localhost:3307";
$db_username = "Shelvea"; // your MySQL username
$db_password = "";        // your MySQL password
$dbname = "petdata";      // your database name

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
$conn->set_charset("utf8");
// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// If no error, connection is ready to use
?>
