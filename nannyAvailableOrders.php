<?php
session_start();
require 'db_connect.php';

$nanny_id = $_SESSION['customer_id'] ?? 0;
if (!$nanny_id) die("Unauthorized");

$stmt2 = $conn->prepare("SELECT id FROM nanny_data WHERE user_id = ?");
$stmt2->bind_param("i",$nanny_id);
$stmt2->execute();

// bind result column(s) to variable(s)
$stmt2->bind_result($nanny_data_id);
$stmt2->fetch();
$stmt2->close();

$stmt = $conn->prepare("
    SELECT 
    no.id AS order_id,
    no.customer_id,
    no.nanny_id,
    no.order_date,
    no.status,
    no.notes,
    no.pet_id,
    no.pickup_time,
    no.branch_dropoff_time,
    no.branch_id,
    no.pickup_address,
    no.pickup_method,
    
    p.name AS pet_name,
    c.client_name,
    bs.id AS service_id,
    bs.service_type,
    bs.fixed_price,
    bs.unit,
    sb.city,
    sb.address

FROM nanny_orders no

JOIN pets_info p ON no.pet_id = p.id
JOIN client_data c ON no.customer_id = c.id
JOIN nanny_data n ON n.work_at_branch = no.branch_id
LEFT JOIN nanny_order_services nos ON nos.order_id = no.id          -- join many-to-many table
JOIN branch_services bs ON nos.service_id = bs.id              -- get service details
JOIN shop_branches sb ON no.branch_id = sb.id

WHERE n.id = ?
  AND no.nanny_id IS NULL
  AND no.status = 'pending'

ORDER BY no.order_date ASC;

");

$stmt->bind_param("i", $nanny_data_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<?php
$orders = [];

// Group services by order_id
while ($row = $result->fetch_assoc()) {
    $orderId = $row['order_id'];
    if (!isset($orders[$orderId])) {
        $orders[$orderId] = $row;
        $orders[$orderId]['services'] = [];
    }
    $orders[$orderId]['services'][] = [
        'service_type' => $row['service_type'],
        'fixed_price'  => $row['fixed_price'],
        'unit'         => $row['unit']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Available Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <style>
        table { width: 90%; margin: auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid chocolate; text-align: center; }
        th { background-color: chocolate; color: white; }
        .select-btn {
            padding: 6px 12px;
            background-color: chocolate;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .select-btn:hover {
            background-color: peru;
        }
        .back-container{
            padding: 13px 13px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            
        }
        .back-container:hover{
            color:chocolate;
            background-color: bisque;
        }
        .top-left {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        /* Mobile Responsive */
@media screen and (max-width: 480px) {
     .back-container{
            padding: 8px 8px;
            border-radius: 8px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            
        }

        .top-left {
        position: fixed;
        top: 10px;
        right: 10px;
        left: auto;   /* 👈 cancel desktop left positioning */
    }
}
    </style>
</head>
<body>
<h2 style="text-align:center; color:crimson;">🐾 Available Orders to Accept</h2>
<table>
    <tr>
        <th>Order Date</th>
        <th>Client Details</th>
        <th>Status</th>
        <th>Notes</th>
        <th>Pet Details</th>
        <th>Pickup</th>
        <th>Branch Dropoff</th>
        <th>Service Type(s)</th>
        <th>Shop Address</th>
        <th>Action</th>
    </tr>
    <?php if (!empty($orders)): ?>
     <?php foreach ($orders as $order): ?>
    <tr>
        <td><?= htmlspecialchars($order['order_date']) ?></td>
        <td>
        <form action="client_details.php" method="POST"> <!-- view client details -->
                <input type="hidden" name="order_id" value="<?=  $order['order_id'] ?>">
                <input type="hidden" name="from" value="nannyAvailableOrders.php">
                <input type="submit" value="View Client Details" class="select-btn">
        </form>
        </td>
        <td><?= htmlspecialchars($order['status']) ?></td>
        <td><?= htmlspecialchars($order['notes']) ?></td>
        <td>
        <form action="pet_details.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <input type="hidden" name="from" value="nannyAvailableOrders.php">
                <input type="submit" value="View Pet Details" class="select-btn">
        </form>
        </td>
        <td>
        <?= "<span style='color:chocolate; '>Pickup method:</span> " . htmlspecialchars($order['pickup_method'] ?? '') . "<br>" . 
        "<span style='color:chocolate; '>Pickup address:</span> " . htmlspecialchars($order['pickup_address'] ?? '') . "<br>" .
        "<span style='color:chocolate; '>Pickup time:</span> " . htmlspecialchars($order['pickup_time'] ?? '') ?>
        </td>
        <td><?= "<span style='color:chocolate; '>Branch dropoff time:</span> " . htmlspecialchars($order['branch_dropoff_time']) ?? " " ?></td>
       <td>
        <ul style="margin:0; padding-left:18px;">
        <?php foreach ($order['services'] as $s): ?>
            <li>
                <?= htmlspecialchars($s['service_type']) ?> <?= htmlspecialchars($s['fixed_price']) . ' ' . htmlspecialchars($s['unit']) ?>
            </li>
        <?php endforeach; ?>
        </ul>
        </td>
        <td><?= htmlspecialchars($order['city'] . ', ' . $order['address']) ?></td>
        <td>
            <form action="select_order.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <input type="submit" value="Accept" class="select-btn">
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
    <p style="color:chocolate; text-align:center;">No available orders at the moment.</p>
<?php endif; ?>

    <div class="top-left">
<button  onclick="window.location.href='nannyMainPage.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>
</body>
</html>
