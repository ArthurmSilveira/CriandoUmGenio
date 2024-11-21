<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['posicoes'])) {
    $conn->beginTransaction();
    
    try {
        $stmt = $conn->prepare("UPDATE apoiadores SET posicao = ? WHERE id = ?");
        
        foreach ($data['posicoes'] as $id => $posicao) {
            $stmt->execute([$posicao, $id]);
        }
        
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}