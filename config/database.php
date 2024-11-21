<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_educacional');

try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Tentar criar o banco de dados se não existir
    $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $conn->exec("USE " . DB_NAME);
    
    // Executar o arquivo SQL de setup
    $sql = file_get_contents('setup.sql');
    $conn->exec($sql);
    
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}