<?php
verificarPermissao('professor');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assunto = $_POST['assunto'];
    $mensagem_aviso = $_POST['mensagem'];
    $professor_id = $_SESSION['usuario']['id'];
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO avisos (professor_id, assunto, mensagem) 
            VALUES (?, ?, ?)
        ");
        
        if ($stmt->execute([$professor_id, $assunto, $mensagem_aviso])) {
            $mensagem = '<div class="alert alert-success">Aviso enviado com sucesso!</div>';
        } else {
            $mensagem = '<div class="alert alert-danger">Erro ao enviar aviso.</div>';
        }
    } catch (PDOException $e) {
        $mensagem = '<div class="alert alert-danger">Erro ao enviar aviso: ' . $e->getMessage() . '</div>';
    }
}

// Buscar avisos enviados
$stmt = $conn->prepare("
    SELECT * FROM avisos 
    WHERE professor_id = ? 
    ORDER BY criado_em DESC
");
$stmt->execute([$_SESSION['usuario']['id']]);
$avisos = $stmt->fetchAll();
?>

<h2 class="mb-4">Enviar Aviso</h2>

<?php echo $mensagem; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Novo Aviso</h5>
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="assunto" class="form-label">Assunto</label>
                        <input type="text" class="form-control" id="assunto" name="assunto" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mensagem" class="form-label">Mensagem</label>
                        <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Enviar Aviso</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Avisos Enviados</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Assunto</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th>Resposta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($avisos as $aviso): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($aviso['assunto']); ?></td>
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
                                    <?php if ($aviso['resposta']): ?>
                                        <button type="button" class="btn btn-sm btn-info"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#respostaModal<?php echo $aviso['id']; ?>">
                                            Ver Resposta
                                        </button>
                                        
                                        <!-- Modal de Resposta -->
                                        <div class="modal fade" id="respostaModal<?php echo $aviso['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Resposta do Aviso</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Assunto:</strong> <?php echo htmlspecialchars($aviso['assunto']); ?></p>
                                                        <p><strong>Seu aviso:</strong> <?php echo htmlspecialchars($aviso['mensagem']); ?></p>
                                                        <hr>
                                                        <p><strong>Resposta:</strong> <?php echo htmlspecialchars($aviso['resposta']); ?></p>
                                                        <p><small class="text-muted">
                                                            Respondido em: <?php echo date('d/m/Y H:i', strtotime($aviso['respondido_em'])); ?>
                                                        </small></p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Aguardando resposta</span>
                                    <?php endif; ?>
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
})();</script>