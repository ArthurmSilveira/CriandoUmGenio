<?php
// Buscar downloads ativos
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$serie = isset($_GET['serie']) ? $_GET['serie'] : '';

$sql = "SELECT * FROM downloads WHERE 1=1";
if ($categoria) {
    $sql .= " AND categoria = :categoria";
}
if ($serie) {
    $sql .= " AND serie = :serie";
}
$sql .= " ORDER BY categoria, titulo";

$stmt = $conn->prepare($sql);
if ($categoria) {
    $stmt->bindParam(':categoria', $categoria);
}
if ($serie) {
    $stmt->bindParam(':serie', $serie);
}
$stmt->execute();
$downloads = $stmt->fetchAll();
?>

<h2 class="mb-4">Downloads</h2>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Filtrar Downloads</h5>
                <form method="GET" class="row g-3">
                    <input type="hidden" name="pagina" value="downloads">
                    
                    <div class="col-md-6">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select class="form-control" id="categoria" name="categoria">
                            <option value="">Todas</option>
                            <option value="material_didatico" <?php echo $categoria === 'material_didatico' ? 'selected' : ''; ?>>
                                Material Didático
                            </option>
                            <option value="atividade" <?php echo $categoria === 'atividade' ? 'selected' : ''; ?>>
                                Atividade Complementar
                            </option>
                            <option value="jogo" <?php echo $categoria === 'jogo' ? 'selected' : ''; ?>>
                                Jogo Educativo
                            </option>
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
                        <?php if ($categoria || $serie): ?>
                            <a href="index.php?pagina=downloads" class="btn btn-secondary">Limpar Filtros</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if (empty($downloads)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                Nenhum material encontrado com os filtros selecionados.
            </div>
        </div>
    <?php endif; ?>
    
    <?php foreach ($downloads as $download): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($download['titulo']); ?></h5>
                    
                    <p class="card-text"><?php echo htmlspecialchars($download['descricao']); ?></p>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary">
                            <?php echo ucwords(str_replace('_', ' ', $download['categoria'])); ?>
                        </span>
                        <span class="badge bg-secondary"><?php echo $download['serie']; ?>º Ano</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Downloads: <?php echo $download['contador']; ?>
                        </small>
                        <a href="download.php?id=<?php echo $download['id']; ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Atualizar página ao mudar filtros
document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', () => {
        document.querySelector('form').submit();
    });
});
</script>