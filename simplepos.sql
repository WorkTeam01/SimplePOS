-- SimplePOS - Estructura de base de datos
-- Generado: 2026-05-18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `simplepos` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `simplepos`;

CREATE TABLE `categoria` (
  `idcategoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fechacreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idcategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `empresa` (
  `idempresa` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `nit` varchar(50) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `imagen` varchar(200) DEFAULT NULL,
  `fechacreacion` datetime DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idempresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sucursal` (
  `idsucursal` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `idempresa` int(11) NOT NULL,
  `fechacreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idsucursal`),
  KEY `idempresa` (`idempresa`),
  CONSTRAINT `sucursal_ibfk_1` FOREIGN KEY (`idempresa`) REFERENCES `empresa` (`idempresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `permiso` (
  `idpermiso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idpermiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `usuarios` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidopaterno` varchar(100) NOT NULL,
  `apellidomaterno` varchar(100) DEFAULT NULL,
  `tipodocumento` varchar(20) NOT NULL,
  `numdocumento` varchar(20) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `clave` varchar(64) NOT NULL,
  `imagen` varchar(200) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL AUTO_INCREMENT,
  `idpermiso` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idrol`),
  KEY `idpermiso` (`idpermiso`),
  KEY `idusuario` (`idusuario`),
  CONSTRAINT `rol_ibfk_1` FOREIGN KEY (`idpermiso`) REFERENCES `permiso` (`idpermiso`),
  CONSTRAINT `rol_ibfk_2` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `persona` (
  `idpersona` int(11) NOT NULL AUTO_INCREMENT,
  `tipopersona` varchar(20) NOT NULL COMMENT 'Cliente o Proveedor',
  `nombre` varchar(100) NOT NULL,
  `apellidopaterno` varchar(100) NOT NULL,
  `apellidomaterno` varchar(100) DEFAULT NULL,
  `tipodocumento` varchar(20) NOT NULL,
  `numdocumento` varchar(20) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fechacreacion` date DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idpersona`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `producto` (
  `idproducto` int(11) NOT NULL AUTO_INCREMENT,
  `idcategoria` int(11) NOT NULL,
  `idsucursal` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(200) DEFAULT NULL,
  `preciocompra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precioventa` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idproducto`),
  KEY `idcategoria` (`idcategoria`),
  KEY `idsucursal` (`idsucursal`),
  CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`idcategoria`) REFERENCES `categoria` (`idcategoria`),
  CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`idsucursal`) REFERENCES `sucursal` (`idsucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `compra` (
  `idcompra` int(11) NOT NULL AUTO_INCREMENT,
  `idproveedor` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fechacreacion` datetime DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idcompra`),
  KEY `idproveedor` (`idproveedor`),
  KEY `idusuario` (`idusuario`),
  CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`idproveedor`) REFERENCES `persona` (`idpersona`),
  CONSTRAINT `compra_ibfk_2` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `detallecompra` (
  `iddetallecompra` int(11) NOT NULL AUTO_INCREMENT,
  `idcompra` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `preciocompra` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fechacreacion` datetime DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`iddetallecompra`),
  KEY `idcompra` (`idcompra`),
  KEY `idproducto` (`idproducto`),
  CONSTRAINT `detallecompra_ibfk_1` FOREIGN KEY (`idcompra`) REFERENCES `compra` (`idcompra`),
  CONSTRAINT `detallecompra_ibfk_2` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `metodos_pago` (
  `id_metodo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_metodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `venta` (
  `idventa` int(11) NOT NULL AUTO_INCREMENT,
  `idcliente` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `totalventa` decimal(10,2) NOT NULL DEFAULT 0.00,
  `metodopago` varchar(100) DEFAULT NULL,
  `fechacreacion` datetime DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idventa`),
  KEY `idcliente` (`idcliente`),
  KEY `idusuario` (`idusuario`),
  CONSTRAINT `venta_ibfk_1` FOREIGN KEY (`idcliente`) REFERENCES `persona` (`idpersona`),
  CONSTRAINT `venta_ibfk_2` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `detalleventa` (
  `iddetalleventa` int(11) NOT NULL AUTO_INCREMENT,
  `idventa` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `precioventa` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fechacreacion` datetime DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`iddetalleventa`),
  KEY `idventa` (`idventa`),
  KEY `idproducto` (`idproducto`),
  CONSTRAINT `detalleventa_ibfk_1` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`),
  CONSTRAINT `detalleventa_ibfk_2` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `pagos_venta` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `idventa` int(11) NOT NULL,
  `idmetodo` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `efectivo_recibido` decimal(10,2) DEFAULT NULL,
  `efectivo_cambio` decimal(10,2) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'APLICADO',
  PRIMARY KEY (`id_pago`),
  KEY `idventa` (`idventa`),
  KEY `idmetodo` (`idmetodo`),
  CONSTRAINT `pagos_venta_ibfk_1` FOREIGN KEY (`idventa`) REFERENCES `venta` (`idventa`),
  CONSTRAINT `pagos_venta_ibfk_2` FOREIGN KEY (`idmetodo`) REFERENCES `metodos_pago` (`id_metodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `traspaso` (
  `idtraspaso` int(11) NOT NULL AUTO_INCREMENT,
  `idusuario` int(11) NOT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_solicitud` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_completado` datetime DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (`idtraspaso`),
  KEY `idusuario` (`idusuario`),
  CONSTRAINT `traspaso_ibfk_1` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `detalletraspaso` (
  `iddetalletraspaso` int(11) NOT NULL AUTO_INCREMENT,
  `idtraspaso` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `estado` varchar(20) NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (`iddetalletraspaso`),
  KEY `idtraspaso` (`idtraspaso`),
  KEY `idproducto` (`idproducto`),
  CONSTRAINT `detalletraspaso_ibfk_1` FOREIGN KEY (`idtraspaso`) REFERENCES `traspaso` (`idtraspaso`),
  CONSTRAINT `detalletraspaso_ibfk_2` FOREIGN KEY (`idproducto`) REFERENCES `producto` (`idproducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
