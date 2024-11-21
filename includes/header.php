<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Educacional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Menu Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/images/logo.png" alt="Logo" height="40">
                Sistema Educacional
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?pagina=downloads">Downloads</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?pagina=sobre">Sobre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?pagina=contato">Contato</a>
                    </li>
                </ul>
                
                <?php if (isset($_SESSION['usuario'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Olá, <?php echo htmlspecialchars($_SESSION['usuario']['usuario']); ?>
                        </button>
                        <ul class="dropdown-menu">
                            <?php if ($_SESSION['usuario']['tipo'] === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item" href="index.php?pagina=admin&acao=dashboard">Dashboard</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?pagina=admin&acao=usuarios">Usuários</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="index.php?pagina=admin&acao=downloads">Downloads</a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a class="dropdown-item" href="index.php?pagina=perfil">Meu Perfil</a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="logout.php">Sair</a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="d-flex">
                        <a href="index.php?pagina=login" class="btn btn-light me-2">Entrar</a>
                        <a href="index.php?pagina=cadastro" class="btn btn-outline-light">Cadastrar</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">