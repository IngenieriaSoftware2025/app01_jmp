<?php

namespace Controllers;

use MVC\Router;
use Model\Productos;
use Model\Categorias;

class ProductoController {



    public static function renderizarPagina(Router $router) {
        $router->render('productos/index', []);
    }

    // public static function guardarAPI() {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $producto = new Productos($_POST);

    //         // Validaciones mínimas
    //         if (
    //             !isset($producto->prod_nombre) || trim($producto->prod_nombre) === '' ||
    //             !isset($producto->prod_cantidad) || $producto->prod_cantidad < 1 ||
    //             !$producto->cat_id || !isset($producto->prod_prioridad) || trim($producto->prod_prioridad) === ''
    //         ) {
    //             echo json_encode(['resultado' => false, 'mensaje' => 'Todos los campos son obligatorios']);
    //             return;
    //         }

    //         // Validar duplicado si es nuevo
    //         if (empty($producto->prod_id)) {
    //             $existe = Productos::whereMultiple([
    //                 'prod_nombre' => $producto->prod_nombre,
    //                 'cat_id' => $producto->cat_id
    //             ]);

    //             if ($existe) {
    //                 echo json_encode(['resultado' => false, 'mensaje' => 'Ya existe un producto con ese nombre en esta categoría']);
    //                 return;
    //             }
    //         }

    //         $resultado = $producto->guardar();

    //         echo json_encode([
    //             'resultado' => $resultado,
    //             'mensaje' => $resultado ? 'Producto guardado correctamente' : 'Error al guardar producto'
    //         ]);
    //     }
    // }

    // public static function obtenerAPI() {
    //     $productos = Productos::consultarProductos();
    //     echo json_encode($productos);
    // }

    // public static function eliminarAPI() {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $id = $_POST['prod_id'] ?? null;
    //         if ($id) {
    //             $producto = Productos::find($id);

    //             if (!$producto) {
    //                 echo json_encode(['resultado' => false, 'mensaje' => 'Producto no encontrado']);
    //                 return;
    //             }

    //             $resultado = $producto->eliminar();
    //             echo json_encode([
    //                 'resultado' => $resultado,
    //                 'mensaje' => $resultado ? 'Producto eliminado correctamente' : 'Error al eliminar producto'
    //             ]);
    //         } else {
    //             echo json_encode(['resultado' => false, 'mensaje' => 'ID no válido']);
    //         }
    //     }
    // }

    // public static function marcarAPI() {
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $id = $_POST['prod_id'] ?? null;
    //         $valor = $_POST['valor'] ?? null;

    //         if ($id !== null && $valor !== null) {
    //             $producto = Productos::find($id);

    //             if (!$producto) {
    //                 echo json_encode(['resultado' => false, 'mensaje' => 'Producto no encontrado']);
    //                 return;
    //             }

    //             $producto->comprado = $valor;
    //             $resultado = $producto->guardar();

    //             echo json_encode([
    //                 'resultado' => $resultado,
    //                 'mensaje' => $resultado ? 'Producto actualizado correctamente' : 'Error al actualizar producto'
    //             ]);
    //         } else {
    //             echo json_encode(['resultado' => false, 'mensaje' => 'Datos incompletos']);
    //         }
    //     }
    // }
}
