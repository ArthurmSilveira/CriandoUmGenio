<?php
verificarPermissao('admin');

$mensagem = '';

// Buscar todos os professores
$sql = "
    SELECT p.*, u.email, u.usuario,
           GROUP_CONCAT(d.nome) as disciplinas_nomes,
           COUNT(DISTINCT d.id) as total_disciplinas,
           COUNT(DISTINCT a.id) as total_alunos
    FROM professores p 
    JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN disciplinas d ON d.professor_id = p.id
    LEFT JOIN alunos_disciplinas ad ON ad.disciplina_id = d.id
    LEFT JOIN alunos a ON a.id = ad.aluno_id
    GROUP BY p.id
    ORDER BY p.nome";

$professores = $conn->query($sql)->fetchAll();
?>

<h2 class="mb-4">Gerenciar Professores</h2>

<?php echo $mensagem; ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Lista de Professores</h5>
            <a href="index.php?pagina=admin&acao=cadastrar_usuario&tipo=professor" 
               class="btn btn-primary">
                Novo Professor
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Email</th>
                        <th>Disciplinas</th>
                        <th>Total Alunos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($professores as $professor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($professor['nome']); ?></td>
                        <td><?php echo htmlspecialchars($professor['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($professor['email']); ?></td>
                        <td>
                            <?php 
                            $disciplinas = explode(',', $professor['disciplinas_nomes']);
                            foreach ($disciplinas as $disciplina) {
                                if ($disciplina) {
                                    echo '<span class="badge bg-info me-1">' . htmlspecialchars($disciplina) . '</span>';
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $professor['total_alunos']; ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="index.php?pagina=admin&acao=editar_professor&id=<?php echo $professor['id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    Editar
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmarExclusao(<?php echo $professor['id']; ?>)">
                                    Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalConfirmacao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este professor? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" id="formExclusao" style="display: inline;">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="professor_id" id="professorIdExclusao">
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(professorId) {
    document.getElementById('professorIdExclusao').value = professorId;
    new bootstrap.Modal(document.getElementById('modalConfirmacao')).show();
}

// DataTables para melhor visualização
$(document).ready(function() {
    $('.table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
        },
        pageLength: 10,
        order: [[0, 'asc']]
    });
});
</script>