<?php
verificarPermissao('admin');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'cadastrar':
                $titulo = $_POST['titulo'];
                $descricao = $_POST['descricao'];
                $categoria = $_POST['categoria'];
                $serie = $_POST['serie'];
                
                $arquivo = $_FILES['arquivo'];
                $nome_arquivo = time() . '_' . basename($arquivo['name']);
                $diretorio = 'uploads/downloads/';
                
                if (!file_exists($diretorio)) {
                    mkdir($diretorio, 0777, true);
                }
                
                if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo)) {
                    $stmt = $conn->prepare("
                        INSERT INTO downloads (titulo, descricao, arquivo, categoria, serie) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    if ($stmt->execute([$titulo, $descricao, $nome_arquivo, $categoria, $serie])) {
                        $mensagem = '<div class="alert alert-success">Material cadastrado com sucesso!</div>';
                    }
                }
                break;
                
            case 'excluir':
                $id = $_POST['id'];
                $stmt = $conn->prepare("SELECT arquivo FROM downloads WHERE id = ?");
                $stmt->execute([$id]);
                $download = $stmt->fetch();
                
                if ($download && unlink('uploads/downloads/' . $download['arquivo'])) {
                    $stmt = $conn->prepare("DELETE FROM downloads WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $mensagem = '<div class="alert alert-success">Material excluído com sucesso!</div>';
                    }
                }
                break;
        }
    }
}

// Buscar downloads cadastrados
$downloads = $conn->query("SELECT * FROM downloads ORDER BY categoria, serie")->fetchAll();
?>

<h2 class="mb-4">Gerenciar Downloads</h2>

<?php echo $mensagem; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Novo Material</h5>
                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <input type="hidden" name="acao" value="cadastrar">
                    
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select class="form-control" id="categoria" name="categoria" required>
                            <option value="">Selecione...</option>
                            <option value="material_didatico">Material Didático</option>
                            <option value="atividade">Atividade Complementar</option>
                            <option value="jogo">Jogo Educativo</option>
                        </select>
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
                        <label for="arquivo" class="form-label">Arquivo</label>
                        <input type="file" class="form-control" id="arquivo" name="arquivo" required>
                        <div class="form-text">Formatos aceitos: PDF, DOC, DOCX, ZIP</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Cadastrar Material</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Materiais Cadastrados</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Série</th>
                                <th>Downloads</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($downloads as $download): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($download['titulo']); ?></td>
                                <td><?php echo ucwords(str_replace('_', ' ', $download['categoria'])); ?></td>
                                <td><?php echo $download['serie'] . 'º Ano'; ?></td>
                                <td><?php echo $download['contador']; ?></td>
                                <td>
                                    <a href="uploads/downloads/<?php echo $download['arquivo']; ?>" 
                                       class="btn btn-sm btn-primary" 
                                       target="_blank">Download</a>
                                       
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?php echo $download['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Tem certeza que deseja excluir este material?')">
                                            Excluir
                                        </button>
                                    </form>
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
// Validação do arquivo
document.getElementById('arquivo').addEventListener('change', function() {
    const arquivo = this.files[0];
    const extensoesPermitidas = ['pdf', 'doc', 'docx', 'zip'];
    const extensao = arquivo.name.split('.').pop().toLowerCase();
    
    if (!extensoesPermitidas.includes(extensao)) {
        alert('Formato de arquivo não permitido!');
        this.value = '';
    }
});</script>