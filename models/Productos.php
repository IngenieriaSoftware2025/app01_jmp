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

    public function validar() {
        if(!$this->prod_nombre) {
            self::$alertas['error'][] = 'El nombre del producto es obligatorio';
        }
        if(!$this->prod_cantidad || $this->prod_cantidad < 1) {
            self::$alertas['error'][] = 'La cantidad debe ser mayor a 0';
        }
        if(!$this->cat_id) {
            self::$alertas['error'][] = 'La categoría es obligatoria';
        }
        if(!$this->pri_id) {
            self::$alertas['error'][] = 'La prioridad es obligatoria';
        }
        return self::$alertas;
    }
}