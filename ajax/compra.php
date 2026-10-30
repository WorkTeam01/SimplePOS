<?php
require_once "../config/auth.php";
require_once "../modelos/Compra.php";
requireAuth();

$compra = new Compra();

$idcompra = isset($_POST["idcompra"]) ? limpiarCadena($_POST["idcompra"]) : "";
$idproveedor = isset($_POST["idproveedor"]) ? limpiarCadena($_POST["idproveedor"]) : "";
$idusuario = $_SESSION["idusuario"];
$total_compra = isset($_POST["total_compra"]) ? limpiarCadena($_POST["total_compra"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        requerirPermiso('compras');
        verificarCsrf();
        if (empty($idcompra)) {
            $rspta = $compra->insertar(
                $idproveedor,
                $idusuario,
                $total_compra,
                $_POST["idproducto"],
                $_POST["cantidad"],
                $_POST["precio_compra"]
            );
            echo $rspta ? "Compra registrada correctamente" : "No se pudo registrar la compra";
        }
        break;

    case 'anular':
        requerirPermiso('compras');
        verificarCsrf();
        $rspta = $compra->anular((int) $idcompra);
        echo $rspta ? "Compra anulada correctamente" : "No se pudo anular la compra";
        break;

    case 'activar':
        requerirPermiso('compras');
        verificarCsrf();
        $rspta = $compra->activar((int) $idcompra);
        echo $rspta ? "Compra activada correctamente" : "No se pudo activar la compra";
        break;

    case 'mostrar':
        requerirPermiso('compras');
        $rspta = $compra->mostrar((int) $idcompra);
        echo json_encode($rspta);
        break;

    case 'listarDetalle':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $rspta = $compra->listarDetalle($id);
        $total = 0;

        echo '<thead style="background-color:#A9D0F5">
                <th>Opciones</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Compra</th>
                <th>Subtotal</th>
              </thead>';

        while ($reg = $rspta->fetch_object()) {
            echo '<tr class="filas">
                    <td></td>
                    <td>' . htmlspecialchars($reg->nombre, ENT_QUOTES) . '</td>
                    <td>' . htmlspecialchars($reg->cantidad, ENT_QUOTES) . '</td>
                    <td>' . htmlspecialchars($reg->precio_compra, ENT_QUOTES) . '</td>
                    <td>' . htmlspecialchars($reg->precio_compra * $reg->cantidad, ENT_QUOTES) . '</td>
                  </tr>';
            $total = $total + ($reg->precio_compra * $reg->cantidad);
        }

        echo '<tfoot>
                <th>TOTAL</th>
                <th></th>
                <th></th>
                <th></th>
                <th><h4 id="total">S/. ' . $total . '</h4><input type="hidden" name="total_compra" id="total_compra"></th>
              </tfoot>';
        break;

    case 'listar':
        requerirPermiso('compras');
        $rspta = $compra->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->estado == 'Aceptado')
                    ? '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idcompra . ')"><i class="fa fa-eye"></i></button>' . ' '
                    . '<button class="btn btn-danger btn-xs" onclick="anular(' . $reg->idcompra . ')"><i class="fa fa-close"></i></button>'
                    : '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idcompra . ')"><i class="fa fa-eye"></i></button>' . ' '
                    . '<button class="btn btn-success btn-xs" onclick="activar(' . $reg->idcompra . ')"><i class="fa fa-check"></i></button>',
                "1" => $reg->fecha,
                "2" => htmlspecialchars($reg->proveedor),
                "3" => htmlspecialchars($reg->usuario),
                "4" => $reg->total,
                "5" => ($reg->estado == 'Aceptado')
                    ? '<span class="label bg-green">Aceptado</span>'
                    : '<span class="label bg-red">Anulado</span>'
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

    case 'selectProveedor':
        require_once "../modelos/Persona.php";
        $persona = new Persona();
        $rspta = $persona->listarp();

        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . $reg->idpersona . '">' . htmlspecialchars($reg->nombre) . ' ' . htmlspecialchars($reg->apellidopaterno) . '</option>';
        }
        break;

    case 'listarProductos':
        require_once "../modelos/Producto.php";
        $producto = new Producto();
        $rspta = $producto->listarActivos();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => '<button class="btn btn-warning" onclick="agregarDetalle(' . $reg->idproducto . ',\'' . addslashes(htmlspecialchars($reg->nombre)) . '\')">
                        <span class="fa fa-plus"></span></button>',
                "1" => htmlspecialchars($reg->nombre),
                "2" => htmlspecialchars($reg->codigo),
                "3" => $reg->stock,
                "4" => "<img src='".(!empty($reg->imagen) ? '../files/productos/'.htmlspecialchars($reg->imagen, ENT_QUOTES) : '../public/img/default-50x50.gif')."' height='50px' width='50px'>"
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
    case 'obtenerPrecioProducto':
        require_once "../modelos/Producto.php";
        $producto = new Producto();
        $idproducto = isset($_POST['idproducto']) ? limpiarCadena($_POST['idproducto']) : '';
        $rspta = $producto->mostrar($idproducto);
        echo json_encode($rspta);
        break;
}
