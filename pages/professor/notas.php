<?php
verificarPermissao('professor');

$professor_id = $_SESSION['usuario']['id'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aluno_id = $_POST['aluno_id'];
    $disciplina_id = $_POST['disciplina_id'];
    $nota = $_POST['nota'];
    $bimestre = $_POST['bimestre'];
    
    $stmt = $conn->prepare("
        INSERT INTO notas (aluno_id, disciplina_id, nota, bimestre)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE nota = ?
    ");
    
    if ($stmt->execute([$aluno_id, $disciplina_id, $nota, $bimestre, $nota])) {
        $mensagem = '<div class="alert alert-success">Nota lançada com sucesso!</div>';
    } else {
        $mensagem = '<div class="alert alert-danger">Erro ao lançar nota.</div>';
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
?>

<h2 class="mb-4">Lançamento de Notas</h2>

<?php echo $mensagem; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6">
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
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="bimestre" class="form-label">Bimestre</label>
                        <select class="form-control" id="bimestre" name="bimestre" required>
                            <option value="">Selecione...</option>
                            <option value="1">1º Bimestre</option>
                            <option value="2">2º Bimestre</option>
                            <option value="3">3º Bimestre</option>
                            <option value="4">4º Bimestre</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="alunos-container">
                <!-- Os alunos serão carregados aqui via AJAX -->
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('disciplina_id').addEventListener('change', function() {
    const disciplinaId = this.value;
    if (disciplinaId) {
        fetch(`ajax/get_alunos.php?disciplina_id=${disciplinaId}`)
            .then(response => response.json())
            .then(alunos => {
                const container = document.getElementById('alunos-container');
                container.innerHTML = '';
                
                alunos.forEach(aluno => {
                    container.innerHTML += `
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label">${aluno.nome}</label>
                                <input type="hidden" name="aluno_id[]" value="${aluno.id}">
                            </div>
                            <div class="col-md-4">
                                <input type="number" class="form-control" name="nota[]" 
                                       min="0" max="10" step="0.5" required>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML += `
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Salvar Notas</button>
                    </div>
                `;
            });
    }
});</script>