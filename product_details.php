<?php
include 'db_connect.php'; // Connect DB
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo "<p style='color:red;'>Product not found.</p>";
        exit;
    }

    echo "<h2>{$row['name']}</h2>";
    echo "<img src='{$row['image']}' width='200'>";
    echo "<p>{$row['description']}</p>";
    echo "<p>Price: $".number_format($row['price'],2)."</p>";
    echo "<p>Stock: {$row['stock']}</p>";

     // Show stock status
    if ($row['stock'] > 0) {
        echo "<p style='color:green;'>In Stock: {$row['stock']}</p>";
        
    } else {
        echo "<p style='color:red;'>Out of Stock</p>";
        echo "<button disabled>Out of Stock</button>";
    }

}
?>
