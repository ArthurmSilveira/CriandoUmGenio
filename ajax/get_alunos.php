<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['disciplina_id'])) {
    echo json_encode(['erro' => 'ID da disciplina não fornecido']);
    exit;
}

$disciplina_id = $_GET['disciplina_id'];

$stmt = $conn->prepare("
    SELECT a.id, a.nome
    FROM alunos a
    JOIN alunos_disciplinas ad ON ad.aluno_id = a.id
    WHERE ad.disciplina_id = ?
    ORDER BY a.nome
");

$stmt->execute([$disciplina_id]);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($alunos);