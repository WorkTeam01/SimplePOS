<?php
// Activamos almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1)
  session_start();

if (!isset($_SESSION['nombre'])) {
  echo "Debe ingresar al sistema correctamente para visualizar el reporte";
} else {
  // Verificar permiso de almacén (según tu tabla de permisos)
  if ($_SESSION['almacen'] == 1) {

    // Incluimos la clase PDF_MC_Table
    require('PDF_MC_Table.php');

    // Instanciamos la clase para generar el documento PDF
    $pdf = new PDF_MC_Table();

    // Agregamos la primera página al documento PDF
    $pdf->AddPage();

    // Configuración inicial
    $y_axis_initial = 25;
    $pdf->SetFont('Arial', 'B', 12);

    // Título del reporte
    $pdf->Cell(40, 6, '', 0, 0, 'C');
    $pdf->Cell(100, 6, 'LISTA DE PRODUCTOS', 1, 0, 'C');
    $pdf->Ln(10);

    // Encabezados de columnas
    $pdf->SetFillColor(232, 232, 232);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Nombre', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, utf8_decode('Categoría'), 1, 0, 'C', 1);
    $pdf->Cell(25, 6, 'Codigo', 1, 0, 'C', 1);
    $pdf->Cell(20, 6, 'Stock', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, 'P. Compra', 1, 0, 'C', 1);
    $pdf->Cell(30, 6, 'P. Venta', 1, 0, 'C', 1);
    $pdf->Cell(20, 6, 'Moneda', 1, 0, 'C', 1);
    $pdf->Ln();

    // Conexión a la base de datos y consulta
    require_once "../modelos/Producto.php";
    $producto = new Producto();
    $rspta = $producto->listar();

    // Configuración de anchos de columnas
    $pdf->SetWidths(array(40, 30, 25, 20, 30, 30, 20));

    // Mostrar los registros
    while ($reg = $rspta->fetch_object()) {
      $pdf->SetFont('Arial', '', 9);
      $pdf->Row(array(
        utf8_decode($reg->nombre),
        utf8_decode($reg->categoria),
        $reg->codigo,
        $reg->stock,
        number_format($reg->preciocompra, 2),
        number_format($reg->precioventa, 2),
        utf8_decode($reg->moneda)
      ));
    }

    // Pie de página
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Fecha: ' . date('d/m/Y'), 0, 1, 'R');

    // Mostrar el documento PDF
    $pdf->Output();
  } else {
    echo "No tiene permiso para visualizar el reporte";
  }
}

ob_end_flush();
