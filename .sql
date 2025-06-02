CREATE DATABASE morataya;

CREATE TABLE categorias (
    cat_id SERIAL PRIMARY KEY,
    cat_nombre VARCHAR(50) NOT NULL
);

CREATE TABLE prioridades (
    pri_id SERIAL PRIMARY KEY,
    pri_nombre VARCHAR(20) NOT NULL
);

CREATE TABLE productos (
    prod_id SERIAL PRIMARY KEY,
    prod_nombre VARCHAR(100) NOT NULL,
    prod_cantidad INTEGER DEFAULT 1,
    cat_id INTEGER NOT NULL,
    pri_id INTEGER NOT NULL,
    comprado SMALLINT DEFAULT 0,
    stock INTEGER DEFAULT 0,

    FOREIGN KEY (cat_id) REFERENCES categorias(cat_id),
    FOREIGN KEY (pri_id) REFERENCES prioridades(pri_id)
);

-- Insertar categorías iniciales
INSERT INTO categorias (cat_nombre) VALUES ('Alimentos');
INSERT INTO categorias (cat_nombre) VALUES ('Higiene');
INSERT INTO categorias (cat_nombre) VALUES ('Hogar');

-- Insertar prioridades iniciales
INSERT INTO prioridades (pri_nombre) VALUES ('Alta');
INSERT INTO prioridades (pri_nombre) VALUES('Media');
INSERT INTO prioridades (pri_nombre) VALUES('Baja');


-- Agregar campos a productos existente
ALTER TABLE productos ADD precio DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE productos ADD stock INTEGER DEFAULT 0;

-- Tabla clientes
CREATE TABLE clientes (
    cliente_id SERIAL PRIMARY KEY,
    cliente_nombre VARCHAR(100) NOT NULL
);

-- Tabla facturas
CREATE TABLE facturas (
    factura_id SERIAL PRIMARY KEY,
    cliente_id INTEGER NOT NULL,
    factura_total DECIMAL(10,2) DEFAULT 0.00
);

-- Tabla detalle_factura
CREATE TABLE detalle_factura (
    detalle_id SERIAL PRIMARY KEY,
    factura_id INTEGER NOT NULL,
    prod_id INTEGER NOT NULL,
    detalle_cantidad INTEGER NOT NULL,
    detalle_precio DECIMAL(10,2) NOT NULL,
    detalle_subtotal DECIMAL(10,2) NOT NULL
);

-- Datos iniciales
INSERT INTO clientes (cliente_nombre) VALUES ('Juan Pérez');
INSERT INTO clientes (cliente_nombre) VALUES ('María García');
INSERT INTO clientes (cliente_nombre) VALUES ('Carlos López');