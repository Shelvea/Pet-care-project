<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // First, optionally fetch and delete the image file from server
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image);
    if ($stmt->fetch() && $image) {
        $image_path = "product_picture/" . $image;
        if (file_exists($image_path)) {
            unlink($image_path); // delete the file
        }
    }
    $stmt->close();

    // Now delete the product from the database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_products.php?message=Product deleted successfully");
        exit;
    } else {
        echo "Error deleting product: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No product ID provided.";
}
?>
