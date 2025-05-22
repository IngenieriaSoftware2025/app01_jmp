<?php
namespace Controllers;

use Model\ActiveRecord;
use MVC\Router;


class CategoriaController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('categorias/index', []);
    }
    
    public static function guardarAPI() {
        getHeadersApi();
    }
}

