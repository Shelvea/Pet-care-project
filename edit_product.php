<?php
include 'db_connect.php';

$message = "";
$product = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $rating = floatval($_POST['rating']);
    $category = $_POST['category'];
    $old_image = $_POST['old_image'];
    $image_name = $old_image; // Default to old image

    // If a new image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];

        $upload_dir = "product_picture/";
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        
        // Get file extension and mime type
    $extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
    $mime_type = mime_content_type($image_tmp);
    
    if (in_array($extension, $allowed_extensions) && in_array($mime_type, $allowed_mime_types)) {
        move_uploaded_file($image_tmp, $image_path);
        $image_name = $image_path;//get the directory also
    } else {
        $message = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
        $image_name = $old_image; // Keep old image if new one is invalid
    }
        
}

    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, rating=?, category=?, image=? WHERE id=?");
    $stmt->bind_param("ssdidssi", $name, $description, $price, $stock, $rating, $category, $image_name, $id);

    if ($stmt->execute()) {
        $message = "Product updated successfully!";
    } else {
        $message = "Error updating product: " . $stmt->error;
    }
    $stmt->close();

} elseif (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "No product ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="adminNavbarStyle.css">
<link rel="stylesheet" href="footerStyle.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            text-align: center;
            color: chocolate;
        }
        input, textarea, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background-color: chocolate;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
        label{
            color:chocolate;
            font-weight: bold;
        }
        
.menu-toggle {
    display: none;  /* hidden on desktop */
    
    }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
<div class="form-container">
    <h2>Edit Product</h2>
    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <?php if ($product): ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
        <input type="hidden" name="update" value="1">
        <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">
        
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label>Description:</label>
        <textarea name="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>

        <label>Price:</label>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label>Stock:</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>

        <label>Rating:</label>
        <input type="number" name="rating" min="0" max="5" step="0.1" value="<?= htmlspecialchars($product['rating']) ?>" required>

        <label>Category:</label>
        <select name="category" required>
            <option value="Cat" <?= $product['category'] === 'Cat' ? 'selected' : '' ?>>Cat</option>
            <option value="Dog" <?= $product['category'] === 'Dog' ? 'selected' : '' ?>>Dog</option>
        </select>

        <label>Current Image:</label><br>
        <img src="<?= htmlspecialchars($product['image']) ?>" width="100"><br><br>

        <label>Upload New Image:</label>
        <input type="file" name="image">

        <button type="submit">Update Product</button>
    </form>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
