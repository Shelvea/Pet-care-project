<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

include 'db_connect.php';

$allowed_roles = ['admin', 'nanny', 'client'];
$filter_role = isset($_GET['role']) && in_array($_GET['role'], $allowed_roles) ? $_GET['role'] : '';

$dompdf = new Dompdf();

// Header title with role
$html = '<h2 style="text-align:center;">Users List';
if ($filter_role) {
    $html .= ' - ' . ucfirst($filter_role);
}
$html .= '</h2>';

$html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; font-family:Arial; font-size:12px;">
<thead>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th>
</tr>
</thead><tbody>';

// Use filtered SQL if role is provided
if ($filter_role) {
    $stmt = $conn->prepare("SELECT id, name, email, phone, role, status FROM users WHERE role = ?");
    $stmt->bind_param("s", $filter_role);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT id, name, email, phone, role, status FROM users");
}

// Render each row
while ($row = $result->fetch_assoc()) {
    $html .= "<tr>
    <td>{$row['id']}</td>
    <td>{$row['name']}</td>
    <td>{$row['email']}</td>
    <td>{$row['phone']}</td>
    <td>{$row['role']}</td>
    <td>{$row['status']}</td>
    </tr>";
}
$html .= '</tbody></table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("users_export.pdf", ["Attachment" => 1]); // Download file
?>
