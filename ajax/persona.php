<?php
require_once "../config/auth.php";
requireAuth();
require_once "../modelos/Persona.php";
require_once "../config/Conexion.php";

$persona = new Persona();

$idpersona = isset($_POST["idpersona"]) ? limpiarCadena($_POST["idpersona"]) : "";
$tipopersona = isset($_POST["tipopersona"]) ? limpiarCadena($_POST["tipopersona"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$apellidopaterno = isset($_POST["apellidopaterno"]) ? limpiarCadena($_POST["apellidopaterno"]) : "";
$apellidomaterno = isset($_POST["apellidomaterno"]) ? limpiarCadena($_POST["apellidomaterno"]) : "";
$tipodocumento = isset($_POST["tipodocumento"]) ? limpiarCadena($_POST["tipodocumento"]) : "";
$numdocumento = isset($_POST["numdocumento"]) ? limpiarCadena($_POST["numdocumento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        // Verificar si el documento ya existe
        $documento_existe = $persona->verificarDocumento($numdocumento, $idpersona);
        if ($documento_existe->num_rows > 0) {
            echo "El número de documento ya está registrado";
        } else {
            // Verificar si el correo ya existe (solo si no está vacío)
            if (!empty($email)) {
                $email_existe = $persona->verificarEmail($email, $idpersona);
                if ($email_existe && $email_existe->num_rows > 0) {
                    echo "El correo electrónico ya está registrado";
                    exit;
                }
            }

            if (empty($idpersona)) {
                $rspta = $persona->insertar($tipopersona, $nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $email);
                echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
            } else {
                $rspta = $persona->editar($idpersona, $tipopersona, $nombre, $apellidopaterno, $apellidomaterno, $tipodocumento, $numdocumento, $direccion, $telefono, $email);
                echo $rspta ? "Datos actualizados correctamente" : "No se pudo actualizar los datos";
            }
        }
        break;

    case 'desactivar':
        $rspta = $persona->desactivar($idpersona);
        echo $rspta ? "Persona desactivada correctamente" : "No se pudo desactivar a la persona";
        break;

    case 'activar':
        $rspta = $persona->activar($idpersona);
        echo $rspta ? "Persona activada correctamente" : "No se pudo activar a la persona";
        break;

    case 'mostrar':
        $rspta = $persona->mostrar($idpersona);
        echo json_encode($rspta);
        break;

    case 'listarp':
        $rspta = $persona->listarp();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idpersona . ')"><i class="fa fa-pencil"></i></button> ' .
                    ($reg->estado == 1 ?
                        '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idpersona . ')"><i class="fa fa-close"></i></button>' :
                        '<button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idpersona . ')"><i class="fa fa-check"></i></button>'),
                "1" => htmlspecialchars($reg->nombre . ' ' . $reg->apellidopaterno . ' ' . $reg->apellidomaterno),
                "2" => htmlspecialchars($reg->tipodocumento),
                "3" => htmlspecialchars($reg->numdocumento),
                "4" => htmlspecialchars($reg->telefono),
                "5" => htmlspecialchars($reg->email),
                "6" => ($reg->estado == 1) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
            );
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;

    case 'listarc':
        $rspta = $persona->listarc();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idpersona . ')"><i class="fa fa-pencil"></i></button> ' .
                    ($reg->estado == 1 ?
                        '<button class="btn btn-danger btn-xs" onclick="desactivar(' . $reg->idpersona . ')"><i class="fa fa-close"></i></button>' :
                        '<button class="btn btn-primary btn-xs" onclick="activar(' . $reg->idpersona . ')"><i class="fa fa-check"></i></button>'),
                "1" => htmlspecialchars($reg->nombre . ' ' . $reg->apellidopaterno . ' ' . $reg->apellidomaterno),
                "2" => htmlspecialchars($reg->tipodocumento),
                "3" => htmlspecialchars($reg->numdocumento),
                "4" => htmlspecialchars($reg->telefono),
                "5" => htmlspecialchars($reg->email),
                "6" => ($reg->estado == 1) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
            );
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );
        echo json_encode($results);
        break;
}
