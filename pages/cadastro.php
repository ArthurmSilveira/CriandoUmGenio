<?php
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $tipo = $_POST['tipo'];
    
    if (cadastrarUsuario($usuario, $senha, $email, $tipo)) {
        if ($tipo === 'aluno') {
            $serie = $_POST['serie'];
            $turma = $_POST['turma'];
            $stmt = $conn->prepare("
                INSERT INTO alunos (usuario_id, nome, serie, turma) 
                VALUES (LAST_INSERT_ID(), ?, ?, ?)
            ");
            $stmt->execute([$nome, $serie, $turma]);
        } elseif ($tipo === 'professor') {
            $disciplinas = implode(',', $_POST['disciplinas']);
            $stmt = $conn->prepare("
                INSERT INTO professores (usuario_id, nome, disciplinas) 
                VALUES (LAST_INSERT_ID(), ?, ?)
            ");
            $stmt->execute([$nome, $disciplinas]);
        }
        
        $mensagem = '<div class="alert alert-success">Cadastro realizado com sucesso!</div>';
    } else {
        $mensagem = '<div class="alert alert-danger">Erro ao realizar cadastro.</div>';
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">
                    Cadastro de <?php echo ucfirst($tipo); ?>
                </h2>
                
                <?php echo $mensagem; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="usuario" class="form-label">Usuário</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        <div class="col-md-6">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" required>
                        </div>
                    </div>
                    
                    <?php if ($tipo === 'aluno'): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="serie" class="form-label">Série</label>
                                <select class="form-control" id="serie" name="serie" required>
                                    <option value="">Selecione...</option>
                                    <option value="1">1º Ano</option>
                                    <option value="2">2º Ano</option>
                                    <option value="3">3º Ano</option>
                                    <option value="4">4º Ano</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="turma" class="form-label">Turma</label>
                                <select class="form-control" id="turma" name="turma" required>
                                    <option value="">Selecione...</option>
                                    <option value="A">Turma A</option>
                                    <option value="B">Turma B</option>
                                    <option value="C">Turma C</option>
                                </select>
                            </div>
                        </div>
                    <?php elseif ($tipo === 'professor'): ?>
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
                    <?php endif; ?>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                        <a href="index.php" class="btn btn-secondary">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>