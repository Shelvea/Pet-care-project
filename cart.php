<?php
session_start();
include 'db_connect.php';


$userId = $_SESSION['user_id'] ?? 0;
$cart   = $_SESSION['cart_user_' . $userId] ?? [];

$errorMsg = '';


// Remove item
if (isset($_GET['remove'])) {
    $removeId = intval($_GET['remove']);
    unset($_SESSION['cart_user_' . $userId][$removeId]);


    header("Location: cart.php"); // Refresh to show changes
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Cart</title>
    <!--<link rel="stylesheet" href="cart.css">  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="navBarStyle.css">
    <link rel="stylesheet" href="footerStyle.css">
    <style>
         html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }

        .page-wrapper {
            flex: 1; /* this takes all available space */
            display: flex;
            flex-direction: column;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

/* add more styles if needed */
      
        th, td { padding: 10px; text-align: center; border: 1px solid #ccc; }
        img { width: 80px; }
        .actions button { padding: 5px 10px; }
        .checkout-btn { margin-top: 15px;
            padding: 10px 20px;
            background-color: chocolate;
            border-radius: 8px;
            margin-bottom: 10px;
            color:beige;
            cursor: pointer;
            font-weight: bold;
            font-family: 'Fredoka One', cursive;
        }
        .remove-link { color: red; text-decoration: none; }

         /* Hamburger Button */
    .menu-toggle {
    display: none;  /* hidden on desktop */
    
    }
    
/* Mobile Responsive */
@media screen and (max-width: 480px) {
    .navbar {
        flex-direction: column;
        display: none; /* hidden by default */
        position: fixed;     /* also fixed */
        top: 50px;           /* push below menu-toggle */
        left: 0;
        right: 0;
        background: chocolate;
        z-index: 1000;
    }
     .navbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 150px;
            text-decoration: none;
            font-weight: bold;
            flex:1;/* 🔥 Make all items take equal space */
      }

      .dropdown-content a {
            color: chocolate;
            padding: 12px 142px;
            text-decoration: none;
            display: flex;/* align icon & text in a row */
            align-items: center; /* vertically center them */
            white-space: nowrap; /* keep text in one line */
        }

       .dropbtn {
            background-color: chocolate;
            color: white;
            padding: 14px 150px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            flex:1;/* 🔥 Make all items take equal space */
            text-align: center;
        }
    .navbar.active {
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
        padding-top: 50px; /* prevent overlap */
    }
     footer {/* override footer style for mobile screen */
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background-color: chocolate;
        color: white;
        padding: 15px 10px;
        text-align: center;
        z-index: 1000;
    }
    .footer-container {
        max-width: 100%;   /* override any width limit */
        padding: 0 10px;
    }
    .footer-links {
        display: block;
        margin-top: 10px;
    }
    .footer-links a {
        display: inline-block;
        margin: 5px 8px;
    }
}
    </style>
</head>
<body>
    <div class="page-wrapper">
<?php include 'navbar.php'; ?>
<h1 style="text-align:center;">🛒 Your Shopping Cart</h1>

<?php if (isset($_SESSION['errorMsg'])): ?>
    <p style="color:red; text-align:center;">
        <?= htmlspecialchars($_SESSION['errorMsg']) ?>
    </p>
    <?php unset($_SESSION['errorMsg']); ?>
<?php endif; ?>

<?php if (empty($cart)): ?>
    <p style="text-align:center;">Your cart is empty. <a href="shopping.php">Continue Shopping</a></p>
<?php else: ?>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Image</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $grandTotal = 0;
            foreach ($cart as $id => $item):

            // ✅ Check current stock
            $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $currentStock = $product ? (int)$product['stock'] : 0;
                
            if ($currentStock == 0) {
                 unset($_SESSION['cart'][$userId][$id]); // ❌ Remove if out of stock
                continue; // Skip this item
            }

            $maxQty = min($item['qty'], $currentStock);
            if ($item['qty'] > $currentStock) {
            $_SESSION['cart'][$userId][$id]['qty'] = $currentStock;  // Adjust session cart
            $qty = $currentStock; // Display adjusted qty
            $_SESSION['errorMsg'] = "Some items in your cart were adjusted because of stock changes.";
            } else {
            $qty = $item['qty'];
            }

                $name = htmlspecialchars($item['name']);
                $price = (float)$item['price'];
                $image = htmlspecialchars($item['image']);
                $subtotal = $price * $qty;
                $grandTotal += $subtotal;
            ?>
            <tr>
                <td><?= $name ?></td>
                <td>
                    <img src="<?= $image ?>" alt="<?= $name ?>">
                    <p style="color: <?= $currentStock > 5 ? 'green' : 'orange' ?>;">
                    Stock: <?= $currentStock ?>
                    </p>
                </td>
                <td>$<?= number_format($price, 2) ?></td>
                <td>
                    <input type="number" name="qty[<?= $id ?>]" value="<?= $qty ?>" min="1" max="<?= $currentStock ?>" style="width:60px;">
                </td>
                <td>$<?= number_format($subtotal, 2) ?></td>
                <td>
                    <a href="cart.php?remove=<?= $id ?>" class="remove-link">Remove ❌</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;"><strong>Grand Total:</strong></td>
                <td colspan="2"><strong>$<?= number_format($grandTotal, 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="text-align:center;">
        <button onclick="window.location.href='checkout.php';" class="checkout-btn">✅ Proceed to Checkout</button>
    </div>

<?php endif; ?>
    </div>
<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('input[type="number"]').on('change', function() {
        const input = $(this);
        const id = input.attr('name').match(/\d+/)[0]; // Get product ID from name
        const newQty = input.val();

        $.ajax({
            url: 'update-cart.php',
            type: 'POST',
            data: {id: id, qty: newQty},
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                } else if (response.status === 'error') {
                    alert(response.message);
                    input.val(response.newQty); // Set input to max stock
                }

                // ✅ Update subtotal and grand total instantly (no reload)
                let price = parseFloat(input.closest('tr').find('td:nth-child(3)').text().replace('$', ''));
                let newSubtotal = price * response.newQty;
                input.closest('tr').find('td:nth-child(5)').text('$' + newSubtotal.toFixed(2));

                // Update grand total
                let grandTotal = 0;
                $('td:nth-child(5)').each(function() {
                    grandTotal += parseFloat($(this).text().replace('$', ''));
                });
                $('tfoot strong').last().text('$' + grandTotal.toFixed(2));
            },
            error: function() {
                alert('Error updating cart.');
            }
        });
    });
});
</script>

 <script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.navbar').classList.toggle('active');
    });
  });
</script>

</body>
</html>