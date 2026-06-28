<?php 
//incluir la conexión de base de datos
require "../config/Conexion.php";

class Producto {
    //implementamos nuestro constructor
    public function __construct() {
    }

    //método para insertar registro
    public function insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen, $preciocompra, $precioventa,  $idsucursal) {
        $sql = "INSERT INTO producto (idcategoria, codigo, nombre, stock, descripcion, imagen, preciocompra, precioventa, idsucursal, estado) 
                VALUES ('$idcategoria','$codigo','$nombre','$stock','$descripcion','$imagen','$preciocompra','$precioventa','$idsucursal','1')";
        return ejecutarConsulta($sql);
    }

    //método para editar registro
    public function editar($idproducto, $idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen, $preciocompra, $precioventa, $idsucursal) {
        $sql = "UPDATE producto SET 
                idcategoria='$idcategoria',
                codigo='$codigo', 
                nombre='$nombre',
                stock='$stock',
                descripcion='$descripcion',
                imagen='$imagen',
                preciocompra='$preciocompra',
                precioventa='$precioventa',
                idsucursal='$idsucursal'
                WHERE idproducto='$idproducto'";
        return ejecutarConsulta($sql);
    }

    //método para desactivar producto
    public function desactivar($idproducto) {
        $sql = "UPDATE producto SET estado='0' WHERE idproducto='$idproducto'";
        return ejecutarConsulta($sql);
    }

    //método para activar producto
    public function activar($idproducto) {
        $sql = "UPDATE producto SET estado='1' WHERE idproducto='$idproducto'";
        return ejecutarConsulta($sql);
    }

    //método para mostrar registros
    public function mostrar($idproducto) {
        $sql = "SELECT * FROM producto WHERE idproducto='$idproducto'";
        return ejecutarConsultaSimpleFila($sql);
    }

    //listar registros 
    public function listar() {
        $sql = "SELECT p.idproducto, p.idcategoria, c.nombre as categoria, p.codigo, p.nombre, p.stock, 
                p.descripcion, p.imagen, p.preciocompra, p.precioventa, 
                 s.nombre as sucursal, p.estado 
                FROM producto p 
                INNER JOIN categoria c ON p.idcategoria = c.idcategoria  
                INNER JOIN sucursal s ON p.idsucursal = s.idsucursal";
        return ejecutarConsulta($sql);
    }

    //listar registros activos
    public function listarActivos() {
        $sql = "SELECT p.idproducto, p.idcategoria, c.nombre as categoria, p.codigo, p.nombre, p.stock, 
                p.descripcion, p.imagen, p.preciocompra, p.precioventa, s.nombre as sucursal, p.estado 
                FROM producto p 
                INNER JOIN categoria c ON p.idcategoria = c.idcategoria
                INNER JOIN sucursal s ON p.idsucursal = s.idsucursal 
                WHERE p.estado='1'";
        return ejecutarConsulta($sql);
    }

    //listar activos para venta
    public function listarActivosVenta() {
        $sql = "SELECT p.idproducto, p.idcategoria, c.nombre as categoria, p.codigo, p.nombre, p.stock, 
                p.precioventa, p.descripcion, p.imagen 
                FROM producto p 
                INNER JOIN categoria c ON p.idcategoria = c.idcategoria 
                WHERE p.estado='1'";
        return ejecutarConsulta($sql);
    }
}
?>