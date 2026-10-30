<?php
require_once "../config/auth.php";
iniciarSesionSegura();
require_once "../modelos/Usuario.php";

const PERMISO_ESCRITORIO = 1;
const PERMISO_ALMACEN = 2;
const PERMISO_COMPRAS = 3;
const PERMISO_VENTAS = 4;
const PERMISO_ACCESO = 5;
const PERMISO_CONSULTAC = 6;
const PERMISO_CONSULTAV = 7;
const PERMISO_CONFIGURACION = 8;

$usuario = new Usuario();

$idusuario = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$apellidopaterno = isset($_POST["apellidopaterno"]) ? limpiarCadena($_POST["apellidopaterno"]) : "";
$apellidomaterno = isset($_POST["apellidomaterno"]) ? limpiarCadena($_POST["apellidomaterno"]) : "";
$tipodocumento = isset($_POST["tipodocumento"]) ? limpiarCadena($_POST["tipodocumento"]) : "";
$numdocumento = isset($_POST["numdocumento"]) ? limpiarCadena($_POST["numdocumento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$correo = isset($_POST["correo"]) ? limpiarCadena($_POST["correo"]) : "";
$cargo = isset($_POST["cargo"]) ? limpiarCadena($_POST["cargo"]) : "";
$clave = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        requireAuth();
        requerirPermiso('acceso');
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

        // Hash de contraseña con password_hash (bcrypt/argon2)
        $clavehash = hashPassword($clave);
        $permisos = $_POST['permiso'] ?? [];
        if (empty($idusuario)) {
            $rspta = $usuario->insertar($nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $correo, $cargo, $clavehash, $imagen, $permisos);
            echo $rspta ? "Usuario registrado correctamente" : "No se pudo registrar el usuario";
        } else {
            // Si no se cambió la contraseña, se conserva el hash actual sin re-leerlo del cliente
            if (empty($clave)) {
                $actual = $usuario->mostrar($idusuario);
                $clavehash = $actual['clave'];
            }
            $rspta = $usuario->editar($idusuario, $nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $correo, $cargo, $clavehash, $imagen, $permisos);
            echo $rspta ? "Usuario actualizado correctamente" : "No se pudo actualizar el usuario";
        }
        break;

    case 'desactivar':
        requireAuth();
        requerirPermiso('acceso');
        verificarCsrf();
        $rspta = $usuario->desactivar((int) $idusuario);
        echo $rspta ? "Usuario desactivado correctamente" : "No se pudo desactivar el usuario";
        break;

    case 'activar':
        requireAuth();
        requerirPermiso('acceso');
        verificarCsrf();
        $rspta = $usuario->activar((int) $idusuario);
        echo $rspta ? "Usuario activado correctamente" : "No se pudo activar el usuario";
        break;

    case 'mostrar':
        requireAuth();
        requerirPermiso('acceso');
        $rspta = $usuario->mostrar((int) $idusuario);
        // Nunca exponer el hash de contraseña al cliente
        if (is_array($rspta)) unset($rspta['clave']);
        echo json_encode($rspta);
        break;

    case 'listar':
        requireAuth();
        requerirPermiso('acceso');
        $rspta = $usuario->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado) ? '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idusuario . ')"><i class="fa fa-pencil"></i></button>' . ' ' . '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idusuario . ')"><i class="fa fa-close"></i></button>' : '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idusuario . ')"><i class="fa fa-pencil"></i></button>' . ' ' . '<button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idusuario . ')"><i class="fa fa-check"></i></button>',
                "1" => htmlspecialchars($reg->nombre . ' ' . $reg->apellidopaterno . ' ' . $reg->apellidomaterno),
                "2" => htmlspecialchars($reg->tipodocumento),
                "3" => htmlspecialchars($reg->numdocumento),
                "4" => htmlspecialchars($reg->telefono),
                "5" => htmlspecialchars($reg->correo),
                "6" => htmlspecialchars($reg->cargo),
                "7" => "<img src='".(!empty($reg->imagen) ? '../files/usuarios/'.htmlspecialchars($reg->imagen) : '../public/img/avatar.png')."' height='50px' width='50px'>",
                "8" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
            );
        }

        $results = array(
            "sEcho" => 1, //info para datatables
            "iTotalRecords" => count($data), //enviamos el total de registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'permisos':
        requireAuth();
        requerirPermiso('acceso');
        require_once "../modelos/Permiso.php";
        $permiso = new Permiso();
        $rspta = $permiso->listar();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $marcados = $usuario->listarmarcados($id);
        $valores = array();

        while ($per = $marcados->fetch_object()) {
            array_push($valores, $per->idpermiso);
        }

        while ($reg = $rspta->fetch_object()) {
            $sw = in_array($reg->idpermiso, $valores) ? 'checked' : '';
            echo '<li><input type="checkbox" ' . $sw . ' name="permiso[]" value="' . $reg->idpermiso . '">' . htmlspecialchars($reg->nombre) . '</li>';
        }
        break;

    case 'verificar':
        $numdocumentoa = limpiarCadena($_POST['numdocumentoa']);
        $clavea = $_POST['clavea'] ?? '';

        $rspta = $usuario->verificar($numdocumentoa);
        $fetch = $rspta->fetch_object();

        if (isset($fetch) && $fetch->estado == 1 && verifyPassword($clavea, $fetch->clave)) {
            // Migración: si el hash aún es SHA256 legacy, re-hasheamos con password_hash
            if (passwordRequiereRehash($fetch->clave)) {
                $usuario->actualizarClaveHash((int) $fetch->idusuario, hashPassword($clavea));
            }

            // Mitiga fijación de sesión: nuevo ID antes de crear la sesión autenticada
            regenerarSesion();

            $_SESSION['idusuario'] = $fetch->idusuario;
            $_SESSION['nombre'] = $fetch->nombre;
            $_SESSION['apellidopaterno'] = $fetch->apellidopaterno;
            $_SESSION['apellidomaterno'] = $fetch->apellidomaterno;
            $_SESSION['imagen'] = $fetch->imagen;
            $_SESSION['numdocumento'] = $fetch->numdocumento;
            $_SESSION['cargo'] = $fetch->cargo;
            $_SESSION['estado'] = $fetch->estado;

            // Generamos el token CSRF de esta sesión autenticada
            csrfToken();

            // Obtenemos los permisos
            $marcados = $usuario->listarmarcados($fetch->idusuario);
            $valores = array();

            while ($per = $marcados->fetch_object()) {
                array_push($valores, $per->idpermiso);
            }

            // Determinamos los accesos
            $_SESSION['escritorio'] = in_array(PERMISO_ESCRITORIO, $valores) ? 1 : 0;
            $_SESSION['almacen'] = in_array(PERMISO_ALMACEN, $valores) ? 1 : 0;
            $_SESSION['compras'] = in_array(PERMISO_COMPRAS, $valores) ? 1 : 0;
            $_SESSION['ventas'] = in_array(PERMISO_VENTAS, $valores) ? 1 : 0;
            $_SESSION['acceso'] = in_array(PERMISO_ACCESO, $valores) ? 1 : 0;
            $_SESSION['consultac'] = in_array(PERMISO_CONSULTAC, $valores) ? 1 : 0;
            $_SESSION['consultav'] = in_array(PERMISO_CONSULTAV, $valores) ? 1 : 0;
            $_SESSION['configuracion'] = in_array(PERMISO_CONFIGURACION, $valores) ? 1 : 0;
        }
        // Nunca exponer el hash de contraseña en la respuesta del login
        if (is_object($fetch)) unset($fetch->clave);
        echo json_encode($fetch);
        break;


    case 'salir':
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        break;
}
