<?php
//incluir la conexion de base de datos
require "../config/Conexion.php";

class Persona
{
    //implementamos nuestro constructor
    public function __construct() {}

    //metodo insertar registro
    public function insertar($tipopersona, $nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $email)
    {
        // Preparar los valores para la consulta SQL
        $apellidomaterno_val = $apellidomaterno === NULL ? "NULL" : "'$apellidomaterno'";
        $direccion_val = $direccion === NULL ? "NULL" : "'$direccion'";
        $telefono_val = $telefono === NULL ? "NULL" : "'$telefono'";
        $email_val = $email === NULL ? "NULL" : "'$email'";

        $sql = "INSERT INTO persona (tipopersona, nombre, apellidopaterno, apellidomaterno, tipodocumento, numdocumento, direccion, telefono, email, fechacreacion, estado) 
                VALUES ('$tipopersona', '$nombre', '$apellidopaterno', $apellidomaterno_val, '$tipodocumento', '$numdocumento', $direccion_val, $telefono_val, $email_val, CURDATE(), 1)";
        return ejecutarConsulta($sql);
    }

    public function editar($idpersona, $tipopersona, $nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $email)
    {
        // Preparar los valores para la consulta SQL
        $apellidomaterno_val = $apellidomaterno === NULL ? "NULL" : "'$apellidomaterno'";
        $direccion_val = $direccion === NULL ? "NULL" : "'$direccion'";
        $telefono_val = $telefono === NULL ? "NULL" : "'$telefono'";
        $email_val = $email === NULL ? "NULL" : "'$email'";

        $sql = "UPDATE persona SET 
                tipopersona = '$tipopersona', 
                nombre = '$nombre', 
                apellidopaterno = '$apellidopaterno', 
                apellidomaterno = $apellidomaterno_val, 
                tipodocumento = '$tipodocumento', 
                numdocumento = '$numdocumento', 
                direccion = $direccion_val, 
                telefono = $telefono_val, 
                email = $email_val 
                WHERE idpersona = '$idpersona'";
        return ejecutarConsulta($sql);
    }

    //funcion para desactivar datos (en lugar de eliminar)
    public function desactivar($idpersona)
    {
        $sql = "UPDATE persona SET estado = 0 WHERE idpersona = '$idpersona'";
        return ejecutarConsulta($sql);
    }

    //funcion para activar registros
    public function activar($idpersona)
    {
        $sql = "UPDATE persona SET estado = 1 WHERE idpersona = '$idpersona'";
        return ejecutarConsulta($sql);
    }

    //metodo para mostrar registros
    public function mostrar($idpersona)
    {
        $sql = "SELECT * FROM persona WHERE idpersona = '$idpersona'";
        return ejecutarConsultaSimpleFila($sql);
    }

    //listar registros de proveedores
    public function listarp()
    {
        $sql = "SELECT * FROM persona WHERE tipopersona = 'Proveedor' AND estado = 1";
        return ejecutarConsulta($sql);
    }

    //listar registros de clientes
    public function listarc()
    {
        $sql = "SELECT * FROM persona WHERE tipopersona = 'Cliente' AND estado = 1";
        return ejecutarConsulta($sql);
    }

    //listar todos los registros (para reportes o administración)
    public function listar()
    {
        $sql = "SELECT * FROM persona";
        return ejecutarConsulta($sql);
    }

    //verificar si existe el número de documento
    public function verificarDocumento($numdocumento, $idpersona = null)
    {
        $sql = $idpersona ?
            "SELECT * FROM persona WHERE numdocumento = '$numdocumento' AND idpersona != '$idpersona'" :
            "SELECT * FROM persona WHERE numdocumento = '$numdocumento'";
        return ejecutarConsulta($sql);
    }

    //verificar si existe el correo electrónico
    public function verificarEmail($email, $idpersona = null)
    {
        if (empty($email)) {
            return false; // Si el email está vacío, no hay duplicados
        }

        $sql = $idpersona ?
            "SELECT * FROM persona WHERE email = '$email' AND idpersona != '$idpersona'" :
            "SELECT * FROM persona WHERE email = '$email'";
        return ejecutarConsulta($sql);
    }

    // Agregar este nuevo método a la clase Persona
    public function buscarClientePublicoGeneral()
    {
        $sql = "SELECT idpersona FROM persona WHERE tipopersona = 'Cliente' AND nombre = 'Público' AND apellidopaterno = 'General' AND estado = 1";
        return ejecutarConsulta($sql);
    }
}
