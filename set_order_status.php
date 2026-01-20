<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connect.php';

$nanny_id = $_SESSION['nanny_id'] ?? 0;
if (!$nanny_id) die("Unauthorized");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {

    $order_id = intval($_POST['order_id']);
    echo "<h2>Set Status for Order #" . $order_id . "</h2>";


}

?>

<!DOCTYPE html>
<html>
    <head>

    </head>
    <body>

    </body>
</html>

<form method="POST" action="">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">    
    <label for="status" class="status-container"><strong>Set Order Status: </strong></label>        
    <label><input type="radio" name="status" value="accepted" required> Accepted </label>
    <label><input type="radio" name="status" value="in service"> In service </label>
    <label><input type="radio" name="status" value="service completed"> Service completed </label>
    <div class="confirm-container"><br><input type="submit" name="set_price" value="Confirm & Offer" class="confirm"></div>
</form><!-- ubah order status buat di halaman nanny order record aja dari accepted baru tinggal tunggu pet nya di kirim ke toko baru ubah
     status in service sampai completed buat payment faktur baru kasih tau lewat automatic message link buat pilih kirim balek sendiri atau client pickup sendiri sama kirim link faktur kasih tau client service sudah completed atau dari email tapi email kayaknya belum ada soalnya itu email palsu -->
    