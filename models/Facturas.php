<?php

namespace Model;

class Facturas extends ActiveRecord {
    
    protected static $tabla = 'facturas';
    protected static $columnasDB = ['factura_id', 'cliente_id', 'factura_total'];
    public static $idTabla = 'factura_id';

    public $factura_id;
    public $cliente_id;
    public $factura_total;

    public function __construct($args = [])
    {
        $this->factura_id = $args['factura_id'] ?? null;
        $this->cliente_id = $args['cliente_id'] ?? null;
        $this->factura_total = $args['factura_total'] ?? 0.00;
    }
}