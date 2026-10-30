<?php
require_once "../config/auth.php";
requireAuth();
require_once "../modelos/Empresa.php";

$empresa = new Empresa();

$idempresa = isset($_POST["idempresa"]) ? limpiarCadena($_POST["idempresa"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$nit = isset($_POST["nit"]) ? limpiarCadena($_POST["nit"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        requerirPermiso('configuracion');
        verificarCsrf();
        // Validar NIT único
        $nitExiste = $empresa->verificarNit($nit);
        if(empty($idempresa) && $nitExiste->num_rows > 0){
            echo "El NIT ya está registrado en el sistema";
            break;
        }

        if (empty($idempresa)) {
            $rspta = $empresa->insertar($nombre, $nit, $direccion, $telefono, $email, $imagen);
            echo $rspta ? "Empresa registrada correctamente" : "No se pudo registrar la empresa";
        } else {
            $rspta = $empresa->editar($idempresa, $nombre, $nit, $direccion, $telefono, $email, $imagen);
            echo $rspta ? "Empresa actualizada correctamente" : "No se pudo actualizar la empresa";
        }
        break;
    
    case 'desactivar':
        requerirPermiso('configuracion');
        verificarCsrf();
        $rspta = $empresa->desactivar((int) $idempresa);
        echo $rspta ? "Empresa desactivada correctamente" : "No se pudo desactivar la empresa";
        break;

    case 'activar':
        requerirPermiso('configuracion');
        verificarCsrf();
        $rspta = $empresa->activar((int) $idempresa);
        echo $rspta ? "Empresa activada correctamente" : "No se pudo activar la empresa";
        break;

    case 'mostrar':
        requerirPermiso('configuracion');
        $rspta = $empresa->mostrar((int) $idempresa);
        echo json_encode($rspta);
        break;

    case 'listar':
        requerirPermiso('configuracion');
        $rspta = $empresa->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado) ? 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idempresa.')"><i class="fa fa-pencil"></i></button>'.' '.
                    '<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idempresa.')"><i class="fa fa-close"></i></button>' : 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idempresa.')"><i class="fa fa-pencil"></i></button>'.' '.
                    '<button class="btn btn-primary btn-xs" onclick="activar('.$reg->idempresa.')"><i class="fa fa-check"></i></button>',
                "1" => htmlspecialchars($reg->nombre),
                "2" => htmlspecialchars($reg->nit),
                "3" => htmlspecialchars($reg->direccion),
                "4" => htmlspecialchars($reg->telefono),
                "5" => htmlspecialchars($reg->email),
                "6" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
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
        
    case 'select':
        $rspta = $empresa->select();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="'.$reg->idempresa.'">'.htmlspecialchars($reg->nombre).'</option>';
        }
        break;
}
?>
