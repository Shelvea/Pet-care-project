<?php
header('Content-Type: application/json');
require 'db_connect.php'; // Or include your DB connection code
//API Application Programming Interface endpoint
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);

    // Search nanny_data
    $sql = "SELECT secret_question FROM nanny_data WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);//s mean string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "role" => "nanny",
            "secret_question" => $row['secret_question']
        ]);
        exit();
    }

    // Search client_data
    $sql = "SELECT secret_question FROM client_data WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "role" => "client",
            "secret_question" => $row['secret_question']
        ]);
        exit();
    }

    // User not found
    echo json_encode([
        "success" => false,
        "error" => "❌ Username not found!"
    ]);
}
?>