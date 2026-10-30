<?php
//incluir la conexión de base de datos
require "../config/Conexion.php";

class Usuario
{

    //implementamos nuestro constructor
    public function __construct() {}

    //método para insertar registro
    public function insertar($nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $correo, $cargo, $clave, $imagen, $permisos)
    {
        $sql = "INSERT INTO usuarios (nombre, apellidopaterno, apellidomaterno, tipodocumento, numdocumento, direccion, telefono, correo, cargo, clave, imagen, estado) 
                VALUES ('$nombre', '$apellidopaterno', '$apellidomaterno', '$tipodocumento', '$numdocumento', '$direccion', '$telefono', '$correo', '$cargo', '$clave', '$imagen', '1')";

        $idusuarionew = ejecutarConsulta_retornarID($sql);
        $num_elementos = 0;
        $sw = true;

        while ($num_elementos < count($permisos)) {
            $sql_detalle = "INSERT INTO rol (idpermiso, idusuario) VALUES('$permisos[$num_elementos]', '$idusuarionew')";
            ejecutarConsulta($sql_detalle) or $sw = false;
            $num_elementos++;
        }
        return $sw;
    }

    //método para editar registro
    public function editar($idusuario, $nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $correo, $cargo, $clave, $imagen, $permisos)
    {
        $sql = "UPDATE usuarios SET 
                nombre = '$nombre',
                apellidopaterno = '$apellidopaterno',
                apellidomaterno = '$apellidomaterno',
                tipodocumento = '$tipodocumento',
                numdocumento = '$numdocumento',
                direccion = '$direccion',
                telefono = '$telefono',
                correo = '$correo',
                cargo = '$cargo',
                clave = '$clave',
                imagen = '$imagen' 
                WHERE idusuario = '$idusuario'";

        ejecutarConsulta($sql);

        //eliminar permisos asignados para volver a insertarlos
        $sqldel = "DELETE FROM rol WHERE idusuario = '$idusuario'";
        ejecutarConsulta($sqldel);

        $num_elementos = 0;
        $sw = true;
        while ($num_elementos < count($permisos)) {
            $sql_detalle = "INSERT INTO rol (idpermiso, idusuario) VALUES('$permisos[$num_elementos]', '$idusuario')";
            ejecutarConsulta($sql_detalle) or $sw = false;
            $num_elementos++;
        }
        return $sw;
    }

    //método para desactivar usuario
    public function desactivar($idusuario)
    {
        $idusuario = (int) $idusuario;
        $sql = "UPDATE usuarios SET estado = '0' WHERE idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }

    //método para activar usuario
    public function activar($idusuario)
    {
        $idusuario = (int) $idusuario;
        $sql = "UPDATE usuarios SET estado = '1' WHERE idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }

    //método para mostrar datos de un usuario específico
    public function mostrar($idusuario)
    {
        $idusuario = (int) $idusuario;
        $sql = "SELECT * FROM usuarios WHERE idusuario = '$idusuario'";
        return ejecutarConsultaSimpleFila($sql);
    }

    //método para listar todos los usuarios
    public function listar()
    {
        $sql = "SELECT * FROM usuarios";
        return ejecutarConsulta($sql);
    }

    //método para listar los permisos marcados de un usuario
    public function listarmarcados($idusuario)
    {
        $idusuario = (int) $idusuario;
        $sql = "SELECT p.idpermiso, p.nombre as permiso 
              FROM rol r 
              INNER JOIN permiso p ON r.idpermiso = p.idpermiso 
              WHERE r.idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }

    //función para verificar el acceso al sistema
    //Devuelve la fila (incluyendo clave) para que el controlador verifique con password_verify.
    public function verificar($numdocumento)
    {
        $sql = "SELECT idusuario, nombre, apellidopaterno, apellidomaterno, tipodocumento, numdocumento, telefono, correo, cargo, imagen, estado, clave
              FROM usuarios 
              WHERE numdocumento = '$numdocumento'";
        return ejecutarConsulta($sql);
    }

    //actualiza únicamente el hash de la contraseña (para migración legacy -> password_hash)
    public function actualizarClaveHash($idusuario, $clavehash)
    {
        $idusuario = (int) $idusuario;
        $sql = "UPDATE usuarios SET clave = '$clavehash' WHERE idusuario = '$idusuario'";
        return ejecutarConsulta($sql);
    }
}
