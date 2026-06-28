<?php 
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
        $rspta = $empresa->desactivar($idempresa);
        echo $rspta ? "Empresa desactivada correctamente" : "No se pudo desactivar la empresa";
        break;
        
    case 'activar':
        $rspta = $empresa->activar($idempresa);
        echo $rspta ? "Empresa activada correctamente" : "No se pudo activar la empresa";
        break;
    
    case 'mostrar':
        $rspta = $empresa->mostrar($idempresa);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $empresa->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado) ? 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idempresa.')"><i class="fa fa-pencil"></i></button>'.' '.
                    '<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idempresa.')"><i class="fa fa-close"></i></button>' : 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idempresa.')"><i class="fa fa-pencil"></i></button>'.' '.
                    '<button class="btn btn-primary btn-xs" onclick="activar('.$reg->idempresa.')"><i class="fa fa-check"></i></button>',
                "1" => $reg->nombre,
                "2" => $reg->nit,
                "3" => $reg->direccion,
                "4" => $reg->telefono,
                "5" => $reg->email,
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
            echo '<option value="'.$reg->idempresa.'">'.$reg->nombre.'</option>';
        }
        break;
}
?>