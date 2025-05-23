<?php

namespace Controllers;

use MVC\Router;
use Model\Categorias;
use Exception;

class CategoriaController {

    public static function renderizarPagina(Router $router) {
        $categorias = Categorias::all();
        $router->render('categorias/index', [
            'categorias' => $categorias
        ]);
    }

    public static function guardarAPI() {
        // Establecer encabezados para JSON
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
            return;
        }
        
        // Sanitizar y validar datos
        $cat_nombre = isset($_POST['cat_nombre']) ? htmlspecialchars(trim($_POST['cat_nombre'])) : '';
        
        // Validaciones básicas
        if (empty($cat_nombre)) {
            echo json_encode(['resultado' => false, 'mensaje' => 'El nombre de la categoría es obligatorio']);
            return;
        }
        
        try {
            // Verificar si es una actualización
            if (isset($_POST['cat_id']) && !empty($_POST['cat_id'])) {
                $cat_id = (int)$_POST['cat_id'];
                
                // Buscar la categoría existente
                $categoria = Categorias::find($cat_id);
                
                if ($categoria) {
                    // Actualizar datos usando sincronizar
                    $categoria->sincronizar([
                        'cat_nombre' => $cat_nombre
                    ]);
                    
                    // Guardar cambios
                    $resultado = $categoria->guardar();
                    
                    // Enviar respuesta
                    echo json_encode([
                        'resultado' => true,
                        'mensaje' => 'La categoría ha sido actualizada correctamente',
                        'id' => $cat_id
                    ]);
                    return;
                }
            }
            
            // Verificar si ya existe la categoría
            $existente = Categorias::where('cat_nombre', $cat_nombre);
            if (!empty($existente)) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Esta categoría ya existe']);
                return;
            }
            
            // Crear nueva categoría
            $categoria = new Categorias([
                'cat_nombre' => $cat_nombre
            ]);
            
            // Guardar la categoría
            $resultado = $categoria->guardar();
            
            // Enviar respuesta
            echo json_encode([
                'resultado' => true,
                'mensaje' => 'La categoría ha sido agregada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al guardar la categoría',
                'error' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerAPI() {
        // Establecer encabezados para JSON
        header('Content-Type: application/json');
        
        try {
            $categorias = Categorias::all();
            echo json_encode($categorias);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al obtener las categorías',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function eliminarAPI() {
        // Establecer encabezados para JSON
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
            return;
        }
        
        // Verificar que se haya enviado el ID
        if (!isset($_POST['cat_id'])) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID no proporcionado']);
            return;
        }
        
        $id = filter_var($_POST['cat_id'], FILTER_VALIDATE_INT);
        
        if (!$id) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID no válido']);
            return;
        }
        
        try {
            $categoria = Categorias::find($id);
            
            if (!$categoria) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Categoría no encontrada']);
                return;
            }
            
            $resultado = $categoria->eliminar();
            
            echo json_encode([
                'resultado' => true,
                'mensaje' => 'La categoría ha sido eliminada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al eliminar la categoría',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}