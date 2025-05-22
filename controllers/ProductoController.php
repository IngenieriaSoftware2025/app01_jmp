<?php

namespace Controllers;

use MVC\Router;
use Model\Productos;
use Model\Categorias;
use Exception;
use Model\ActiveRecord;

class ProductoController extends ActiveRecord
{
    public function renderizarPagina(Router $router)
    {
        $router->render('productos/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validación del nombre
            $_POST['prod_nombre'] = htmlspecialchars($_POST['prod_nombre']);
            $nombre_length = strlen($_POST['prod_nombre']);

            if ($nombre_length < 2) {
                http_response_code(400);
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'El nombre del producto debe tener al menos 2 caracteres'
                ]);
                return;
            }

            // Validación de cantidad
            $_POST['prod_cantidad'] = filter_var($_POST['prod_cantidad'], FILTER_VALIDATE_INT);

            if ($_POST['prod_cantidad'] < 1) {
                http_response_code(400);
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'La cantidad debe ser mayor a 0'
                ]);
                return;
            }

            // Validar categoría y prioridad
            $_POST['cat_id'] = filter_var($_POST['cat_id'], FILTER_VALIDATE_INT);
            $_POST['pri_id'] = filter_var($_POST['pri_id'], FILTER_VALIDATE_INT);

            // Verificar si el producto ya existe en la misma categoría
            if (Productos::existeProducto($_POST['prod_nombre'], $_POST['cat_id'])) {
                http_response_code(400);
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Este producto ya existe en la categoría seleccionada'
                ]);
                return;
            }

            try {
                $producto = new Productos($_POST);
                $producto->limpiarDatos(); // Limpieza adicional
                
                // Si no hay ID, es nuevo (comprado = 0)
                if (empty($_POST['prod_id'])) {
                    $producto->comprado = 0;
                }
                
                $resultado = $producto->guardar();

                echo json_encode([
                    'resultado' => true,
                    'mensaje' => !empty($_POST['prod_id']) ? 
                        'El producto ha sido actualizado correctamente' : 
                        'El producto ha sido agregado correctamente'
                ]);
                
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Error al guardar el producto',
                    'detalle' => $e->getMessage(),
                ]);
            }
        }
    }

    public static function obtenerAPI()
    {
        try {
            $productos = Productos::consultarProductos();
            echo json_encode($productos);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al obtener los productos',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function eliminarAPI()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_var($_POST['prod_id'], FILTER_VALIDATE_INT);
            
            if (!$id) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID no válido'
                ]);
                return;
            }
            
            try {
                $producto = Productos::find($id);
                
                if (!$producto) {
                    echo json_encode([
                        'resultado' => false,
                        'mensaje' => 'Producto no encontrado'
                    ]);
                    return;
                }
                
                $resultado = $producto->eliminar();
                
                echo json_encode([
                    'resultado' => true,
                    'mensaje' => 'El producto ha sido eliminado correctamente'
                ]);
                
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Error al eliminar el producto',
                    'detalle' => $e->getMessage(),
                ]);
            }
        }
    }

    public static function marcarAPI()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_var($_POST['prod_id'], FILTER_VALIDATE_INT);
            $valor = filter_var($_POST['valor'], FILTER_VALIDATE_INT);
            
            if (!$id) {
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'ID no válido'
                ]);
                return;
            }
            
            try {
                $producto = Productos::find($id);
                
                if (!$producto) {
                    echo json_encode([
                        'resultado' => false,
                        'mensaje' => 'Producto no encontrado'
                    ]);
                    return;
                }
                
                $resultado = $producto->actualizarEstadoComprado($valor);
                
                $mensaje = $valor == 1 ? 'Producto marcado como comprado' : 'Producto desmarcado como comprado';
                
                echo json_encode([
                    'resultado' => true,
                    'mensaje' => $mensaje
                ]);
                
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Error al actualizar el estado del producto',
                    'detalle' => $e->getMessage(),
                ]);
            }
        }
    }
}