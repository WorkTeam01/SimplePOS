-- SimplePOS - Datos de demo
-- Generado: 2026-05-18
-- Importar DESPUES de simplepos.sql

USE `simplepos`;

SET FOREIGN_KEY_CHECKS = 0;

-- ========================================================
-- PERMISOS
-- ========================================================
INSERT INTO `permiso` (`idpermiso`, `nombre`, `estado`) VALUES
(1,  'Empresa',          1),
(2,  'Sucursal',         1),
(3,  'Categoria',        1),
(4,  'Producto',         1),
(5,  'Proveedor',        1),
(6,  'Cliente',          1),
(7,  'Usuario',          1),
(8,  'Permiso',          1),
(9,  'Compra',           1),
(10, 'Venta',            1),
(11, 'Reporte Compras',  1),
(12, 'Reporte Ventas',   1),
(13, 'Traspaso',         1),
(14, 'Escritorio',       1);

-- ========================================================
-- METODOS DE PAGO
-- ========================================================
INSERT INTO `metodos_pago` (`id_metodo`, `nombre`, `estado`) VALUES
(1, 'Efectivo',               1),
(2, 'Tarjeta de credito',     1),
(3, 'Tarjeta de debito',      1),
(4, 'Transferencia bancaria', 1);

-- ========================================================
-- EMPRESA
-- ========================================================
INSERT INTO `empresa` (`idempresa`, `nombre`, `nit`, `direccion`, `telefono`, `email`, `imagen`, `fechacreacion`, `estado`) VALUES
(1, 'Distribuidora El Progreso', '0614-150390-101-5', '4a Calle Oriente #23, San Salvador', '2222-3344', 'contacto@elprogreso.com', '', NOW(), 1);

-- ========================================================
-- SUCURSALES
-- ========================================================
INSERT INTO `sucursal` (`idsucursal`, `nombre`, `idempresa`, `fechacreacion`, `estado`) VALUES
(1, 'Sucursal Central',    1, NOW(), 1),
(2, 'Sucursal Norte',      1, NOW(), 1),
(3, 'Sucursal Santa Ana',  1, NOW(), 1);

