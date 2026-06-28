<?php
require_once "../modelos/Consultas.php";

$consulta = new Consultas();

// Determinar qué operación se está solicitando
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {
  case 'comprasfecha':
    $fecha_inicio = isset($_GET["fecha_inicio"]) ? limpiarCadena($_GET["fecha_inicio"]) : "";
    $fecha_fin = isset($_GET["fecha_fin"]) ? limpiarCadena($_GET["fecha_fin"]) : "";

    $rspta = $consulta->comprasfecha($fecha_inicio, $fecha_fin);
    $data = array();

    while ($reg = $rspta->fetch_object()) {
      $data[] = array(
        "0" => $reg->fecha,
        "1" => $reg->usuario,
        "2" => $reg->proveedor,
        "3" => $reg->total_compra,
        "4" => ($reg->estado == 1) ? '<span class="label bg-green">Aceptado</span>' :
          '<span class="label bg-red">Anulado</span>'
      );
    }

    $results = array(
      "sEcho" => 1, // Información para DataTables
      "iTotalRecords" => count($data), // Total de registros
      "iTotalDisplayRecords" => count($data), // Total de registros a visualizar
      "aaData" => $data
    );

    echo json_encode($results);
    break;

  case 'ventasfechacliente':
    $fecha_inicio = isset($_GET["fecha_inicio"]) ? limpiarCadena($_GET["fecha_inicio"]) : "";
    $fecha_fin = isset($_GET["fecha_fin"]) ? limpiarCadena($_GET["fecha_fin"]) : "";
    $idcliente = isset($_GET["idcliente"]) ? limpiarCadena($_GET["idcliente"]) : "";

    $rspta = $consulta->ventasfechacliente($fecha_inicio, $fecha_fin, $idcliente);
    $data = array();

    while ($reg = $rspta->fetch_object()) {
      $urlTicket = '../reportes/rptventas.php?id=' . $reg->idventa;

      $data[] = array(
        "0" => $reg->fecha,
        "1" => $reg->usuario,
        "2" => $reg->cliente,
        "3" => $reg->totalventa,
        "4" => ($reg->estado == 1) ? '<span class="label bg-green">Aceptado</span>' :
          '<span class="label bg-red">Anulado</span>',
        "5" => $reg->metodopago,
        "6" => '<a href="' . $urlTicket . '" target="_blank"><button class="btn btn-info btn-xs"><i class="fa fa-print"></i> Ver</button></a>'
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
