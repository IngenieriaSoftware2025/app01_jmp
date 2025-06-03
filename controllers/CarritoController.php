<?php

namespace Controllers;

use MVC\Router;
use Model\Productos;
use Model\Clientes;
use Model\Facturas;
use Model\DetalleFactura;
use Exception;

class CarritoController
{
    public static function renderizarPagina(Router $router)
    {
        $clientes = Clientes::all();
        $productos = Productos::consultarProductos();

        $router->render('carrito/index', [
            'clientes' => $clientes,
            'productos' => $productos
        ]);
    }
    
    public static function obtenerDetalleFacturaAPI()
    {
        header('Content-Type: application/json');

        if (!isset($_GET['factura_id'])) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID de factura no proporcionado']);
            return;
        }
        
        $factura_id = filter_var($_GET['factura_id'], FILTER_VALIDATE_INT);

        if (!$factura_id) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID de factura no válido']);
            return;
        }

        try {
            // Obtener información de la factura
            $consultaFactura = "SELECT f.factura_id, f.cliente_id, f.factura_total
                               FROM facturas f 
                               WHERE f.factura_id = $factura_id";
            
            $facturas = Facturas::consultarSQL($consultaFactura);

            if (empty($facturas)) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Factura no encontrada']);
                return;
            }

            $factura = $facturas[0];

            // Obtener nombre del cliente por separado
            $consultaCliente = "SELECT cliente_nombre FROM clientes WHERE cliente_id = " . (int)$factura->cliente_id;
            $cliente = Clientes::consultarSQL($consultaCliente);
            
            $nombreCliente = 'Cliente no encontrado';
            if (!empty($cliente)) {
                $nombreCliente = $cliente[0]->cliente_nombre;
            }

            // Obtener detalles de la factura con información de productos
            $consultaDetalles = "SELECT d.detalle_id, d.prod_id, d.detalle_cantidad, d.detalle_precio, d.detalle_subtotal,
                                       p.prod_nombre, p.stock as stock_actual
                                FROM detalle_factura d
                                INNER JOIN productos p ON d.prod_id = p.prod_id
                                WHERE d.factura_id = $factura_id";

            $detalles = DetalleFactura::consultarSQL($consultaDetalles);

            echo json_encode([
                'resultado' => true,
                'factura' => [
                    'factura_id' => $factura->factura_id,
                    'cliente_id' => $factura->cliente_id,
                    'cliente_nombre' => $nombreCliente,
                    'factura_total' => $factura->factura_total
                ],
                'detalles' => $detalles
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al obtener detalles de la factura',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function actualizarCompraAPI()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
            return;
        }

        $factura_id = isset($_POST['factura_id']) ? (int)$_POST['factura_id'] : 0;
        $cliente_id = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;
        $productos_data = isset($_POST['productos']) ? $_POST['productos'] : '';
        $total = isset($_POST['total']) ? (float)$_POST['total'] : 0.00;

        if ($factura_id < 1) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID de factura inválido']);
            return;
        }

        if ($cliente_id < 1) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Debe seleccionar un cliente']);
            return;
        }

        if (empty($productos_data)) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Debe seleccionar al menos un producto']);
            return;
        }

        if ($total <= 0) {
            echo json_encode(['resultado' => false, 'mensaje' => 'El total debe ser mayor a 0']);
            return;
        }

        try {
            $productos = json_decode($productos_data, true);

            if (!$productos || count($productos) === 0) {
                echo json_encode(['resultado' => false, 'mensaje' => 'No hay productos válidos']);
                return;
            }

            // Usar la conexión del ActiveRecord
            require_once __DIR__ . '/../includes/database.php';
            global $db;

            // 1. Obtener productos actuales de la factura para devolver stock
            $consultaActuales = "SELECT prod_id, detalle_cantidad FROM detalle_factura WHERE factura_id = $factura_id";
            $productosActuales = DetalleFactura::consultarSQL($consultaActuales);

            // 2. Devolver stock de productos actuales
            foreach ($productosActuales as $productoActual) {
                $sqlDevolver = "UPDATE productos SET stock = stock + {$productoActual->detalle_cantidad} WHERE prod_id = {$productoActual->prod_id}";
                $db->exec($sqlDevolver);
            }

            // 3. Verificar stock disponible para los nuevos productos
            $productos_validados = [];
            foreach ($productos as $producto) {
                $consulta = "SELECT prod_id, prod_nombre, stock, precio 
                             FROM productos 
                             WHERE prod_id = " . (int)$producto['prod_id'];
                
                $resultado = Productos::consultarSQL($consulta);
                
                if (empty($resultado)) {
                    echo json_encode(['resultado' => false, 'mensaje' => 'Producto no encontrado con ID: ' . $producto['prod_id']]);
                    return;
                }
                
                $prod_data = $resultado[0];
                
                if ($prod_data->stock < $producto['cantidad']) {
                    echo json_encode(['resultado' => false, 'mensaje' => 'Stock insuficiente para: ' . $prod_data->prod_nombre . '. Stock disponible: ' . $prod_data->stock]);
                    return;
                }

                $productos_validados[] = [
                    'prod_id' => $producto['prod_id'],
                    'prod_nombre' => $prod_data->prod_nombre,
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'subtotal' => $producto['subtotal']
                ];
            }

            // 4. Eliminar detalles actuales
            $sqlEliminar = "DELETE FROM detalle_factura WHERE factura_id = $factura_id";
            $db->exec($sqlEliminar);

            // 5. Actualizar factura
            $sqlFactura = "UPDATE facturas SET cliente_id = $cliente_id, factura_total = $total WHERE factura_id = $factura_id";
            $db->exec($sqlFactura);

            // 6. Crear nuevos detalles y descontar stock
            foreach ($productos_validados as $producto) {
                // Crear detalle
                $sqlDetalle = "INSERT INTO detalle_factura (factura_id, prod_id, detalle_cantidad, detalle_precio, detalle_subtotal) 
                              VALUES ($factura_id, {$producto['prod_id']}, {$producto['cantidad']}, {$producto['precio']}, {$producto['subtotal']})";
                
                $resultadoDetalle = $db->exec($sqlDetalle);
                
                if (!$resultadoDetalle) {
                    echo json_encode(['resultado' => false, 'mensaje' => 'Error al actualizar detalle del producto: ' . $producto['prod_nombre']]);
                    return;
                }

                // Descontar stock
                $sqlStock = "UPDATE productos SET stock = stock - {$producto['cantidad']} WHERE prod_id = {$producto['prod_id']}";
                $db->exec($sqlStock);
            }

            echo json_encode([
                'resultado' => true,
                'mensaje' => 'Compra actualizada correctamente',
                'factura_id' => $factura_id
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al actualizar la compra',
                'error' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerProductosAPI()
    {
        header('Content-Type: application/json');

        try {
            $productos = Productos::consultarProductos();
            echo json_encode($productos);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al obtener los productos',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function obtenerClientesAPI()
    {
        header('Content-Type: application/json');

        try {
            $clientes = Clientes::all();
            echo json_encode($clientes);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al obtener los clientes',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function guardarCompraAPI()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
            return;
        }

        $cliente_id = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;
        $productos_data = isset($_POST['productos']) ? $_POST['productos'] : '';
        $total = isset($_POST['total']) ? (float)$_POST['total'] : 0.00;

        if ($cliente_id < 1) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Debe seleccionar un cliente']);
            return;
        }

        if (empty($productos_data)) {
            echo json_encode(['resultado' => false, 'mensaje' => 'Debe seleccionar al menos un producto']);
            return;
        }

        if ($total <= 0) {
            echo json_encode(['resultado' => false, 'mensaje' => 'El total debe ser mayor a 0']);
            return;
        }

        try {
            $productos = json_decode($productos_data, true);

            if (!$productos || count($productos) === 0) {
                echo json_encode(['resultado' => false, 'mensaje' => 'No hay productos válidos']);
                return;
            }

            // Verificar stock disponible y obtener nombres de productos
            $productos_validados = [];
            foreach ($productos as $producto) {
                $consulta = "SELECT prod_id, prod_nombre, stock, precio 
                             FROM productos 
                             WHERE prod_id = " . (int)$producto['prod_id'];
                
                $resultado = Productos::consultarSQL($consulta);
                
                if (empty($resultado)) {
                    echo json_encode(['resultado' => false, 'mensaje' => 'Producto no encontrado con ID: ' . $producto['prod_id']]);
                    return;
                }
                
                $prod_data = $resultado[0];
                
                if ($prod_data->stock < $producto['cantidad']) {
                    echo json_encode(['resultado' => false, 'mensaje' => 'Stock insuficiente para: ' . $prod_data->prod_nombre . '. Stock disponible: ' . $prod_data->stock]);
                    return;
                }

                // Agregar el nombre del producto al array
                $productos_validados[] = [
                    'prod_id' => $producto['prod_id'],
                    'prod_nombre' => $prod_data->prod_nombre,
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'subtotal' => $producto['subtotal']
                ];
            }

            // Crear factura manualmente con SQL
            $sql_factura = "INSERT INTO facturas (cliente_id, factura_total) VALUES ($cliente_id, $total)";
            
            // Usar la conexión del ActiveRecord
            require_once __DIR__ . '/../includes/database.php';
            global $db;
            
            $resultado_factura = $db->exec($sql_factura);

            if (!$resultado_factura) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al crear la factura']);
                return;
            }

            // Obtener el último ID insertado
            $factura_id = $db->lastInsertId();

            if (!$factura_id) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Error al obtener ID de factura']);
                return;
            }

            // Crear detalles y actualizar stock
            foreach ($productos_validados as $producto) {
                // Crear detalle con SQL directo
                $sql_detalle = "INSERT INTO detalle_factura (factura_id, prod_id, detalle_cantidad, detalle_precio, detalle_subtotal) 
                               VALUES ($factura_id, {$producto['prod_id']}, {$producto['cantidad']}, {$producto['precio']}, {$producto['subtotal']})";
                
                $resultado_detalle = $db->exec($sql_detalle);
                
                if (!$resultado_detalle) {
                    echo json_encode(['resultado' => false, 'mensaje' => 'Error al guardar detalle del producto: ' . $producto['prod_nombre']]);
                    return;
                }

                // Actualizar stock con SQL directo
                $sql_stock = "UPDATE productos SET stock = stock - {$producto['cantidad']} WHERE prod_id = {$producto['prod_id']}";
                $db->exec($sql_stock);
            }

            echo json_encode([
                'resultado' => true,
                'mensaje' => 'Compra guardada correctamente',
                'factura_id' => $factura_id
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al guardar la compra',
                'error' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerFacturasAPI()
    {
        header('Content-Type: application/json');

        try {
            // Consulta simple primero para debug
            $consulta = "SELECT f.factura_id, f.cliente_id, f.factura_total 
                        FROM facturas f 
                        ORDER BY f.factura_id DESC";

            $facturas = Facturas::consultarSQL($consulta);

            // Crear nuevo array con los datos completos
            $facturasCompletas = [];
            
            foreach ($facturas as $factura) {
                $consultaCliente = "SELECT cliente_nombre FROM clientes WHERE cliente_id = " . (int)$factura->cliente_id;
                $cliente = Clientes::consultarSQL($consultaCliente);
                
                $nombreCliente = 'Cliente no encontrado';
                if (!empty($cliente)) {
                    $nombreCliente = $cliente[0]->cliente_nombre;
                }

                // Crear nuevo objeto con todos los datos
                $facturasCompletas[] = [
                    'factura_id' => $factura->factura_id,
                    'factura_total' => $factura->factura_total,
                    'cliente_nombre' => $nombreCliente
                ];
            }

            echo json_encode($facturasCompletas);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al obtener las facturas',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}