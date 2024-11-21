<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$aviso_id = $data['aviso_id'];

$stmt = $conn->prepare("UPDATE avisos SET visualizado = 1 WHERE id = ?");
$success = $stmt->execute([$aviso_id]);

echo json_encode(['success' => $success]);