<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'You must be logged in to update cart.'
        ]);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Load this user's cart only
    $cart = $_SESSION['cart_user_' . $user_id] ?? [];
 

    $id = intval($_POST['id']);
    $qty = intval($_POST['qty']);

    if (isset($cart[$id])) {
        // Get stock from DB
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $stock = (int)$row['stock'];

            if ($qty > $stock) {
                $cart[$id]['qty'] = $stock; // set to max stock
                $_SESSION['cart_user_' . $user_id] = $cart;

                echo json_encode([
                    'status' => 'error',
                    'message' => "Max stock for this item is $stock.",
                    'newQty' => $stock
                ]);
                exit;
            } else {
                $cart[$id]['qty'] = max(1, $qty);
                $_SESSION['cart_user_' . $user_id] = $cart;

                echo json_encode([
                    'status' => 'success',
                    'message' => "Quantity updated.",
                    'newQty' => $cart[$id]['qty']
                ]);
                exit;
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "Product not found."
            ]);
            exit;
        }
    }
}

echo json_encode([
    'status' => 'error',
    'message' => "Invalid request."
]);
