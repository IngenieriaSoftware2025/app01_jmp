<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\CategoriaController;
use MVC\Router;
use Controllers\AppController;
use Controllers\ProductoController;


$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);


//estas son las URLs para toda mierda

//enderizar la pagina de Categorias
$router->get('/categorias', [CategoriaController::class, 'renderizarPagina']);
//activar la clase de guardar
$router->post('/categorias/guardarAPI', [CategoriaController::class, 'guardarAPI']);
//renderiz<ar la pagina de PRoductos
$router->get('/productos', [CategoriaController::class, 'renderizarPagina']);
//avtivar eel gaurdar de categoriasw
$router->post('/productos', [CategoriaController::class, 'guardarAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
