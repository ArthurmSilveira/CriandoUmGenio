<?php
// Buscar informações da escola
$stmt = $conn->prepare("SELECT * FROM configuracoes WHERE id = 1");
$stmt->execute();
$config = $stmt->fetch();

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $assunto = $_POST['assunto'];
    $mensagem_texto = $_POST['mensagem'];
    
    // Enviar e-mail (você precisará configurar o servidor de e-mail)
    $para = $config['email'];
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    $corpo_email = "Nome: $nome\n";
    $corpo_email .= "E-mail: $email\n\n";
    $corpo_email .= "Mensagem:\n$mensagem_texto";
    
    if (mail($para, $assunto, $corpo_email, $headers)) {
        $mensagem = '<div class="alert alert-success">Mensagem enviada com sucesso! Entraremos em contato em breve.</div>';
    } else {
        $mensagem = '<div class="alert alert-danger">Erro ao enviar mensagem. Por favor, tente novamente.</div>';
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Entre em Contato</h2>
                    
                    <?php echo $mensagem; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Informações de Contato</h4>
                            <?php if (isset($config['nome_escola'])): ?>
                                <p><strong><?php echo htmlspecialchars($config['nome_escola']); ?></strong></p>
                            <?php endif; ?>
                            
                            <?php if (isset($config['endereco'])): ?>
                                <p>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($config['endereco']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (isset($config['telefone'])): ?>
                                <p>
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($config['telefone']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (isset($config['email'])): ?>
                                <p>
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($config['email']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <h5>Horário de Atendimento</h5>
                                <p>Segunda a Sexta: 8h às 18h</p>
                                <p>Sábado: 8h às 12h</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="assunto" class="form-label">Assunto</label>
                                    <select class="form-control" id="assunto" name="assunto" required>
                                        <option value="">Selecione...</option>
                                        <option value="Informações">Informações</option>
                                        <option value="Matrícula">Matrícula</option>
                                        <option value="Financeiro">Financeiro</option>
                                        <option value="Suporte">Suporte</option>
                                        <option value="Outros">Outros</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="mensagem" class="form-label">Mensagem</label>
                                    <textarea class="form-control" id="mensagem" name="mensagem" rows="5" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Enviar Mensagem</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4 class="mb-3">Localização</h4>
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3657.1194925179415!2d-46.6463989!3d-23.5646162!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjPCsDMzJzUyLjYiUyA0NsKwMzgnNDcuMCJX!5e0!3m2!1spt-BR!2sbr!4v1637097754789!5m2!1spt-BR!2sbr"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>
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
})();
</script>