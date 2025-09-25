DROP DATABASE IF EXISTS tienda_pc;
CREATE DATABASE tienda_pc CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE tienda_pc;

CREATE TABLE Usuario (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    NombreUsuario VARCHAR(50) NOT NULL UNIQUE,
    Tipo ENUM('Administrador','Cliente') NOT NULL,
    Contrasena VARCHAR(50) NOT NULL
);

INSERT INTO Usuario (NombreUsuario, Tipo, Contrasena) VALUES
('admin','Administrador','admin123'),
('cliente1','Cliente','cliente1'),
('cliente2','Cliente','cliente2');

CREATE TABLE Productos (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Tipo ENUM('RAM','SSD','Fuente','Gabinete','Procesador') NOT NULL,
    Descripcion TEXT,
    CantidadDisponible INT NOT NULL,
    Precio DECIMAL(10,2) NOT NULL
);

INSERT INTO Productos (Nombre, Tipo, Descripcion, CantidadDisponible, Precio) VALUES
('RAM 8GB', 'RAM', 'Memoria DDR4 8GB', 10, 40.00),
('RAM 16GB', 'RAM', 'Memoria DDR4 16GB', 10, 70.00),
('RAM 32GB', 'RAM', 'Memoria DDR4 32GB', 5, 120.00),

('SSD 256GB', 'SSD', 'Unidad SSD 256GB', 15, 50.00),
('SSD 512GB', 'SSD', 'Unidad SSD 512GB', 10, 90.00),
('SSD 1TB', 'SSD', 'Unidad SSD 1TB', 5, 150.00),

('Fuente 500W', 'Fuente', 'Fuente ATX 500W', 10, 40.00),
('Fuente 650W', 'Fuente', 'Fuente ATX 650W', 7, 60.00),
('Fuente 750W', 'Fuente', 'Fuente ATX 750W', 5, 80.00),

('Gabinete Gamer', 'Gabinete', 'Gabinete con RGB', 8, 70.00),
('Gabinete Slim', 'Gabinete', 'Gabinete compacto', 10, 50.00),
('Gabinete ATX', 'Gabinete', 'Gabinete ATX clásico', 6, 60.00),

('Procesador i5', 'Procesador', 'Intel Core i5 12va', 10, 200.00),
('Procesador i7', 'Procesador', 'Intel Core i7 12va', 7, 300.00),
('Procesador Ryzen 5', 'Procesador', 'AMD Ryzen 5 5600X', 5, 250.00);

CREATE TABLE Computadora (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    ProductoRAM INT NOT NULL,
    ProductoSSD INT NOT NULL,
    ProductoFuente INT NOT NULL,
    ProductoGabinete INT NOT NULL,
    ProductoProcesador INT NOT NULL,
    Precio DECIMAL(10,2) NOT NULL,
    Estado ENUM('En venta','Vendido') DEFAULT 'En venta',
    FOREIGN KEY (ProductoRAM) REFERENCES Productos(ID),
    FOREIGN KEY (ProductoSSD) REFERENCES Productos(ID),
    FOREIGN KEY (ProductoFuente) REFERENCES Productos(ID),
    FOREIGN KEY (ProductoGabinete) REFERENCES Productos(ID),
    FOREIGN KEY (ProductoProcesador) REFERENCES Productos(ID)
);

INSERT INTO Computadora (Nombre, ProductoRAM, ProductoSSD, ProductoFuente, ProductoGabinete, ProductoProcesador, Precio, Estado) VALUES
('PC Gamer Basica', 1, 4, 7, 10, 13, 450.00, 'En venta'),
('PC Gamer Media', 2, 5, 8, 11, 14, 600.00, 'En venta'),
('PC Gamer Avanzada', 3, 6, 9, 12, 15, 900.00, 'En venta'),
('PC Personalizada Cliente1', 1, 4, 7, 10, 13, 450.00, 'Vendido');

CREATE TABLE Venta (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    IDCliente INT NOT NULL,
    IDCompu INT NOT NULL,
    Precio DECIMAL(10,2) NOT NULL,
    Estado ENUM('En Proceso','Ensamblado','Enviado','Finalizado') NOT NULL DEFAULT 'En Proceso',
    FOREIGN KEY (IDCliente) REFERENCES Usuario(ID),
    FOREIGN KEY (IDCompu) REFERENCES Computadora(ID)
);

