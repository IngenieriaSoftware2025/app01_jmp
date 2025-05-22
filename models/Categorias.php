<?php

namespace Model;

class Categorias extends ActiveRecord {
    public static $tabla = 'categorias';
    public static $columnasDB = [
        'cat_nombre'

    ];

    public static $idTabla = 'cat_id';
    public $cat_id;
    public $cat_nombre;

    public function __construct($args = []){
        $this->cat_id = $args['cat_id'] ?? null;
        $this->cat_nombre = $args['cat_nombre'] ?? '';
    }
}