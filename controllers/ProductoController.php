<?php
namespace Controllers;

use Exception;

use Model\ActiveRecord;
Use Model\Productos;
use MVC\Router;

class ProductoController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('productos/index', []);
    }

}