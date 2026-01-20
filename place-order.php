<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $station = trim($_POST['station']);
    $pickup_code = password_hash(trim($_POST['pickup_code']), PASSWORD_DEFAULT);//hash password or code*//orders e customer_id connect sama users aja enggak usah sama customers*
    $payment = trim($_POST['payment']);

    $userId = $_SESSION['user_id'] ?? 0;
    $cart = $_SESSION['cart_user_' . $userId] ?? [];


    if (empty($cart)) {
        header('Location: cart.php');
        exit;
    }

    // ✅ Final stock check
    foreach ($cart as $id => $item) {
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            $_SESSION['errorMsg'] = "Product '{$item['name']}' no longer exists.";
            header('Location: cart.php');
            exit;
        }

        if ($item['qty'] > $row['stock']) {
            $_SESSION['errorMsg'] = "Sorry, only {$row['stock']} left for '{$item['name']}'. Please update your cart.";
            header('Location: cart.php');
            exit;
        }
    }

    // ✅ Deduct stock
    foreach ($cart as $id => $item) {
        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->bind_param('ii', $item['qty'], $id);
        $stmt->execute();
    }

    
    // ✅ Get customer_id and customer_type
    if (isset($_SESSION['client_id'])) {
        $client_id = $_SESSION['client_id'];//id from users table

        $stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id = ?");
        $stmt2->bind_param('i', $client_id);
        $stmt2->execute();
        $stmt2->bind_result($user_id); //id dari client_data
        $stmt2->fetch();
        $stmt2->close();

        $customer_type = 'client';
        $role_id = $client_id;
        
        $stmtUser = $conn->prepare("SELECT u.name, u.email
        FROM client_data cd
        JOIN users u ON cd.user_id = u.id
        WHERE cd.id = ?
        ");

        $stmtUser->bind_param("i", $user_id);

    } elseif (isset($_SESSION['nanny_id'])) {
        $nanny_id = $_SESSION['nanny_id'];//id from users
        $stmt3 = $conn->prepare("SELECT id FROM nanny_data WHERE user_id = ?");
        $stmt3->bind_param('i', $nanny_id);
        $stmt3->execute();
        $stmt3->bind_result($user_id);//id dari nanny_data
        $stmt3->fetch();
        $stmt3->close();

        $customer_type = 'nanny';
        $role_id = $nanny_id;//id from users
        
        $stmtUser = $conn->prepare("SELECT u.name, u.email
        FROM nanny_data nd
        JOIN users u ON nd.user_id = u.id
        WHERE nd.id = ?
        ");

        $stmtUser->bind_param("i", $user_id);

    }else {
        $_SESSION['errorMsg'] = "You must log in to place an order.";
        header('Location: login.php');
        exit;
    }


    $stmtUser->execute();
    $result = $stmtUser->get_result();
    $user = $result->fetch_assoc();
    $stmtUser->close();

    if ($user) {
        $name = $user['name'];
        $email = $user['email'];
    } else {
        $_SESSION['errorMsg'] = "Unable to find your customer information. Please contact support.";
        $_SESSION['customer'] = $role_id;
        header('Location: cart.php');
        exit;
    }
    
    // ✅ Save order header
    $grandTotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
    $status = 'pending';
    date_default_timezone_set('Asia/Taipei');
    $orderDate = date('Y-m-d H:i:s');//order date salah waktunya*
    
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, email, address, phone, station, pickup_code, payment_method, total_amount, order_date, customer_id, customer_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssdsiss', $name, $email, $address, $phone, $station, $pickup_code, $payment, $grandTotal, $orderDate, $role_id, $customer_type, $status);
    $stmt->execute();
    $orderId = $stmt->insert_id; // Get auto-increment order ID
    $stmt->close();

    // ✅ Save order items
    foreach ($cart as $id => $item) {
        $subtotal = $item['price'] * $item['qty'];
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiid', $orderId, $id, $item['qty'], $item['price']);
        $stmt->execute();

    }
    
    $stmt->close();
 


    //  Clear cart
    // Deduct stock, insert orders/items, then:
    unset($_SESSION['cart_user_' . $userId]);

    
    //  Redirect to thank you page
    header("Location: thank-you.php?order=$orderId");
    exit;

} else {
    header('Location: checkout.php');
    exit;
}
?>
