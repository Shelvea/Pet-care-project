<?php
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $rating = floatval($_POST['rating'] ?? 0);
    $category = $_POST['category'] ?? '';
    
    // Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_file = basename($_FILES['image']['name']);
        $target_dir = "product_picture/";
        $target_file = $target_dir . $image_file;
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));//only get the extension likw jpg,jpeg,etc

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        

        if (in_array($image_type, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_name = $target_file;// Save full path to DB like "product_picture/10001.jpg"
                // Insert into DB
                $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock, rating, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdsids", $name, $description, $price, $image_name, $stock, $rating, $category);

                if ($stmt->execute()) {
                    $message = "Product added successfully!";
                } else {
                    $message = "Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Failed to upload image.";
            }
        } else {
            $message = "Only JPG, PNG, JPEG, and GIF files are allowed.";
        }
    } else {
        $message = "Please choose an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="adminNavbarStyle.css">
    <link rel="stylesheet" href="footerStyle.css">
    <style>
        .add-form-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .add-form-container h2 {
            text-align: center;
            color: chocolate;
            margin-bottom: 20px;
        }

        .add-form-container form input,
        .add-form-container form textarea,
        .add-form-container form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .add-form-container form button {
            background-color: chocolate;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }
        .category-btn {
            margin-top: 10px;
            display: inline-block;
            padding: 5px 10px;
            background-color: #f4f4f4;
            border: 2px solid chocolate;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 10px;
            transition: 0.3s;
    }

input[type="radio"] {
  display: none;
}

input[type="radio"]:checked + .category-btn {
  background-color: chocolate;
  color: white;
}
.image-text{
    margin-top: 10px;
}
   /* Hamburger Button */
    .menu-toggle {
    display: none;  /* hidden on desktop */
    
    }

/* Mobile Responsive */
@media screen and (max-width: 480px) {
    .AdminNavbar {
        flex-direction: column;
        display: none; /* hidden by default */
        position: fixed;     /* also fixed */
        top: 50px;           /* push below menu-toggle */
        left: 0;
        right: 0;
        background: chocolate;
        z-index: 1000;
    }
    
    .AdminNavbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 130px;
            text-decoration: none;
            font-weight: bold;
            flex:1;/* 🔥 Make all items take equal space */
    }
    
    .AdminNavbar.active {
        display: flex; /* shown when toggled */
    }

    .menu-toggle {
    display: block; /* show only on mobile */
    position: fixed;     /* 🧲 stick to top */
    top: 0;              /* align top */
    left: 0;             /* align left */
    width: 100%;         /* full width bar */
    cursor: pointer;
    font-size: 22px;
    text-decoration: none;
    font-weight: bold;
    padding: 10px;
    background: chocolate;
    z-index: 1100;       /* higher than navbar */
    color: white;
    text-align: left;
    }
    
    body {
        padding-top: 60px; /* prevent overlap */
    }
}
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="add-form-container">
        <h2>Add New Product</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
        <form method="POST" enctype="multipart/form-data">
            
            <label for="name" style="color:chocolate; font-weight:bold;">Product Name:</label>
            <input type="text" name="name" id="name" placeholder="Product Name" required>
                        
            <label for="description" style="color:chocolate; font-weight:bold;">Description:</label>
            <textarea name="description" placeholder="Description" rows="4" id="description" required></textarea>
            
            <label for="price" style="color:chocolate; font-weight:bold;">Price:</label>
            <input type="number" name="price" min="0" placeholder="Price" id="price" required>
            
            <label for="stock" style="color:chocolate; font-weight:bold;">Stock Quantity:</label>
            <input type="number" name="stock" min="0" placeholder="Stock Quantity" id="stock" required>
            
            
            <label for="rating" style="color:chocolate; font-weight:bold;">Rating:</label>
            <input type="number" name="rating" placeholder="Rating" min="0" max="5" step="0.5" id="rating" required>
            
            <label style="color:chocolate; font-weight:bold;">Category:</label><br>
            <input type="radio" name="category" id="cat" value="Cat" required>
            <label for="cat" class="category-btn">Cat</label>

            <input type="radio" id="dog" name="category" value="Dog">
            <label for="dog" class="category-btn">Dog</label>
            <br>
            
            <div class="image-text">
            <label for="image" style="color:chocolate; font-weight:bold;">Product Image:</label>
            </div>

            <input type="file" name="image" id="image" required>
            <button type="submit">Add Product</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>

    <script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.AdminNavbar').classList.toggle('active');
    });
  });
</script>

</body>
</html>
