<?php

namespace Model;

class Categorias extends ActiveRecord {
    public static $tabla = 'categorias';
    public static $columnasDB = [
        'cat_id',
        'cat_nombre'
    ];

    public static $idTabla = 'cat_id';
    public $cat_id;
    public $cat_nombre;

    public function __construct($args = []){
        $this->cat_id = $args['cat_id'] ?? null;
        $this->cat_nombre = $args['cat_nombre'] ?? '';
    }
    
    /**
     * Validar que la categoría sea válida
     */
    public function validar() {
        $errores = [];
        
        if(!$this->cat_nombre || trim($this->cat_nombre) === '') {
            $errores[] = 'El nombre de la categoría es obligatorio';
        }
        
        return $errores;
    }
}