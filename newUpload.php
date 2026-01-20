<?php
session_start();
include 'db_connect.php';  // Your DB connection file

// Assuming customer is logged in and you have customer_id in session
$customer_id = $_SESSION['customer_id'];//from users id

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id=?");
$stmt2->bind_param("i", $customer_id);
$stmt2->execute();
$stmt2->bind_result($user_id);//id from client_data
$stmt2->fetch();
$stmt2->close();

if (!$customer_id) {
    die("請先登入");
}

// You may need to assign nanny_id, or select based on logic
// For now, let's say nanny_id is fixed or assigned later
$nanny_id = null;  // or set to some ID or from your logic

// Default status for new orders
$status = 'pending';

$_SESSION['success'] = [];
$_SESSION['fail'] = [];

// Check if POST and pets data exist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['pets'] as $petId => $petData) {//pet_id
       

        $branchId = intval($petData['branch_id']) ?? null;
        $serviceIds = $petData['service_ids'] ?? []; // array of selected service IDs

        if (empty($branchId) || empty($serviceIds)) continue;
        
        $notes = $petData['comment'] ?? null;

        $pickup_method = $petData['pickup_method'] ?? null;

        $pickup_address = $petData['pickup_address'] ?? null;

        $pickup_time = !empty($petData['pickup_time']) ? $petData['pickup_time'] : null;

        $branch_dropoff_time = !empty($petData['branch_dropoff_time']) ? $petData['branch_dropoff_time'] : null;

        // Insert into nanny_orders
        $firstServiceId = $serviceIds[0];

    // Prepare SQL insert statement with placeholders
    $stmt = $conn->prepare("INSERT INTO nanny_orders (
        customer_id, nanny_id, status, notes, pet_id, pickup_time, 
        branch_dropoff_time, branch_id, services_id, pickup_address, pickup_method
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(//harus BERURUTAN letaknya persis sama dengan prepare query kalo enggak bisa error
            "iississiiss",
            $user_id,           // i
            $nanny_id,              // i (or null if allowed)
            $status,                // s
            $notes,                 // s
            $petId,                 // i  ✅ correct column
            $pickup_time,           // s
            $branch_dropoff_time,   // s
            $branchId,              // i
            $firstServiceId,       // i
            $pickup_address,        // s
            $pickup_method          // s
        );

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;
        // Insert multiple services into nanny_order_services
        foreach ($serviceIds as $serviceId) {
            $stmt2 = $conn->prepare("INSERT INTO nanny_order_services (order_id, service_id) VALUES (?, ?)");
            $stmt2->bind_param("ii", $order_id, $serviceId);
            if (!$stmt2->execute()) {
                    $_SESSION['fail'][] = "寵物 ID {$petId} 插入服務 {$serviceId} 失敗: " . $stmt2->error;
            }
            $stmt2->close();
        }

        $_SESSION['success'][] = "寵物 ID {$petId} 預約成功";
    } else {
        $_SESSION['fail'][] = "寵物 ID {$petId} 預約失敗: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();

header("Location: apply.php");
exit;

}else{

    echo "未收到任何預約資料";

}    
?>