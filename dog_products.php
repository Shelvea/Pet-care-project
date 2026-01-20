<?php
session_start();
include 'db_connect.php';
$result = mysqli_query($conn, "SELECT * FROM products WHERE category='dog'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pet Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="shop.css">
    <link rel="stylesheet" href="navBarStyle.css">
    <link rel="stylesheet" href="footerStyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    align-items: center; justify-content: center;
}

.modal.show {                   /* new class for visible state */
    display: flex;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 500px;
}
.close {
    float: right;
    font-size: 28px;
    cursor: pointer;
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
    }
}
</style>

<body>
<?php include 'navbar.php'; ?>

<div class="grid-container">
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <div class="product-card" data-id="<?= $row['id']; ?>">
        <img src="<?= $row['image']; ?>" alt="<?= $row['name']; ?>">
        <h3><?= htmlspecialchars($row['name']); ?></h3>
        <p class="price">$<?= number_format($row['price'],2); ?></p>
        <p class="stock <?= $row['stock'] > 0 ? 'in-stock' : 'out-stock'; ?>">
            <?= $row['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
        </p>
        <div class="rating">
            <?php
            $fullStars = floor($row['rating']);
            $halfStar = ($row['rating'] - $fullStars) >= 0.5;
            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
            echo str_repeat("⭐", $fullStars);
            if ($halfStar) echo "🌓";
            echo str_repeat("☆", $emptyStars);
            ?>
        </div>

        <div class="qty-controls">
            <button class="qty-btn minus">-</button>
            <input type="number" value="1" min="1" max="<?= $row['stock']; ?>" class="qty">
            <button class="qty-btn plus">+</button>
        </div>

        <?php
        $inCartQty = $_SESSION['cart'][$row['id']]['qty'] ?? 0;
        $maxReached = $inCartQty >= $row['stock'];
        ?>
        <button class="add-to-cart" <?= $maxReached ? 'disabled' : ''; ?>>
            <?= $maxReached ? 'Max Stock in Cart' : 'Add to Cart'; ?>
        </button>

        <button class="quick-view">👁 Quick View</button>
    </div>
    <?php endwhile; ?>
</div>

<div id="quickViewModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="quickViewContent"></div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {

    // Quantity buttons
    $(document).on('click', '.qty-btn.plus', function() {
        const input = $(this).prev('.qty');
        let val = parseInt(input.val());
        const max = parseInt(input.attr('max'));
        if(val < max) input.val(val + 1);
    });
    $(document).on('click', '.qty-btn.minus', function() {
        const input = $(this).next('.qty');
        let val = parseInt(input.val());
        const min = parseInt(input.attr('min'));
        if(val > min) input.val(val - 1);
    });

    // Quick view
    $(document).on('click', '.quick-view', function() {
        const productId = $(this).closest('.product-card').data('id');
        $.post('product_details.php', {id: productId}, function(response) {
            $('#quickViewContent').html(response);
            $('#quickViewModal').addClass('show');  // use flex layout
        });
    });

    // Close modal
    $(document).on('click', '.close', function() {
        $('#quickViewModal').removeClass('show');
        $('#quickViewContent').html('');
        
    });
    $(window).click(function(e) {
        if($(e.target).is('#quickViewModal')) $('.close').trigger('click');
    });

    // Add to cart
    $(document).off('click', '.add-to-cart'); // remove old handlers
    $(document).on('click', '.add-to-cart', function() {
        const button = $(this);
        const card = button.closest('.product-card');
        const productId = card.data('id');
        const qty = card.find('.qty').val();

        $.ajax({
            url: 'add_to_cart.php',
            type: 'POST',
            data: {id: productId, qty: qty},
            dataType: 'json',
            success: function(res) {
                alert(res.message);

                // Disable button if max stock
                if(res.message.includes('available stock')) {
                    button.prop('disabled', true).text('Max Stock in Cart');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert('AJAX error: ' + xhr.responseText);
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
