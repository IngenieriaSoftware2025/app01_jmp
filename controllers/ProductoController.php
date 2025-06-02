<?php

namespace Controllers;

use MVC\Router;
use Model\Productos;
use Exception;
use PDO;

class ProductoController
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('productos/index', []);
    }

    public static function guardarAPI()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
            return;
        }

        // Sanitizar y validar datos
        $prod_nombre = isset($_POST['prod_nombre']) ? ucwords(strtolower(trim(htmlspecialchars($_POST['prod_nombre'])))) : '';
        $prod_cantidad = isset($_POST['prod_cantidad']) ? (int)$_POST['prod_cantidad'] : 0;
        $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
        $pri_id = isset($_POST['pri_id']) ? (int)$_POST['pri_id'] : 0;
        $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0.00;
        $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

        // Validaciones básicas
        if (empty($prod_nombre)) {
            echo json_encode(['resultado' => false, 'mensaje' => 'El nombre del producto es obligatorio']);
            return;
        }
        if ($prod_cantidad < 1) {
            echo json_encode(['resultado' => false, 'mensaje' => 'La cantidad debe ser mayor a 0']);
            return;
        }
        if ($cat_id < 1) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Debe seleccionar una categoría']);
            return;
        }
        if ($pri_id < 1) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Debe seleccionar una prioridad']);
            return;
        }
        if ($precio < 0) {
            echo json_encode(['resultado' => false, 'mensaje' => 'El precio no puede ser negativo']);
            return;
        }
        if ($stock < 0) {
            echo json_encode(['resultado' => false, 'mensaje' => 'El stock no puede ser negativo']);
            return;
        }
        
        try {
            // Verificar si es una actualización
            if (isset($_POST['prod_id']) && !empty($_POST['prod_id'])) {
                $prod_id = (int)$_POST['prod_id'];

                // Buscar el producto existente
                $producto = Productos::find($prod_id);

                if ($producto) {
                    // Actualizar datos usando sincronizar
                    $producto->sincronizar([
                        'prod_nombre' => $prod_nombre,
                        'prod_cantidad' => $prod_cantidad,
                        'cat_id' => $cat_id,
                        'pri_id' => $pri_id,
                        'precio' => $precio,
                        'stock' => $stock
                    ]);

                    // Guardar cambios
                    $resultado = $producto->guardar();

                    // Enviar respuesta
                    echo json_encode([
                        'resultado' => true,
                        'mensaje' => 'El producto ha sido actualizado correctamente',
                        'id' => $prod_id
                    ]);
                    return;
                }
            }

            // Crear producto con datos validados
            $producto = new Productos([
                'prod_nombre' => $prod_nombre,
                'prod_cantidad' => $prod_cantidad,
                'cat_id' => $cat_id,
                'pri_id' => $pri_id,
                'comprado' => 0,
                'precio' => $precio,
                'stock' => $stock
            ]);

            // Guardar el producto
            $resultado = $producto->guardar();

            // Enviar respuesta
            echo json_encode([
                'resultado' => true,
                'mensaje' => 'El producto ha sido agregado correctamente'
            ]);
        } catch (Exception $e) {
            // Si ocurre un error, enviamos una respuesta de error en formato JSON
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al guardar el producto',
                'error' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerAPI()
    {
        header('Content-Type: application/json');
        
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
        header('Content-Type: application/json');

        // Verificar que se haya enviado el ID (por GET)
        if (!isset($_GET['prod_id'])) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID no proporcionado']);
            return;
        }

        $id = filter_var($_GET['prod_id'], FILTER_VALIDATE_INT);

        if (!$id) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID no válido']);
            return;
        }

        try {
            $producto = Productos::find($id);

            if (!$producto) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Producto no encontrado']);
                return;
            }

            // Verificar que no tenga stock antes de eliminar usando consulta directa
            $stockActual = self::validarStockProducto($id);
            
            if ($stockActual > 0) {
                echo json_encode([
                    'resultado' => false, 
                    'mensaje' => "No se puede eliminar el producto. Tiene stock disponible: $stockActual unidades"
                ]);
                return;
            }

            $resultado = $producto->eliminar();

            echo json_encode([
                'resultado' => true,
                'mensaje' => 'El producto ha sido eliminado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al eliminar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function marcarAPI()
    {
        header('Content-Type: application/json');
        
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

    /**
     * Validar stock de un producto usando consulta SQL directa
     */
    private static function validarStockProducto($prod_id)
    {
        try {
            $consulta = "SELECT stock FROM productos WHERE prod_id = " . (int)$prod_id;
            $resultado = Productos::consultarSQL($consulta);
            
            if (!empty($resultado)) {
                return (int)$resultado[0]->stock;
            }
            
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}