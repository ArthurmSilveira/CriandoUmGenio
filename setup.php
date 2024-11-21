<?php
require_once 'config/database.php';

try {
    // Criar as tabelas do banco de dados
    $sql = file_get_contents('setup.sql');
    $conn->exec($sql);
    
    // Verificar se o usuário admin já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetch();
    
    if ($adminExists) {
        // Atualizar senha do admin existente
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE usuario = 'admin'");
        $stmt->execute([$senha_hash]);
        $mensagem = "Senha do usuário admin atualizada com sucesso!";
    } else {
        // Criar novo usuário admin
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO usuarios (usuario, senha, email, tipo) 
            VALUES ('admin', ?, 'admin@escola.com', 'admin')
        ");
        $stmt->execute([$senha_hash]);
        $mensagem = "Usuário admin criado com sucesso!";
    }
    
    // Criar diretórios necessários
    $diretorios = [
        'uploads/materiais',
        'uploads/downloads',
        'assets/images/banners',
        'assets/images/sponsors',
        'assets/images/jogos',
        'relatorios'
    ];
    
    foreach ($diretorios as $diretorio) {
        if (!file_exists($diretorio)) {
            mkdir($diretorio, 0777, true);
        }
    }
    
    echo "<div style='text-align: center; margin-top: 50px;'>";
    echo "<h2>Setup concluído com sucesso!</h2>";
    echo "<p>{$mensagem}</p>";
    echo "<p>Todas as tabelas foram criadas e os diretórios configurados.</p>";
    echo "<p>Você pode fazer login com:</p>";
    echo "<p>Usuário: <strong>admin</strong></p>";
    echo "<p>Senha: <strong>admin123</strong></p>";
    echo "<p><a href='index.php' class='btn btn-primary'>Voltar para a página inicial</a></p>";
    echo "</div>";
    
} catch(PDOException $e) {
    die("Erro no setup: " . $e->getMessage());
}