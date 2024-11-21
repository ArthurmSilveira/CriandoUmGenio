<?php
verificarPermissao('admin');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'cadastrar':
                $titulo = $_POST['titulo'];
                $descricao = $_POST['descricao'];
                $status = $_POST['status'];
                
                $arquivo = $_FILES['imagem'];
                $nome_arquivo = time() . '_' . basename($arquivo['name']);
                $diretorio = 'assets/images/banners/';
                
                if (!file_exists($diretorio)) {
                    mkdir($diretorio, 0777, true);
                }
                
                if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $nome_arquivo)) {
                    $stmt = $conn->prepare("
                        INSERT INTO banners (titulo, descricao, imagem, status) 
                        VALUES (?, ?, ?, ?)
                    ");
                    
                    if ($stmt->execute([$titulo, $descricao, $nome_arquivo, $status])) {
                        $mensagem = '<div class="alert alert-success">Banner cadastrado com sucesso!</div>';
                    }
                }
                break;
                
            case 'excluir':
                $id = $_POST['id'];
                $stmt = $conn->prepare("SELECT imagem FROM banners WHERE id = ?");
                $stmt->execute([$id]);
                $banner = $stmt->fetch();
                
                if ($banner && unlink('assets/images/banners/' . $banner['imagem'])) {
                    $stmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $mensagem = '<div class="alert alert-success">Banner excluído com sucesso!</div>';
                    }
                }
                break;
        }
    }
}

// Buscar banners cadastrados
$banners = $conn->query("SELECT * FROM banners ORDER BY ordem")->fetchAll();
?>

<h2 class="mb-4">Gerenciar Banners</h2>

<?php echo $mensagem; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Novo Banner</h5>
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
                        <label for="imagem" class="form-label">Imagem (1200x650px)</label>
                        <input type="file" class="form-control" id="imagem" name="imagem" required 
                               accept="image/jpeg,image/png">
                        <div class="form-text">Dimensões recomendadas: 1200x650 pixels</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Cadastrar Banner</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Banners Cadastrados</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Imagem</th>
                                <th>Título</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="banners-lista">
                            <?php foreach ($banners as $banner): ?>
                            <tr>
                                <td>
                                    <img src="assets/images/banners/<?php echo $banner['imagem']; ?>" 
                                         alt="<?php echo $banner['titulo']; ?>"
                                         style="max-width: 100px;">
                                </td>
                                <td><?php echo htmlspecialchars($banner['titulo']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $banner['status'] === 'ativo' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($banner['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Tem certeza que deseja excluir este banner?')">
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
// Drag and drop para reordenar banners
new Sortable(document.getElementById('banners-lista'), {
    animation: 150,
    onEnd: function(evt) {
        const ordem = Array.from(evt.to.children).map(tr => tr.dataset.id);
        fetch('ajax/reordenar_banners.php', {
            method: 'POST',
            body: JSON.stringify({ ordem }),
            headers: { 'Content-Type': 'application/json' }
        });
    }
});

// Preview da imagem
document.getElementById('imagem').addEventListener('change', function() {
    const arquivo = this.files[0];
    const img = new Image();
    img.onload = function() {
        if (img.width !== 1200 || img.height !== 650) {
            alert('A imagem deve ter 1200x650 pixels!');
            document.getElementById('imagem').value = '';
        }
    };
    img.src = URL.createObjectURL(arquivo);
});</script>