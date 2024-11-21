<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$stmt = $conn->query("SELECT COUNT(*) as total FROM avisos WHERE status = 'novo'");
$result = $stmt->fetch();

echo json_encode(['novos_avisos' => $result['total']]);