<?php
verificarPermissao('professor');

// Buscar informações do professor
$professor_id = $_SESSION['usuario']['id'];
$stmt = $conn->prepare("
    SELECT p.*, COUNT(DISTINCT d.id) as total_disciplinas, COUNT(DISTINCT a.id) as total_alunos
    FROM professores p 
    LEFT JOIN disciplinas d ON d.professor_id = p.id
    LEFT JOIN alunos_disciplinas ad ON ad.disciplina_id = d.id
    LEFT JOIN alunos a ON a.id = ad.aluno_id
    WHERE p.usuario_id = ?
    GROUP BY p.id
");
$stmt->execute([$professor_id]);
$professor = $stmt->fetch();

// Buscar últimas atividades
$stmt = $conn->prepare("
    SELECT m.titulo, m.criado_em, d.nome as disciplina
    FROM materiais m
    JOIN disciplinas d ON d.id = m.disciplina_id
    WHERE m.enviado_por = ?
    ORDER BY m.criado_em DESC
    LIMIT 5
");
$stmt->execute([$professor_id]);
$atividades = $stmt->fetchAll();
?>

<h2 class="mb-4">Dashboard do Professor</h2>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Minhas Disciplinas</h5>
                <h2 class="card-text"><?php echo $professor['total_disciplinas']; ?></h2>
                <a href="index.php?pagina=professor&acao=disciplinas" class="btn btn-light">Ver Disciplinas</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Total de Alunos</h5>
                <h2 class="card-text"><?php echo $professor['total_alunos']; ?></h2>
                <a href="index.php?pagina=professor&acao=alunos" class="btn btn-light">Ver Alunos</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Materiais Didáticos</h5>
                <a href="index.php?pagina=professor&acao=materiais" class="btn btn-light">Gerenciar Materiais</a>
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
                    <a href="index.php?pagina=professor&acao=lancar_notas" class="list-group-item list-group-item-action">
                        Lançar Notas
                    </a>
                    <a href="index.php?pagina=professor&acao=registrar_frequencia" class="list-group-item list-group-item-action">
                        Registrar Frequência
                    </a>
                    <a href="index.php?pagina=professor&acao=upload_material" class="list-group-item list-group-item-action">
                        Upload de Material
                    </a>
                    <a href="index.php?pagina=professor&acao=enviar_aviso" class="list-group-item list-group-item-action">
                        Enviar Aviso
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Últimas Atividades</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Disciplina</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($atividades as $atividade): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($atividade['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($atividade['disciplina']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($atividade['criado_em'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>