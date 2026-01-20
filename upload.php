<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db_connect.php';

function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$customer_id  = $_SESSION['client_id'];
if (!$customer_id) {
    die("❌ Session client_id is missing.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pets'])) {

    $errors = [];
    $successes = [];

    // Debug raw input, you can comment this out if not needed
    //echo "<pre>";
    //print_r($_POST);
    //echo "</pre>";

    foreach ($_POST['pets'] as $petId => $data) {
        // Skip unselected pets
        if (empty($data['selected']) || $data['selected'] != "1") continue;

        // Sanitize inputs
        $serviceTypes = isset($data['service_type']) ? implode(",", $data['service_type']) : '';
        $startTime = !empty($data['start_time']) ? date("Y-m-d H:i:s", strtotime(test_input($data['start_time']))) : null;
        $endTime = !empty($data['end_time']) ? date("Y-m-d H:i:s", strtotime(test_input($data['end_time']))) : null;
        
        $comment = test_input($data['comment'] ?? '');
        $pickup_method = test_input($data['pickup_method'] ?? '');
        $pickup_address = test_input($data['pickup_address'] ?? '');

        $return_method = test_input($data['return_method'] ?? '');
        $return_address = test_input($data['return_address'] ?? '');
   

        $return_time = !empty($data['return_time']) ? date("Y-m-d H:i:s", strtotime(test_input($data['return_time']))) : null;
        $return_dropoff_time = !empty($data['return_dropoff_time']) ? date("Y-m-d H:i:s", strtotime(test_input($data['return_dropoff_time']))) : null;
        $return_pickup_time = !empty($data['return_pickup_time']) ? date("Y-m-d H:i:s", strtotime(test_input($data['return_pickup_time']))) : null;


        $pickup_time = !empty($data['pickup_time']) ? date("Y-m-d H:i:s", strtotime(test_input($data['pickup_time']))) : null;
        $dropoff_time = !empty($data['dropoff_time']) ? date("Y-m-d H:i:s", strtotime(test_input($data['dropoff_time']))) : null;

        // Validate return method details
        if ($return_method === 'nanny_return') {
            if (!$return_time) {
                $errors[] = "⚠️ 寵物 ID $petId：請提供保母送回的時間。";
                continue;
            }
        } elseif ($return_method === 'branch_pickup') {
            if (!$return_pickup_time) {
            $errors[] = "⚠️ 寵物 ID $petId：請提供分店取回的時間。";
            continue;
            }
        }

        $return_branch_id = isset($data['return_branch']) ? intval($data['return_branch']) : null;
        // Optional: If method is branch drop-off, use selected branch
        $branch_id = isset($data['branch_id']) ? intval($data['branch_id']) : null;
        // Initialize dropoff_address as null
        $dropoff_address = null;
        
        if ($pickup_method !== 'branch_dropoff') {
            $branch_id = null;
        }

if ($pickup_method === 'nanny_pickup') {
    if (!$pickup_time || !$dropoff_time) {
        $errors[] = "⚠️ 寵物 ID $petId：請提供接送的時間。";
        continue;
    }
    $dropoff_address = test_input($data['dropoff_address'] ?? '');
    if (!$pickup_address) {
        $errors[] = "⚠️ 寵物 ID $petId：請提供保母前往接寵物的地址。";
        continue;
    }

} else {
    $pickup_time = null;
    $dropoff_time = null;
    $dropoff_address = null;

    if ($pickup_method === 'branch_dropoff' && $branch_id) {
        $stmtBranch = $conn->prepare("SELECT address FROM shop_branches WHERE id = ?");
        $stmtBranch->bind_param("i", $branch_id);
        $stmtBranch->execute();
        $result = $stmtBranch->get_result();
        
        if ($result && $result->num_rows > 0) {
            $branchRow = $result->fetch_assoc();
            $dropoff_address = $branchRow['address'];
        } else {
            $errors[] = "⚠️ 寵物 ID $petId：找不到分店地址，請重新選擇。";
            continue;
        }

        $stmtBranch->close();
    }
}


        $nanny_id = null; // No nanny selected at the time of request

        if (!$startTime || !$endTime) {
            $errors[] = "⚠️ 寵物 ID $petId：開始時間與結束時間為必填欄位。";
            continue;
        }

        if ($startTime > $endTime) {
            $errors[] = "⚠️ 寵物 ID $petId：開始時間不能晚於結束時間。";
            continue;
        }

        // Check for time overlaps for this pet and customer
        $checkOverlapStmt = $conn->prepare("
            SELECT COUNT(*) FROM nanny_orders
            WHERE pet_id = ? AND customer_id = ?
            AND NOT ( ? <= start_time OR ? >= end_time )
        ");
        $checkOverlapStmt->bind_param("iiss", $petId, $customer_id, $endTime, $startTime);
        $checkOverlapStmt->execute();
        $checkOverlapStmt->bind_result($overlapCount);
        $checkOverlapStmt->fetch();
        $checkOverlapStmt->close();

        if ($overlapCount > 0) {
            $errors[] = "⚠️ 寵物 ID $petId 的新時間與已有委託重疊，請重新選擇時間。";
            continue;
        }

        // Clean branch ID depending on method
        if ($pickup_method !== 'branch_dropoff') {
            $branch_id = null;
        }

        if ($return_method !== 'branch_pickup') {
            $return_branch_id = null;
        } else {
            $branch_id = null; // avoid conflicting data
        }

        // Insert new order
   $stmt = $conn->prepare("INSERT INTO nanny_orders 
    (customer_id, pet_id, service_type, start_time, end_time, notes, nanny_id,
    pickup_method, pickup_address, dropoff_address, pickup_time, dropoff_time, branch_id,
    return_method, return_address, return_time, return_dropoff_time, return_pickup_time, return_branch)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "iisssssssssssssssi",
        $customer_id,
        $petId,
        $serviceTypes,
        $startTime,
        $endTime,
        $comment,
        $nanny_id,
        $pickup_method,
        $pickup_address,
        $dropoff_address,
        $pickup_time,
        $dropoff_time,
        $branch_id,
        $return_method,
        $return_address,
        $return_time,
        $return_dropoff_time,
        $return_pickup_time,
        $return_branch_id
    );


        if ($stmt->execute()) {
            $successes[] = "✅ 寵物 ID $petId 的委託已成功提交！";
        } else {
            $errors[] = "❌ 寵物 ID $petId 儲存失敗：" . htmlspecialchars($stmt->error);
            error_log("DB error: " . $stmt->error);
        }

        $stmt->close();
    }

    // Close DB connection
    $conn->close();

    // Display messages
    foreach ($successes as $msg) {
        echo "<p style='color:green;'>$msg</p>";
    }
    foreach ($errors as $msg) {
        echo "<p style='color:red;'>$msg</p>";
    }

    // Redirect to apply.php after 3 seconds
    //header("Refresh: 3; url=apply.php");
    //echo "<p>3秒後將自動返回申請頁面...</p>";
    //exit();

} else {
    echo "<p>⚠️ 沒有收到任何寵物資料。</p>";
}
?>
