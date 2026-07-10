<?php
require_once "../config/auth.php";
requireAuth();

require_once "../modelos/Traspaso.php";

$traspaso = new Traspaso();

$idusuario = $_SESSION["idusuario"];

$idtraspaso = isset($_POST["idtraspaso"]) ? limpiarCadena($_POST["idtraspaso"]) : "";
$motivo = isset($_POST["motivo"]) ? limpiarCadena($_POST["motivo"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarCadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($idtraspaso)) {
            // Verificar si hay productos en el formulario
            if (isset($_POST["idproducto"]) && isset($_POST["cantidad"])) {
                $idproducto = $_POST["idproducto"];
                $cantidad = $_POST["cantidad"];

                // Validar que el usuario esté autenticado
                if ($idusuario === null) {
                    echo "Error: Sesión no iniciada. Por favor, inicie sesión.";
                    break;
                }

                // Validar productos y stock
                $valid = true;
                $mensaje = "";

                for ($i = 0; $i < count($idproducto); $i++) {
                    // Verificar stock disponible
                    if (!$traspaso->verificarStock($idproducto[$i], $cantidad[$i])) {
                        $valid = false;
                        $mensaje = "Stock insuficiente para el producto: " . $idproducto[$i];
                        break;
                    }
                }

                if ($valid) {
                    $rspta = $traspaso->insertar($idusuario, $motivo, $observaciones, $idproducto, $cantidad);
                    echo $rspta ? "Traspaso registrado correctamente" : "No se pudieron registrar todos los productos";
                } else {
                    echo $mensaje;
                }
            } else {
                echo "No se han especificado productos para el traspaso";
            }
        }
        break;

    case 'completar':
        if (isset($_POST["iddetalletraspaso"])) {
            $iddetalletraspaso = $_POST["iddetalletraspaso"];

            $rspta = $traspaso->completar($idtraspaso, $iddetalletraspaso);
            echo $rspta ? "Traspaso completado correctamente" : "No se pudo completar el traspaso";
        } else {
            echo "No se han especificado detalles de traspaso";
        }
        break;

    case 'cancelar':
        $rspta = $traspaso->cancelar($idtraspaso);
        echo $rspta ? "Traspaso cancelado correctamente" : "No se pudo cancelar el traspaso";
        break;

    case 'mostrar':
        $rspta = $traspaso->mostrar($idtraspaso);
        echo json_encode($rspta);
        break;

    case 'listarDetalle':
        $rspta = $traspaso->listarDetalle($idtraspaso);

        $data = [];
        while ($reg = $rspta->fetch_object()) {
            $imagen = empty($reg->imagen) ?
                "../public/img/default-50x50.gif" :
                "../files/productos/" . $reg->imagen;

            $data[] = [
                "iddetalletraspaso" => $reg->iddetalletraspaso,
                "idproducto" => $reg->idproducto,
                "producto" => htmlspecialchars($reg->producto),
                "codigo" => htmlspecialchars($reg->codigo),
                "stock_actual" => $reg->stock,
                "imagen" => $imagen,
                "cantidad" => $reg->cantidad,
                "estado" => $reg->estado
            ];
        }

        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ];

        echo json_encode($results);
        break;

    case 'listar':
        $rspta = $traspaso->listar();

        $data = [];
        while ($reg = $rspta->fetch_object()) {
            $estado_clase = '';
            if ($reg->estado == 'Pendiente') $estado_clase = 'label-warning';
            else if ($reg->estado == 'Completado') $estado_clase = 'label-success';
            else $estado_clase = 'label-danger';

            $data[] = [
                "idtraspaso" => $reg->idtraspaso,
                "fecha_solicitud" => $reg->fecha_solicitud,
                "motivo" => htmlspecialchars($reg->motivo),
                "usuario" => htmlspecialchars($reg->usuario),
                "estado" => '<span class="label ' . $estado_clase . '">' . htmlspecialchars($reg->estado) . '</span>',
                "opciones" =>
                '<div class="btn-group">' .
                    '<button class="btn btn-info btn-sm" onclick="mostrar(' . $reg->idtraspaso . ')"><i class="fa fa-eye"></i></button>' .
                    ($reg->estado == 'Pendiente' ?
                        '<button class="btn btn-success btn-sm" onclick="completar(' . $reg->idtraspaso . ')"><i class="fa fa-check"></i></button>' : '') .
                    ($reg->estado == 'Pendiente' ?
                        '<button class="btn btn-danger btn-sm" onclick="cancelar(' . $reg->idtraspaso . ')"><i class="fa fa-close"></i></button>' : '') .
                    '</div>'
            ];
        }

        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ];

        echo json_encode($results);
        break;

    case 'listarProductos':
        require_once "../modelos/Producto.php";
        $producto = new Producto();

        $rspta = $producto->listarActivos();

        $data = [];
        while ($reg = $rspta->fetch_object()) {
            $imagen = empty($reg->imagen) ?
                "../public/img/default-50x50.gif" :
                "../files/productos/" . $reg->imagen;

            $data[] = [
                "opciones" => '<button class="btn btn-info" onclick="agregarDetalle(' . $reg->idproducto . ',\'' . htmlspecialchars($reg->nombre, ENT_QUOTES) . '\',' . $reg->stock . ')"><i class="fa fa-plus"></i></button>',
                "codigo" => htmlspecialchars($reg->codigo),
                "nombre" => htmlspecialchars($reg->nombre),
                "stock" => $reg->stock,
                "imagen" => '<img src="' . $imagen . '" alt="Imagen de producto" height="50" width="50" class="img-thumbnail">'
            ];
        }

        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ];

        echo json_encode($results);
        break;

    case 'dashboard':
        $rspta = $traspaso->contarTraspasosPorEstado();

        $data = [];
        while ($reg = $rspta->fetch_object()) {
            $data[$reg->estado] = $reg->total;
        }

        echo json_encode($data);
        break;

    default:
        echo 'Operación no válida';
}
