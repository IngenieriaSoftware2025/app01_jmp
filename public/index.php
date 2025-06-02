<?php
require_once __DIR__ . '/../includes/app.php';

use Controllers\AppController;
use Controllers\CategoriaController;
use Controllers\ProductoController;
use Controllers\ClienteController;
use MVC\Router;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

// RUTA INICIAL
$router->get('/', [AppController::class, 'index']);

// RUTAS PARA CATEGORÍAS
$router->get('/categorias', [CategoriaController::class, 'renderizarPagina']);
$router->post('/categorias/guardarAPI', [CategoriaController::class, 'guardarAPI']);
$router->post('/categorias/eliminarAPI', [CategoriaController::class, 'eliminarAPI']);
$router->get('/categorias/obtenerAPI', [CategoriaController::class, 'obtenerAPI']);

// RUTAS PARA PRODUCTOS
$router->get('/productos', [ProductoController::class, 'renderizarPagina']);
$router->post('/productos/guardarAPI', [ProductoController::class, 'guardarAPI']);
$router->get('/productos/eliminar', [ProductoController::class, 'eliminarAPI']);
$router->post('/productos/marcarCompradoAPI', [ProductoController::class, 'marcarAPI']);
$router->get('/productos/obtenerAPI', [ProductoController::class, 'obtenerAPI']);

// RUTAS PARA CLIENTES
$router->get('/clientes', [ClienteController::class, 'renderizarPagina']);
$router->post('/clientes/guardarAPI', [ClienteController::class, 'guardarAPI']);
$router->post('/clientes/eliminarAPI', [ClienteController::class, 'eliminarAPI']);
$router->get('/clientes/obtenerAPI', [ClienteController::class, 'obtenerAPI']);

// VALIDAR RUTAS
$router->comprobarRutas();