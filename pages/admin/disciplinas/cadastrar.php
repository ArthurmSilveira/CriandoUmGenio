<?php
verificarPermissao('admin');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $serie = $_POST['serie'];
    $professor_id = $_POST['professor_id'];
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO disciplinas (nome, descricao, serie, professor_id) 
            VALUES (?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$nome, $descricao, $serie, $professor_id])) {
            $mensagem = '<div class="alert alert-success">Disciplina cadastrada com sucesso!</div>';
        } else {
            $mensagem = '<div class="alert alert-danger">Erro ao cadastrar disciplina.</div>';
        }
    } catch (PDOException $e) {
        $mensagem = '<div class="alert alert-danger">Erro ao cadastrar disciplina: ' . $e->getMessage() . '</div>';
    }
}

// Buscar professores para o select
$professores = $conn->query("SELECT id, nome FROM professores ORDER BY nome")->fetchAll();
?>

<h2 class="mb-4">Cadastrar Nova Disciplina</h2>

<?php echo $mensagem; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da Disciplina</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                </div>
                
                <div class="col-md-6">
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
                </div>
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label for="professor_id" class="form-label">Professor Responsável</label>
                <select class="form-control" id="professor_id" name="professor_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?php echo $professor['id']; ?>">
                            <?php echo htmlspecialchars($professor['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Cadastrar Disciplina</button>
                <a href="index.php?pagina=admin&acao=disciplinas" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</div>

<script>
// Validação do formulário
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>