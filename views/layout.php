<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="build/js/app.js"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>DemoApp</title>
</head>

<body>
<<<<<<< HEAD


    <nav class="navbar navbar-expand-lg navbar-dark  bg-dark">

        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="/app01_jmp/">
                <img src="<?= asset('./images/cit.png') ?>" width="35px'" alt="cit">
                Inventario
            </a>
            <div class="collapse navbar-collapse" id="navbarToggler">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin: 0;">
                    <!-- NAV DE PRODUCTOS -->
                <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app01_jmp/productos/"><i class="bi bi-house-fill me-2"></i>Productos</a>
                </li>
                <!-- NAV DE CATEGORIA -->
                <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app01_jmp/categorias/"><i class="bi bi-house-fill me-2"></i>Categorias</a>
                </li>
                        <ul class="dropdown-menu  dropdown-menu-dark " id="dropwdownRevision" style="margin: 0;">
                            <!-- <h6 class="dropdown-header">Información</h6> -->
                            <li>
                                <a class="dropdown-item nav-link text-white " href="/aplicaciones/nueva"><i class="ms-lg-0 ms-2 bi bi-plus-circle me-2"></i>Subitem</a>
                            </li>



                        </ul>
                    </div>

                </ul>
                <div class="col-lg-1 d-grid mb-lg-0 mb-2">
                    <!-- Ruta relativa desde el archivo donde se incluye menu.php -->
                    <a href="/menu/" class="btn btn-danger"><i class="bi bi-arrow-bar-left"></i>MENÚ</a>
                </div>


            </div>
        </div>

    </nav>
=======



<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Botón colapsable para móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Logo y nombre -->
        <a class="navbar-brand" href="/app01_jmp/">
            <img src="<?= asset('./images/cit.png') ?>" width="35" alt="cit" class="me-2">
            Lista de Compras
        </a>

        <!-- Contenido del navbar -->
        <div class="collapse navbar-collapse" id="navbarToggler">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- Enlace a inicio -->
                <li class="nav-item">
                    <a class="nav-link" href="/app01_jmp">
                        <i class="bi bi-house-fill me-2"></i>Inicio
                    </a>
                </li>

                <!-- Enlace a productos -->
                <li class="nav-item">
                     <a class="nav-link" href="/app01_jmp/productos"><!-- ESTA PENDEINTE -->
                        <i class="bi bi-list-check me-2"></i>Productos
                    </a>
                </li>

                <!-- Enlace a categorías -->
                <li class="nav-item">
                    <a class="nav-link" href="/app01_jmp/categorias">
                        <i class="bi bi-tags me-2"></i>Categorías
                    </a>
                </li>

                <!-- Enlace a prioridades -->
                <li class="nav-item">
                    <a class="nav-link" href="/app01_jmp/prioridades">
                        <i class="bi bi-exclamation-circle me-2"></i>Prioridades
                    </a>
                </li>
            </ul>

            <!-- Botón de regreso o salida -->
            <div class="d-grid">
                <a href="/menu/" class="btn btn-danger">
                    <i class="bi bi-arrow-bar-left"></i> Menú
                </a>
            </div>
        </div>
    </div>
</nav>






>>>>>>> da1e1c3c8d987cb39a1817c7f2631c35cc978b21
    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">

        <?php echo $contenido; ?>
    </div>
    <div class="container-fluid ">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                    Comando de Informática y Tecnología, <?= date('Y') ?> &copy;
                </p>
            </div>
        </div>
    </div>
</body>

</html>