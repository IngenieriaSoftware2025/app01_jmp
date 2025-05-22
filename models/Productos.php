<?php

namespace Model;

class Productos extends ActiveRecord {
    protected static $tabla = 'productos';
    protected static $columnasDB = [
        'prod_id',
        'prod_nombre',
        'prod_cantidad',
        'cat_id',
        'pri_id',
        'comprado'
    ];

    public $prod_id;
    public $prod_nombre;
    public $prod_cantidad;
    public $cat_id;
    public $pri_id;
    public $comprado;

    public function __construct($args = []) {
        $this->prod_id = $args['prod_id'] ?? null;
        $this->prod_nombre = $args['prod_nombre'] ?? '';
        $this->prod_cantidad = $args['prod_cantidad'] ?? 1;
        $this->cat_id = $args['cat_id'] ?? null;
        $this->pri_id = $args['pri_id'] ?? null;
        $this->comprado = $args['comprado'] ?? 0;
    }

    // Método para obtener productos con JOIN a categorías y prioridades
    public static function consultarProductos() {
        $query = "SELECT 
                    p.*,
                    c.cat_nombre,
                    pr.pri_nombre
                  FROM productos p
                  INNER JOIN categorias c ON p.cat_id = c.cat_id
                  INNER JOIN prioridades pr ON p.pri_id = pr.pri_id";

        $resultado = self::$db->query($query);

        $productos = [];
        while ($registro = $resultado->fetch_assoc()) {
            $productos[] = $registro;
        }

        return $productos;
    }


    public static function whereMultiple($conditions) {
        $query = "SELECT * FROM productos WHERE ";
        $params = [];
        $clauses = [];
        foreach ($conditions as $field => $value) {
            $clauses[] = "$field = ?";
            $params[] = $value;
        }
        $query .= implode(' AND ', $clauses) . " LIMIT 1";
        $stmt = self::$db->prepare($query);
        $stmt->execute($params);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, self::class);
        return $stmt->fetch();
    }
}
