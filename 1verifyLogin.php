<?php
session_start();

// Database connection
include "db_connect.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$role = $_POST['role'] ?? '';  // Get the selected role
$username = $conn->real_escape_string($username);

$role_table_map = [
    'nanny'  => 'nanny_data',
    'client' => 'client_data',
    'admin'  => 'admins'
];


if (!isset($role_table_map[$role])) {
    $_SESSION['error'] = "請選擇有效的身份類別";
    header("Location: login.php");
    exit();
}

$table = $role_table_map[$role];

$stmt = $conn->prepare(//
    "SELECT u.id AS u_id, u.status, u.suspended_until, r.*
    FROM users u
    JOIN {$table} r ON r.user_id = u.id
    WHERE r.username = ?
    ");

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if($user){
    if($user['status'] === 'suspended' ){
        if($user['suspended_until'] !== null && strtotime($user['suspended_until']) > time()){
            //Suspension active
            $_SESSION['error'] = "Your account is suspended until " . $user['suspended_until'];
            header("Location: login.php");
            exit();

        }else if($user['suspended_until'] !== null && strtotime($user['suspended_until']) <= time()){
        // Suspension expired, reactivate
        $stmt = $conn->prepare("UPDATE users SET status='active', suspended_until = NULL WHERE id=?");
        $stmt->bind_param("i", $user['u_id']);//
        $stmt->execute();
        $user['status'] = 'active'; // continue login
    }

    }
 
    if (password_verify($password, $user['pass'])) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Set specific session id
        $_SESSION["{$role}_id"] = $user['u_id'];
        $_SESSION['user_id'] = $user['u_id'];  // <-- add this here

        // ✅ Set admin_id if role is admin
        if ($role === 'admin') {
        $_SESSION['admin_id'] = $user['u_id'];
        $dashboard = "adminDashboard.php";

        } else if ($role === 'nanny' ) {//non editable email and username
            $_SESSION['customer_id'] = $user['u_id']; //id of users table
            $dashboard = "nannyMainPage.php";
            
        } else if($role === 'client'){
            $_SESSION['customer_id'] = $user['u_id'];
            $dashboard = "clientPage.php";
        
        } 

        $conn->close();
        header("Location: $dashboard");
        exit();

    } else {

        $conn->close();
        $_SESSION['error'] = "❌ 密碼錯誤";
        header("Location: login.php");
        exit();
    }

} else {

    $conn->close();
    $_SESSION['error'] = "❌ 用戶名不存在";
    header("Location: login.php");
    exit();
}

?>