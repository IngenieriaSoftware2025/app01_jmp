-- Crear base de datos (si no la tiene, aplique este script .sql

CREATE DATABASE morataya;

-- Crear tabla de categorías
CREATE TABLE categorias (
    cat_id SERIAL PRIMARY KEY,
    cat_nombre VARCHAR(50) NOT NULL
);

-- Crear tabla de prioridades
CREATE TABLE prioridades (
    pri_id SERIAL PRIMARY KEY,
    pri_nombre VARCHAR(20) NOT NULL
);

-- Crear tabla de productos
CREATE TABLE productos (
    prod_id SERIAL PRIMARY KEY,
    prod_nombre VARCHAR(100) NOT NULL,
    prod_cantidad INTEGER DEFAULT 1,
    cat_id INTEGER NOT NULL,
    pri_id INTEGER NOT NULL,
    comprado SMALLINT DEFAULT 0,
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

-- Insertar algunos productos de ejemplo
INSERT INTO productos (prod_nombre, prod_cantidad, cat_id, pri_id, comprado) VALUES ('Papel higiénico', 3, 2, 1, 0);
INSERT INTO productos (prod_nombre, prod_cantidad, cat_id, pri_id, comprado) VALUES ('Arroz', 2, 1, 2, 0);
INSERT INTO productos (prod_nombre, prod_cantidad, cat_id, pri_id, comprado) VALUES ('Jabón de manos', 1, 2, 3, 0);
INSERT INTO productos (prod_nombre, prod_cantidad, cat_id, pri_id, comprado) VALUES ('Detergente', 1, 3, 2, 0);