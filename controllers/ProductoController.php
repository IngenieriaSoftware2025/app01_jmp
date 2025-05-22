<?php
namespace Controllers;

use Exception;

use Model\ActiveRecord;
Use Model\Productos;
use MVC\Router;

class ProductoController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('productos/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

    
        

    }
}