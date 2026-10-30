<?php
require_once "../config/auth.php";
requireAuth();
require_once "../modelos/Categoria.php";

$categoria = new Categoria();

$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        requerirPermiso('almacen');
        verificarCsrf();
        if (empty($idcategoria)) {
            $rspta = $categoria->insertar($nombre);
            echo $rspta ? "Categoría registrada correctamente" : "No se pudo registrar la categoría";
        } else {
            $rspta = $categoria->editar((int) $idcategoria, $nombre);
            echo $rspta ? "Categoría actualizada correctamente" : "No se pudo actualizar la categoría";
        }
        break;

    case 'desactivar':
        requerirPermiso('almacen');
        verificarCsrf();
        $rspta = $categoria->desactivar((int) $idcategoria);
        echo $rspta ? "Categoría desactivada correctamente" : "No se pudo desactivar la categoría";
        break;

    case 'activar':
        requerirPermiso('almacen');
        verificarCsrf();
        $rspta = $categoria->activar((int) $idcategoria);
        echo $rspta ? "Categoría activada correctamente" : "No se pudo activar la categoría";
        break;

    case 'mostrar':
        requerirPermiso('almacen');
        $rspta = $categoria->mostrar((int) $idcategoria);
        echo json_encode($rspta);
        break;

    case 'listar':
        requerirPermiso('almacen');
        $rspta = $categoria->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado) ? 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idcategoria.')"><i class="fa fa-pencil"></i></button>'.' '.
                    '<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idcategoria.')"><i class="fa fa-close"></i></button>' : 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idcategoria.')"><i class="fa fa-pencil"></i></button>'.' '.
                    '<button class="btn btn-primary btn-xs" onclick="activar('.$reg->idcategoria.')"><i class="fa fa-check"></i></button>',
                "1" => htmlspecialchars($reg->nombre),
                "2" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
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
?>
