<?php
require_once "../config/auth.php";
requireAuth();
require_once "../modelos/Sucursal.php";

$sucursal = new Sucursal();

$idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$idempresa = isset($_POST["idempresa"]) ? limpiarCadena($_POST["idempresa"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        requerirPermiso('configuracion');
        verificarCsrf();
        if (empty($idsucursal)) {
            $rspta = $sucursal->insertar($nombre, $idempresa);
            echo $rspta ? "Sucursal registrada correctamente" : "No se pudo registrar la sucursal";
        } else {
            $rspta = $sucursal->editar((int) $idsucursal, $nombre, $idempresa);
            echo $rspta ? "Sucursal actualizada correctamente" : "No se pudo actualizar la sucursal";
        }
        break;

    case 'desactivar':
        requerirPermiso('configuracion');
        verificarCsrf();
        $rspta = $sucursal->desactivar((int) $idsucursal);
        echo $rspta ? "Sucursal desactivada correctamente" : "No se pudo desactivar la sucursal";
        break;

    case 'activar':
        requerirPermiso('configuracion');
        verificarCsrf();
        $rspta = $sucursal->activar((int) $idsucursal);
        echo $rspta ? "Sucursal activada correctamente" : "No se pudo activar la sucursal";
        break;

    case 'mostrar':
        requerirPermiso('configuracion');
        $rspta = $sucursal->mostrar((int) $idsucursal);
        echo json_encode($rspta);
        break;

    case 'listar':
        requerirPermiso('configuracion');
        $rspta = $sucursal->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado) ? 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idsucursal.')"><i class="fa fa-pencil"></i></button>'.' '.
                    '<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idsucursal.')"><i class="fa fa-close"></i></button>' : 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idsucursal.')"><i class="fa fa-pencil"></i></button>'.' '.
                    '<button class="btn btn-primary btn-xs" onclick="activar('.$reg->idsucursal.')"><i class="fa fa-check"></i></button>',
                "1" => htmlspecialchars($reg->nombre),
                "2" => htmlspecialchars($reg->empresa), // Assuming this comes from the JOIN in the listar() method
                "3" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
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
        
    case 'selectEmpresa':
        require_once "../modelos/Empresa.php";
        $empresa = new Empresa();
        $rspta = $empresa->select();
        echo '<option value="">Seleccione una empresa</option>';
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="'.$reg->idempresa.'">'.htmlspecialchars($reg->nombre).'</option>';
        }
        break;
        
    case 'listarPorEmpresa':
        $idempresa = isset($_POST["idempresa"]) ? limpiarCadena($_POST["idempresa"]) : "";
        $rspta = $sucursal->listarPorEmpresa($idempresa);
        echo '<option value="">Seleccione una sucursal</option>';
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="'.$reg->idsucursal.'">'.htmlspecialchars($reg->nombre).'</option>';
        }
        break;
}
?>
