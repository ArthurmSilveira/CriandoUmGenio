<?php
verificarPermissao('admin');

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    
    try {
        $conn->beginTransaction();
        
        // Cadastrar usuário
        if (cadastrarUsuario($usuario, $senha, $email, $tipo)) {
            $usuario_id = $conn->lastInsertId();
            
            // Cadastrar informações específicas baseado no tipo
            if ($tipo === 'aluno') {
                $serie = $_POST['serie'];
                $turma = $_POST['turma'];
                
                $stmt = $conn->prepare("
                    INSERT INTO alunos (usuario_id, nome, serie, turma) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$usuario_id, $nome, $serie, $turma]);
                
            } elseif ($tipo === 'professor') {
                $disciplinas = isset($_POST['disciplinas']) ? implode(',', $_POST['disciplinas']) : '';
                
                $stmt = $conn->prepare("
                    INSERT INTO professores (usuario_id, nome, disciplinas) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$usuario_id, $nome, $disciplinas]);
            }
            
            $conn->commit();
            $mensagem = '<div class="alert alert-success">Usuário cadastrado com sucesso!</div>';
            
        } else {
            throw new Exception("Erro ao cadastrar usuário");
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $mensagem = '<div class="alert alert-danger">Erro ao cadastrar usuário: ' . $e->getMessage() . '</div>';
    }
}
?>

<h2 class="mb-4">Cadastrar Novo Usuário</h2>

<?php echo $mensagem; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Usuário</label>
                <select class="form-control" id="tipo" name="tipo" required>
                    <option value="">Selecione...</option>
                    <option value="admin">Administrador</option>
                    <option value="professor">Professor</option>
                    <option value="aluno">Aluno</option>
                </select>
            </div>

            <!-- Campos específicos para Aluno -->
            <div id="campos-aluno" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="serie" class="form-label">Série</label>
                            <select class="form-control" id="serie" name="serie">
                                <option value="">Selecione...</option>
                                <option value="1">1º Ano</option>
                                <option value="2">2º Ano</option>
                                <option value="3">3º Ano</option>
                                <option value="4">4º Ano</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="turma" class="form-label">Turma</label>
                            <select class="form-control" id="turma" name="turma">
                                <option value="">Selecione...</option>
                                <option value="A">Turma A</option>
                                <option value="B">Turma B</option>
                                <option value="C">Turma C</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campos específicos para Professor -->
            <div id="campos-professor" style="display: none;">
                <div class="mb-3">
                    <label class="form-label">Disciplinas</label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="disciplinas[]" value="portugues">
                                <label class="form-check-label">Português</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="disciplinas[]" value="matematica">
                                <label class="form-check-label">Matemática</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="disciplinas[]" value="ciencias">
                                <label class="form-check-label">Ciências</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="disciplinas[]" value="historia">
                                <label class="form-check-label">História</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="disciplinas[]" value="geografia">
                                <label class="form-check-label">Geografia</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="disciplinas[]" value="artes">
                                <label class="form-check-label">Artes</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Cadastrar Usuário</button>
                <a href="index.php?pagina=admin&acao=usuarios" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</div>

<script>
// Mostrar/ocultar campos específicos baseado no tipo de usuário
document.getElementById('tipo').addEventListener('change', function() {
    const camposAluno = document.getElementById('campos-aluno');
    const camposProfessor = document.getElementById('campos-professor');
    
    camposAluno.style.display = 'none';
    camposProfessor.style.display = 'none';
    
    if (this.value === 'aluno') {
        camposAluno.style.display = 'block';
        // Tornar campos obrigatórios
        document.getElementById('serie').required = true;
        document.getElementById('turma').required = true;
    } else if (this.value === 'professor') {
        camposProfessor.style.display = 'block';
    }
});

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