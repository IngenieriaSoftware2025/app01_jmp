<?php

namespace Controllers;

use MVC\Router;
use Model\Clientes;
use Exception;

class ClienteController {

    public static function renderizarPagina(Router $router) {
        $clientes = Clientes::all();
        $router->render('clientes/index', [
            'clientes' => $clientes
        ]);
    }

    public static function guardarAPI() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
            return;
        }
        
        $cliente_nombre = isset($_POST['cliente_nombre']) ? ucwords(strtolower(trim(htmlspecialchars($_POST['cliente_nombre'])))) : '';
        
        if (empty($cliente_nombre)) {
            echo json_encode(['resultado' => false, 'mensaje' => 'El nombre del cliente es obligatorio']);
            return;
        }
        
        try {
            if (isset($_POST['cliente_id']) && !empty($_POST['cliente_id'])) {
                $cliente_id = (int)$_POST['cliente_id'];
                
                $cliente = Clientes::find($cliente_id);
                
                if ($cliente) {
                    $cliente->sincronizar([
                        'cliente_nombre' => $cliente_nombre
                    ]);
                    
                    $resultado = $cliente->guardar();
                    
                    echo json_encode([
                        'resultado' => true,
                        'mensaje' => 'El cliente ha sido actualizado correctamente',
                        'id' => $cliente_id
                    ]);
                    return;
                }
            }
            
            $existente = Clientes::where('cliente_nombre', $cliente_nombre);
            if (!empty($existente)) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Este cliente ya existe']);
                return;
            }
            
            $cliente = new Clientes([
                'cliente_nombre' => $cliente_nombre
            ]);
            
            $resultado = $cliente->guardar();
            
            echo json_encode([
                'resultado' => true,
                'mensaje' => 'El cliente ha sido agregado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al guardar el cliente',
                'error' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerAPI() {
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

    public static function eliminarAPI() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['resultado' => false, 'mensaje' => 'Método no permitido']);
            return;
        }
        
        if (!isset($_POST['cliente_id'])) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID no proporcionado']);
            return;
        }
        
        $id = filter_var($_POST['cliente_id'], FILTER_VALIDATE_INT);
        
        if (!$id) {
            echo json_encode(['resultado' => false, 'mensaje' => 'ID no válido']);
            return;
        }
        
        try {
            $cliente = Clientes::find($id);
            
            if (!$cliente) {
                echo json_encode(['resultado' => false, 'mensaje' => 'Cliente no encontrado']);
                return;
            }
            
            $resultado = $cliente->eliminar();
            
            echo json_encode([
                'resultado' => true,
                'mensaje' => 'El cliente ha sido eliminado correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'resultado' => false,
                'mensaje' => 'Error al eliminar el cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}