<?php
verificarPermissao('admin');

$total_alunos = $conn->query("SELECT COUNT(*) FROM alunos")->fetchColumn();
$total_professores = $conn->query("SELECT COUNT(*) FROM professores")->fetchColumn();
$total_disciplinas = $conn->query("SELECT COUNT(*) FROM disciplinas")->fetchColumn();
$total_downloads = $conn->query("SELECT COUNT(*) FROM downloads")->fetchColumn();
?>

<h2 class="mb-4">Dashboard Administrativo</h2>

<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total de Alunos</h5>
                <h2 class="card-text"><?php echo $total_alunos; ?></h2>
                <a href="index.php?pagina=admin&acao=alunos" class="btn btn-light">Gerenciar Alunos</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total de Professores</h5>
                <h2 class="card-text"><?php echo $total_professores; ?></h2>
                <a href="index.php?pagina=admin&acao=professores" class="btn btn-light">Gerenciar Professores</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Total de Disciplinas</h5>
                <h2 class="card-text"><?php echo $total_disciplinas; ?></h2>
                <a href="index.php?pagina=admin&acao=disciplinas" class="btn btn-light">Gerenciar Disciplinas</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Total de Downloads</h5>
                <h2 class="card-text"><?php echo $total_downloads; ?></h2>
                <a href="index.php?pagina=admin&acao=downloads" class="btn btn-light">Gerenciar Downloads</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Ações Rápidas</h5>
                <div class="list-group">
                    <a href="index.php?pagina=admin&acao=cadastrar_usuario" class="list-group-item list-group-item-action">
                        Cadastrar Novo Usuário
                    </a>
                    <a href="index.php?pagina=admin&acao=cadastrar_disciplina" class="list-group-item list-group-item-action">
                        Cadastrar Nova Disciplina
                    </a>
                    <a href="index.php?pagina=admin&acao=apoiadores" class="list-group-item list-group-item-action">
                        Gerenciar Apoiadores
                    </a>
                    <a href="index.php?pagina=admin&acao=downloads" class="list-group-item list-group-item-action">
                        Gerenciar Downloads
                    </a>
                    <a href="index.php?pagina=admin&acao=relatorios" class="list-group-item list-group-item-action">
                        Gerar Relatórios
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Últimos Usuários Cadastrados</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Tipo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $usuarios = $conn->query("SELECT usuario, tipo, criado_em FROM usuarios ORDER BY criado_em DESC LIMIT 5");
                            while ($usuario = $usuarios->fetch()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['tipo']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($usuario['criado_em'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>