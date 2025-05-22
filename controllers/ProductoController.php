<?php

namespace Controllers;

use MVC\Router;
use Model\Productos;
//use Model\Categorias;
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
    
    // Registrar datos recibidos
    error_log("POST data: " . print_r($_POST, true));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validaciones...
        
        try {
            $producto = new Productos($_POST);
            $producto->limpiarDatos();
            
            // Registrar datos del producto antes de guardar
            error_log("Producto antes de guardar: " . print_r($producto, true));
            
            // Si no hay ID, es nuevo (comprado = 0)
            if (empty($_POST['prod_id'])) {
                $producto->comprado = 0;
            }
            
            // Guardar y registrar resultado
            $resultado = $producto->guardar();
            error_log("Resultado de guardar: " . print_r($resultado, true));
            
            // Registrar el producto después de guardar
            error_log("Producto después de guardar: " . print_r($producto, true));
            
            // Hacer una consulta independiente para verificar si se guardó
            $sql = "SELECT * FROM productos WHERE prod_nombre = '" . 
                    addslashes($producto->prod_nombre) . "' ORDER BY prod_id DESC LIMIT 1";
            $resultado_consulta = self::fetchFirst($sql);
            error_log("Consulta de verificación: " . print_r($resultado_consulta, true));
            
            echo json_encode([
                'resultado' => true,
                'mensaje' => !empty($_POST['prod_id']) ?
                    'El producto ha sido actualizado correctamente' :
                    'El producto ha sido agregado correctamente',
                'id' => $producto->prod_id,
                'debug' => [
                    'post' => $_POST,
                    'producto' => $producto,
                    'resultado_guardar' => $resultado,
                    'consulta_verificacion' => $resultado_consulta
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Excepción al guardar: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(400);
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al guardar el producto',
                'detalle' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
                // Obtenemos el producto
                $producto = Productos::find($id);

                if (!$producto) {
                    echo json_encode([
                        'resultado' => false,
                        'mensaje' => 'Producto no encontrado'
                    ]);
                    return;
                }

                // Actualizamos el estado usando sincronizar
                $producto->sincronizar([
                    'comprado' => $valor
                ]);
                $resultado = $producto->guardar();

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
