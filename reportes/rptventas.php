<?php
// Asegurarse de que no haya salida antes de este punto
ob_start(); // Inicia el buffer de salida

session_start();
if (!isset($_SESSION['nombre'])) {
    die('No autorizado');
}
if ($_SESSION['ventas'] != 1) {
    die('No tiene permiso para acceder a este reporte');
}

// Definir la función obsoleta si no existe
if (!function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime()
    {
        return false;
    }
}

// Función alternativa a utf8_decode
function utf8_to_latin1($str)
{
    return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
}

// Función para detectar si es un dispositivo móvil
function isMobile()
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    return (
        strpos($userAgent, 'Android') !== false
        || strpos($userAgent, 'iPhone') !== false
        || strpos($userAgent, 'iPad') !== false
        || strpos($userAgent, 'iPod') !== false
        || strpos($userAgent, 'Opera Mini') !== false
        || strpos($userAgent, 'Mobile') !== false
        || strpos($userAgent, 'BlackBerry') !== false
        || strpos($userAgent, 'webOS') !== false
    );
}

/**
 * Convierte un número a su representación en letras (Bolivianos)
 */
function numeroALetras($cantidad)
{
    $cantidad = number_format($cantidad, 2, '.', '');
    $partes = explode('.', $cantidad);
    $entero = (int)$partes[0];
    $decimal = isset($partes[1]) ? $partes[1] : '00';

    // Moneda en singular y plural
    $monedaSingular = 'BOLIVIANO';
    $monedaPlural = 'BOLIVIANOS';

    // Unidades, decenas y centenas
    $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
    $especiales = ['', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
    $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
    $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

    // Para casos especiales
    if ($entero == 0) {
        return 'CERO ' . $monedaPlural . ' CON ' . $decimal . '/100';
    }

    $texto = '';

    // Procesar millones
    $millones = floor($entero / 1000000);
    if ($millones > 0) {
        if ($millones == 1) {
            $texto .= 'UN MILLON ';
        } else {
            $texto .= numeroALetrasInterno($millones) . ' MILLONES ';
        }
        $entero = $entero % 1000000;
    }

    // Procesar miles
    $miles = floor($entero / 1000);
    if ($miles > 0) {
        if ($miles == 1) {
            $texto .= 'MIL ';
        } else {
            $texto .= numeroALetrasInterno($miles) . ' MIL ';
        }
        $entero = $entero % 1000;
    }

    // Procesar centenas, decenas y unidades
    $texto .= numeroALetrasInterno($entero);

    // Agregar moneda (singular o plural)
    if ($entero == 1) {
        $texto .= ' ' . $monedaSingular;
    } else {
        $texto .= ' ' . $monedaPlural;
    }

    // Agregar centavos
    $texto .= ' CON ' . $decimal . '/100';

    return $texto;
}

/**
 * Función auxiliar para convertir números menores a 1000 a letras
 */
function numeroALetrasInterno($numero)
{
    $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
    $especiales = ['', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
    $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
    $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

    $texto = '';

    // Procesar centenas
    $centena = floor($numero / 100);
    if ($centena > 0) {
        if ($centena == 1 && $numero % 100 == 0) {
            $texto .= 'CIEN';
        } else {
            $texto .= $centenas[$centena];
        }
        $numero = $numero % 100;
        if ($numero > 0) {
            $texto .= ' ';
        }
    }

    // Procesar decenas y unidades
    if ($numero < 10) {
        $texto .= $unidades[$numero];
    } else if ($numero < 20) {
        $texto .= $especiales[$numero - 10];
    } else {
        $decena = floor($numero / 10);
        $unidad = $numero % 10;

        if ($unidad == 0) {
            $texto .= $decenas[$decena];
        } else if ($decena == 2) {
            $texto .= 'VEINTI' . $unidades[$unidad];
        } else {
            $texto .= $decenas[$decena] . ' Y ' . $unidades[$unidad];
        }
    }

    return $texto;
}

// Incluir la biblioteca FPDF y extensiones
require('../fpdf181/fpdf.php');
require_once "../modelos/Venta.php";
require_once "../config/Conexion.php";
// Ahora sí incluimos Letras.php
include_once "Letras.php";

// Verificar si se recibieron los parámetros necesarios
$idventa = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($idventa <= 0) {
    ob_end_clean(); // Limpia el buffer
    echo "ID de venta no válido";
    exit;
}

// Clase para el reporte de ventas en formato ticket
class PDF_Ticket extends FPDF
{
    // Constructor personalizado para tamaño de ticket
    function __construct()
    {
        // Ancho de 80mm (8cm) - similar a un ticket estándar
        $ancho = 80;
        // Llamar al constructor padre con orientación vertical, unidades en mm y tamaño personalizado
        parent::__construct('P', 'mm', array($ancho, 220)); // Aumentamos un poco la altura para asegurar que quepa todo
    }

    // Cabecera de página
    function Header()
    {
        // No incluimos nada en el encabezado automático
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1.5 cm del final
        $this->SetY(-15);
        // Arial italic 7
        $this->SetFont('Arial', '', 8);
        // Texto centrado
        $this->Cell(0, 4, 'GRACIAS POR SU PREFERENCIA', 0, 1, 'C');
        // Texto
        $this->Cell(0, 4, 'VUELVA PRONTO', 0, 1, 'C');
    }

    // Función para crear una línea de separación con estilo discontinuo
    function Separator($tipo = 'discontinua')
    {
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.1);

        // Ancho disponible para la línea
        $ancho_disponible = $this->GetPageWidth() - 10;
        $x_inicial = 5;
        $y_actual = $this->GetY();

        if ($tipo == 'recta') {
            // Línea recta continua
            $this->Line($x_inicial, $y_actual, $x_inicial + $ancho_disponible, $y_actual);
        } else {
            // Parámetros para línea discontinua
            $segmento = 2; // Longitud de cada segmento
            $espacio = 1;  // Espacio entre segmentos

            // Dibujar línea discontinua
            $x = $x_inicial;
            while ($x < $x_inicial + $ancho_disponible) {
                $this->Line($x, $y_actual, $x + $segmento, $y_actual);
                $x += $segmento + $espacio;
            }
        }

        $this->Ln(2);
    }
}

// Obtener datos de la venta
$venta = new Venta();
$rspta = $venta->mostrar($idventa);

if (!$rspta) {
    ob_end_clean(); // Limpia el buffer
    echo "Venta no encontrada";
    exit;
}

// Obtener detalles de la venta
$rsptad = $venta->listarDetalle($idventa);
$detalles = array();

while ($reg = $rsptad->fetch_object()) {
    // Calcula el subtotal SIN descuento (precio × cantidad)
    $subtotalSinDescuento = $reg->cantidad * $reg->precioventa;

    // Calcula el descuento total para este producto
    $descuentoTotal = $reg->cantidad * $reg->descuento;

    // Subtotal final después de aplicar el descuento
    $subtotalFinal = $subtotalSinDescuento - $descuentoTotal;

    $detalles[] = array(
        $reg->nombre,
        $reg->cantidad,
        $reg->precioventa,
        $descuentoTotal,
        $subtotalSinDescuento, // Ahora guardamos el subtotal SIN descuento para mostrarlo en la tabla
        $subtotalFinal // Subtotal con descuento aplicado (para cálculos internos)
    );
}

// Obtener los métodos de pago utilizados
$rsptaPagos = $venta->listarPagos($idventa);
$pagos = array();
$metodosPagoTexto = '';

// Si hay pagos registrados, procesarlos
if ($rsptaPagos && $rsptaPagos->num_rows > 0) {
    while ($regPago = $rsptaPagos->fetch_object()) {
        $pagos[] = array(
            'metodo' => $regPago->metodo_pago,
            'monto' => $regPago->monto,
            'recibido' => $regPago->efectivo_recibido,
            'cambio' => $regPago->efectivo_cambio
        );
    }

    // Crear texto de métodos de pago
    if (count($pagos) == 1) {
        $metodosPagoTexto = $pagos[0]['metodo'];
    } else {
        $metodosPagoTexto = 'MÚLTIPLE';
    }
} else {
    // Si no hay pagos registrados, usar el método de pago de la tabla venta
    $metodosPagoTexto = $rspta['metodopago'];
}

// Creación del PDF
$pdf = new PDF_Ticket();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(true, 15);

// Título principal centrado - Ajustado para mejor centrado
$pdf->SetFont('Arial', 'B', 12);
// Usar el ancho completo del papel para centrar correctamente
$pdf->Cell($pdf->GetPageWidth() - 20, 8, utf8_to_latin1('RECIBO'), 0, 1, 'C');

// Información de la empresa
$sql = "SELECT * FROM empresa WHERE estado = 1 LIMIT 1";
$empresa = ejecutarConsultaSimpleFila($sql);

// Información de la empresa CENTRADA
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, utf8_to_latin1($empresa['nombre']), 0, 1, 'C');

