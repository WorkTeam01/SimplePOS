<?php 
//incluir la conexión de base de datos
require "../config/Conexion.php";

class Sucursal {
    //implementamos nuestro constructor
    public function __construct(){
    }

    //método insertar registro
    public function insertar($nombre, $idempresa){
        $sql = "INSERT INTO sucursal (nombre, idempresa, fechacreacion, estado) 
                VALUES ('$nombre', '$idempresa', CURRENT_TIMESTAMP, '1')";
        return ejecutarConsulta($sql);
    }

    public function editar($idsucursal, $nombre, $idempresa){
        $sql = "UPDATE sucursal SET nombre = '$nombre', idempresa = '$idempresa' 
                WHERE idsucursal = '$idsucursal'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idsucursal){
        $sql = "UPDATE sucursal SET estado = '0' WHERE idsucursal = '$idsucursal'";
        return ejecutarConsulta($sql);
    }

    public function activar($idsucursal){
        $sql = "UPDATE sucursal SET estado = '1' WHERE idsucursal = '$idsucursal'";
        return ejecutarConsulta($sql);
    }

    //método para mostrar registros
    public function mostrar($idsucursal){
        $sql = "SELECT s.*, e.nombre as empresa 
                FROM sucursal s 
                INNER JOIN empresa e ON s.idempresa = e.idempresa 
                WHERE s.idsucursal = '$idsucursal'";
        return ejecutarConsultaSimpleFila($sql);
    }

    //listar registros
    public function listar(){
        $sql = "SELECT s.*, e.nombre as empresa 
                FROM sucursal s 
                INNER JOIN empresa e ON s.idempresa = e.idempresa";
        return ejecutarConsulta($sql);
    }

    //listar y mostrar en select
    public function select(){
        $sql = "SELECT * FROM sucursal WHERE estado = 1";
        return ejecutarConsulta($sql);
    }
    
    //listar sucursales por empresa
    public function listarPorEmpresa($idempresa){
        $sql = "SELECT * FROM sucursal 
                WHERE idempresa = '$idempresa' AND estado = 1";
        return ejecutarConsulta($sql);
    }
}
?>