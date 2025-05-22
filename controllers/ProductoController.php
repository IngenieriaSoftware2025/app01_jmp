<?php
namespace Controllers;

use Model\ActiveRecord;
use MVC\Router;


class ProductoController extends ActiveRecord{

    public function renderizarPagina(Router $router)
    {
        $router->render('productos/index', []);
    }
    public static function guardarAPI() {
        getHeadersApi();
    }
}

