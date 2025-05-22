<?php

namespace Model;

class Productos extends ActiveRecord {
    public static $tabla = 'productos';
    public static $columnasDB = [
        'prod_nombre',
        'prod_cantidad',
        'cat_id',
        'pri_id',
        'comprado'
    ];

    public static $idTabla = 'prod_id';
    public $prod_id;
    public $prod_nombre;
    public $prod_cantidad;
    public $cat_id;
    public $pri_id;
    public $comprado;

    public function __construct($args = []){
        $this->prod_id = $args['prod_id'] ?? null;
        $this->prod_nombre = $args['prod_nombre'] ?? '';
        $this->prod_cantidad = $args['prod_cantidad'] ?? 1;
        $this->cat_id = $args['cat_id'] ?? null;
        $this->pri_id = $args['pri_id'] ?? null;
        $this->comprado = $args['comprado'] ?? 0;
    }
    
    // Obtener productos con información de categorías y prioridades
    public static function allWithJoins() {
        $query = "SELECT p.*, c.cat_nombre, pr.pri_nombre 
                  FROM productos p 
                  JOIN categorias c ON p.cat_id = c.cat_id 
                  JOIN prioridades pr ON p.pri_id = pr.pri_id 
                  ORDER BY p.comprado ASC, c.cat_nombre ASC, 
                  CASE pr.pri_nombre 
                      WHEN 'Alta' THEN 1 
                      WHEN 'Media' THEN 2 
                      WHEN 'Baja' THEN 3 
                  END ASC";
        
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    
    // Verificar si existe producto duplicado en la misma categoría
    public static function whereDuplicate($nombre, $cat_id) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE prod_nombre = ? AND cat_id = ?";
        $resultado = self::consultarSQL($query, [$nombre, $cat_id]);
        return array_shift($resultado);
    }
    
    // Obtener productos agrupados por categoría
    public static function getGroupedByCategory() {
        $query = "SELECT p.*, c.cat_nombre, pr.pri_nombre 
                  FROM productos p 
                  JOIN categorias c ON p.cat_id = c.cat_id 
                  JOIN prioridades pr ON p.pri_id = pr.pri_id 
                  WHERE p.comprado = 0
                  ORDER BY c.cat_nombre ASC, 
                  CASE pr.pri_nombre 
                      WHEN 'Alta' THEN 1 
                      WHEN 'Media' THEN 2 
                      WHEN 'Baja' THEN 3 
                  END ASC";
        
        $productos = self::consultarSQL($query);
        
        // Agrupar por categoría
        $agrupados = [];
        foreach($productos as $producto) {
            $categoria = $producto->cat_nombre;
            if(!isset($agrupados[$categoria])) {
                $agrupados[$categoria] = [];
            }
            $agrupados[$categoria][] = $producto;
        }
        
        return $agrupados;
    }
    
    // Obtener productos comprados
    public static function getComprados() {
        $query = "SELECT p.*, c.cat_nombre, pr.pri_nombre 
                  FROM productos p 
                  JOIN categorias c ON p.cat_id = c.cat_id 
                  JOIN prioridades pr ON p.pri_id = pr.pri_id 
                  WHERE p.comprado = 1
                  ORDER BY c.cat_nombre ASC";
        
        return self::consultarSQL($query);
    }
}