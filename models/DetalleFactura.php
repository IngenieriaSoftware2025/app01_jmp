<?php

namespace Model;

class DetalleFactura extends ActiveRecord {
    
    protected static $tabla = 'detalle_factura';
    protected static $columnasDB = ['detalle_id', 'factura_id', 'prod_id', 'detalle_cantidad', 'detalle_precio', 'detalle_subtotal'];
    
    public static $idTabla = 'detalle_id';

    public $detalle_id;
    public $factura_id;
    public $prod_id;
    public $detalle_cantidad;
    public $detalle_precio;
    public $detalle_subtotal;

    public function __construct($args = [])
    {
        $this->detalle_id = $args['detalle_id'] ?? null;
        $this->factura_id = $args['factura_id'] ?? null;
        $this->prod_id = $args['prod_id'] ?? null;
        $this->detalle_cantidad = $args['detalle_cantidad'] ?? 1;
        $this->detalle_precio = $args['detalle_precio'] ?? 0.00;
        $this->detalle_subtotal = $args['detalle_subtotal'] ?? 0.00;
    }
}