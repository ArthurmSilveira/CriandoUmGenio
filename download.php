<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    header('Location: index.php?pagina=downloads');
    exit;
}

$id = $_GET['id'];

// Buscar informações do arquivo
$stmt = $conn->prepare("SELECT * FROM downloads WHERE id = ?");
$stmt->execute([$id]);
$download = $stmt->fetch();

if (!$download) {
    header('Location: index.php?pagina=downloads');
    exit;
}

// Incrementar contador de downloads
$stmt = $conn->prepare("UPDATE downloads SET contador = contador + 1 WHERE id = ?");
$stmt->execute([$id]);

// Definir headers para download
$arquivo = 'uploads/downloads/' . $download['arquivo'];
if (file_exists($arquivo)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($download['arquivo']) . '"');
    header('Content-Length: ' . filesize($arquivo));
    readfile($arquivo);
    exit;
} else {
    header('Location: index.php?pagina=downloads');
    exit;
}