-- ========================================================
-- USUARIOS
-- All passwords are: admin (SHA256)
-- ========================================================
INSERT INTO `usuarios` (`idusuario`, `nombre`, `apellidopaterno`, `apellidomaterno`, `tipodocumento`, `numdocumento`, `direccion`, `telefono`, `correo`, `cargo`, `clave`, `imagen`, `estado`) VALUES
(1, 'Carlos',   'Mendoza',   'Rivera',    'DUI', '01234567-8', 'Colonia Escalon, San Salvador',    '7890-1234', 'cmendoza@elprogreso.com',    'Administrador',  '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '', 1),
(2, 'Maria',    'Hernandez', 'Lopez',     'DUI', '02345678-9', 'Colonia Miramonte, San Salvador',  '7891-2345', 'mhernandez@elprogreso.com',  'Vendedor',       '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '', 1),
(3, 'Jose',     'Garcia',    'Morales',   'DUI', '03456789-0', 'Reparto Las Brisas, Soyapango',    '7892-3456', 'jgarcia@elprogreso.com',     'Vendedor',       '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '', 1),
(4, 'Ana',      'Martinez',  'Perez',     'DUI', '04567890-1', 'Colonia Roma, San Salvador',       '7893-4567', 'amartinez@elprogreso.com',   'Bodeguero',      '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '', 1),
(5, 'Luis',     'Ramirez',   'Castro',    'DUI', '05678901-2', 'Mejicanos, San Salvador',          '7894-5678', 'lramirez@elprogreso.com',    'Vendedor',       '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', '', 1);

-- Permisos: admin tiene todos
INSERT INTO `rol` (`idpermiso`, `idusuario`) VALUES
(1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),
-- vendedores: venta, cliente, escritorio
(6,2),(10,2),(12,2),(14,2),
(6,3),(10,3),(12,3),(14,3),
(6,5),(10,5),(12,5),(14,5),
-- bodeguero: compra, producto, traspaso
(4,4),(9,4),(13,4),(14,4);

-- ========================================================
-- CATEGORIAS
-- ========================================================
INSERT INTO `categoria` (`idcategoria`, `nombre`, `fechacreacion`, `estado`) VALUES
(1,  'Lacteos',              NOW(), 1),
(2,  'Bebidas',              NOW(), 1),
(3,  'Granos y Cereales',    NOW(), 1),
(4,  'Limpieza',             NOW(), 1),
(5,  'Snacks',               NOW(), 1),
(6,  'Carnes y Embutidos',   NOW(), 1),
(7,  'Panaderia',            NOW(), 1),
(8,  'Higiene Personal',     NOW(), 1),
(9,  'Condimentos',          NOW(), 1),
(10, 'Congelados',           NOW(), 1);

-- ========================================================
-- PROVEEDORES
-- ========================================================
INSERT INTO `persona` (`idpersona`, `tipopersona`, `nombre`, `apellidopaterno`, `apellidomaterno`, `tipodocumento`, `numdocumento`, `direccion`, `telefono`, `email`, `fechacreacion`, `estado`) VALUES
(1,  'Proveedor', 'Lacteos',     'El Buen Pastor',   NULL, 'NIT', '0614-010101-001-1', 'Zona Industrial, San Salvador',    '2233-4455', 'ventas@buenpastor.com',    '2025-01-05', 1),
(2,  'Proveedor', 'Distribuidora','Centroamerica',   NULL, 'NIT', '0614-020202-002-2', 'Bulevar del Ejercito, Soyapango',  '2244-5566', 'pedidos@districa.com',     '2025-01-10', 1),
(3,  'Proveedor', 'Alimentos',   'San Jorge',        NULL, 'NIT', '0614-030303-003-3', '3a Av. Sur #45, Santa Ana',        '2255-6677', 'info@alsanjorge.com',      '2025-01-15', 1),
(4,  'Proveedor', 'Limpieza',    'Total Clean',      NULL, 'NIT', '0614-040404-004-4', 'Col. Jardines de Guadalupe, SS',   '2266-7788', 'ventas@totalclean.com',    '2025-02-01', 1),
(5,  'Proveedor', 'Bebidas',     'Refrescos del Sur',NULL, 'NIT', '0614-050505-005-5', 'Carretera Panamericana Km 34',     '2277-8899', 'pedidos@refrescossur.com', '2025-02-10', 1),
-- CLIENTE PUBLICO GENERAL (id=6)
(6,  'Cliente',   'Publico',     'General',          NULL, 'DUI', '00000000-0',        NULL,                               NULL,        NULL,                       '2025-01-01', 1),
-- CLIENTES
(7,  'Cliente',   'Roberto',     'Flores',     'Aguilar',  'DUI', '06789012-3', 'Apopa, San Salvador',              '7895-6789', 'rflores@gmail.com',        '2025-01-20', 1),
(8,  'Cliente',   'Sandra',      'Lopez',      'Vega',     'DUI', '07890123-4', 'Ciudad Merliot, La Libertad',      '7896-7890', 'slopez@hotmail.com',       '2025-01-22', 1),
(9,  'Cliente',   'Miguel',      'Torres',     'Rivas',    'DUI', '08901234-5', 'Colonia Universitaria, SS',        '7897-8901', 'mtorres@yahoo.com',        '2025-02-03', 1),
(10, 'Cliente',   'Patricia',    'Gomez',      'Diaz',     'DUI', '09012345-6', 'San Marcos, San Salvador',         '7898-9012', 'pgomez@gmail.com',         '2025-02-14', 1),
(11, 'Cliente',   'Eduardo',     'Vasquez',    'Cruz',     'DUI', '00123456-7', 'Sonsonate Centro',                 '7899-0123', 'evasquez@gmail.com',       '2025-02-28', 1),
(12, 'Cliente',   'Diana',       'Reyes',      'Portillo', 'DUI', '01234560-8', 'Nueva San Salvador, La Libertad',  '7800-1234', 'dreyes@outlook.com',       '2025-03-05', 1),
(13, 'Cliente',   'Fernando',    'Castillo',   'Mejia',    'DUI', '02345670-9', 'Antiguo Cuscatlan',                '7801-2345', 'fcastillo@gmail.com',      '2025-03-12', 1),
(14, 'Cliente',   'Lucia',       'Orellana',   'Santos',   'DUI', '03456780-1', 'Colonia San Benito, SS',           '7802-3456', 'lorellana@gmail.com',      '2025-03-20', 1),
(15, 'Cliente',   'Hector',      'Molina',     'Guardado', 'DUI', '04567891-2', 'Zacatecoluca, La Paz',             '7803-4567', 'hmolina@yahoo.com',        '2025-04-01', 1),
(16, 'Cliente',   'Claudia',     'Fuentes',    'Barrera',  'DUI', '05678902-3', 'Usulutan Centro',                  '7804-5678', 'cfuentes@gmail.com',       '2025-04-10', 1);

-- ========================================================
-- PRODUCTOS
-- ========================================================
INSERT INTO `producto` (`idproducto`, `idcategoria`, `idsucursal`, `codigo`, `nombre`, `stock`, `descripcion`, `imagen`, `preciocompra`, `precioventa`, `estado`) VALUES
-- Lacteos (cat 1)
(1,  1, 1, 'LAC-001', 'Leche entera 1L',              80,  'Leche entera pasteurizada 1 litro',        '', 0.65, 0.95,  1),
(2,  1, 1, 'LAC-002', 'Queso blanco 500g',            40,  'Queso blanco fresco 500 gramos',           '', 2.00, 2.75,  1),
(3,  1, 1, 'LAC-003', 'Crema acida 250ml',            35,  'Crema acida entera 250 ml',                '', 0.90, 1.25,  1),
(4,  1, 2, 'LAC-004', 'Mantequilla 200g',             25,  'Mantequilla sin sal 200 gramos',           '', 1.30, 1.75,  1),
(5,  1, 2, 'LAC-005', 'Yogur natural 500ml',          20,  'Yogur natural sin azucar 500 ml',          '', 1.10, 1.50,  1),
-- Bebidas (cat 2)
(6,  2, 1, 'BEB-001', 'Agua purificada 600ml',        150, 'Agua purificada en botella 600 ml',        '', 0.25, 0.50,  1),
(7,  2, 1, 'BEB-002', 'Refresco cola 355ml',          120, 'Refresco sabor cola lata 355 ml',          '', 0.55, 0.85,  1),
(8,  2, 1, 'BEB-003', 'Jugo naranja 1L',              45,  'Jugo de naranja 100% natural 1 litro',     '', 1.20, 1.75,  1),
(9,  2, 2, 'BEB-004', 'Bebida energetica 250ml',      60,  'Bebida energetica lata 250 ml',            '', 0.90, 1.40,  1),
(10, 2, 3, 'BEB-005', 'Te negro frio 500ml',          50,  'Te negro endulzado frio 500 ml',           '', 0.60, 0.95,  1),
-- Granos y Cereales (cat 3)
(11, 3, 1, 'GRA-001', 'Arroz blanco 2kg',             70,  'Arroz blanco grano largo 2 kg',            '', 1.50, 2.10,  1),
(12, 3, 1, 'GRA-002', 'Frijoles negros 1lb',          90,  'Frijoles negros secos 1 libra',            '', 0.70, 1.00,  1),
(13, 3, 2, 'GRA-003', 'Avena en hojuelas 400g',       30,  'Avena en hojuelas tradicional 400g',       '', 0.80, 1.20,  1),
(14, 3, 1, 'GRA-004', 'Maiz molido 1kg',              55,  'Maiz molido nixtamal 1 kg',                '', 0.60, 0.90,  1),
-- Limpieza (cat 4)
(15, 4, 1, 'LIM-001', 'Detergente en polvo 1kg',      40,  'Detergente multiusos en polvo 1 kg',       '', 1.80, 2.50,  1),
(16, 4, 1, 'LIM-002', 'Jabon lavaplatos 500g',        55,  'Jabon para lavar platos 500 gramos',       '', 0.70, 1.00,  1),
(17, 4, 2, 'LIM-003', 'Cloro 1L',                    35,  'Cloro desinfectante concentrado 1 litro',  '', 0.80, 1.10,  1),
(18, 4, 1, 'LIM-004', 'Suavizante de ropa 900ml',     20,  'Suavizante para ropa fragancia floral',    '', 1.50, 2.20,  1),
-- Snacks (cat 5)
(19, 5, 1, 'SNA-001', 'Papas fritas 150g',            75,  'Papas fritas sabor original 150g',         '', 0.80, 1.25,  1),
(20, 5, 1, 'SNA-002', 'Galletas soda 350g',           60,  'Galletas de soda clasicas 350g',           '', 1.00, 1.50,  1),
(21, 5, 2, 'SNA-003', 'Maiz tostado 200g',            45,  'Maiz tostado con sal 200g',                '', 0.50, 0.85,  1),
(22, 5, 1, 'SNA-004', 'Chocolate en barra 45g',       80,  'Chocolate con leche en barra 45g',         '', 0.45, 0.75,  1),
-- Carnes y Embutidos (cat 6)
(23, 6, 1, 'CAR-001', 'Jamon ahumado 200g',           30,  'Jamon ahumado en rebanadas 200g',          '', 1.80, 2.50,  1),
(24, 6, 1, 'CAR-002', 'Mortadela 250g',               25,  'Mortadela clasica en rebanadas 250g',      '', 1.40, 2.00,  1),
(25, 6, 2, 'CAR-003', 'Salchichas 400g',              20,  'Salchichas de cerdo 400g x8 unidades',     '', 2.00, 2.75,  1),
-- Panaderia (cat 7)
(26, 7, 1, 'PAN-001', 'Pan frances unidad',           100, 'Pan frances individual horneado',          '', 0.10, 0.15,  1),
(27, 7, 1, 'PAN-002', 'Pan de caja 680g',             40,  'Pan de caja blanco 680g 20 rebanadas',     '', 1.20, 1.75,  1),
(28, 7, 2, 'PAN-003', 'Tortillas de maiz x10',        60,  'Tortillas de maiz artesanales x10',        '', 0.80, 1.20,  1),
-- Higiene Personal (cat 8)
(29, 8, 1, 'HIG-001', 'Shampoo 400ml',                25,  'Shampoo para cabello normal 400ml',        '', 2.50, 3.50,  1),
(30, 8, 1, 'HIG-002', 'Jabon de bano 125g',           60,  'Jabon de bano antibacterial 125g',         '', 0.50, 0.75,  1),
(31, 8, 2, 'HIG-003', 'Pasta dental 90ml',            40,  'Pasta dental menta 90ml',                  '', 0.90, 1.35,  1),
(32, 8, 1, 'HIG-004', 'Desodorante roll-on 50ml',     30,  'Desodorante roll-on sin alcohol 50ml',     '', 1.80, 2.50,  1),
-- Condimentos (cat 9)
(33, 9, 1, 'CON-001', 'Sal yodada 500g',              70,  'Sal yodada fina 500 gramos',               '', 0.30, 0.50,  1),
(34, 9, 1, 'CON-002', 'Azucar blanca 1kg',            65,  'Azucar blanca refinada 1 kg',              '', 0.70, 1.00,  1),
(35, 9, 1, 'CON-003', 'Aceite vegetal 1L',            50,  'Aceite vegetal puro 1 litro',              '', 1.60, 2.20,  1),
(36, 9, 2, 'CON-004', 'Salsa de tomate 397g',         45,  'Salsa de tomate natural 397g',             '', 0.80, 1.20,  1),
-- Congelados (cat 10)
(37, 10, 1, 'CON-101', 'Helado vainilla 1L',          15,  'Helado de vainilla 1 litro',               '', 2.50, 3.50,  1),
(38, 10, 1, 'CON-102', 'Pizza congelada personal',    12,  'Pizza congelada personal 4 porciones',     '', 2.80, 4.00,  1),
(39, 10, 2, 'CON-103', 'Nuggets de pollo 500g',       18,  'Nuggets de pollo 500g x20 unidades',       '', 3.00, 4.25,  1),
(40, 10, 1, 'CON-104', 'Papas precocidas 750g',       10,  'Papas precocidas para freir 750g',         '', 2.20, 3.00,  1);

-- ========================================================
-- COMPRAS (3 meses atras hasta hoy)
-- ========================================================
INSERT INTO `compra` (`idcompra`, `idproveedor`, `idusuario`, `total`, `fechacreacion`, `estado`) VALUES
(1,  1, 4, 156.00, DATE_SUB(NOW(), INTERVAL 85 DAY), 1),
(2,  2, 4, 320.50, DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
(3,  3, 4, 210.00, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(4,  4, 4,  98.00, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(5,  5, 4, 175.00, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(6,  1, 4, 140.00, DATE_SUB(NOW(), INTERVAL 58 DAY), 1),
(7,  2, 4, 290.00, DATE_SUB(NOW(), INTERVAL 52 DAY), 1),
(8,  3, 4, 180.00, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(9,  4, 4,  75.00, DATE_SUB(NOW(), INTERVAL 40 DAY), 1),
(10, 5, 4, 220.00, DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
(11, 1, 4, 160.00, DATE_SUB(NOW(), INTERVAL 28 DAY), 1),
(12, 2, 4, 305.00, DATE_SUB(NOW(), INTERVAL 21 DAY), 1),
(13, 3, 4, 195.00, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(14, 4, 4,  88.00, DATE_SUB(NOW(), INTERVAL  7 DAY), 1),
(15, 5, 4, 240.00, DATE_SUB(NOW(), INTERVAL  2 DAY), 1);

INSERT INTO `detallecompra` (`idcompra`, `idproducto`, `cantidad`, `preciocompra`, `fechacreacion`, `estado`) VALUES
-- compra 1 (lacteos)
(1,  1, 50, 0.65, DATE_SUB(NOW(), INTERVAL 85 DAY), 1),
(1,  2, 30, 2.00, DATE_SUB(NOW(), INTERVAL 85 DAY), 1),
(1,  3, 40, 0.90, DATE_SUB(NOW(), INTERVAL 85 DAY), 1),
-- compra 2 (granos, snacks, condimentos)
(2, 11, 40, 1.50, DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
(2, 12, 60, 0.70, DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
(2, 19, 50, 0.80, DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
(2, 20, 40, 1.00, DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
(2, 34, 50, 0.70, DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
(2, 35, 30, 1.60, DATE_SUB(NOW(), INTERVAL 80 DAY), 1),
-- compra 3 (carnes y embutidos, congelados)
(3, 23, 20, 1.80, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(3, 24, 20, 1.40, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(3, 37, 15, 2.50, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(3, 38, 15, 2.80, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(3, 39, 20, 3.00, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
-- compra 4 (limpieza)
(4, 15, 20, 1.80, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(4, 16, 30, 0.70, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(4, 17, 20, 0.80, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
-- compra 5 (bebidas)
(5,  6, 80, 0.25, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(5,  7, 60, 0.55, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(5,  8, 30, 1.20, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(5,  9, 40, 0.90, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
-- compra 6 (lacteos reposicion)
(6,  1, 60, 0.65, DATE_SUB(NOW(), INTERVAL 58 DAY), 1),
(6,  4, 20, 1.30, DATE_SUB(NOW(), INTERVAL 58 DAY), 1),
(6,  5, 25, 1.10, DATE_SUB(NOW(), INTERVAL 58 DAY), 1),
-- compra 7 (granos reposicion)
(7, 11, 50, 1.50, DATE_SUB(NOW(), INTERVAL 52 DAY), 1),
(7, 12, 70, 0.70, DATE_SUB(NOW(), INTERVAL 52 DAY), 1),
(7, 14, 40, 0.60, DATE_SUB(NOW(), INTERVAL 52 DAY), 1),
(7, 33, 60, 0.30, DATE_SUB(NOW(), INTERVAL 52 DAY), 1),
(7, 34, 50, 0.70, DATE_SUB(NOW(), INTERVAL 52 DAY), 1),
-- compra 8 (panaderia higiene)
(8, 26,100, 0.10, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(8, 27, 30, 1.20, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(8, 29, 20, 2.50, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(8, 30, 50, 0.50, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(8, 31, 30, 0.90, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
-- compra 9 (limpieza reposicion)
(9, 15, 15, 1.80, DATE_SUB(NOW(), INTERVAL 40 DAY), 1),
(9, 16, 25, 0.70, DATE_SUB(NOW(), INTERVAL 40 DAY), 1),
(9, 18, 10, 1.50, DATE_SUB(NOW(), INTERVAL 40 DAY), 1),
-- compra 10 (bebidas reposicion)
(10,  6,100, 0.25, DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
(10,  7, 80, 0.55, DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
(10, 10, 40, 0.60, DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
-- compra 11 (lacteos)
(11,  1, 60, 0.65, DATE_SUB(NOW(), INTERVAL 28 DAY), 1),
(11,  2, 25, 2.00, DATE_SUB(NOW(), INTERVAL 28 DAY), 1),
(11,  3, 30, 0.90, DATE_SUB(NOW(), INTERVAL 28 DAY), 1),
-- compra 12 (surtido)
(12, 19, 60, 0.80, DATE_SUB(NOW(), INTERVAL 21 DAY), 1),
(12, 20, 50, 1.00, DATE_SUB(NOW(), INTERVAL 21 DAY), 1),
(12, 22, 70, 0.45, DATE_SUB(NOW(), INTERVAL 21 DAY), 1),
(12, 23, 15, 1.80, DATE_SUB(NOW(), INTERVAL 21 DAY), 1),
(12, 35, 30, 1.60, DATE_SUB(NOW(), INTERVAL 21 DAY), 1),
-- compra 13 (carnes congelados)
(13, 24, 20, 1.40, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(13, 25, 15, 2.00, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(13, 37, 10, 2.50, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(13, 40, 15, 2.20, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
-- compra 14 (limpieza higiene)
(14, 16, 20, 0.70, DATE_SUB(NOW(), INTERVAL  7 DAY), 1),
(14, 30, 40, 0.50, DATE_SUB(NOW(), INTERVAL  7 DAY), 1),
(14, 32, 20, 1.80, DATE_SUB(NOW(), INTERVAL  7 DAY), 1),
-- compra 15 (bebidas snacks)
(15,  6,120, 0.25, DATE_SUB(NOW(), INTERVAL  2 DAY), 1),
(15,  7, 80, 0.55, DATE_SUB(NOW(), INTERVAL  2 DAY), 1),
(15,  9, 50, 0.90, DATE_SUB(NOW(), INTERVAL  2 DAY), 1),
(15, 21, 40, 0.50, DATE_SUB(NOW(), INTERVAL  2 DAY), 1);

-- ========================================================
-- VENTAS
-- ========================================================
INSERT INTO `venta` (`idventa`, `idcliente`, `idusuario`, `totalventa`, `metodopago`, `fechacreacion`, `estado`) VALUES
(1,  7,  2,  8.55,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(2,  8,  2, 15.30,  'Tarjeta de debito',  DATE_SUB(NOW(), INTERVAL 74 DAY), 1),
(3,  9,  3, 12.00,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 73 DAY), 1),
(4,  10, 2,  6.75,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 72 DAY), 1),
(5,  6,  3,  4.50,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 71 DAY), 1),
(6,  11, 2, 22.80,  'Tarjeta de credito', DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(7,  12, 3,  9.25,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 68 DAY), 1),
(8,  7,  2, 18.50,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(9,  13, 5, 31.40,  'Transferencia bancaria', DATE_SUB(NOW(), INTERVAL 63 DAY), 1),
(10, 8,  2, 11.25,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 61 DAY), 1),
(11, 14, 3,  7.50,  'Tarjeta de debito',  DATE_SUB(NOW(), INTERVAL 59 DAY), 1),
(12, 9,  5, 27.00,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 57 DAY), 1),
(13, 15, 2, 14.85,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 55 DAY), 1),
(14, 6,  3,  5.25,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 53 DAY), 1),
(15, 16, 5, 38.00,  'Tarjeta de credito', DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(16, 10, 2, 13.50,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 48 DAY), 1),
(17, 7,  3, 24.75,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(18, 11, 2, 16.20,  'Tarjeta de debito',  DATE_SUB(NOW(), INTERVAL 43 DAY), 1),
(19, 12, 5,  9.80,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 41 DAY), 1),
(20, 13, 2, 45.50,  'Transferencia bancaria', DATE_SUB(NOW(), INTERVAL 39 DAY), 1),
(21, 8,  3, 11.00,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 37 DAY), 1),
(22, 14, 2, 19.25,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
(23, 9,  5, 33.60,  'Tarjeta de credito', DATE_SUB(NOW(), INTERVAL 33 DAY), 1),
(24, 15, 2,  8.75,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 31 DAY), 1),
(25, 6,  3,  6.00,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 29 DAY), 1),
(26, 16, 2, 28.40,  'Tarjeta de debito',  DATE_SUB(NOW(), INTERVAL 27 DAY), 1),
(27, 10, 5, 17.50,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 25 DAY), 1),
(28, 7,  2, 41.00,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 22 DAY), 1),
(29, 11, 3, 23.75,  'Tarjeta de credito', DATE_SUB(NOW(), INTERVAL 20 DAY), 1),
(30, 12, 2, 12.50,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 18 DAY), 1),
(31, 13, 5, 36.80,  'Transferencia bancaria', DATE_SUB(NOW(), INTERVAL 16 DAY), 1),
(32, 8,  2, 15.60,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(33, 14, 3, 10.25,  'Tarjeta de debito',  DATE_SUB(NOW(), INTERVAL 12 DAY), 1),
(34, 9,  2, 52.00,  'Efectivo',           DATE_SUB(NOW(), INTERVAL 10 DAY), 1),
(35, 15, 5, 18.90,  'Efectivo',           DATE_SUB(NOW(), INTERVAL  8 DAY), 1),
(36, 6,  2,  7.50,  'Efectivo',           DATE_SUB(NOW(), INTERVAL  7 DAY), 1),
(37, 16, 3, 29.00,  'Tarjeta de credito', DATE_SUB(NOW(), INTERVAL  6 DAY), 1),
(38, 10, 2, 21.75,  'Efectivo',           DATE_SUB(NOW(), INTERVAL  5 DAY), 1),
(39, 7,  5, 44.20,  'Transferencia bancaria', DATE_SUB(NOW(), INTERVAL  4 DAY), 1),
(40, 11, 2, 13.50,  'Efectivo',           DATE_SUB(NOW(), INTERVAL  3 DAY), 1),
(41, 12, 3, 31.00,  'Tarjeta de debito',  DATE_SUB(NOW(), INTERVAL  2 DAY), 1),
(42, 13, 2, 26.80,  'Efectivo',           DATE_SUB(NOW(), INTERVAL  1 DAY), 1),
(43, 8,  5, 19.50,  'Efectivo',           NOW(),                             1),
(44, 14, 2, 37.25,  'Tarjeta de credito', NOW(),                             1),
(45, 9,  3, 14.00,  'Efectivo',           NOW(),                             1);

INSERT INTO `detalleventa` (`idventa`, `idproducto`, `cantidad`, `precioventa`, `descuento`, `fechacreacion`, `estado`) VALUES
(1,  1, 3, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(1,  7, 4, 0.85, 0.00, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(1, 26, 5, 0.15, 0.00, DATE_SUB(NOW(), INTERVAL 75 DAY), 1),
(2, 11, 2, 2.10, 0.00, DATE_SUB(NOW(), INTERVAL 74 DAY), 1),
(2, 12, 3, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 74 DAY), 1),
(2, 35, 2, 2.20, 0.00, DATE_SUB(NOW(), INTERVAL 74 DAY), 1),
(2, 34, 2, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 74 DAY), 1),
(3,  6, 6, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 73 DAY), 1),
(3, 19, 3, 1.25, 0.00, DATE_SUB(NOW(), INTERVAL 73 DAY), 1),
(3, 22, 4, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL 73 DAY), 1),
(4,  2, 1, 2.75, 0.00, DATE_SUB(NOW(), INTERVAL 72 DAY), 1),
(4,  3, 2, 1.25, 0.00, DATE_SUB(NOW(), INTERVAL 72 DAY), 1),
(4,  1, 1, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 72 DAY), 1),
(5,  7, 3, 0.85, 0.00, DATE_SUB(NOW(), INTERVAL 71 DAY), 1),
(5, 30, 2, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL 71 DAY), 1),
(6, 15, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(6, 16, 3, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(6, 18, 2, 2.20, 0.00, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(6, 29, 1, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(6, 32, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 70 DAY), 1),
(7,  1, 2, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 68 DAY), 1),
(7, 33, 3, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 68 DAY), 1),
(7, 34, 2, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 68 DAY), 1),
(7, 26,10, 0.15, 0.00, DATE_SUB(NOW(), INTERVAL 68 DAY), 1),
(8, 23, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(8, 24, 2, 2.00, 0.00, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(8, 27, 2, 1.75, 0.00, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(8, 36, 2, 1.20, 0.00, DATE_SUB(NOW(), INTERVAL 65 DAY), 1),
(9, 37, 2, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL 63 DAY), 1),
(9, 38, 2, 4.00, 0.00, DATE_SUB(NOW(), INTERVAL 63 DAY), 1),
(9, 39, 1, 4.25, 0.00, DATE_SUB(NOW(), INTERVAL 63 DAY), 1),
(9, 40, 2, 3.00, 0.00, DATE_SUB(NOW(), INTERVAL 63 DAY), 1),
(9,  8, 2, 1.75, 0.00, DATE_SUB(NOW(), INTERVAL 63 DAY), 1),
(10, 6, 5, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 61 DAY), 1),
(10,19, 3, 1.25, 0.00, DATE_SUB(NOW(), INTERVAL 61 DAY), 1),
(10,20, 1, 1.50, 0.00, DATE_SUB(NOW(), INTERVAL 61 DAY), 1),
(11, 1, 2, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 59 DAY), 1),
(11, 3, 2, 1.25, 0.00, DATE_SUB(NOW(), INTERVAL 59 DAY), 1),
(11,33, 2, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 59 DAY), 1),
(12,11, 4, 2.10, 0.00, DATE_SUB(NOW(), INTERVAL 57 DAY), 1),
(12,12, 5, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 57 DAY), 1),
(12,14, 3, 0.90, 0.00, DATE_SUB(NOW(), INTERVAL 57 DAY), 1),
(13, 7, 5, 0.85, 0.00, DATE_SUB(NOW(), INTERVAL 55 DAY), 1),
(13, 9, 3, 1.40, 0.00, DATE_SUB(NOW(), INTERVAL 55 DAY), 1),
(13,10, 2, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 55 DAY), 1),
(14, 6, 4, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 53 DAY), 1),
(14,26, 7, 0.15, 0.00, DATE_SUB(NOW(), INTERVAL 53 DAY), 1),
(15,29, 1, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(15,30, 4, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(15,31, 2, 1.35, 0.00, DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(15,32, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(15,18, 2, 2.20, 0.00, DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(15,15, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(15,17, 2, 1.10, 0.00, DATE_SUB(NOW(), INTERVAL 50 DAY), 1),
(16, 2, 1, 2.75, 0.00, DATE_SUB(NOW(), INTERVAL 48 DAY), 1),
(16, 1, 3, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 48 DAY), 1),
(16,34, 2, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 48 DAY), 1),
(17,23, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(17,24, 2, 2.00, 0.00, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(17,25, 1, 2.75, 0.00, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(17,35, 2, 2.20, 0.00, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(17,36, 2, 1.20, 0.00, DATE_SUB(NOW(), INTERVAL 45 DAY), 1),
(18, 6, 4, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 43 DAY), 1),
(18, 8, 2, 1.75, 0.00, DATE_SUB(NOW(), INTERVAL 43 DAY), 1),
(18,20, 2, 1.50, 0.00, DATE_SUB(NOW(), INTERVAL 43 DAY), 1),
(18,22, 3, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL 43 DAY), 1),
(19,33, 2, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 41 DAY), 1),
(19,34, 2, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 41 DAY), 1),
(19,26, 5, 0.15, 0.00, DATE_SUB(NOW(), INTERVAL 41 DAY), 1),
(20,37, 3, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL 39 DAY), 1),
(20,38, 3, 4.00, 0.00, DATE_SUB(NOW(), INTERVAL 39 DAY), 1),
(20,39, 2, 4.25, 0.00, DATE_SUB(NOW(), INTERVAL 39 DAY), 1),
(20,40, 2, 3.00, 0.00, DATE_SUB(NOW(), INTERVAL 39 DAY), 1),
(21, 1, 4, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 37 DAY), 1),
(21, 3, 2, 1.25, 0.00, DATE_SUB(NOW(), INTERVAL 37 DAY), 1),
(21, 5, 1, 1.50, 0.00, DATE_SUB(NOW(), INTERVAL 37 DAY), 1),
(22,11, 3, 2.10, 0.00, DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
(22,12, 4, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
(22,35, 1, 2.20, 0.00, DATE_SUB(NOW(), INTERVAL 35 DAY), 1),
(23,15, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 33 DAY), 1),
(23,16, 3, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 33 DAY), 1),
(23,17, 2, 1.10, 0.00, DATE_SUB(NOW(), INTERVAL 33 DAY), 1),
(23,29, 2, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL 33 DAY), 1),
(23,30, 3, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL 33 DAY), 1),
(24, 7, 4, 0.85, 0.00, DATE_SUB(NOW(), INTERVAL 31 DAY), 1),
(24,10, 2, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 31 DAY), 1),
(24,22, 2, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL 31 DAY), 1),
(25, 6, 6, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 29 DAY), 1),
(25,26, 8, 0.15, 0.00, DATE_SUB(NOW(), INTERVAL 29 DAY), 1),
(26,23, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 27 DAY), 1),
(26,24, 2, 2.00, 0.00, DATE_SUB(NOW(), INTERVAL 27 DAY), 1),
(26,27, 2, 1.75, 0.00, DATE_SUB(NOW(), INTERVAL 27 DAY), 1),
(26,36, 2, 1.20, 0.00, DATE_SUB(NOW(), INTERVAL 27 DAY), 1),
(27, 2, 2, 2.75, 0.00, DATE_SUB(NOW(), INTERVAL 25 DAY), 1),
(27, 4, 2, 1.75, 0.00, DATE_SUB(NOW(), INTERVAL 25 DAY), 1),
(27, 1, 3, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 25 DAY), 1),
(28,37, 2, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL 22 DAY), 1),
(28,38, 2, 4.00, 0.00, DATE_SUB(NOW(), INTERVAL 22 DAY), 1),
(28,39, 2, 4.25, 0.00, DATE_SUB(NOW(), INTERVAL 22 DAY), 1),
(28,40, 2, 3.00, 0.00, DATE_SUB(NOW(), INTERVAL 22 DAY), 1),
(28,25, 2, 2.75, 0.00, DATE_SUB(NOW(), INTERVAL 22 DAY), 1),
(29,11, 3, 2.10, 0.00, DATE_SUB(NOW(), INTERVAL 20 DAY), 1),
(29,12, 4, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 20 DAY), 1),
(29,14, 2, 0.90, 0.00, DATE_SUB(NOW(), INTERVAL 20 DAY), 1),
(29,19, 3, 1.25, 0.00, DATE_SUB(NOW(), INTERVAL 20 DAY), 1),
(30, 6, 4, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 18 DAY), 1),
(30, 9, 2, 1.40, 0.00, DATE_SUB(NOW(), INTERVAL 18 DAY), 1),
(30,21, 3, 0.85, 0.00, DATE_SUB(NOW(), INTERVAL 18 DAY), 1),
(31,29, 2, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL 16 DAY), 1),
(31,30, 4, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL 16 DAY), 1),
(31,31, 2, 1.35, 0.00, DATE_SUB(NOW(), INTERVAL 16 DAY), 1),
(31,32, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 16 DAY), 1),
(31,15, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 16 DAY), 1),
(32, 1, 3, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(32, 3, 2, 1.25, 0.00, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(32,33, 3, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(32,34, 2, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL 14 DAY), 1),
(33, 7, 4, 0.85, 0.00, DATE_SUB(NOW(), INTERVAL 12 DAY), 1),
(33,10, 2, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL 12 DAY), 1),
(33,13, 1, 1.20, 0.00, DATE_SUB(NOW(), INTERVAL 12 DAY), 1),
(34,23, 3, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL 10 DAY), 1),
(34,24, 3, 2.00, 0.00, DATE_SUB(NOW(), INTERVAL 10 DAY), 1),
(34,25, 2, 2.75, 0.00, DATE_SUB(NOW(), INTERVAL 10 DAY), 1),
(34,37, 2, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL 10 DAY), 1),
(34,38, 2, 4.00, 0.00, DATE_SUB(NOW(), INTERVAL 10 DAY), 1),
(35,11, 3, 2.10, 0.00, DATE_SUB(NOW(), INTERVAL  8 DAY), 1),
(35,12, 3, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL  8 DAY), 1),
(35,35, 1, 2.20, 0.00, DATE_SUB(NOW(), INTERVAL  8 DAY), 1),
(35,36, 2, 1.20, 0.00, DATE_SUB(NOW(), INTERVAL  8 DAY), 1),
(36, 6, 5, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL  7 DAY), 1),
(36,26,10, 0.15, 0.00, DATE_SUB(NOW(), INTERVAL  7 DAY), 1),
(36,22, 3, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL  7 DAY), 1),
(37,15, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL  6 DAY), 1),
(37,16, 3, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL  6 DAY), 1),
(37,18, 2, 2.20, 0.00, DATE_SUB(NOW(), INTERVAL  6 DAY), 1),
(37,17, 2, 1.10, 0.00, DATE_SUB(NOW(), INTERVAL  6 DAY), 1),
(37,20, 2, 1.50, 0.00, DATE_SUB(NOW(), INTERVAL  6 DAY), 1),
(38, 2, 2, 2.75, 0.00, DATE_SUB(NOW(), INTERVAL  5 DAY), 1),
(38, 5, 2, 1.50, 0.00, DATE_SUB(NOW(), INTERVAL  5 DAY), 1),
(38, 4, 2, 1.75, 0.00, DATE_SUB(NOW(), INTERVAL  5 DAY), 1),
(38,34, 2, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL  5 DAY), 1),
(38,33, 3, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL  5 DAY), 1),
(39,37, 2, 3.50, 0.00, DATE_SUB(NOW(), INTERVAL  4 DAY), 1),
(39,38, 2, 4.00, 0.00, DATE_SUB(NOW(), INTERVAL  4 DAY), 1),
(39,39, 2, 4.25, 0.00, DATE_SUB(NOW(), INTERVAL  4 DAY), 1),
(39,40, 2, 3.00, 0.00, DATE_SUB(NOW(), INTERVAL  4 DAY), 1),
(39,25, 1, 2.75, 0.00, DATE_SUB(NOW(), INTERVAL  4 DAY), 1),
(40, 1, 4, 0.95, 0.00, DATE_SUB(NOW(), INTERVAL  3 DAY), 1),
(40, 6, 4, 0.50, 0.00, DATE_SUB(NOW(), INTERVAL  3 DAY), 1),
(40,19, 2, 1.25, 0.00, DATE_SUB(NOW(), INTERVAL  3 DAY), 1),
(41,11, 3, 2.10, 0.00, DATE_SUB(NOW(), INTERVAL  2 DAY), 1),
(41,12, 3, 1.00, 0.00, DATE_SUB(NOW(), INTERVAL  2 DAY), 1),
(41,35, 2, 2.20, 0.00, DATE_SUB(NOW(), INTERVAL  2 DAY), 1),
(41,36, 2, 1.20, 0.00, DATE_SUB(NOW(), INTERVAL  2 DAY), 1),
(42,23, 2, 2.50, 0.00, DATE_SUB(NOW(), INTERVAL  1 DAY), 1),
(42,24, 2, 2.00, 0.00, DATE_SUB(NOW(), INTERVAL  1 DAY), 1),
(42,27, 1, 1.75, 0.00, DATE_SUB(NOW(), INTERVAL  1 DAY), 1),
(42,22, 3, 0.75, 0.00, DATE_SUB(NOW(), INTERVAL  1 DAY), 1),
(43, 7, 5, 0.85, 0.00, NOW(), 1),
(43, 8, 3, 1.75, 0.00, NOW(), 1),
(43, 9, 2, 1.40, 0.00, NOW(), 1),
(44,29, 1, 3.50, 0.00, NOW(), 1),
(44,31, 2, 1.35, 0.00, NOW(), 1),
(44,32, 2, 2.50, 0.00, NOW(), 1),
(44,30, 4, 0.75, 0.00, NOW(), 1),
(44,33, 4, 0.50, 0.00, NOW(), 1),
(45, 6, 6, 0.50, 0.00, NOW(), 1),
(45,26,10, 0.15, 0.00, NOW(), 1),
(45,22, 4, 0.75, 0.00, NOW(), 1);

-- ========================================================
-- PAGOS DE VENTAS
-- ========================================================
INSERT INTO `pagos_venta` (`idventa`, `idmetodo`, `monto`, `efectivo_recibido`, `efectivo_cambio`, `fecha_pago`, `estado`) VALUES
(1,  1,  8.55, 10.00, 1.45, DATE_SUB(NOW(), INTERVAL 75 DAY), 'APLICADO'),
(2,  3, 15.30, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 74 DAY), 'APLICADO'),
(3,  1, 12.00, 15.00, 3.00, DATE_SUB(NOW(), INTERVAL 73 DAY), 'APLICADO'),
(4,  1,  6.75, 10.00, 3.25, DATE_SUB(NOW(), INTERVAL 72 DAY), 'APLICADO'),
(5,  1,  4.50,  5.00, 0.50, DATE_SUB(NOW(), INTERVAL 71 DAY), 'APLICADO'),
(6,  2, 22.80, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 70 DAY), 'APLICADO'),
(7,  1,  9.25, 10.00, 0.75, DATE_SUB(NOW(), INTERVAL 68 DAY), 'APLICADO'),
(8,  1, 18.50, 20.00, 1.50, DATE_SUB(NOW(), INTERVAL 65 DAY), 'APLICADO'),
(9,  4, 31.40, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 63 DAY), 'APLICADO'),
(10, 1, 11.25, 15.00, 3.75, DATE_SUB(NOW(), INTERVAL 61 DAY), 'APLICADO'),
(11, 3,  7.50, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 59 DAY), 'APLICADO'),
(12, 1, 27.00, 30.00, 3.00, DATE_SUB(NOW(), INTERVAL 57 DAY), 'APLICADO'),
(13, 1, 14.85, 15.00, 0.15, DATE_SUB(NOW(), INTERVAL 55 DAY), 'APLICADO'),
(14, 1,  5.25,  6.00, 0.75, DATE_SUB(NOW(), INTERVAL 53 DAY), 'APLICADO'),
(15, 2, 38.00, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 50 DAY), 'APLICADO'),
(16, 1, 13.50, 15.00, 1.50, DATE_SUB(NOW(), INTERVAL 48 DAY), 'APLICADO'),
(17, 1, 24.75, 25.00, 0.25, DATE_SUB(NOW(), INTERVAL 45 DAY), 'APLICADO'),
(18, 3, 16.20, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 43 DAY), 'APLICADO'),
(19, 1,  9.80, 10.00, 0.20, DATE_SUB(NOW(), INTERVAL 41 DAY), 'APLICADO'),
(20, 4, 45.50, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 39 DAY), 'APLICADO'),
(21, 1, 11.00, 15.00, 4.00, DATE_SUB(NOW(), INTERVAL 37 DAY), 'APLICADO'),
(22, 1, 19.25, 20.00, 0.75, DATE_SUB(NOW(), INTERVAL 35 DAY), 'APLICADO'),
(23, 2, 33.60, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 33 DAY), 'APLICADO'),
(24, 1,  8.75, 10.00, 1.25, DATE_SUB(NOW(), INTERVAL 31 DAY), 'APLICADO'),
(25, 1,  6.00,  6.00, 0.00, DATE_SUB(NOW(), INTERVAL 29 DAY), 'APLICADO'),
(26, 3, 28.40, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 27 DAY), 'APLICADO'),
(27, 1, 17.50, 20.00, 2.50, DATE_SUB(NOW(), INTERVAL 25 DAY), 'APLICADO'),
(28, 1, 41.00, 50.00, 9.00, DATE_SUB(NOW(), INTERVAL 22 DAY), 'APLICADO'),
(29, 2, 23.75, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 20 DAY), 'APLICADO'),
(30, 1, 12.50, 15.00, 2.50, DATE_SUB(NOW(), INTERVAL 18 DAY), 'APLICADO'),
(31, 4, 36.80, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 16 DAY), 'APLICADO'),
(32, 1, 15.60, 20.00, 4.40, DATE_SUB(NOW(), INTERVAL 14 DAY), 'APLICADO'),
(33, 3, 10.25, NULL,  NULL, DATE_SUB(NOW(), INTERVAL 12 DAY), 'APLICADO'),
(34, 1, 52.00, 55.00, 3.00, DATE_SUB(NOW(), INTERVAL 10 DAY), 'APLICADO'),
(35, 1, 18.90, 20.00, 1.10, DATE_SUB(NOW(), INTERVAL  8 DAY), 'APLICADO'),
(36, 1,  7.50, 10.00, 2.50, DATE_SUB(NOW(), INTERVAL  7 DAY), 'APLICADO'),
(37, 2, 29.00, NULL,  NULL, DATE_SUB(NOW(), INTERVAL  6 DAY), 'APLICADO'),
(38, 1, 21.75, 25.00, 3.25, DATE_SUB(NOW(), INTERVAL  5 DAY), 'APLICADO'),
(39, 4, 44.20, NULL,  NULL, DATE_SUB(NOW(), INTERVAL  4 DAY), 'APLICADO'),
(40, 1, 13.50, 15.00, 1.50, DATE_SUB(NOW(), INTERVAL  3 DAY), 'APLICADO'),
(41, 3, 31.00, NULL,  NULL, DATE_SUB(NOW(), INTERVAL  2 DAY), 'APLICADO'),
(42, 1, 26.80, 30.00, 3.20, DATE_SUB(NOW(), INTERVAL  1 DAY), 'APLICADO'),
(43, 1, 19.50, 20.00, 0.50, NOW(), 'APLICADO'),
(44, 2, 37.25, NULL,  NULL, NOW(), 'APLICADO'),
(45, 1, 14.00, 15.00, 1.00, NOW(), 'APLICADO');

