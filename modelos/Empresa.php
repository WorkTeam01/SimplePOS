<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";

class Empresa {
    //implementamos nuestro constructor
    public function __construct(){
    }

    //metodo insertar registro
    public function insertar($nombre, $nit, $direccion, $telefono, $email, $imagen){
        $sql = "INSERT INTO empresa (nombre, nit, direccion, telefono, email, imagen, fechacreacion, estado) 
                VALUES ('$nombre', '$nit', '$direccion', '$telefono', '$email', '$imagen', NOW(), '1')";
        return ejecutarConsulta($sql);
    }

    public function editar($idempresa, $nombre, $nit, $direccion, $telefono, $email, $imagen){
        $sql = "UPDATE empresa SET 
                nombre = '$nombre', 
                nit = '$nit', 
                direccion = '$direccion', 
                telefono = '$telefono', 
                email = '$email', 
                imagen = '$imagen' 
                WHERE idempresa = '$idempresa'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idempresa){
        $sql = "UPDATE empresa SET estado = '0' WHERE idempresa = '$idempresa'";
        return ejecutarConsulta($sql);
    }

    public function activar($idempresa){
        $sql = "UPDATE empresa SET estado = '1' WHERE idempresa = '$idempresa'";
        return ejecutarConsulta($sql);
    }

    //metodo para mostrar registros
    public function mostrar($idempresa){
        $sql = "SELECT * FROM empresa WHERE idempresa = '$idempresa'";
        return ejecutarConsultaSimpleFila($sql);
    }

    //listar registros
    public function listar(){
        $sql = "SELECT * FROM empresa";
        return ejecutarConsulta($sql);
    }

    //listar y mostrar en select
    public function select(){
        $sql = "SELECT * FROM empresa WHERE estado = 1";
        return ejecutarConsulta($sql);
    }
    
    //método para verificar si ya existe el NIT
    public function verificarNit($nit){
        $sql = "SELECT * FROM empresa WHERE nit = '$nit'";
        return ejecutarConsulta($sql);
    }
}
?>