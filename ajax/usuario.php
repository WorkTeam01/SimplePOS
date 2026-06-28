<?php
session_start();
require_once "../modelos/Usuario.php";

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
        if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            $imagen = $_POST["imagenactual"];
        } else {
            $ext = explode(".", $_FILES["imagen"]["name"]);
            if ($_FILES['imagen']['type'] == "image/jpg" || $_FILES['imagen']['type'] == "image/jpeg" || $_FILES['imagen']['type'] == "image/png") {
                $imagen = round(microtime(true)) . '.' . end($ext);
                move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/usuarios/" . $imagen);
            }
        }

        //Hash SHA256 para la contraseña
        $clavehash = hash("SHA256", $clave);
        if (empty($idusuario)) {
            $rspta = $usuario->insertar($nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $correo, $cargo, $clavehash, $imagen, $_POST['permiso']);
            echo $rspta ? "Usuario registrado correctamente" : "No se pudo registrar el usuario";
        } else {
            // Si no se cambió la contraseña
            if (empty($clave)) {
                $clavehash = $_POST["claveactual"];
            }
            $rspta = $usuario->editar($idusuario, $nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $correo, $cargo, $clavehash, $imagen, $_POST['permiso']);
            echo $rspta ? "Usuario actualizado correctamente" : "No se pudo actualizar el usuario";
        }
        break;

    case 'desactivar':
        $rspta = $usuario->desactivar($idusuario);
        echo $rspta ? "Usuario desactivado correctamente" : "No se pudo desactivar el usuario";
        break;

    case 'activar':
        $rspta = $usuario->activar($idusuario);
        echo $rspta ? "Usuario activado correctamente" : "No se pudo activar el usuario";
        break;

    case 'mostrar':
        $rspta = $usuario->mostrar($idusuario);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $usuario->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado) ? '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idusuario . ')"><i class="fa fa-pencil"></i></button>' . ' ' . '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idusuario . ')"><i class="fa fa-close"></i></button>' : '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idusuario . ')"><i class="fa fa-pencil"></i></button>' . ' ' . '<button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idusuario . ')"><i class="fa fa-check"></i></button>',
                "1" => $reg->nombre . ' ' . $reg->apellidopaterno . ' ' . $reg->apellidomaterno,
                "2" => $reg->tipodocumento,
                "3" => $reg->numdocumento,
                "4" => $reg->telefono,
                "5" => $reg->correo,
                "6" => $reg->cargo,
                "7" => "<img src='".(!empty($reg->imagen) ? '../files/usuarios/'.$reg->imagen : '../public/img/avatar.png')."' height='50px' width='50px'>",
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
        require_once "../modelos/Permiso.php";
        $permiso = new Permiso();
        $rspta = $permiso->listar();

        $id = $_GET['id'];
        $marcados = $usuario->listarmarcados($id);
        $valores = array();

        while ($per = $marcados->fetch_object()) {
            array_push($valores, $per->idpermiso);
        }

        while ($reg = $rspta->fetch_object()) {
            $sw = in_array($reg->idpermiso, $valores) ? 'checked' : '';
            echo '<li><input type="checkbox" ' . $sw . ' name="permiso[]" value="' . $reg->idpermiso . '">' . $reg->nombre . '</li>';
        }
        break;

    case 'verificar':
        $numdocumentoa = $_POST['numdocumentoa'];
        $clavea = $_POST['clavea'];

        $clavehash = hash("SHA256", $clavea);

        $rspta = $usuario->verificar($numdocumentoa, $clavehash);
        $fetch = $rspta->fetch_object();

        if (isset($fetch)) {
            $_SESSION['idusuario'] = $fetch->idusuario;
            $_SESSION['nombre'] = $fetch->nombre;
            $_SESSION['apellidopaterno'] = $fetch->apellidopaterno;
            $_SESSION['apellidomaterno'] = $fetch->apellidomaterno;
            $_SESSION['imagen'] = $fetch->imagen;
            $_SESSION['numdocumento'] = $fetch->numdocumento;
            $_SESSION['cargo'] = $fetch->cargo;
            $_SESSION['estado'] = $fetch->estado; // Agregamos el estado a la sesión

            // Obtenemos los permisos
            $marcados = $usuario->listarmarcados($fetch->idusuario);
            $valores = array();

            while ($per = $marcados->fetch_object()) {
                array_push($valores, $per->idpermiso);
            }

            // Determinamos los accesos
            in_array(1, $valores) ? $_SESSION['escritorio'] = 1 : $_SESSION['escritorio'] = 0;
            in_array(2, $valores) ? $_SESSION['almacen'] = 1 : $_SESSION['almacen'] = 0;
            in_array(3, $valores) ? $_SESSION['compras'] = 1 : $_SESSION['compras'] = 0;
            in_array(4, $valores) ? $_SESSION['ventas'] = 1 : $_SESSION['ventas'] = 0;
            in_array(5, $valores) ? $_SESSION['acceso'] = 1 : $_SESSION['acceso'] = 0;
            in_array(6, $valores) ? $_SESSION['consultac'] = 1 : $_SESSION['consultac'] = 0;
            in_array(7, $valores) ? $_SESSION['consultav'] = 1 : $_SESSION['consultav'] = 0;
            in_array(8, $valores) ? $_SESSION['configuracion'] = 1 : $_SESSION['configuracion'] = 0;
        }
        echo json_encode($fetch);
        break;


    case 'salir':
        session_unset();
        session_destroy();
        header("Location: ../index.php");
        break;
}
