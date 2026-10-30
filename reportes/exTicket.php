<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();

if (!isset($_SESSION['nombre'])) {
    exit("Debe ingresar al sistema correctamente para visualizar el reporte");
}

if ($_SESSION['ventas'] != 1) {
    exit("No tiene permiso para visualizar el reporte");
}

require_once "../modelos/Venta.php";
require_once "../modelos/Empresa.php";

$venta = new Venta();
$empresa = new Empresa();

// Obtener ID de venta (siempre entero para evitar inyección SQL)
$idventa = isset($_GET["id"]) ? (int) $_GET["id"] : exit("ID de venta no especificado");

// 1. Obtener datos de la venta
$datosVenta = $venta->ventacabecera($idventa);
if (!$datosVenta || $datosVenta->num_rows == 0) {
    exit("No se encontró la venta especificada");
}
$venta_data = $datosVenta->fetch_object();

// 2. Obtener detalles de la venta
$detallesVenta = $venta->ventadetalles($idventa);
if (!$detallesVenta) {
    exit("Error al obtener los detalles de la venta");
}

// 3. Obtener datos de la empresa
$datosEmpresa = $empresa->mostrar(1); // ID de empresa = 1
if (!$datosEmpresa) {
    exit("Error al obtener datos de la empresa");
}

// Calcular totales
$cantidad_total = 0;
$subtotal = 0;
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Comprobante de Venta <?php echo isset($venta_data->serie_comprobante) ? $venta_data->serie_comprobante . "-" . $venta_data->num_comprobante : ''; ?></title>
    <style>
        @page {
            size: auto;
            margin: 0mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 5px;
            width: 80mm;
        }

        .ticket {
            width: 100%;
            max-width: 80mm;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #eee;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }

        .logo {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .titulo {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
            text-align: center;
        }

        .info-venta table {
            width: 100%;
            margin-bottom: 10px;
        }

        .detalle-productos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .detalle-productos th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 3px 0;
        }

        .detalle-productos td {
            padding: 3px 0;
            vertical-align: top;
        }

        .totales {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }
    </style>
</head>

<body onload="window.print(); setTimeout(window.close, 100);">
    <div class="ticket">
        <!-- Encabezado con datos de la empresa -->
        <div class="header">
            <div class="logo"><?php echo strtoupper($datosEmpresa['nombre']); ?></div>
            <div><?php echo "RUC: " . $datosEmpresa['nit']; ?></div>
            <div><?php echo $datosEmpresa['direccion']; ?></div>
            <div><?php echo "Tel: " . $datosEmpresa['telefono']; ?></div>
            <div><?php echo "Email: " . $datosEmpresa['email']; ?></div>
        </div>

        <div class="titulo">COMPROBANTE DE VENTA</div>

        <!-- Información de la venta -->
        <div class="info-venta">
            <table>
                <tr>
                    <td width="30%"><strong>Fecha:</strong></td>
                    <td><?php echo isset($venta_data->fechacreacion) ? date('d/m/Y H:i:s', strtotime($venta_data->fechacreacion)) : date('d/m/Y H:i:s'); ?></td>
                </tr>
                <tr>
                    <td><strong>Cliente:</strong></td>
                    <td><?php echo isset($venta_data->cliente) ? htmlspecialchars($venta_data->cliente) : 'NO ESPECIFICADO'; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo isset($venta_data->tipo_documento) ? $venta_data->tipo_documento : 'DOC'; ?>:</strong></td>
                    <td><?php echo isset($venta_data->numdocumento) ? $venta_data->numdocumento : '00000000'; ?></td>
                </tr>
                <?php if (isset($venta_data->serie_comprobante) && isset($venta_data->num_comprobante)): ?>
                    <tr>
                        <td><strong>Comprobante:</strong></td>
                        <td><?php echo $venta_data->serie_comprobante . "-" . $venta_data->num_comprobante; ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td><strong>Método Pago:</strong></td>
                    <td><?php echo isset($venta_data->metodopago) ? strtoupper($venta_data->metodopago) : 'EFECTIVO'; ?></td>
                </tr>
            </table>
        </div>

        <!-- Detalle de productos -->
        <table class="detalle-productos">
            <thead>
                <tr>
                    <th width="15%">Cant</th>
                    <th width="45%">Descripción</th>
                    <th width="20%" class="text-right">P.Unit.</th>
                    <th width="20%" class="text-right">Importe</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($detallesVenta->num_rows > 0) {
                    while ($producto = $detallesVenta->fetch_object()):
                        $cantidad_total += $producto->cantidad;
                        $subtotal += ($producto->precioventa * $producto->cantidad) - (isset($producto->descuento) ? $producto->descuento : 0);
                ?>
                        <tr>
                            <td><?php echo $producto->cantidad; ?></td>
                            <td><?php echo isset($producto->producto) ? htmlspecialchars($producto->producto) : 'Producto no especificado'; ?></td>
                            <td class="text-right">S/. <?php echo number_format($producto->precioventa, 2); ?></td>
                            <td class="text-right">S/. <?php echo number_format(($producto->precioventa * $producto->cantidad) - (isset($producto->descuento) ? $producto->descuento : 0), 2); ?></td>
                        </tr>
                <?php
                    endwhile;
                } else {
                    echo '<tr><td colspan="4" class="text-center">No hay productos en esta venta</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totales">
            <table width="100%">
                <tr>
                    <td width="60%"><strong>Subtotal:</strong></td>
                    <td width="40%" class="text-right">S/. <?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <tr>
                    <td><strong>IGV (18%):</strong></td>
                    <td class="text-right">S/. <?php echo number_format($subtotal * 0.18, 2); ?></td>
                </tr>
                <tr>
                    <td class="text-bold">TOTAL:</td>
                    <td class="text-right text-bold">S/. <?php echo isset($venta_data->totalventa) ? number_format($venta_data->totalventa, 2) : number_format($subtotal * 1.18, 2); ?></td>
                </tr>
                <?php if (isset($venta_data->pagorecibido)): ?>
                    <tr>
                        <td><strong>Pago Recibido:</strong></td>
                        <td class="text-right">S/. <?php echo number_format($venta_data->pagorecibido, 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cambio:</strong></td>
                        <td class="text-right">S/. <?php echo number_format($venta_data->cambio, 2); ?></td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <div>Artículos vendidos: <?php echo $cantidad_total; ?></div>
            <div style="margin: 5px 0;">¡Gracias por su compra!</div>
            <div><?php echo $datosEmpresa['nombre']; ?></div>
            <div>Fecha impresión: <?php echo date('d/m/Y H:i:s'); ?></div>
        </div>
    </div>
</body>

</html>
<?php
ob_end_flush();
?>