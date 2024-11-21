<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Definir página padrão
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

// Incluir cabeçalho
include 'includes/header.php';

// Array de páginas permitidas
$paginas_permitidas = [
    'home', 'login', 'cadastro', 'sobre', 'contato',
    'admin', 'professor', 'aluno',
    'jogos', 'downloads'
];

// Verificar se a página existe e está na lista de permitidas
if (in_array($pagina, $paginas_permitidas)) {
    $arquivo = "pages/{$pagina}.php";
    if (file_exists($arquivo)) {
        include $arquivo;
    } else {
        include 'pages/404.php';
    }
} else {
    include 'pages/404.php';
}

// Incluir rodapé
include 'includes/footer.php';