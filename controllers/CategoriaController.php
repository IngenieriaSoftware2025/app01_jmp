<?php
namespace Controllers;

use Model\ActiveRecord;
use Model\Categorias;
use MVC\Router;

class CategoriaController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $categorias = Categorias::all();
        $router->render('categorias/index', [
            'categorias' => $categorias
        ]);
    }
    
    public static function guardarAPI() {
        getHeadersApi();
        
        try {
            $_POST['cat_nombre'] = trim($_POST['cat_nombre']);
            
            if(empty($_POST['cat_nombre'])) {
                echo json_encode(['resultado' => false, 'mensaje' => 'El nombre es obligatorio']);
                return;
            }
            
            // Verificar si ya existe
            $existente = Categorias::where('cat_nombre', $_POST['cat_nombre']);
            if($existente && (!isset($_POST['cat_id']) || $existente->cat_id != $_POST['cat_id'])) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Esta categoría ya existe']);
                return;
            }
            
            $categoria = new Categorias($_POST);
            
            if(isset($_POST['cat_id']) && !empty($_POST['cat_id'])) {
                $categoria->cat_id = $_POST['cat_id'];
                $resultado = $categoria->actualizar();
            } else {
                $resultado = $categoria->crear();
            }
            
            if($resultado['resultado']) {
                echo json_encode(['resultado' => true, 'mensaje' => 'Categoría guardada exitosamente']);
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al guardar la categoría']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public static function eliminarAPI() {
        getHeadersApi();
        
        try {
            $id = $_POST['id'];
            $categoria = Categorias::find($id);
            
            if(!$categoria) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Categoría no encontrada']);
                return;
            }
            
            $resultado = $categoria->eliminar();
            
            if($resultado['resultado']) {
                echo json_encode(['resultado' => true, 'mensaje' => 'Categoría eliminada exitosamente']);
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al eliminar la categoría']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public static function obtenerAPI() {
        getHeadersApi();
        
        try {
            $categorias = Categorias::all();
            echo json_encode($categorias);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al obtener categorías: ' . $e->getMessage()]);
        }
    }
}