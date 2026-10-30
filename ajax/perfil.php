<?php
require_once "../config/auth.php";
iniciarSesionSegura();
require_once "../modelos/Perfil.php";

// Verificar si el usuario está logueado
requiereAutenticacion();

$perfil = new Perfil();
$idusuario = $_SESSION['idusuario'];

$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$apellidopaterno = isset($_POST["apellidopaterno"]) ? limpiarCadena($_POST["apellidopaterno"]) : "";
$apellidomaterno = isset($_POST["apellidomaterno"]) ? limpiarCadena($_POST["apellidomaterno"]) : "";
$tipodocumento = isset($_POST["tipodocumento"]) ? limpiarCadena($_POST["tipodocumento"]) : "";
$numdocumento = isset($_POST["numdocumento"]) ? limpiarCadena($_POST["numdocumento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$correo = isset($_POST["correo"]) ? limpiarCadena($_POST["correo"]) : "";
$claveActual = isset($_POST["claveActual"]) ? limpiarCadena($_POST["claveActual"]) : "";
$claveNueva = isset($_POST["claveNueva"]) ? limpiarCadena($_POST["claveNueva"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

switch ($_GET["op"]) {
    case 'obtener':
        $rspta = $perfil->obtenerPerfil($idusuario);
        echo json_encode($rspta);
        break;

    case 'actualizar':
        verificarCsrf();
        if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            $imagen = limpiarCadena($_POST["imagenactual"]);
        } else {
            $tipos_permitidos = ["image/jpeg" => "jpg", "image/png" => "png"];
            $info = getimagesize($_FILES["imagen"]["tmp_name"]);
            if ($info !== false && isset($tipos_permitidos[$info['mime']])) {
                $imagen = round(microtime(true)) . '.' . $tipos_permitidos[$info['mime']];
                move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/usuarios/" . $imagen);
            }
        }

        $rspta = $perfil->actualizarPerfil(
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
        );

        // Actualizar datos de sesión
        if ($rspta) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['apellidopaterno'] = $apellidopaterno;
            $_SESSION['apellidomaterno'] = $apellidomaterno;
            $_SESSION['imagen'] = $imagen;
            $_SESSION['numdocumento'] = $numdocumento;
        }

        echo $rspta ? "Perfil actualizado correctamente" : "No se pudo actualizar el perfil";
        break;

    case 'cambiarClave':
        verificarCsrf();
        $rspta = $perfil->cambiarClave($idusuario, $claveActual, $claveNueva);
        echo $rspta ? "Contraseña actualizada correctamente" : "La contraseña actual es incorrecta";
        break;
}
