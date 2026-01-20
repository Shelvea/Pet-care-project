<?php
session_start();
include 'db_connect.php';

$userId = $_SESSION['customer_id'] ?? 0;//id from users
$cart = $_SESSION['cart_user_' . $userId] ?? [];

if (empty($cart)) {
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="footerStyle.css">
    <style>
         .navbar {
            background-color: chocolate;
            display: flex;
            align-items: center;
            width: 100%; /* 🟢 Make it span the full width */
        }
 .navbar a,
.dropbtn {
    background-color: chocolate;
    color: white;
    font-weight: bold;
    padding: 14px 16px;
    height: 48px;
    flex: 1;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
    box-sizing: border-box;
    border: none; /* needed only for buttons */
}

.dropbtn{
    font-size: 15px;
}


     .navbar a:hover,
.dropdown button:hover {
    background-color: #ffcc80;
    color: chocolate;
    /* ✅ Do NOT change padding, border, or font-size here */
}

        .navbar .right {
            display: flex;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: chocolate;
            background-color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;/* align icon & text in a row */
            align-items: center; /* vertically center them */
            white-space: nowrap; /* keep text in one line */
        }
     
        .dropdown:hover .dropdown-content {
            display: block;
        }
   
        .dropdown button:hover{
            background-color: #ffcc80;
            color:chocolate;
        }

        .dropdown {
            position: relative;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

.dropbtn:hover {
    background-color: #ffcc80;
    color: chocolate;
}

       

         html, body {
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100%;
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
    table th, table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
    vertical-align: middle;
}
table img {
    border-radius: 5px;
    box-shadow: 0 0 5px #ccc;
}
table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background: #fff;
}
form {
    display: flex;
    flex-direction: column;
    align-items: center; /* center the form contents */
    background-color: rgba(127, 255, 0, 0.7);
    margin: 50px auto;
    padding: 20px 40px;
    border: 1px solid chocolate;
    border-radius: 8px;
    width: fit-content;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.form-group {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin-bottom: 15px;
    width: 100%;
}

.form-group label {
    width: 150px;
    font-weight: bold;
    margin-right: 10px;
    text-align: right;
}

.form-group input,
.form-group select,
.form-group textarea {
    flex: 1;
    padding: 5px;
    font-size: 14px;
}

.payment-options .radio-group {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.payment-options .radio-group label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: bold;
    color: #333;
}

    h1, h2{
        text-align: center;
        color:crimson;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        font-weight: bold;
        font-size: 30px;
    }
    .button-container{
        text-align: center;
        border-radius: 8px;
        background-color: chocolate;
        color:antiquewhite;
        font-family: Arial, Helvetica, sans-serif;
        cursor: pointer;
        font-weight: bold;
        font-size: 15px;
        padding: 5px 5px;
        border-color: wheat;
        margin-top: 20px;
    }
    .button-container:hover{
        color:whitesmoke;
    }
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
        padding-bottom: 0; /* ✅ remove forced bottom padding */
    
    }

    
    footer {
    width: 100% !important;   /* make footer itself full width */
    max-width: 100% !important;
    margin: 0 !important;
    padding: 15px 10px;  
  }

  footer .footer-container {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 10px;
    box-sizing: border-box; /* prevent overflow */
  }

  .footer-links {
    display: block;
    margin-top: 10px;
  }
  .footer-links a {
    display: inline-block;
    margin: 5px 8px;
  }
  form {
    display: flex;
    flex-direction: column;
    align-items: center; /* center the form contents */
    background-color: rgba(127, 255, 0, 0.7);
    margin: 20px auto;
    padding: 10px 15px;
    border: 1px solid chocolate;
    border-radius: 6px;
    width: fit-content;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

}
    </style>
</head>
<body>


<?php include 'navbar.php'; ?>

<div class="page-wrapper">
<h1>Checkout</h1>

<table>
    <thead>
    <tr>
        <th>Image</th> <!-- add this column -->
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Subtotal</th>
    </tr>
</thead>

    <tbody>
    
    <?php
    $grandTotal = 0;
    foreach ($cart as $id => $item):
    $subtotal = $item['price'] * $item['qty'];
    $grandTotal += $subtotal;

    // ✅ This line: Add image path
    $imageSrc = htmlspecialchars($item['image']);
?>
<tr>
    <td><img src="<?= $imageSrc ?>" alt="Product Image" style="max-width: 80px; max-height: 80px;"><br>
 <small><?= $imageSrc ?></small>
</td>
    <td><?= htmlspecialchars($item['name']) ?></td>
    <td><?= $item['qty'] ?></td>
    <td>$<?= number_format($item['price'], 2) ?></td>
    <td>$<?= number_format($subtotal, 2) ?></td>
</tr>
    <?php endforeach; ?>
    </tbody>

    <tfoot>
        <tr>
            <td colspan="4" style="text-align:right;">Grand Total:</td>
            <td><strong>$<?= number_format($grandTotal, 2) ?></strong></td>
        </tr>
    </tfoot>
</table>

<h2>Shipping & Delivery</h2>

<form action="place-order.php" method="post">
    
    <div class="form-group">
        <label for="address">Address:</label>
        <textarea name="address" id="address" required></textarea>
    </div>

    <div class="form-group">
        <label for="phone">Phone:<span class="required">*</span></label>
        <input type="tel" id="phone" name="phone" placeholder="0912-345-678"
               pattern="[0-9]{4}-[0-9]{3}-[0-9]{3}" required>
    </div>

    <div class="form-group">
        <label for="station">Choose Station:</label>
        <select name="station" id="station" required>
            <option value="">--Select Station--</option>
            <option value="Family Mart 全家">Station A - Family Mart 全家</option>
            <option value="OK Mart 萊爾富">Station B - OK Mart 萊爾富</option>
            <option value="Seven Eleven 7-11">Station C - Seven Eleven 7-11</option>
        </select>
    </div>

    <div class="form-group">
        <label for="pickup_code">Pickup Password:</label>
        <input type="text" name="pickup_code" id="pickup_code" maxlength="10" placeholder="e.g. 1234" required>
    </div>

    <h2>Payment Method</h2>
    <div class="payment-options">
        <label></label> <!-- empty label for alignment -->
        <div class="radio-group">
            <label><input type="radio" name="payment" value="Credit Card" required> Credit Card</label>
            <label><input type="radio" name="payment" value="Cash on Delivery"> Cash on Delivery</label>
            <label><input type="radio" name="payment" value="LINE Pay"> LINE Pay</label>
        </div>
    </div>

    <div class="form-group">
        <label></label>
        <button class ="button-container" type="submit">Place Order</button>
    </div>
</form>

</div>

<?php include 'footer.php'; ?>

 <script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.navbar').classList.toggle('active');
    });
  });
</script>

</body>
</html>
