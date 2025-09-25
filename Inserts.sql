USE tienda_computadoras;

INSERT INTO Usuario (NombreUsuario, Tipo, Contrasena) VALUES
('admin','Administrador','admin123'),
('cliente1','Cliente','cliente1'),
('cliente2','Cliente','cliente2');

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

INSERT INTO Computadora (Nombre, ProductoRAM, ProductoSSD, ProductoFuente, ProductoGabinete, ProductoProcesador, Precio, Estado) VALUES
('PC Gamer Básica', 1, 4, 7, 10, 13, 400.00, 'En venta');