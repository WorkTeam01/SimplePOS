<?php
require_once "../config/Conexion.php";

class Perfil
{
    // Constructor
    public function __construct() {}

    // Método para obtener el perfil del usuario
    public function obtenerPerfil($idusuario)
    {
        $sql = "SELECT u.idusuario, u.nombre, u.apellidopaterno, u.apellidomaterno, 
                u.tipodocumento, u.numdocumento, u.direccion, u.telefono, u.correo, 
                u.cargo, u.imagen, u.estado 
                FROM usuarios u 
                WHERE u.idusuario = '$idusuario'";
        return ejecutarConsultaSimpleFila($sql);
    }

    // Método para actualizar el perfil
    public function actualizarPerfil(
        $idusuario,
        $nombre,
        $apellidopaterno,
        $apellidomaterno,
        $tipodocumento,
        $numdocumento,
        $direccion,
        $telefono,
        $correo,
        $imagen
    ) {
        $sql = "UPDATE usuarios SET 
                nombre = '$nombre',
                apellidopaterno = '$apellidopaterno',
                apellidomaterno = '$apellidomaterno',
                tipodocumento = '$tipodocumento',
                numdocumento = '$numdocumento',
                direccion = '$direccion',
                telefono = '$telefono',
                correo = '$correo',
                imagen = '$imagen' 
                WHERE idusuario = '$idusuario'";

        return ejecutarConsulta($sql);
    }

    // Método para cambiar la contraseña
    public function cambiarClave($idusuario, $claveActual, $claveNueva)
    {
        // Primero verificamos que la clave actual sea correcta
        $sql = "SELECT clave FROM usuarios WHERE idusuario = '$idusuario'";
        $claveDB = ejecutarConsultaSimpleFila($sql);

        if ($claveDB['clave'] == hash("SHA256", $claveActual)) {
            $claveNuevaHash = hash("SHA256", $claveNueva);
            $sql = "UPDATE usuarios SET clave = '$claveNuevaHash' WHERE idusuario = '$idusuario'";
            return ejecutarConsulta($sql);
        } else {
            return false;
        }
    }
}