-- ========================================================
-- TRASPASOS
-- ========================================================
INSERT INTO `traspaso` (`idtraspaso`, `idusuario`, `motivo`, `observaciones`, `fecha_solicitud`, `fecha_completado`, `estado`) VALUES
(1, 4, 'Reabastecimiento Sucursal Norte',    'Traslado de productos de alta rotacion', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 59 DAY), 'Completado'),
(2, 4, 'Reabastecimiento Santa Ana',         'Productos solicitados por sucursal',     DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(NOW(), INTERVAL 39 DAY), 'Completado'),
(3, 4, 'Equilibrio de inventario',           'Ajuste mensual de stock entre sucursales',DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY), 'Completado'),
(4, 4, 'Traslado bebidas Sucursal Norte',    'Refuerzo para temporada alta',           DATE_SUB(NOW(), INTERVAL  5 DAY), NULL,                            'Pendiente');

INSERT INTO `detalletraspaso` (`idtraspaso`, `idproducto`, `cantidad`, `estado`) VALUES
(1,  1, 20, 'Completado'),
(1,  6, 30, 'Completado'),
(1, 11, 15, 'Completado'),
(1, 19, 20, 'Completado'),
(2,  7, 25, 'Completado'),
(2, 12, 20, 'Completado'),
(2, 15, 10, 'Completado'),
(2, 34, 15, 'Completado'),
(3,  6, 20, 'Completado'),
(3,  9, 15, 'Completado'),
(3, 22, 20, 'Completado'),
(4,  6, 40, 'Pendiente'),
(4,  7, 30, 'Pendiente'),
(4,  9, 20, 'Pendiente');

SET FOREIGN_KEY_CHECKS = 1;