$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, utf8_to_latin1('NIT: ' . $empresa['nit']), 0, 1, 'C');
$pdf->Cell(0, 4, utf8_to_latin1($empresa['direccion']), 0, 1, 'C');
$pdf->Cell(0, 4, utf8_to_latin1('Tel: ' . $empresa['telefono']), 0, 1, 'C');

// Línea separadora
$pdf->Separator(); // Línea discontinua por defecto

// Datos de la venta en formato de tabla
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 4, 'RECIBO No.:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, $idventa, 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 4, 'FECHA:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, date('d/m/Y', strtotime($rspta['fechacreacion'])), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 4, 'NOMBRE/RAZON:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, utf8_to_latin1($rspta['cliente']), 0, 1, 'L');

// Método de pago (ahora mostrará MÚLTIPLE si hay más de un método)
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 4, 'TIPO DE PAGO:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, utf8_to_latin1($metodosPagoTexto), 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 4, 'VENDEDOR: ', 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, utf8_to_latin1($rspta['usuario']), 0, 1, 'L');

// Línea separadora
$pdf->Separator(); // Línea discontinua por defecto

// Título de detalle SIN fondo
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 5, 'DETALLE', 0, 1, 'C');

// Línea separadora
$pdf->Separator(); // Línea discontinua por defecto

