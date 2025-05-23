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