<?php
session_start();
require 'db_connect.php';

$client_id = $_SESSION['customer_id'] ?? 0;//from users table

// Query care records for this client

$stmt = $conn->prepare("
   SELECT 
    no.id, no.customer_id, no.nanny_id, no.order_date, no.status, no.notes, 
    no.pet_id, no.pickup_time, no.branch_dropoff_time, no.branch_id, 
    no.services_id, no.pickup_address, no.pickup_method, no.return_method, no.return_address, no.return_pickup_time, no.return_time,
    p.name AS pet_name, 
    c.client_name, 
    sb.city, sb.address,
    -- combine main + extra services into one string
    CONCAT(
        bs.service_type, ' $', bs.fixed_price, ' ', bs.unit,
        IF(GROUP_CONCAT(bs2.service_type ORDER BY bs2.service_type SEPARATOR ', ') IS NOT NULL,
            CONCAT(', ', GROUP_CONCAT(bs2.service_type, ' $', bs2.fixed_price, ' ', bs2.unit ORDER BY bs2.service_type SEPARATOR ', ')),
            ''
        )
    ) AS service_info
FROM nanny_orders no
JOIN pets_info p ON no.pet_id = p.id
JOIN client_data c ON no.customer_id = c.id
LEFT JOIN nanny_order_services nos 
    ON nos.order_id = no.id
LEFT JOIN branch_services bs2 
    ON nos.service_id = bs2.id 
    AND bs2.id <> no.services_id  -- exclude main service
JOIN branch_services bs ON no.services_id = bs.id
JOIN shop_branches sb ON no.branch_id = sb.id
WHERE c.user_id = ?
GROUP BY no.id
ORDER BY no.order_date ASC

");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['noRecord'] = "no care record found for this client.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Order Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');
            background-size: cover;
        }

        h2 {
            text-align: center;
            color: chocolate;
        }

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.9);
        }

        th, td {
            padding: 10px;
            border: 1px solid chocolate;
            text-align: center;
            
           
        }

        th {
            background-color: chocolate;
            color: white;
        }

        .top-left {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .back-container {
            padding: 10px 15px;
            border-radius: 10px;
            background-color: chocolate;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .back-container:hover {
            background-color: bisque;
            color: chocolate;
            
        }
        .submit-container{
            background-color: lawngreen;
            color: white;
            border-radius: 8px;
            padding: 5px 5px;
            border: none;
        }
        .submit-container:hover{
            
            color: seagreen;
        }
    </style>
</head>
<body>
<h2>🐾 Your Order Records</h2>

<?php 
    if(isset($_SESSION['noRecord'])){
        echo '<div style="text-align:center;">' . $_SESSION['noRecord'] . '</div>';
    }
    unset($_SESSION['noRecord']);
?>

<table>
    <tr>
        <th>Order Date</th>
        <th>Nanny Details</th>
        <th>Status</th>
        <th>Notes</th>
        <th>Pet Details</th>
        <th>Pickup</th>
        <th>Branch Dropoff</th>
        <th>Return</th>
        <th>Service Details</th>

        <th>Shop Address</th>        
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['order_date']) ?></td>
        <td>
            <form method="post" action="nanny_details.php"><!-- 尚無托育員 -->
                <input type="hidden" value="<?= htmlspecialchars($row['id']) ?>" name="order_id">
                <input class="submit-container" type="submit" value="Nanny Details">
            </form>
        </td>
        <td><?= htmlspecialchars($row['status']) ?></td>
        <td><?= htmlspecialchars($row['notes']) ?></td>
        <td>
            <form action="pet_details.php" method="POST">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <input type="submit" value="View Pet Details" class="submit-container">
            </form>
        </td>
         <td>
        <?= "<span style='color:chocolate; '>Pickup method:</span> " . htmlspecialchars($row['pickup_method'] ?? '') . "<br>" . 
        "<span style='color:chocolate;  '>Pickup address:</span> " . htmlspecialchars($row['pickup_address'] ?? '') . "<br>" .
        "<span style='color:chocolate; '>Pickup time:</span> " . htmlspecialchars($row['pickup_time'] ?? '') ?>
        </td>
        <td><?= "<span style='color:chocolate; '>Branch dropoff time:</span> " . htmlspecialchars($row['branch_dropoff_time']) ?? " " ?></td>
        <td>
        <?= "<span style='color:chocolate; '>Return method:</span> " . htmlspecialchars($row['return_method'] ?? '') . "<br>" . 
        "<span style='color:chocolate; '>Return address:</span> " . htmlspecialchars($row['return_address'] ?? '') . "<br>" .
        "<span style='color:chocolate; '>Return pickup time:</span> " . htmlspecialchars($row['return_pickup_time'] ?? '') . "<br>" .
        "<span style='color:chocolate; '>Return time:</span> " . htmlspecialchars($row['return_time'] ?? '') ?>
        </td>
        <td><?= htmlspecialchars($row['service_info']) ?></td>
        <td><?= htmlspecialchars($row['city']) . ' ' . htmlspecialchars($row['address']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

<div class="top-left">
    <button onclick="window.location.href='clientPage.php'" class="back-container">
        <i class="fa-solid fa-right-from-bracket"></i> 返回
    </button>
</div>

</body>
</html>
