<?php
session_start();
include 'db_connect.php';  // Your DB connection file

// Assuming customer is logged in and you have customer_id in session
$customer_id = $_SESSION['client_id'] ?? null;
if (!$customer_id) {
    die("請先登入");
}