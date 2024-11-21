<?php
verificarPermissao('admin');

// Obter a ação principal e subação
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'dashboard';
$subacao = isset($_GET['subacao']) ? $_GET['subacao'] : '';

// Construir o caminho do arquivo
if ($subacao) {
    $arquivo = "pages/admin/{$acao}/{$subacao}.php";
} else {
    $arquivo = "pages/admin/{$acao}.php";
}

// Verificar se o arquivo existe e incluí-lo
if (file_exists($arquivo)) {
    include $arquivo;
} else {
    include 'pages/404.php';
}
?>