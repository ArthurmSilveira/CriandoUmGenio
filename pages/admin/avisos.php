<?php
verificarPermissao('admin');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        switch ($_POST['acao']) {
            case 'responder':
                $aviso_id = $_POST['aviso_id'];
                $resposta = $_POST['resposta'];
                
                $stmt = $conn->prepare("
                    UPDATE avisos 
                    SET resposta = ?, status = 'respondido', respondido_em = NOW() 
                    WHERE id = ?
                ");
                
                if ($stmt->execute([$resposta, $aviso_id])) {
                    $mensagem = '<div class="alert alert-success">Aviso respondido com sucesso!</div>';
                }
                break;
                
            case 'arquivar':
                $aviso_i d = $_POST['aviso_id'];
                
                $stmt = $conn->prepare("UPDATE avisos SET status = 'arquivado' WHERE id = ?");
                
                if ($stmt->execute([$aviso_id])) {
                    $mensagem = '<div class="alert alert-success">Aviso arquivado com sucesso!</div>';
                }
                break;
        }
    }
}

// Buscar avisos não arquivados
$avisos = $conn->query("
    SELECT a.*, p.nome as professor 
    FROM avisos a 
    JOIN professores p ON a.professor_id = p.id 
    WHERE a.status != 'arquivado' 
    ORDER BY a.criado_em DESC
")->fetchAll();
?>

<h2 class="mb-4">Avisos dos Professores</h2>

<?php echo $mensagem; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Professor</th>
                                <th>Assunto</th>
                                <th>Mensagem</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($avisos as $aviso): ?>
                            <tr class="<?php echo $aviso['status'] === 'novo' ? 'table-warning' : ''; ?>">
                                <td><?php echo htmlspecialchars($aviso['professor']); ?></td>
                                <td><?php echo htmlspecialchars($aviso['assunto']); ?></td>
                                <td><?php echo htmlspecialchars($aviso['mensagem']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $aviso['status'] === 'novo' ? 'warning' : 
                                             ($aviso['status'] === 'respondido' ? 'success' : 'secondary'); 
                                    ?>">
                                        <?php echo ucfirst($aviso['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($aviso['criado_em'])); ?></td>
                                <td>
                                    <?php if ($aviso['status'] === 'novo'): ?>
                                        <button type="button" class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#responderModal<?php echo $aviso['id']; ?>">
                                            Responder
                                        </button>
                                    <?php endif; ?>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="acao" value="arquivar">
                                        <input type="hidden" name="aviso_id" value="<?php echo $aviso['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-secondary">Arquivar</button>
                                    </form>
                                </td>
                            </tr>
                            
                            <!-- Modal de Resposta -->
                            <div class="modal fade" id="responderModal<?php echo $aviso['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Responder Aviso</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="acao" value="responder">
                                                <input type="hidden" name="aviso_id" value="<?php echo $aviso['id']; ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Professor</label>
                                                    <input type="text" class="form-control" 
                                                           value="<?php echo htmlspecialchars($aviso['professor']); ?>" 
                                                           readonly>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Assunto</label>
                                                    <input type="text" class="form-control" 
                                                           value="<?php echo htmlspecialchars($aviso['assunto']); ?>" 
                                                           readonly>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Mensagem</label>
                                                    <textarea class="form-control" rows="3" readonly>
                                                        <?php echo htmlspecialchars($aviso['mensagem']); ?>
                                                    </textarea>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="resposta" class="form-label">Sua Resposta</label>
                                                    <textarea class="form-control" id="resposta" name="resposta" 
                                                              rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Fechar
                                                </button>
                                                <button type="submit" class="btn btn-primary">Enviar Resposta</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Notificação sonora para novos avisos
function verificarNovosAvisos() {
    fetch('ajax/verificar_avisos.php')
        .then(response => response.json())
        .then(data => {
            if (data.novos_avisos > 0) {
                const audio = new Audio('assets/sounds/notification.mp3');
                audio.play();
                
                // Atualizar a página após 3 segundos
                setTimeout(() => {
                    location.reload();
                }, 3000);
            }
        });
}

// Verificar a cada 30 segundos
setInterval(verificarNovosAvisos, 30000);

// Marcar avisos como lidos
document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
    button.addEventListener('click', function() {
        const avisoId = this.closest('tr').dataset.id;
        fetch('ajax/marcar_lido.php', {
            method: 'POST',
            body: JSON.stringify({ aviso_id: avisoId }),
            headers: { 'Content-Type': 'application/json' }
        });
    });
});</script>