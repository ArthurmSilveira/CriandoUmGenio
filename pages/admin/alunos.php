<?php
verificarPermissao('admin');

$mensagem = '';

// Buscar todos os alunos
$sql = "
    SELECT a.*, u.email, u.usuario,
           COUNT(DISTINCT ad.disciplina_id) as total_disciplinas
    FROM alunos a 
    JOIN usuarios u ON a.usuario_id = u.id
    LEFT JOIN alunos_disciplinas ad ON ad.aluno_id = a.id
    GROUP BY a.id
    ORDER BY a.nome";

$alunos = $conn->query($sql)->fetchAll();
?>

<h2 class="mb-4">Gerenciar Alunos</h2>

<?php echo $mensagem; ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Lista de Alunos</h5>
            <a href="index.php?pagina=admin&acao=cadastrar_usuario&tipo=aluno" 
               class="btn btn-primary">
                Novo Aluno
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Usuário</th>
                        <th>Email</th>
                        <th>Série</th>
                        <th>Turma</th>
                        <th>Disciplinas</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($aluno['email']); ?></td>
                        <td><?php echo $aluno['serie'] . 'º Ano'; ?></td>
                        <td>Turma <?php echo $aluno['turma']; ?></td>
                        <td><?php echo $aluno['total_disciplinas']; ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="index.php?pagina=admin&acao=editar_aluno&id=<?php echo $aluno['id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    Editar
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-danger"
                                        onclick="confirmarExclusao(<?php echo $aluno['id']; ?>)">
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
                <p>Tem certeza que deseja excluir este aluno? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" id="formExclusao" style="display: inline;">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="aluno_id" id="alunoIdExclusao">
                    <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(alunoId) {
    document.getElementById('alunoIdExclusao').value = alunoId;
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