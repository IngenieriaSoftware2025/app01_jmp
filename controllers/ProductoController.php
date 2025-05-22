<?php
namespace Controllers;

use Model\ActiveRecord;
use Model\Productos;
use Model\Categorias;
use Model\Prioridades;
use MVC\Router;

class ProductoController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $categorias = Categorias::all();
        $prioridades = Prioridades::all();
        $productos = Productos::allWithJoins();
        
        $router->render('productos/index', [
            'categorias' => $categorias,
            'prioridades' => $prioridades,
            'productos' => $productos
        ]);
    }
    
    public static function guardarAPI() {
        getHeadersApi();
        
        try {
            // Limpiar datos
            $_POST['prod_nombre'] = trim($_POST['prod_nombre']);
            $_POST['prod_cantidad'] = (int)$_POST['prod_cantidad'];
            $_POST['cat_id'] = (int)$_POST['cat_id'];
            $_POST['pri_id'] = (int)$_POST['pri_id'];
            
            // Validaciones
            if(empty($_POST['prod_nombre'])) {
                echo json_encode(['resultado' => false, 'mensaje' => 'El nombre del producto es obligatorio']);
                return;
            }
            
            if($_POST['prod_cantidad'] <= 0) {
                echo json_encode(['resultado' => false, 'mensaje' => 'La cantidad debe ser mayor a 0']);
                return;
            }
            
            if($_POST['cat_id'] <= 0 || $_POST['pri_id'] <= 0) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Debe seleccionar categoría y prioridad']);
                return;
            }
            
            // Verificar duplicado
            $existente = Productos::whereDuplicate($_POST['prod_nombre'], $_POST['cat_id']);
            if($existente && (!isset($_POST['prod_id']) || $existente->prod_id != $_POST['prod_id'])) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Este producto ya existe en esta categoría']);
                return;
            }
            
            $producto = new Productos($_POST);
            
            if(isset($_POST['prod_id']) && !empty($_POST['prod_id'])) {
                $producto->prod_id = $_POST['prod_id'];
                $resultado = $producto->actualizar();
            } else {
                $resultado = $producto->crear();
            }
            
            if($resultado['resultado']) {
                echo json_encode(['resultado' => true, 'mensaje' => 'Producto guardado exitosamente']);
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al guardar el producto']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public static function marcarCompradoAPI() {
        getHeadersApi();
        
        try {
            $id = $_POST['id'];
            $comprado = $_POST['comprado'];
            
            $producto = Productos::find($id);
            if(!$producto) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Producto no encontrado']);
                return;
            }
            
            $producto->comprado = $comprado;
            $resultado = $producto->actualizar();
            
            if($resultado['resultado']) {
                echo json_encode(['resultado' => true, 'mensaje' => 'Estado actualizado']);
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al actualizar']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public static function eliminarAPI() {
        getHeadersApi();
        
        try {
            $id = $_POST['id'];
            $producto = Productos::find($id);
            
            if(!$producto) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Producto no encontrado']);
                return;
            }
            
            $resultado = $producto->eliminar();
            
            if($resultado['resultado']) {
                echo json_encode(['resultado' => true, 'mensaje' => 'Producto eliminado exitosamente']);
            } else {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al eliminar el producto']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    public static function obtenerAPI() {
        getHeadersApi();
        
        try {
            $productos = Productos::allWithJoins();
            echo json_encode($productos);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al obtener productos: ' . $e->getMessage()]);
        }
    }
}