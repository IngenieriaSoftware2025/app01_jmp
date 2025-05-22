<?php

namespace Model;

class Productos extends ActiveRecord {
    
    // CONFIGURACIÓN DE LA TABLA
    protected static $tabla = 'productos';
    protected static $columnasDB = ['prod_id', 'prod_nombre', 'prod_cantidad', 'cat_id', 'pri_id', 'comprado'];
    protected static $idTabla = 'prod_id'; // CLAVE: Definir la clave primaria

    // PROPIEDADES DEL MODELO
    public $prod_id;
    public $prod_nombre;
    public $prod_cantidad;
    public $cat_id;
    public $pri_id;
    public $comprado;

    /**
     * CONSTRUCTOR - INICIALIZAR PROPIEDADES
     */
    public function __construct($args = []) {
        $this->prod_id = $args['prod_id'] ?? null;
        $this->prod_nombre = $args['prod_nombre'] ?? '';
        $this->prod_cantidad = $args['prod_cantidad'] ?? 1;
        $this->cat_id = $args['cat_id'] ?? '';
        $this->pri_id = $args['pri_id'] ?? '';
        $this->comprado = $args['comprado'] ?? 0;
    }
    

    /**
     * CONSULTAR PRODUCTOS CON INFORMACIÓN DE CATEGORÍAS Y PRIORIDADES
     * ADAPTADO PARA INFORMIX
     */
    public static function consultarProductos() {
        // Sintaxis específica para Informix - usando aliases más explícitos
        $query = "SELECT 
                    p.prod_id,
                    p.prod_nombre,
                    p.prod_cantidad,
                    p.cat_id,
                    p.pri_id,
                    p.comprado,
                    c.cat_nombre,
                    pr.pri_nombre
                  FROM productos p
                  INNER JOIN categorias c ON p.cat_id = c.cat_id
                  INNER JOIN prioridades pr ON p.pri_id = pr.pri_id
                  ORDER BY p.comprado ASC, pr.pri_id ASC, p.prod_nombre ASC";

        // Usar el método fetchArray del ActiveRecord
        return self::fetchArray($query);
    }

    /**
     * BUSCAR PRODUCTO POR MÚLTIPLES CONDICIONES
     * ADAPTADO PARA INFORMIX - Manejo específico de comillas y sintaxis
     */
    public static function whereMultiple($conditions) {
        
        // Construir la consulta usando PDO compatible con Informix
        $query = "SELECT * FROM " . static::$tabla . " WHERE ";
        $clauses = [];
        
        foreach ($conditions as $field => $value) {
            // Para Informix, asegurar el manejo correcto de strings
            if (is_string($value)) {
                $clauses[] = "$field = " . self::$db->quote(trim($value));
            } else {
                $clauses[] = "$field = " . self::$db->quote($value);
            }
        }
        
        $query .= implode(' AND ', $clauses);
        
        // En Informix, FIRST es equivalente a LIMIT en otros SGBD
        $query .= " ORDER BY prod_id FIRST 1";
        
        // Usar consultarSQL del ActiveRecord
        $resultado = self::consultarSQL($query);
        
        // Si hay resultados, devolver el primero
        return !empty($resultado) ? $resultado[0] : false;
    }

    /**
     * MÉTODO ALTERNATIVO PARA INFORMIX - Buscar duplicados
     * Usando EXISTS que es más eficiente en Informix
     */
    public static function existeProducto($nombre, $categoria_id) {
        $query = "SELECT COUNT(*) as total 
                  FROM " . static::$tabla . " 
                  WHERE TRIM(prod_nombre) = " . self::$db->quote(trim($nombre)) . "
                  AND cat_id = " . self::$db->quote($categoria_id);
        
        $resultado = self::fetchFirst($query);
        return $resultado && $resultado['total'] > 0;
    }

    /**
     * OBTENER PRODUCTOS POR ESTADO - Método específico para la aplicación
     * Separar comprados de no comprados
     */
    public static function obtenerPorEstado($comprado = 0) {
        $query = "SELECT 
                    p.prod_id,
                    p.prod_nombre,
                    p.prod_cantidad,
                    p.cat_id,
                    p.pri_id,
                    p.comprado,
                    c.cat_nombre,
                    pr.pri_nombre
                  FROM productos p
                  INNER JOIN categorias c ON p.cat_id = c.cat_id
                  INNER JOIN prioridades pr ON p.pri_id = pr.pri_id
                  WHERE p.comprado = " . self::$db->quote($comprado) . "
                  ORDER BY pr.pri_id ASC, p.prod_nombre ASC";

        return self::fetchArray($query);
    }

    /**
     * MÉTODO ESPECÍFICO PARA INFORMIX - Actualizar solo el campo comprado
     * Más eficiente que actualizar todo el registro
     */
    public function actualizarEstadoComprado($nuevoEstado) {
        $query = "UPDATE " . static::$tabla . " 
                  SET comprado = " . self::$db->quote($nuevoEstado) . "
                  WHERE prod_id = " . self::$db->quote($this->prod_id);
        
        $resultado = self::$db->exec($query);
        
        // Actualizar la propiedad local si la BD se actualizó
        if ($resultado > 0) {
            $this->comprado = $nuevoEstado;
        }
        
        return $resultado > 0;
    }

    

    /**
     * VALIDACIONES PERSONALIZADAS
     */
    public function validar() {
        $errores = [];

        // Validar nombre (trim para espacios en Informix)
        if (!$this->prod_nombre || trim($this->prod_nombre) === '') {
            $errores[] = 'El nombre del producto es obligatorio';
        }

        // Validar cantidad
        if (!is_numeric($this->prod_cantidad) || $this->prod_cantidad < 1) {
            $errores[] = 'La cantidad debe ser mayor a 0';
        }

        // Validar categoría
        if (!$this->cat_id || !is_numeric($this->cat_id)) {
            $errores[] = 'La categoría es obligatoria';
        }

        // Validar prioridad
        if (!$this->pri_id || !is_numeric($this->pri_id)) {
            $errores[] = 'La prioridad es obligatoria';
        }

        return $errores;
    }

    /**
     * MÉTODO PARA LIMPIAR DATOS ANTES DE GUARDAR - Específico para Informix
     * Informix es sensible a espacios y caracteres especiales
     */
    public function limpiarDatos() {
        $this->prod_nombre = trim($this->prod_nombre);
        $this->prod_cantidad = (int)$this->prod_cantidad;
        $this->cat_id = (int)$this->cat_id;
        $this->pri_id = (int)$this->pri_id;
        $this->comprado = (int)$this->comprado;
    }
}