// Encabezados de la tabla de productos
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.5, 4, 'Producto', 0, 0, 'L');
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.15, 4, 'Cant', 0, 0, 'C');
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.15, 4, 'Precio', 0, 0, 'R');
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.2, 4, 'Total', 0, 1, 'R');

// Línea separadora RECTA debajo de los encabezados
$pdf->Separator('recta');

// Detalles de la venta en formato de tabla
$pdf->SetFont('Arial', '', 8);
$total = 0;

foreach ($detalles as $det) {
    // Nombre del producto
    $nombre = $det[0];
    if (strlen($nombre) > 25) {
        $nombre = substr($nombre, 0, 22) . '...';
    }

    $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.5, 4, utf8_to_latin1($nombre), 0, 0, 'L');
    $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.15, 4, $det[1], 0, 0, 'C');
    $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.15, 4, number_format($det[2], 2), 0, 0, 'R');
    $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.2, 4, number_format($det[4], 2), 0, 1, 'R');

    $total += $det[4];
}

// Línea separadora RECTA después de los productos
$pdf->Separator('recta');

// Totales
$pdf->SetFont('Arial', '', 8);

// Subtotal (antes de descuentos)
$subtotalAntesDescuento = 0;
$descuento = 0;
$total = 0;

foreach ($detalles as $det) {
    $subtotalAntesDescuento += ($det[1] * $det[2]); // cantidad * precio
    $descuento += $det[3]; // descuento total
    $total += $det[4]; // subtotal después de descuentos
    $subtotalConDescuento = $subtotalAntesDescuento - $descuento;
}

