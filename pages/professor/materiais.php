<?php
verificarPermissao('professor');

$professor_id = $_SESSION['usuario']['id'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $disciplina_id = $_POST['disciplina_id'];
    
    // Configuração do upload
    $diretorio_destino = 'uploads/materiais/';
    if (!file_exists($diretorio_destino)) {
        mkdir($diretorio_destino, 0777, true);
    }
    
    $arquivo = $_FILES['arquivo'];
    $nome_arquivo = time() . '_' . basename($arquivo['name']);
    $caminho_arquivo = $diretorio_destino . $nome_arquivo;
    
    if (move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
        $stmt = $conn->prepare("
            INSERT INTO materiais (titulo, descricao, arquivo, disciplina_id, enviado_por)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$titulo, $descricao, $caminho_arquivo, $disciplina_id, $professor_id])) {
            $mensagem = '<div class="alert alert-success">Material enviado com sucesso!</div>';
        } else {
            $mensagem = '<div class="alert alert-danger">Erro ao salvar o material.</div>';
        }
    } else {
        $mensagem = '<div class="alert alert-danger">Erro ao fazer upload do arquivo.</div>';
    }
}

// Buscar disciplinas do professor
$stmt = $conn->prepare("
    SELECT id, nome, serie
    FROM disciplinas
    WHERE professor_id = ?
    ORDER BY serie, nome
");
$stmt->execute([$professor_id]);
$disciplinas = $stmt->fetchAll();

// Buscar materiais já enviados
$stmt = $conn->prepare("
    SELECT m.*, d.nome as disciplina
    FROM materiais m
    JOIN disciplinas d ON d.id = m.disciplina_id
    WHERE m.enviado_por = ?
    ORDER BY m.criado_em DESC
");
$stmt->execute([$professor_id]);
$materiais = $stmt->fetchAll();
?>

<h2 class="mb-4">Gerenciar Materiais Didáticos</h2>

<?php echo $mensagem; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Novo Material</h5>
                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="disciplina_id" class="form-label">Disciplina</label>
                        <select class="form-control" id="disciplina_id" name="disciplina_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($disciplinas as $disciplina): ?>
                                <option value="<?php echo $disciplina['id']; ?>">
                                    <?php echo htmlspecialchars($disciplina['nome']) . ' - ' . $disciplina['serie'] . 'º Ano'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="arquivo" class="form-label">Arquivo</label>
                        <input type="file" class="form-control" id="arquivo" name="arquivo" required>
                        <div class="form-text">Formatos aceitos: PDF, DOC, DOCX, PPT, PPTX</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Enviar Material</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Materiais Enviados</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Disciplina</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materiais as $material): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($material['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($material['disciplina']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($material['criado_em'])); ?></td>
                                <td>
                                    <a href="<?php echo $material['arquivo']; ?>" 
                                       class="btn btn-sm btn-primary" 
                                       target="_blank">Download</a>
                                    <a href="index.php?pagina=professor&acao=materiais&acao=excluir&id=<?php echo $material['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Tem certeza que deseja excluir este material?')">Excluir</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview do arquivo selecionado
document.getElementById('arquivo').addEventListener('change', function() {
    const arquivo = this.files[0];
    const extensoesPermitidas = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
    const extensao = arquivo.name.split('.').pop().toLowerCase();
    
    if (!extensoesPermitidas.includes(extensao)) {
        alert('Formato de arquivo não permitido!');
        this.value = '';
    }
});</script>