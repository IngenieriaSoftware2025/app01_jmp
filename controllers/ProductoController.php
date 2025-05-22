<?php

namespace Controllers;

use MVC\Router;
use Model\Productos;
use Model\Categorias;
use Exception;

class ProductoController {

    /**
     * RENDERIZAR PÁGINA PRINCIPAL DE PRODUCTOS
     */
    public static function renderizarPagina(Router $router) {
        $router->render('productos/index', []);
    }

    /**
     * GUARDAR O ACTUALIZAR PRODUCTO VIA API - ADAPTADO PARA INFORMIX
     */
    public static function guardarAPI() {
        // Solo procesar si es POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Crear instancia del producto con los datos recibidos
            $producto = new Productos($_POST);
            
            // LIMPIAR DATOS PARA INFORMIX
            $producto->limpiarDatos();

            // VALIDACIONES BÁSICAS
            if (
                !isset($producto->prod_nombre) || trim($producto->prod_nombre) === '' ||
                !isset($producto->prod_cantidad) || $producto->prod_cantidad < 1 ||
                !$producto->cat_id || 
                !isset($producto->pri_id) || trim($producto->pri_id) === ''
            ) {
                echo json_encode([
                    'resultado' => false, 
                    'mensaje' => 'Todos los campos son obligatorios'
                ]);
                return;
            }

            // VALIDAR DUPLICADO SOLO SI ES NUEVO PRODUCTO - Método optimizado para Informix
            if (empty($producto->prod_id)) {
                $existe = Productos::existeProducto($producto->prod_nombre, $producto->cat_id);

                if ($existe) {
                    echo json_encode([
                        'resultado' => false, 
                        'mensaje' => 'Ya existe un producto con ese nombre en esta categoría'
                    ]);
                    return;
                }
            }

            try {
                // INTENTAR GUARDAR EL PRODUCTO
                $resultado = $producto->guardar();

                // MANEJAR RESPUESTA DEL ACTIVERECORD (que devuelve array)
                $exito = false;
                if (is_array($resultado)) {
                    $exito = $resultado['resultado'] > 0;
                } else {
                    $exito = $resultado;
                }

                // RESPONDER CON RESULTADO
                echo json_encode([
                    'resultado' => $exito,
                    'mensaje' => $exito ? 'Producto guardado correctamente' : 'Error al guardar producto'
                ]);

            } catch (Exception $e) {
                // Manejo de errores específicos de Informix
                echo json_encode([
                    'resultado' => false,
                    'mensaje' => 'Error de base de datos: ' . $e->getMessage()
                ]);
            }
        }
    }

    /**
     * OBTENER TODOS LOS PRODUCTOS VIA API
     */
    public static function obtenerAPI() {
        $productos = Productos::consultarProductos();
        echo json_encode($productos);
    }

    /**
     * ELIMINAR PRODUCTO VIA API
     */
    public static function eliminarAPI() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Obtener ID del producto
            $id = $_POST['prod_id'] ?? null;
            
            if ($id) {
                // Buscar el producto usando el método find del ActiveRecord
                $producto = Productos::find($id);

                if (!$producto) {
                    echo json_encode([
                        'resultado' => false, 
                        'mensaje' => 'Producto no encontrado'
                    ]);
                    return;
                }

                // Eliminar producto
                $resultado = $producto->eliminar();
                echo json_encode([
                    'resultado' => $resultado > 0,
                    'mensaje' => $resultado > 0 ? 'Producto eliminado correctamente' : 'Error al eliminar producto'
                ]);
                
            } else {
                echo json_encode([
                    'resultado' => false, 
                    'mensaje' => 'ID no válido'
                ]);
            }
        }
    }

    /**
     * MARCAR PRODUCTO COMO COMPRADO/NO COMPRADO VIA API - OPTIMIZADO PARA INFORMIX
     */
    public static function marcarAPI() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Obtener datos
            $id = $_POST['prod_id'] ?? null;
            $valor = $_POST['valor'] ?? null;

            if ($id !== null && $valor !== null) {
                
                // Buscar producto
                $producto = Productos::find($id);

                if (!$producto) {
                    echo json_encode([
                        'resultado' => false, 
                        'mensaje' => 'Producto no encontrado'
                    ]);
                    return;
                }

                try {
                    // USAR MÉTODO OPTIMIZADO PARA INFORMIX
                    $resultado = $producto->actualizarEstadoComprado($nuevoEstado);

                    echo json_encode([
                        'resultado' => $resultado,
                        'mensaje' => $resultado ? 'Producto actualizado correctamente' : 'Error al actualizar producto'
                    ]);

                } catch (Exception $e) {
                    echo json_encode([
                        'resultado' => false,
                        'mensaje' => 'Error de base de datos: ' . $e->getMessage()
                    ]);
                }
                
            } else {
                echo json_encode([
                    'resultado' => false, 
                    'mensaje' => 'Datos incompletos'
                ]);
            }
        }
    }
}