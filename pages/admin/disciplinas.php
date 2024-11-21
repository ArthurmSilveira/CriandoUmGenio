<?php
verificarPermissao('admin');

// Verificar se há uma subação
$subacao = isset($_GET['subacao']) ? $_GET['subacao'] : 'listar';

switch($subacao) {
    case 'cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            $serie = $_POST['serie'];
            $professor_id = $_POST['professor_id'];
            
            $stmt = $conn->prepare("
                INSERT INTO disciplinas (nome, descricao, serie, professor_id) 
                VALUES (?, ?, ?, ?)
            ");
            
            if ($stmt->execute([$nome, $descricao, $serie, $professor_id])) {
                $mensagem = '<div class="alert alert-success">Disciplina cadastrada com sucesso!</div>';
            } else {
                $mensagem = '<div class="alert alert-danger">Erro ao cadastrar disciplina.</div>';
            }
        }
        
        // Buscar professores para o select
        $professores = $conn->query("SELECT id, nome FROM professores ORDER BY nome")->fetchAll();
        ?>
        <h2 class="mb-4">Cadastrar Nova Disciplina</h2>
        
        <?php if (isset($mensagem)) echo $mensagem; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="serie" class="form-label">Série</label>
                        <select class="form-control" id="serie" name="serie" required>
                            <option value="">Selecione...</option>
                            <option value="1">1º Ano</option>
                            <option value="2">2º Ano</option>
                            <option value="3">3º Ano</option>
                            <option value="4">4º Ano</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="professor_id" class="form-label">Professor</label>
                        <select class="form-control" id="professor_id" name="professor_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($professores as $professor): ?>
                                <option value="<?php echo $professor['id']; ?>">
                                    <?php echo htmlspecialchars($professor['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                        <a href="index.php?pagina=admin&acao=disciplinas" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
        <?php
        break;
        
    default:
        // Listar disciplinas
        $disciplinas = $conn->query("
            SELECT d.*, p.nome as professor_nome 
            FROM disciplinas d 
            LEFT JOIN professores p ON d.professor_id = p.id 
            ORDER BY d.serie, d.nome
        ")->fetchAll();
        ?>
        <h2 class="mb-4">Gerenciar Disciplinas</h2>
        
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <a href="index.php?pagina=admin&acao=disciplinas&subacao=cadastrar" 
                       class="btn btn-primary">Nova Disciplina</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Série</th>
                                <th>Professor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disciplinas as $disciplina): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($disciplina['nome']); ?></td>
                                <td><?php echo $disciplina['serie'] . 'º Ano'; ?></td>
                                <td><?php echo htmlspecialchars($disciplina['professor_nome']); ?></td>
                                <td>
                                    <a href="index.php?pagina=admin&acao=disciplinas&subacao=editar&id=<?php echo $disciplina['id']; ?>" 
                                       class="btn btn-sm btn-warning">Editar</a>
                                    <a href="index.php?pagina=admin&acao=disciplinas&subacao=excluir&id=<?php echo $disciplina['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Tem certeza que deseja excluir esta disciplina?')">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
}
?>