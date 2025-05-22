<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\CategoriaController;
use MVC\Router;
use Controllers\AppController;
use Controllers\ProductoController;


$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);


// Estas son las URLs para la aplicación

// Renderizar la página de Categorías
$router->get('/categorias', [CategoriaController::class, 'renderizarPagina']);
// Activar la clase de guardar
$router->post('/categorias/guardarAPI', [CategoriaController::class, 'guardarAPI']);
// Renderizar la página de Productos
$router->get('/productos', [ProductoController::class, 'renderizarPagina']);
// Activar el guardar de Productos
$router->post('/productos/guardarAPI', [ProductoController::class, 'guardarAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
