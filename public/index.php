<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\CategoriaController;
use MVC\Router;
use Controllers\AppController;
use Controllers\ProductoController;
<<<<<<< HEAD

=======
use Controllers\CategoriaController;
>>>>>>> da1e1c3c8d987cb39a1817c7f2631c35cc978b21


$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

<<<<<<< HEAD

//estas son las URLs para toda mierda

//enderizar la pagina de Categorias
$router->get('/categorias', [CategoriaController::class, 'renderizarPagina']);
//activar la clase de guardar
$router->post('/categorias/guardarAPI', [CategoriaController::class, 'guardarAPI']);
//renderiz<ar la pagina de PRoductos
$router->get('/productos', [CategoriaController::class, 'renderizarPagina']);
//avtivar eel gaurdar de categoriasw
$router->post('/productos', [CategoriaController::class, 'guardarAPI']);
=======
//Rutas para productos
$router->get('/productos', [ProductoController::class,'renderizarPagina']);
>>>>>>> da1e1c3c8d987cb39a1817c7f2631c35cc978b21

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
