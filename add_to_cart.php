<?php
session_start();
header('Content-Type: application/json');

// Prevent accidental output
ini_set('display_errors', 0);
error_reporting(E_ALL);

require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to add items to cart.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $productId = intval($_POST['id']);
    $qty = intval($_POST['qty'] ?? 1); // Default quantity = 1

    // Fetch product from DB
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    error_log("Looking for product ID: $productId");
    $product = $result->fetch_assoc();

    if (!$product) {
        error_log("Product ID $productId not found in database");
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        exit;
    }

    $availableStock = (int)$product['stock'];

    // Initialize user-specific cart if not set
    if (!isset($_SESSION['cart_user_' . $user_id])) {
        $_SESSION['cart_user_' . $user_id] = [];
    }
    $cart =& $_SESSION['cart_user_' . $user_id]; // reference

    // Current qty in cart
    $currentQty = isset($cart[$productId]['qty']) ? $cart[$productId]['qty'] : 0;

    // Check if adding exceeds stock
    if ($currentQty >= $availableStock) {
        echo json_encode([
            'status' => 'error',
            'message' => "You already have all available stock ($availableStock) of '{$product['name']}' in your cart."
        ]);
        exit;
    }

    // Calculate allowed quantity to add
    $qtyToAdd = min($qty, $availableStock - $currentQty);

    // Add/update product in cart
    if (!isset($cart[$productId])) {
        $cart[$productId] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'qty' => 0,
            'image' => $product['image']
        ];
    }
    $cart[$productId]['qty'] += $qtyToAdd;

    if ($qtyToAdd < $qty) {
        echo json_encode([
            'status' => 'partial',
            'message' => "Only $qtyToAdd item(s) added to cart because stock is limited to $availableStock."
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => 'Added to cart'
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}
?>
