<?php

namespace Controllers;

use MVC\Router;
use Model\Categorias;

class CategoriaController {

    public static function index(Router $router) {
        $categorias = Categorias::all();
        $router->render('categorias/index', [
            'categorias' => $categorias
        ]);
    }

    public static function guardarAPI() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria = new Categorias($_POST);

            if (!$categoria->cat_nombre) {
                echo json_encode(['resultado' => false, 'mensaje' => 'El nombre de la categoría es obligatorio']);
                return;
            }

            // Evitar duplicados
            $existe = Categorias::where('cat_nombre', $categoria->cat_nombre);
            if ($existe) {
                echo json_encode(['resultado' => false, 'mensaje' => 'La categoría ya existe']);
                return;
            }

            $resultado = $categoria->guardar();
            echo json_encode([
                'resultado' => $resultado,
                'mensaje' => $resultado ? 'Categoría registrada correctamente' : 'Error al guardar categoría'
            ]);
        }
    }

    public static function obtenerAPI() {
        $categorias = Categorias::all();
        echo json_encode($categorias);
    }

    public static function eliminarAPI() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['cat_id'] ?? null;
            if ($id) {
                $categoria = Categorias::find($id);
                $resultado = $categoria->eliminar();
                echo json_encode([
                    'resultado' => $resultado,
                    'mensaje' => $resultado ? 'Categoría eliminada correctamente' : 'Error al eliminar categoría'
                ]);
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'ID no válido']);
            }
        }
    }
}
