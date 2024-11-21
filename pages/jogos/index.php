<?php
verificarPermissao('aluno');

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : 'todos';
$serie = isset($_GET['serie']) ? $_GET['serie'] : '';

$sql = "SELECT * FROM jogos WHERE 1=1";
if ($categoria !== 'todos') {
    $sql .= " AND categoria = :categoria";
}
if ($serie) {
    $sql .= " AND serie = :serie";
}
$sql .= " ORDER BY nome";

$stmt = $conn->prepare($sql);
if ($categoria !== 'todos') {
    $stmt->bindParam(':categoria', $categoria);
}
if ($serie) {
    $stmt->bindParam(':serie', $serie);
}
$stmt->execute();
$jogos = $stmt->fetchAll();
?>

<h2 class="mb-4">Jogos Educativos</h2>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Filtrar Jogos</h5>
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select class="form-control" id="categoria" name="categoria">
                            <option value="todos">Todos</option>
                            <option value="alfabetizacao" <?php echo $categoria === 'alfabetizacao' ? 'selected' : ''; ?>>Alfabetização</option>
                            <option value="matematica" <?php echo $categoria === 'matematica' ? 'selected' : ''; ?>>Matemática</option>
                            <option value="ciencias" <?php echo $categoria === 'ciencias' ? 'selected' : ''; ?>>Ciências</option>
                            <option value="memoria" <?php echo $categoria === 'memoria' ? 'selected' : ''; ?>>Memória</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="serie" class="form-label">Série</label>
                        <select class="form-control" id="serie" name="serie">
                            <option value="">Todas</option>
                            <option value="1" <?php echo $serie === '1' ? 'selected' : ''; ?>>1º Ano</option>
                            <option value="2" <?php echo $serie === '2' ? 'selected' : ''; ?>>2º Ano</option>
                            <option value="3" <?php echo $serie === '3' ? 'selected' : ''; ?>>3º Ano</option>
                            <option value="4" <?php echo $serie === '4' ? 'selected' : ''; ?>>4º Ano</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php foreach ($jogos as $jogo): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <img src="<?php echo htmlspecialchars($jogo['imagem']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($jogo['nome']); ?>">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($jogo['nome']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($jogo['descricao']); ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary"><?php echo htmlspecialchars($jogo['categoria']); ?></span>
                    <span class="badge bg-secondary"><?php echo $jogo['serie'] . 'º Ano'; ?></span>
                </div>
            </div>
            <div class="card-footer">
                <a href="index.php?pagina=jogos&acao=jogar&id=<?php echo $jogo['id']; ?>" class="btn btn-success w-100">Jogar</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>