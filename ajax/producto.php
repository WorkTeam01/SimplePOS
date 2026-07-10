<?php
require_once "../config/auth.php";
requireAuth();
require_once "../modelos/Producto.php";

$producto = new Producto();

$idproducto = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";
$idcategoria = isset($_POST["idcategoria"]) ? limpiarCadena($_POST["idcategoria"]) : "";
$codigo = isset($_POST["codigo"]) ? limpiarCadena($_POST["codigo"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$stock = isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";
$preciocompra = isset($_POST["preciocompra"]) ? limpiarCadena($_POST["preciocompra"]) : "";
$precioventa = isset($_POST["precioventa"]) ? limpiarCadena($_POST["precioventa"]) : "";
$idsucursal = isset($_POST["idsucursal"]) ? limpiarCadena($_POST["idsucursal"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            $imagen = limpiarCadena($_POST["imagenactual"]);
        } else {
            $tipos_permitidos = ["image/jpeg" => "jpg", "image/png" => "png"];
            $info = getimagesize($_FILES["imagen"]["tmp_name"]);
            if ($info !== false && isset($tipos_permitidos[$info['mime']])) {
                $imagen = round(microtime(true)) . '.' . $tipos_permitidos[$info['mime']];
                move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/productos/" . $imagen);
            }
        }
        if (empty($idproducto)) {
            $rspta = $producto->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen, $preciocompra, $precioventa, $idsucursal);
            echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
        } else {
            $rspta = $producto->editar($idproducto, $idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen, $preciocompra, $precioventa, $idsucursal);
            echo $rspta ? "Datos actualizados correctamente" : "No se pudo actualizar los datos";
        }
        break;

    case 'desactivar':
        $rspta = $producto->desactivar($idproducto);
        echo $rspta ? "Producto desactivado correctamente" : "No se pudo desactivar el producto";
        break;

    case 'activar':
        $rspta = $producto->activar($idproducto);
        echo $rspta ? "Producto activado correctamente" : "No se pudo activar el producto";
        break;

    case 'mostrar':
        $rspta = $producto->mostrar($idproducto);
        echo json_encode($rspta);
        break;

    case 'listar':
        $rspta = $producto->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado) ? 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idproducto.')"><i class="fa fa-pencil"></i></button> ' .
                    '<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->idproducto.')"><i class="fa fa-close"></i></button>' : 
                    '<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->idproducto.')"><i class="fa fa-pencil"></i></button> ' .
                    '<button class="btn btn-primary btn-xs" onclick="activar('.$reg->idproducto.')"><i class="fa fa-check"></i></button>',
                "1" => htmlspecialchars($reg->nombre),
                "2" => htmlspecialchars($reg->categoria),
                "3" => htmlspecialchars($reg->codigo),
                "4" => $reg->stock,
                "5" => htmlspecialchars($reg->sucursal),
                "6" => "S/.".$reg->preciocompra,
                "7" => "S/.".$reg->precioventa,
                "8" => "<img src='".(!empty($reg->imagen) ? '../files/productos/'.htmlspecialchars($reg->imagen) : '../public/img/default-50x50.gif')."' height='50px' width='50px'>",
                "9" => htmlspecialchars($reg->descripcion),
                "10" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
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

    case 'selectCategoria':
        require_once "../modelos/Categoria.php";
        $categoria = new Categoria();
        $rspta = $categoria->select();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->idcategoria . '">' . htmlspecialchars($reg->nombre) . '</option>';
        }
        break;

    
        case 'selectSucursal':
            require_once "../modelos/Sucursal.php";
            $sucursal = new Sucursal();
            $rspta = $sucursal->select();
            while ($reg = $rspta->fetch_object()) {
                echo '<option value="' . $reg->idsucursal . '">' . htmlspecialchars($reg->nombre) . '</option>';
            }
            break;


}
?>