$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.6, 4, 'SUB-TOTAL:', 0, 0, 'R');
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.4, 4, 'Bs. ' . number_format($subtotalAntesDescuento, 2), 0, 1, 'R');

// Descuento
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.6, 4, 'DESCUENTO:', 0, 0, 'R');
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.4, 4, 'Bs. - ' . number_format($descuento, 2), 0, 1, 'R');

// Si hay múltiples métodos de pago, mostrarlos detalladamente
// (ahora integrado directamente en la sección de totales)
if (count($pagos) > 1) {
    // Ordenar los pagos para que el efectivo aparezca al final
    usort($pagos, function ($a, $b) {
        if ($a['metodo'] == 'EFECTIVO') return 1;
        if ($b['metodo'] == 'EFECTIVO') return -1;
        return 0;
    });

    // Mostrar los métodos de pago
    foreach ($pagos as $pago) {
        $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.6, 4, $pago['metodo'] . ':', 0, 0, 'R');
        $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.4, 4, 'Bs. ' . number_format($pago['monto'], 2), 0, 1, 'R');

        // Si es efectivo y hay información de recibido/cambio, mostrarla indentada
        if ($pago['metodo'] == 'EFECTIVO' && !is_null($pago['recibido'])) {
            $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.6, 4, '   Cambio:', 0, 0, 'R');
            $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.4, 4, 'Bs. ' . number_format($pago['cambio'], 2), 0, 1, 'R');
        }
    }
} else if (count($pagos) == 1 && $pagos[0]['metodo'] == 'EFECTIVO' && !is_null($pagos[0]['recibido'])) {
    // Si solo hay un pago con efectivo, mostrar recibido/cambio
    $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.6, 4, 'EFECTIVO:', 0, 0, 'R');
    $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.4, 4, 'Bs. ' . number_format($pagos[0]['monto'], 2), 0, 1, 'R');

    $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.6, 4, '   Cambio:', 0, 0, 'R');
    $pdf->Cell(($pdf->GetPageWidth() - 10) * 0.4, 4, 'Bs. ' . number_format($pagos[0]['cambio'], 2), 0, 1, 'R');
}

// Línea separadora
$pdf->Separator('recta');

// Total (siempre al final, después de mostrar los métodos de pago)
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.6, 4, 'TOTAL:', 0, 0, 'R');
$pdf->Cell(($pdf->GetPageWidth() - 10) * 0.4, 4, 'Bs. ' . number_format($subtotalConDescuento, 2), 0, 1, 'R');

// Línea separadora para crear espacio entre el total y el total en letras
$pdf->Separator('recta');

// Agregar total en letras usando la función personalizada
$totalEnLetras = numeroALetras($subtotalConDescuento);

$pdf->SetFont('Arial', 'I', 7); // Itálica para destacar
$pdf->MultiCell(0, 4, utf8_to_latin1($totalEnLetras), 0, 'C');

// Línea separadora
$pdf->Separator(); // Línea discontinua por defecto

// Aviso fiscal
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(0, 4, 'ESTE RECIBO NO TIENE VALOR FISCAL', 0, 1, 'C');

// Hora de generación del PDF
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, 'Generado: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

// Limpiar cualquier salida anterior
ob_end_clean();

// Detectar si es un dispositivo móvil o computadora
$esMobile = isMobile();

// Generar el PDF
if ($esMobile) {
    // Si es un dispositivo móvil, forzar la descarga
    $pdf->Output('D', 'Ticket-' . $idventa . '.pdf');
} else {
    // Si es una computadora, mostrar en el navegador
    $pdf->Output('I', 'Ticket-' . $idventa . '.pdf');
}
