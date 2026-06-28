<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";

class Categoria {
    //implementamos nuestro constructor
    public function __construct(){
    }

    //metodo insertar registro
    public function insertar($nombre){
        $sql = "INSERT INTO categoria (nombre, fechacreacion, estado) VALUES ('$nombre', CURRENT_TIMESTAMP, '1')";
        return ejecutarConsulta($sql);
    }

    public function editar($idcategoria, $nombre){
        $sql = "UPDATE categoria SET nombre = '$nombre' WHERE idcategoria = '$idcategoria'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idcategoria){
        $sql = "UPDATE categoria SET estado = '0' WHERE idcategoria = '$idcategoria'";
        return ejecutarConsulta($sql);
    }

    public function activar($idcategoria){
        $sql = "UPDATE categoria SET estado = '1' WHERE idcategoria = '$idcategoria'";
        return ejecutarConsulta($sql);
    }

    //metodo para mostrar registros
    public function mostrar($idcategoria){
        $sql = "SELECT * FROM categoria WHERE idcategoria = '$idcategoria'";
        return ejecutarConsultaSimpleFila($sql);
    }

    //listar registros
    public function listar(){
        $sql = "SELECT * FROM categoria";
        return ejecutarConsulta($sql);
    }

    //listar y mostrar en select
    public function select(){
        $sql = "SELECT * FROM categoria WHERE estado = 1";
        return ejecutarConsulta($sql);
    }
}
?>