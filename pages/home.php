<?php
// Buscar banners ativos
$stmt = $conn->prepare("
    SELECT * FROM apoiadores 
    WHERE status = 'ativo' 
    AND local_exibicao IN ('banner', 'geral')
    AND data_inicio <= CURRENT_DATE 
    AND data_fim >= CURRENT_DATE
    ORDER BY posicao
");
$stmt->execute();
$banners = $stmt->fetchAll();

// Buscar apoiadores do rodapé
$stmt = $conn->prepare("
    SELECT * FROM apoiadores 
    WHERE status = 'ativo' 
    AND local_exibicao IN ('rodape', 'geral')
    AND data_inicio <= CURRENT_DATE 
    AND data_fim >= CURRENT_DATE
    ORDER BY posicao
");
$stmt->execute();
$apoiadores_rodape = $stmt->fetchAll();
?>

<!-- Banner Rotativo -->
<div id="bannerCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
    <?php if (!empty($banners)): ?>
        <div class="carousel-indicators">
            <?php foreach ($banners as $index => $banner): ?>
                <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="<?php echo $index; ?>" 
                        <?php echo $index === 0 ? 'class="active"' : ''; ?>></button>
            <?php endforeach; ?>
        </div>
        
        <div class="carousel-inner">
            <?php foreach ($banners as $index => $banner): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="assets/images/sponsors/<?php echo $banner['logo']; ?>" 
                         class="d-block w-100" alt="<?php echo $banner['nome']; ?>"
                         style="height: 650px; object-fit: cover;">
                    <?php if ($banner['link']): ?>
                        <a href="<?php echo $banner['link']; ?>" target="_blank" class="carousel-caption">
                            <h2><?php echo $banner['nome']; ?></h2>
                        </a>
                    <?php else: ?>
                        <div class="carousel-caption">
                            <h2><?php echo $banner['nome']; ?></h2>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    <?php else: ?>
        <!-- Banner padrão quando não houver apoiadores -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/banners/default.jpg" class="d-block w-100" alt="Bem-vindo"
                     style="height: 650px; object-fit: cover;">
                <div class="carousel-caption">
                    <h2>Bem-vindo ao Sistema Educacional</h2>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Áreas de Acesso -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <h3 class="card-title">Área do Aluno</h3>
                <p class="card-text">Acesse suas notas, materiais e atividades online.</p>
                <a href="index.php?pagina=cadastro&tipo=aluno" class="btn btn-outline-primary mb-2">Cadastrar</a>
                <a href="index.php?pagina=login&tipo=aluno" class="btn btn-primary">Entrar</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <h3 class="card-title">Área do Professor</h3>
                <p class="card-text">Gerencie suas turmas, notas e materiais didáticos.</p>
                <a href="index.php?pagina=login&tipo=professor" class="btn btn-primary">Entrar</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <h3 class="card-title">Área Administrativa</h3>
                <p class="card-text">Acesso restrito para administradores do sistema.</p>
                <a href="index.php?pagina=login&tipo=admin" class="btn btn-primary">Entrar</a>
            </div>
        </div>
    </div>
</div>

<!-- Área de Apoiadores -->
<?php if (!empty($apoiadores_rodape)): ?>
<div class="apoiadores-section py-4 bg-light">
    <div class="container">
        <h3 class="text-center mb-4">Nossos Apoiadores</h3>
        <div class="row justify-content-center align-items-center">
            <?php foreach ($apoiadores_rodape as $apoiador): ?>
                <div class="col-md-3 mb-3 text-center">
                    <?php if ($apoiador['link']): ?>
                        <a href="<?php echo $apoiador['link']; ?>" target="_blank">
                            <img src="assets/images/sponsors/<?php echo $apoiador['logo']; ?>" 
                                 alt="<?php echo $apoiador['nome']; ?>"
                                 class="img-fluid sponsor-img" width="300" height="150">
                        </a>
                    <?php else: ?>
                        <img src="assets/images/sponsors/<?php echo $apoiador['logo']; ?>" 
                             alt="<?php echo $apoiador['nome']; ?>"
                             class="img-fluid sponsor-img" width="300" height="150">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>