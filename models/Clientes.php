<?php

namespace Model;

class Clientes extends ActiveRecord {
    
    protected static $tabla = 'clientes';
    protected static $columnasDB = [
        'cliente_nombre'];


    public static $idTabla = 'cliente_id';

    public $cliente_id;
    public $cliente_nombre;

    public function __construct($args = [])
    {
        $this->cliente_id = $args['cliente_id'] ?? null;
        $this->cliente_nombre = $args['cliente_nombre'] ?? '';
    }
}