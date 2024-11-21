<?php
verificarPermissao('admin');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_escola = $_POST['nome_escola'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $sobre_texto = $_POST['sobre_texto'];
    $horario_funcionamento = $_POST['horario_funcionamento'];
    $mapa_url = $_POST['mapa_url'];
    
    try {
        $stmt = $conn->prepare("
            UPDATE configuracoes 
            SET nome_escola = ?, 
                endereco = ?, 
                telefone = ?, 
                email = ?, 
                sobre_texto = ?,
                horario_funcionamento = ?,
                mapa_url = ?
            WHERE id = 1
        ");
        
        if ($stmt->execute([
            $nome_escola, 
            $endereco, 
            $telefone, 
            $email, 
            $sobre_texto,
            $horario_funcionamento,
            $mapa_url
        ])) {
            $mensagem = '<div class="alert alert-success">Configurações atualizadas com sucesso!</div>';
        } else {
            throw new Exception('Erro ao atualizar configurações');
        }
    } catch (Exception $e) {
        $mensagem = '<div class="alert alert-danger">Erro: ' . $e->getMessage() . '</div>';
    }
}

// Buscar configurações atuais
$stmt = $conn->prepare("SELECT * FROM configuracoes WHERE id = 1");
$stmt->execute();
$config = $stmt->fetch();
?>

<h2 class="mb-4">Configurações do Sistema</h2>

<?php echo $mensagem; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nome_escola" class="form-label">Nome da Escola</label>
                        <input type="text" class="form-control" id="nome_escola" name="nome_escola" 
                               value="<?php echo htmlspecialchars($config['nome_escola']); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($config['email']); ?>" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco" 
                       value="<?php echo htmlspecialchars($config['endereco']); ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" 
                               value="<?php echo htmlspecialchars($config['telefone']); ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="horario_funcionamento" class="form-label">Horário de Funcionamento</label>
                        <textarea class="form-control" id="horario_funcionamento" name="horario_funcionamento" 
                                  rows="2" required><?php echo htmlspecialchars($config['horario_funcionamento']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="sobre_texto" class="form-label">Texto da Página Sobre</label>
                <textarea class="form-control" id="sobre_texto" name="sobre_texto" 
                          rows="5"><?php echo htmlspecialchars($config['sobre_texto']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="mapa_url" class="form-label">URL do Google Maps</label>
                <input type="text" class="form-control" id="mapa_url" name="mapa_url" 
                       value="<?php echo htmlspecialchars($config['mapa_url']); ?>">
                <div class="form-text">Cole aqui o código de incorporação do Google Maps</div>
            </div>

            <button type="submit" class="btn btn-primary">Salvar Configurações</button>
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

// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
    e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
});</script>