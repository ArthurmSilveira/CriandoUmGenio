<?php
verificarPermissao('admin');

$acao = isset($_GET['acao']) ? $_GET['acao'] : 'listar';

switch($acao) {
    case 'cadastrar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_POST['usuario'];
            $senha = $_POST['senha'];
            $email = $_POST['email'];
            $tipo = $_POST['tipo'];
            
            if (cadastrarUsuario($usuario, $senha, $email, $tipo)) {
                $mensagem = "Usuário cadastrado com sucesso!";
                $tipo_mensagem = "success";
            } else {
                $mensagem = "Erro ao cadastrar usuário.";
                $tipo_mensagem = "danger";
            }
        }
        ?>
        <h2 class="mb-4">Cadastrar Novo Usuário</h2>
        
        <?php if (isset($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuário</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="">Selecione...</option>
                            <option value="admin">Administrador</option>
                            <option value="professor">Professor</option>
                            <option value="aluno">Aluno</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                    <a href="index.php?pagina=admin&acao=usuarios" class="btn btn-secondary">Voltar</a>
                </form>
            </div>
        </div>
        <?php
        break;
        
    default:
        $usuarios = $conn->query("SELECT * FROM usuarios ORDER BY criado_em DESC");
        ?>
        <h2 class="mb-4">Gerenciar Usuários</h2>
        
        <div class="card">
            <div class="card-body">
                <a href="index.php?pagina=admin&acao=usuarios&acao=cadastrar" class="btn btn-primary mb-3">
                    Novo Usuário
                </a>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuário</th>
                                <th>E-mail</th>
                                <th>Tipo</th>
                                <th>Data de Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($usuario = $usuarios->fetch()): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['tipo']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($usuario['criado_em'])); ?></td>
                                <td>
                                    <a href="index.php?pagina=admin&acao=usuarios&acao=editar&id=<?php echo $usuario['id']; ?>" 
                                       class="btn btn-sm btn-warning">Editar</a>
                                    <a href="index.php?pagina=admin&acao=usuarios&acao=excluir&id=<?php echo $usuario['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja excluir este usuário?')">Excluir</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
}
?>