<?php
function verificarPermissao($tipo_requerido) {
    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== $tipo_requerido) {
        header('Location: index.php?pagina=login');
        exit;
    }
}

function login($usuario, $senha) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario'] = [
                'id' => $user['id'],
                'usuario' => $user['usuario'],
                'tipo' => $user['tipo']
            ];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        return false;
    }
}

function cadastrarUsuario($usuario, $senha, $email, $tipo) {
    global $conn;
    
    try {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("
            INSERT INTO usuarios (usuario, senha, email, tipo) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$usuario, $senha_hash, $email, $tipo]);
    } catch (PDOException $e) {
        error_log("Erro no cadastro: " . $e->getMessage());
        return false;
    }
}