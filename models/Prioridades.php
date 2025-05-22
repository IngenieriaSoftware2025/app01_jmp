<?php

namespace Model;

class Prioridades extends ActiveRecord {
    public static $tabla = 'prioridades';
    public static $columnasDB = [
        'pri_id',
        'pri_nombre'
    ];

    public static $idTabla = 'pri_id';
    public $pri_id;
    public $pri_nombre;

    public function __construct($args = []){
        $this->pri_id = $args['pri_id'] ?? null;
        $this->pri_nombre = $args['pri_nombre'] ?? '';
    }
    
    /**
     * Validar que la prioridad sea válida
     */
    public function validar() {
        $errores = [];
        
        if(!$this->pri_nombre || trim($this->pri_nombre) === '') {
            $errores[] = 'El nombre de la prioridad es obligatorio';
        }
        
        return $errores;
    }
    
    /**
     * Obtener todas las prioridades ordenadas por nombre
     */
    public static function obtenerTodas() {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY pri_id ASC";
        return self::consultarSQL($query);
    }
}