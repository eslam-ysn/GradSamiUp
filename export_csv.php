<?php
require_once 'config.php';
require_once 'db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit; }

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=feedbacks.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Token', 'Feedback', 'Label', 'Score', 'Created At']);

$conn = db();
$query = "SELECT * FROM feedbacks ORDER BY id DESC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
