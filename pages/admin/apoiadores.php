<?php
verificarPermissao('admin');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'cadastrar':
                $nome = $_POST['nome'];
                $link = $_POST['link'];
                $status = $_POST['status'];
                $local_exibicao = $_POST['local_exibicao'];
                $posicao = $_POST['posicao'];
                $data_inicio = $_POST['data_inicio'];
                $data_fim = $_POST['data_fim'];
                
                try {
                    $arquivo = $_FILES['logo'];
                    $nome_arquivo = time() . '_' . basename($arquivo['name']);
                    $diretorio = 'assets/images/sponsors/';
                    
                    if (!file_exists($diretorio)) {
                        mkdir($diretorio, 0777, true);
                    }
                    
                    list($largura, $altura) = getimagesize($arquivo['tmp_name']);
                    $dimensoes_corretas = false;
                    
                    if ($local_exibicao === 'banner' || $local_exibicao === 'geral') {
                        $dimensoes_corretas = ($largura == 1200 && $altura == 650);
                        if (!$dimensoes_corretas) {
                            throw new Exception('Para banners rotativos, a imagem deve ter exatamente 1200x650 pixels!');
                        }
                    } else {
                        $dimensoes_corretas = ($largura == 300 && $altura == 150);
                        if (!$dimensoes_corretas) {
                            throw new Exception('Para logos no rodapé, a imagem deve ter exatamente 300x150 pixels!');
                        }
                    }
                    
                    if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo)) {
                        $stmt = $conn->prepare("
                            INSERT INTO apoiadores (nome, logo, link, local_exibicao, posicao, data_inicio, data_fim, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        
                        if ($stmt->execute([$nome, $nome_arquivo, $link, $local_exibicao, $posicao, $data_inicio, $data_fim, $status])) {
                            $mensagem = '<div class="alert alert-success">Apoiador cadastrado com sucesso!</div>';
                        } else {
                            throw new Exception('Erro ao salvar no banco de dados');
                        }
                    } else {
                        throw new Exception('Erro ao fazer upload da imagem');
                    }
                } catch (Exception $e) {
                    $mensagem = '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
                }
                break;

            case 'excluir':
                $id = $_POST['id'];
                try {
                    // Buscar informações do apoiador
                    $stmt = $conn->prepare("SELECT logo FROM apoiadores WHERE id = ?");
                    $stmt->execute([$id]);
                    $apoiador = $stmt->fetch();
                    
                    if ($apoiador) {
                        // Excluir arquivo de imagem
                        $arquivo = 'assets/images/sponsors/' . $apoiador['logo'];
                        if (file_exists($arquivo)) {
                            unlink($arquivo);
                        }
                        
                        // Excluir registro do banco
                        $stmt = $conn->prepare("DELETE FROM apoiadores WHERE id = ?");
                        if ($stmt->execute([$id])) {
                            $mensagem = '<div class="alert alert-success">Apoiador excluído com sucesso!</div>';
                        }
                    }
                } catch (Exception $e) {
                    $mensagem = '<div class="alert alert-danger">Erro ao excluir apoiador: ' . $e->getMessage() . '</div>';
                }
                break;
        }
    }
}

// Buscar apoiadores cadastrados
$apoiadores = $conn->query("
    SELECT * FROM apoiadores 
    ORDER BY local_exibicao, posicao, data_inicio
")->fetchAll();
?>

<!-- Resto do código HTML permanece o mesmo -->