<?php
verificarPermissao('professor');

$professor_id = $_SESSION['usuario']['id'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aluno_ids = $_POST['aluno_id'];
    $presencas = $_POST['presente'];
    $disciplina_id = $_POST['disciplina_id'];
    $data = $_POST['data'];
    
    $stmt = $conn->prepare("
        INSERT INTO frequencia (aluno_id, disciplina_id, data, presente)
        VALUES (?, ?, ?, ?)
    ");
    
    $conn->beginTransaction();
    try {
        foreach ($aluno_ids as $key => $aluno_id) {
            $presente = isset($presencas[$key]) ? 1 : 0;
            $stmt->execute([$aluno_id, $disciplina_id, $data, $presente]);
        }
        $conn->commit();
        $mensagem = '<div class="alert alert-success">Frequência registrada com sucesso!</div>';
    } catch (Exception $e) {
        $conn->rollBack();
        $mensagem = '<div class="alert alert-danger">Erro ao registrar frequência.</div>';
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

<h2 class="mb-4">Registro de Frequência</h2>

<?php echo $mensagem; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="data" class="form-label">Data</label>
                        <input type="date" class="form-control" id="data" name="data" 
                               value="<?php echo date('Y-m-d'); ?>" required>
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
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="presente[]" value="${aluno.id}" checked>
                                    <label class="form-check-label">Presente</label>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML += `
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Registrar Frequência</button>
                    </div>
                `;
            });
    }
});</script>