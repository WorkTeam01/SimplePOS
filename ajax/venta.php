<?php
require_once "../config/auth.php";
require_once "../modelos/Venta.php";
requireAuth();

$venta = new Venta();

$idventa = isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";
$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
$idusuario = $_SESSION["idusuario"];
$metodopago = isset($_POST["metodopago"]) ? limpiarCadena($_POST["metodopago"]) : "";
$totalventa = isset($_POST["totalventa"]) ? limpiarCadena($_POST["totalventa"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        requerirPermiso('ventas');
        verificarCsrf();
        if (empty($idventa)) {
            $pagos_detalle = array();

            if (strpos($metodopago, '+') !== false) {
                $metodos = explode('+', $metodopago);

                foreach ($_POST['pagos_detalle'] as $index => $pago_data) {
                    $metodo = $metodos[$index];
                    $pago = array();

                    switch (trim($metodo)) {
                        case 'EFECTIVO':
                            $pago['idmetodo'] = 1;
                            $pago['monto'] = floatval($pago_data['monto']) ?: 0;
                            $pago['efectivo_recibido'] = floatval($pago_data['efectivo_recibido']) ?: 0;
                            $pago['efectivo_cambio'] = floatval($pago_data['efectivo_cambio']) ?: 0;
                            break;
                        case 'QR':
                            $pago['idmetodo'] = 4;
                            $pago['monto'] = floatval($pago_data['monto']) ?: 0;
                            break;
                        case 'TARJETA':
                            $pago['idmetodo'] = 2;
                            $pago['monto'] = floatval($pago_data['monto']) ?: 0;
                            break;
                        case 'TRANSFERENCIA':
                            $pago['idmetodo'] = 3;
                            $pago['monto'] = floatval($pago_data['monto']) ?: 0;
                            break;
                    }

                    $pagos_detalle[] = $pago;
                }
            } else {
                // Pago único
                $pago = array(
                    'idmetodo' => $metodopago === 'EFECTIVO' ? 1 : ($metodopago === 'TARJETA' ? 2 : ($metodopago === 'TRANSFERENCIA' ? 3 : 4)),
                    'monto' => floatval($_POST['pagos_detalle'][0]['monto']) ?: 0
                );

                if ($metodopago === 'EFECTIVO') {
                    $pago['efectivo_recibido'] = floatval($_POST['pagos_detalle'][0]['efectivo_recibido']) ?: 0;
                    $pago['efectivo_cambio'] = floatval($_POST['pagos_detalle'][0]['efectivo_cambio']) ?: 0;
                }

                $pagos_detalle[] = $pago;
            }

            $rspta = $venta->insertar(
                $idcliente,
                $idusuario,
                $totalventa,
                $metodopago,
                $_POST["idproducto"],
                $_POST["cantidad"],
                $_POST["precioventa"],
                $_POST["descuento"],
                $pagos_detalle
            );

            echo $rspta === "Agotado" ? "Agotado" : (is_numeric($rspta) ? $rspta : "0");
        }
        break;

    case 'anular':
        requerirPermiso('ventas');
        verificarCsrf();
        echo $venta->anular((int) $idventa) ? "Venta anulada correctamente" : "No se pudo anular la venta";
        break;

    case 'mostrar':
        requerirPermiso('ventas');
        $id = (int) $idventa;
        $rspta = $venta->mostrar($id);
        $detalle_pagos = $venta->listarPagosVenta($id);
        $pagos_data = array();
        while ($pago = $detalle_pagos->fetch_object()) {
            $pagos_data[] = $pago;
        }
        $rspta['pagos'] = $pagos_data;
        echo json_encode($rspta);
        break;

    case 'listarDetalle':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $rspta = $venta->listarDetalle($id);
        $total = 0;

        echo '<thead style="background-color:#A9D0F5">
                <th>Opciones</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Venta</th>
                <th>Descuento</th>
                <th>Subtotal</th>
              </thead>';

        while ($reg = $rspta->fetch_object()) {
            echo '<tr class="filas">
                    <td></td>
                    <td>' . htmlspecialchars($reg->nombre, ENT_QUOTES) . '</td>
                    <td>' . htmlspecialchars($reg->cantidad, ENT_QUOTES) . '</td>
                    <td>' . htmlspecialchars($reg->precioventa, ENT_QUOTES) . '</td>
                    <td>' . htmlspecialchars($reg->descuento, ENT_QUOTES) . '</td>
                    <td>' . htmlspecialchars($reg->subtotal, ENT_QUOTES) . '</td>
                  </tr>';
            $total += ($reg->precioventa * $reg->cantidad - $reg->descuento);
        }

        echo '<tfoot>
                <th>TOTAL</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th><h4 id="total">S/. ' . $total . '</h4><input type="hidden" name="totalventa" id="totalventa"></th>
              </tfoot>';
        break;

    case 'listar':
        requerirPermiso('ventas');
        $rspta = $venta->listar();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => (($reg->estado == '1') ?
                    '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>' . ' ' .
                    '<button class="btn btn-danger btn-xs" onclick="anular(' . $reg->idventa . ')"><i class="fa fa-close"></i></button>' :
                    '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>') .
                    '<a target="_blank" href="../reportes/rptventas.php?id=' . $reg->idventa . '"> <button class="btn btn-info btn-xs"><i class="fa fa-file"></i></button></a>',
                "1" => $reg->fecha,
                "2" => htmlspecialchars($reg->cliente),
                "3" => htmlspecialchars($reg->usuario),
                "4" => htmlspecialchars($reg->metodopago),
                "5" => $reg->totalventa,
                "6" => ($reg->estado == '1') ? '<span class="label bg-green">Aceptado</span>' : '<span class="label bg-red">Anulado</span>'
            );
        }

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
        break;

    case 'selectCliente':
        require_once "../modelos/Persona.php";
        $persona = new Persona();
        $rspta = $persona->listarc();

        while ($reg = $rspta->fetch_object()) {
            echo '<option value=' . $reg->idpersona . '>' . htmlspecialchars($reg->nombre . ' ' . $reg->apellidopaterno . ' ' . $reg->apellidomaterno) . '</option>';
        }
        break;

    case 'listarProductos':
        require_once "../modelos/Producto.php";
        $producto = new Producto();
        $rspta = $producto->listarActivosVenta();
        $data = array();

        while ($reg = $rspta->fetch_object()) {
            $disabled = ($reg->stock <= 0) ? 'disabled' : '';
            $tooltip = ($reg->stock <= 0) ? 'title="Producto agotado"' : '';

            $data[] = array(
                "0" => '<button class="btn btn-warning" ' . $disabled . ' ' . $tooltip . ' onclick="agregarDetalle(' . $reg->idproducto . ',\'' . addslashes(htmlspecialchars($reg->nombre)) . '\',' . $reg->precioventa . ')">
                        <span class="fa fa-plus"></span></button>',
                "1" => htmlspecialchars($reg->nombre),
                "2" => htmlspecialchars($reg->categoria),
                "3" => htmlspecialchars($reg->codigo),
                "4" => $reg->stock,
                "5" => $reg->precioventa,
                "6" => "<img src='".(!empty($reg->imagen) ? '../files/productos/'.htmlspecialchars($reg->imagen) : '../public/img/default-50x50.gif')."' height='50px' width='50px'>"
            );
        }

        echo json_encode(array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ));
        break;

    case 'listarMetodosPago':
        $rspta = $venta->listarMetodosPago();
        while ($reg = $rspta->fetch_object()) {
            echo '<option value="' . htmlspecialchars($reg->nombre, ENT_QUOTES) . '">' . htmlspecialchars($reg->nombre) . '</option>';
        }
        break;
}
