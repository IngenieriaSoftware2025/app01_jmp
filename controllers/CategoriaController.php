<?php

namespace Controllers;

use MVC\Router;
use Model\Categorias;

class CategoriaController {

    public static function renderizarPagina(Router $router) {
        $categorias = Categorias::all();
        $router->render('categorias/index', [
            'categorias' => $categorias
        ]);
    }

    public static function guardarAPI() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categoria = new Categorias($_POST);

            // Validar
            if (!isset($_POST['cat_nombre']) || trim($_POST['cat_nombre']) === '') {
                echo json_encode([
                    'resultado' => false, 
                    'mensaje' => 'El nombre de la categoría es obligatorio'
                ]);
                return;
            }

            // Verificar si ya existe
            $existe = Categorias::where('cat_nombre', $categoria->cat_nombre);
            if ($existe && !isset($_POST['cat_id'])) {
                echo json_encode([
                    'resultado' => false, 
                    'mensaje' => 'La categoría ya existe'
                ]);
                return;
            }

            $resultado = $categoria->guardar();

            echo json_encode([
                'resultado' => true,
                'mensaje' => isset($_POST['cat_id']) ? 
                    'Categoría actualizada correctamente' : 
                    'Categoría registrada correctamente'
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
            
            if (!$id) {
                echo json_encode([
                    'resultado' => false, 
                    'mensaje' => 'ID no válido'
                ]);
                return;
            }

            $categoria = Categorias::find($id);

            if (!$categoria) {
                echo json_encode([
                    'resultado' => false, 
                    'mensaje' => 'Categoría no encontrada'
                ]);
                return;
            }

            $resultado = $categoria->eliminar();
            echo json_encode([
                'resultado' => true,
                'mensaje' => 'Categoría eliminada correctamente'
            ]);
        }
    }
}