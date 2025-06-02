<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Compras - María</title>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler"
                aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand" href="/app01_jmp/">
                <i class="bi bi-cart4 me-2"></i>
                Lista de Compras
            </a>

            <div class="collapse navbar-collapse" id="navbarToggler">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/app01_jmp/productos">
                            <i class="bi bi-list-check me-1"></i>Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/app01_jmp/categorias">
                            <i class="bi bi-tags me-1"></i>Categorías
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/app01_jmp/clientes">
                            <i class="bi bi-people me-1"></i>Clientes
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="progress fixed-bottom" style="height: 4px;">
        <div class="progress-bar progress-bar-animated bg-success" id="bar" role="progressbar"
            aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <div class="container-fluid py-4" style="min-height: 85vh">
        <?php echo $contenido; ?>
    </div>

    <footer class="bg-light py-3 mt-auto">
        <div class="container text-center">
            <p class="text-muted mb-0 small">
                Lista de Compras de María &copy; <?= date('Y') ?>
            </p>
        </div>
    </footer>

    <script src="<?= asset('build/js/app.js') ?>"></script>
</body>

</html>