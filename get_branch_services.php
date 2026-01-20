<?php
include 'db_connect.php';

header('Content-Type: application/json');

$branch_id = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : 0;

if ($branch_id > 0) {
    $stmt = $conn->prepare("SELECT id, service_type, fixed_price, unit FROM branch_services WHERE branch_id = ?");
    $stmt->bind_param("i", $branch_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $services = [];
    while ($service = $result->fetch_assoc()) {
        // Escape to prevent XSS
        $service['service_type'] = htmlspecialchars($service['service_type'], ENT_QUOTES, 'UTF-8');
        $service['unit'] = htmlspecialchars($service['unit'], ENT_QUOTES, 'UTF-8');
        $services[] = $service;
    }

    echo json_encode($services);
} else {
    echo json_encode([]);
}
