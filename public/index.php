<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\ProductoController;
use Controllers\CategoriaController;


$router = new Router();
$router->setBaseURL('/' . $_ENV['app01_jmp']);

$router->get('/', [AppController::class,'index']);

//Rutas para productos
$router->get('/productos', [ProductoController::class,'renderizarPagina']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
