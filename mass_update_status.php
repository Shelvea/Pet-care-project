<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedUsers = $_POST['selected_users'] ?? '';
    if (!is_array($selectedUsers)) {
        $selectedUsers = array_filter(explode(',', $selectedUsers));
    }

    $action = $_POST['action'] ?? '';

    if (!empty($selectedUsers) && in_array($action, ['suspend', 'reactivate'])) {
        // Determine new status (must match ENUM values exactly!)
        $newStatus = $action === 'suspend' ? 'suspended' : 'active';

        // Ensure IDs are integers
        $ids = array_map('intval', $selectedUsers);

        // Build safe ID list
        $idList = implode(',', $ids);

        if($newStatus === 'active'){
        // SQL query (only bind status)
        $sql = "UPDATE users SET status = ?, suspended_until = NULL WHERE id IN ($idList)";
        $stmt = $conn->prepare($sql);

            
        if ($stmt) {
            $stmt->bind_param("s", $newStatus);

            if ($stmt->execute()) {
                $_SESSION['status_message'] = [
                'text' => "Updated {$stmt->affected_rows} user(s).",
                'type' => 'success'
            ];
            } else {
                $_SESSION['status_message'] = [
                'text' => "Execute failed: " . $stmt->error,
                'type' => 'error'
            ];
            }

            $stmt->close();
        } else {
            $_SESSION['status_message'] = [
                'text' => "Prepare failed: " . $conn->error,
                'type' => 'error'
            ];
        }

        }else if($newStatus === 'suspended'){
            $duration_days = 7; // 1 week
            $suspended_until = date('Y-m-d H:i:s', strtotime("+{$duration_days} days"));

            $sql = "UPDATE users SET status = ?, suspended_until = ? WHERE id IN ($idList)";
            $stmt = $conn->prepare($sql);

            
            if ($stmt) {
                $stmt->bind_param("ss", $newStatus, $suspended_until);

            if ($stmt->execute()) {
                $_SESSION['status_message'] = [
                    'text' => "Updated {$stmt->affected_rows} user(s).",
                    'type' => 'success'
                ];
            } else {
                $_SESSION['status_message'] = [
                    'text' => "Execute failed: " . $stmt->error,
                    'type' => 'error'
                ];
            }

                $stmt->close();

            } else {
                $_SESSION['status_message'] = [
                    'text' => "Prepare failed: " . $conn->error,
                    'type' => 'error'
                ];
            }
            }
        
    } else {
        $_SESSION['status_message'] = [
            'text' => "No users selected or invalid action.",
            'type' => 'error'
        ];
    }
}

$conn->close();
header('Location: admin_users.php');
exit;
?>