<?php

namespace Model;

class Productos extends ActiveRecord {
    public static $tabla = 'poroductos';
    public static $columnasDB = [
        'prod_id',
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
        $this->prod_nombre =$args['prod_nombre'] ?? '';
        $this->prod_cantidad = $args['prod_cantidad'] ?? '';
        $this->cat_id = $args['cat_id'] ?? 0;
        $this->pri_id = $args['pri_id'] ?? 0;
        $this->comprado = $args['comprado'] ?? 1;
    }
};