CREATE DATABASE morataya;

-- Crear tabla de categorías
CREATE TABLE categorias (
    cat_id SERIAL PRIMARY KEY,
    cat_nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Crear tabla de prioridades
CREATE TABLE prioridades (
    pri_id SERIAL PRIMARY KEY,
    pri_nombre VARCHAR(20) NOT NULL UNIQUE
);

-- Crear tabla de productos
CREATE TABLE productos (
    prod_id SERIAL PRIMARY KEY,
    prod_nombre VARCHAR(100) NOT NULL,
    prod_cantidad INTEGER DEFAULT 1 CHECK (prod_cantidad > 0),
    cat_id INTEGER NOT NULL,
    pri_id INTEGER NOT NULL,
    comprado SMALLINT DEFAULT 0, -- 0 = no, 1 = sí
    UNIQUE (prod_nombre, cat_id),
    FOREIGN KEY (cat_id) REFERENCES categorias(cat_id),
    FOREIGN KEY (pri_id) REFERENCES prioridades(pri_id)
);
