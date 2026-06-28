<?php
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['escritorio'] == 1) {
    require_once "../modelos/Consultas.php";
    $consulta = new Consultas();

    // Existing queries
    $rsptac = $consulta->totalcomprahoy();
    $regc = $rsptac->fetch_object();
    $totalc = $regc->total_compra;

    $rsptav = $consulta->totalventahoy();
    $regv = $rsptav->fetch_object();
    $totalv = $regv->total_venta;

    // New queries for additional metrics
    $rsptav_semana = $consulta->totalventasemana();
    $regv_semana = $rsptav_semana->fetch_object();
    $totalv_semana = $regv_semana->total_venta;

    $rsptav_mes = $consulta->totalventames();
    $regv_mes = $rsptav_mes->fetch_object();
    $totalv_mes = $regv_mes->total_venta;

    // Client and supplier counts
    $rspta_clientes = $consulta->totalclientes();
    $reg_clientes = $rspta_clientes->fetch_object();
    $total_clientes = $reg_clientes->total;

    $rspta_proveedores = $consulta->totalproveedores();
    $reg_proveedores = $rspta_proveedores->fetch_object();
    $total_proveedores = $reg_proveedores->total;

    // Product queries
    $rsptamasvendido = $consulta->productos_mas_vendidos();
    $rsptaporagotar = $consulta->productos_por_agotar();
    // Get top clients
    $top_client_hoy = $consulta->top_cliente_hoy();
    $top_client_semana = $consulta->top_cliente_semana();
    $top_client_mes = $consulta->top_cliente_mes();
    $top_client_anio = $consulta->top_cliente_anio();

    // Obtener datos para comparativas
    $rsptav_ayer = $consulta->totalventaayer();
    $regv_ayer = $rsptav_ayer->fetch_object();
    $totalv_ayer = $regv_ayer->total_venta;

    $rsptav_semana_anterior = $consulta->totalventasemaneanterior();
    $regv_semana_anterior = $rsptav_semana_anterior->fetch_object();
    $totalv_semana_anterior = $regv_semana_anterior->total_venta;

    $rsptav_mes_anterior = $consulta->totalventamesanterior();
    $regv_mes_anterior = $rsptav_mes_anterior->fetch_object();
    $totalv_mes_anterior = $regv_mes_anterior->total_venta;

    // Calcular porcentajes de cambio
    $porcentaje_dia = ($totalv_ayer > 0) ? (($totalv - $totalv_ayer) / $totalv_ayer) * 100 : 0;
    $porcentaje_semana = ($totalv_semana_anterior > 0) ? (($totalv_semana - $totalv_semana_anterior) / $totalv_semana_anterior) * 100 : 0;
    $porcentaje_mes = ($totalv_mes_anterior > 0) ? (($totalv_mes - $totalv_mes_anterior) / $totalv_mes_anterior) * 100 : 0;

    // Primero aplicar abs() y luego formatear
    $porcentaje_dia_abs = abs($porcentaje_dia);
    $porcentaje_semana_abs = abs($porcentaje_semana);
    $porcentaje_mes_abs = abs($porcentaje_mes);

    // Formatear porcentajes con 2 decimales
    $porcentaje_dia_formateado = number_format($porcentaje_dia_abs, 2);
    $porcentaje_semana_formateado = number_format($porcentaje_semana_abs, 2);
    $porcentaje_mes_formateado = number_format($porcentaje_mes_abs, 2);

    // Determinar si el cambio es positivo o negativo
    $tendencia_dia = $porcentaje_dia >= 0 ? 'up' : 'down';
    $tendencia_semana = $porcentaje_semana >= 0 ? 'up' : 'down';
    $tendencia_mes = $porcentaje_mes >= 0 ? 'up' : 'down';

    // Clase CSS según tendencia
    $clase_dia = $tendencia_dia == 'up' ? 'text-green' : 'text-red';
    $clase_semana = $tendencia_semana == 'up' ? 'text-green' : 'text-red';
    $clase_mes = $tendencia_mes == 'up' ? 'text-green' : 'text-red';
?>
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Panel de Control
        </h1>
        <ol class="breadcrumb">
          <li><a href="escritorio.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
          <li class="active">Página actual</li>
        </ol>
      </section>
      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <!-- Primera fila: Tarjetas de resumen -->
            <div class="row">
              <!-- Tarjeta de Clientes -->
              <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="small-box bg-purple">
                  <div class="inner">
                    <h3><?php echo $total_clientes; ?></h3>
                    <p><i class="fa fa-users"></i> Clientes</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-user-circle"></i>
                  </div>
                  <a href="cliente.php" class="small-box-footer">Ver clientes <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>

              <!-- Tarjeta de Proveedores -->
              <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="small-box bg-maroon">
                  <div class="inner">
                    <h3><?php echo $total_proveedores; ?></h3>
                    <p><i class="fa fa-truck"></i> Proveedores</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-building"></i>
                  </div>
                  <a href="proveedor.php" class="small-box-footer">Ver proveedores <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>

              <!-- Tarjeta de Compras -->
              <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="small-box bg-aqua">
                  <div class="inner">
                    <h3>Bs. <?php echo $totalc; ?></h3>
                    <p><i class="fa fa-shopping-cart"></i> Compras</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-cart-plus"></i>
                  </div>
                  <a href="ingreso.php" class="small-box-footer">Ver compras <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>

              <!-- Tarjeta de Ventas -->
              <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="small-box bg-green">
                  <div class="inner">
                    <h3>Bs. <?php echo $totalv; ?></h3>
                    <p><i class="fa fa-line-chart"></i> Ventas</p>
                  </div>
                  <div class="icon">
                    <i class="fa fa-money"></i>
                  </div>
                  <a href="venta.php" class="small-box-footer">Ver ventas <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>

            <!-- Segunda fila: Métricas y Productos más vendidos -->
            <div class="row">
              <!-- Columna izquierda: Métricas de ventas -->
              <div class="col-md-4">
                <!-- Panel de métricas de ventas -->
                <div class="box box-info">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bar-chart"></i> Métricas de Ventas</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <!-- Ventas hoy -->
                    <div class="metric-item bg-ventas-hoy">
                      <h4><i class="fa fa-sun-o"></i> Ventas hoy</h4>
                      <?php if (isset($totalv) && $totalv > 0): ?>
                        <p class="metric-value">Bs. <?php echo number_format($totalv, 2); ?></p>
                        <?php if (isset($porcentaje_dia_formateado)): ?>
                          <p class="metric-change">
                            <i class="fa fa-arrow-<?php echo $tendencia_dia; ?> <?php echo $clase_dia; ?>"></i>
                            <?php echo number_format($porcentaje_dia_formateado, 0); ?>% Desde ayer
                          </p>
                        <?php else: ?>
                          <p class="metric-change">
                            <i class="fa fa-info-circle"></i> Sin datos comparativos
                          </p>
                        <?php endif; ?>
                      <?php else: ?>
                        <p class="metric-value">Bs. 0.00</p>
                        <p class="metric-change">
                          <i class="fa fa-info-circle"></i> Sin ventas hoy
                        </p>
                      <?php endif; ?>
                    </div>

                    <!-- Ventas semana -->
                    <div class="metric-item bg-ventas-semana">
                      <h4><i class="fa fa-calendar"></i> Ventas semana</h4>
                      <?php if (isset($totalv_semana) && $totalv_semana > 0): ?>
                        <p class="metric-value">Bs. <?php echo number_format($totalv_semana, 2); ?></p>
                        <?php if (isset($porcentaje_semana_formateado)): ?>
                          <p class="metric-change">
                            <i class="fa fa-arrow-<?php echo $tendencia_semana; ?> <?php echo $clase_semana; ?>"></i>
                            <?php echo number_format($porcentaje_semana_formateado, 0); ?>% Desde semana pasada
                          </p>
                        <?php else: ?>
                          <p class="metric-change">
                            <i class="fa fa-info-circle"></i> Sin datos comparativos
                          </p>
                        <?php endif; ?>
                      <?php else: ?>
                        <p class="metric-value">Bs. 0.00</p>
                        <p class="metric-change">
                          <i class="fa fa-info-circle"></i> Sin ventas esta semana
                        </p>
                      <?php endif; ?>
                    </div>

                    <!-- Ventas mes -->
                    <div class="metric-item bg-ventas-mes">
                      <h4><i class="fa fa-calendar-check-o"></i> Ventas mes</h4>
                      <?php if (isset($totalv_mes) && $totalv_mes > 0): ?>
                        <p class="metric-value">Bs. <?php echo number_format($totalv_mes, 2); ?></p>
                        <?php if (isset($porcentaje_mes_formateado)): ?>
                          <p class="metric-change">
                            <i class="fa fa-arrow-<?php echo $tendencia_mes; ?> <?php echo $clase_mes; ?>"></i>
                            <?php echo number_format($porcentaje_mes_formateado, 0); ?>% Desde mes pasado
                          </p>
                        <?php else: ?>
                          <p class="metric-change">
                            <i class="fa fa-info-circle"></i> Sin datos comparativos
                          </p>
                        <?php endif; ?>
                      <?php else: ?>
                        <p class="metric-value">Bs. 0.00</p>
                        <p class="metric-change">
                          <i class="fa fa-info-circle"></i> Sin ventas este mes
                        </p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Columna derecha: Productos -->
              <div class="col-md-8">
                <!-- Panel de productos más vendidos -->
                <div class="box box-success box-product-tables">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-star"></i> Artículos más vendidos</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover table-productos-vendidos">
                        <thead>
                          <tr class="bg-success">
                            <th width="80" class="hidden-xs"><i class="fa fa-image"></i> Imagen</th>
                            <th><i class="fa fa-cube"></i> Artículo</th>
                            <th class="hidden-xs hidden-sm"><i class="fa fa-barcode"></i> Código</th>
                            <th class="hidden-xs"><i class="fa fa-tag"></i> Precio</th>
                            <th><i class="fa fa-sort-numeric-asc"></i> Unidades</th>
                            <th class="hidden-xs"><i class="fa fa-shopping-cart"></i> Ventas</th>
                            <th><i class="fa fa-money"></i> Total</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $total_unidades = 0;
                          $total_ventas = 0;
                          $total_bruto = 0;
                          $hayDatos = false;

                          if ($rsptamasvendido && $rsptamasvendido->num_rows > 0) {
                            $hayDatos = true;

                            while ($reg = $rsptamasvendido->fetch_object()) {
                              // Cálculos para cada producto
                              $venta_bruta = $reg->venta_bruta;

                              // Acumuladores para totales
                              $total_unidades += $reg->total_unidades;
                              $total_ventas += $reg->num_ventas;
                              $total_bruto += $venta_bruta;

                              // Imagen del producto
                              $imagen = (!empty($reg->imagen)) ? "../files/productos/" . $reg->imagen : "../public/img/default-50x50.gif";
                          ?>
                              <tr>
                                <td class="text-center hidden-xs">
                                  <img src="<?php echo $imagen; ?>" alt="Imagen artículo" class="img-thumbnail rounded-img" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td class="producto-nombre-celda">
                                  <strong class="producto-nombre"><?php echo $reg->nombre; ?></strong>
                                  <?php if (!empty($reg->descripcion)): ?>
                                    <br><small class="text-muted producto-descripcion"><?php echo substr($reg->descripcion, 0, 60); ?><?php echo (strlen($reg->descripcion) > 60) ? '...' : ''; ?></small>
                                  <?php endif; ?>
                                </td>
                                <td class="text-center hidden-xs hidden-sm"><?php echo $reg->codigo; ?></td>
                                <td class="text-right hidden-xs">Bs. <?php echo number_format($reg->precio_actual, 2); ?></td>
                                <td class="text-center">
                                  <span class="badge bg-blue"><?php echo $reg->total_unidades; ?></span>
                                </td>
                                <td class="text-center hidden-xs">
                                  <span class="badge bg-green-active"><?php echo $reg->num_ventas; ?></span>
                                </td>
                                <td class="text-right">Bs. <?php echo number_format($venta_bruta, 2); ?></td>
                              </tr>
                            <?php
                            } // Fin del while

                            // Fila de totales (solo se muestra si hay datos)
                            ?>
                            <tr class="bg-gray">
                              <td class="hidden-xs" colspan="4" class="text-right"><strong>TOTALES:</strong></td>
                              <td class="visible-xs" colspan="1" class="text-right"><strong>TOTALES:</strong></td>
                              <td class="text-center"><strong><?php echo $total_unidades; ?></strong></td>
                              <td class="text-center hidden-xs"><strong><?php echo $total_ventas; ?></strong></td>
                              <td class="text-right"><strong>Bs. <?php echo number_format($total_bruto, 2); ?></strong></td>
                            </tr>
                          <?php
                          } // Fin del if de validación

                          // Mensaje cuando no hay datos
                          if (!$hayDatos) {
                          ?>
                            <tr>
                              <td colspan="7" class="text-center no-data-message">
                                <i class="fa fa-info-circle text-info"></i> No hay ventas registradas para mostrar.
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <!-- Panel de clientes destacados -->
                <div class="box box-warning">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-users"></i> Clientes Destacados</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="box-body">

                    <!-- Cliente del día -->
                    <div class="client-metric bg-cliente-hoy">
                      <h4><i class="fa fa-trophy"></i> Cliente del día</h4>
                      <?php
                      $hay_cliente_hoy = false;
                      if ($top_client_hoy && $top_client_hoy->num_rows > 0) {
                        $reg = $top_client_hoy->fetch_object();
                        if ($reg && isset($reg->nombre) && isset($reg->total_comprado)) {
                          $hay_cliente_hoy = true;
                      ?>
                          <p class="client-name"><?php echo htmlspecialchars($reg->nombre); ?></p>
                          <p class="client-amount">Compró Bs. <?php echo number_format($reg->total_comprado, 2); ?></p>
                        <?php
                        }
                      }
                      if (!$hay_cliente_hoy):
                        ?>
                        <p class="client-name">Ninguna compra hoy</p>
                        <p class="client-amount"><i class="fa fa-info-circle"></i> Sin datos</p>
                      <?php endif; ?>
                    </div>

                    <!-- Cliente de la semana -->
                    <div class="client-metric bg-cliente-semana">
                      <h4><i class="fa fa-star"></i> Cliente de la semana</h4>
                      <?php
                      $hay_cliente_semana = false;
                      if ($top_client_semana && $top_client_semana->num_rows > 0) {
                        $reg = $top_client_semana->fetch_object();
                        if ($reg && isset($reg->nombre) && isset($reg->total_comprado)) {
                          $hay_cliente_semana = true;
                      ?>
                          <p class="client-name"><?php echo htmlspecialchars($reg->nombre); ?></p>
                          <p class="client-amount">Compró Bs. <?php echo number_format($reg->total_comprado, 2); ?></p>
                        <?php
                        }
                      }
                      if (!$hay_cliente_semana):
                        ?>
                        <p class="client-name">Ninguna compra esta semana</p>
                        <p class="client-amount"><i class="fa fa-info-circle"></i> Sin datos</p>
                      <?php endif; ?>
                    </div>

                    <!-- Cliente del mes -->
                    <div class="client-metric bg-cliente-mes">
                      <h4><i class="fa fa-certificate"></i> Cliente del mes</h4>
                      <?php
                      $hay_cliente_mes = false;
                      if ($top_client_mes && $top_client_mes->num_rows > 0) {
                        $reg = $top_client_mes->fetch_object();
                        if ($reg && isset($reg->nombre) && isset($reg->total_comprado)) {
                          $hay_cliente_mes = true;
                      ?>
                          <p class="client-name"><?php echo htmlspecialchars($reg->nombre); ?></p>
                          <p class="client-amount">Compró Bs. <?php echo number_format($reg->total_comprado, 2); ?></p>
                        <?php
                        }
                      }
                      if (!$hay_cliente_mes):
                        ?>
                        <p class="client-name">Ninguna compra este mes</p>
                        <p class="client-amount"><i class="fa fa-info-circle"></i> Sin datos</p>
                      <?php endif; ?>
                    </div>

                    <!-- Cliente del año -->
                    <div class="client-metric bg-cliente-anio">
                      <h4><i class="fa fa-crown"></i> Cliente del año</h4>
                      <?php
                      $hay_cliente_anio = false;
                      if ($top_client_anio && $top_client_anio->num_rows > 0) {
                        $reg = $top_client_anio->fetch_object();
                        if ($reg && isset($reg->nombre) && isset($reg->total_comprado)) {
                          $hay_cliente_anio = true;
                      ?>
                          <p class="client-name"><?php echo htmlspecialchars($reg->nombre); ?></p>
                          <p class="client-amount">Compró Bs. <?php echo number_format($reg->total_comprado, 2); ?></p>
                        <?php
                        }
                      }
                      if (!$hay_cliente_anio):
                        ?>
                        <p class="client-name">Ninguna compra este año</p>
                        <p class="client-amount"><i class="fa fa-info-circle"></i> Sin datos</p>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <!-- Panel de productos por agotar -->
                <div class="box box-danger box-product-tables">
                  <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Artículos por agotar</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table table-bordered table-hover table-productos-agotando">
                        <thead>
                          <tr class="bg-danger">
                            <th width="80" class="hidden-xs"><i class="fa fa-image"></i> Imagen</th>
                            <th><i class="fa fa-cube"></i> Artículo</th>
                            <th class="hidden-xs hidden-sm"><i class="fa fa-barcode"></i> Código</th>
                            <th><i class="fa fa-cubes"></i> Stock</th>
                            <th class="hidden-xs"><i class="fa fa-tag"></i> Precio</th>
                            <th><i class="fa fa-info-circle"></i> Estado</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          // Definir stock mínimo predeterminado
                          $stock_minimo_default = 5;
                          $hayProductosPorAgotar = false;

                          if ($rsptaporagotar && $rsptaporagotar->num_rows > 0) {
                            $hayProductosPorAgotar = true;

                            while ($reg = $rsptaporagotar->fetch_object()) {
                              $imagen = (!empty($reg->imagen)) ? "../files/productos/" . $reg->imagen : "../public/img/default-50x50.gif";

                              // Calcular el porcentaje de stock respecto al mínimo predeterminado
                              $porcentaje_stock = ($reg->stock / $stock_minimo_default) * 100;

                              // Determinar clase CSS según el nivel de stock
                              $stock_class = "";
                              $stock_text = "";

                              if ($reg->stock <= 0) {
                                $stock_class = "label-danger";
                                $stock_text = "Agotado";
                              } elseif ($reg->stock <= 3) {
                                $stock_class = "label-warning";
                                $stock_text = "Crítico";
                              } else {
                                $stock_class = "label-info";
                                $stock_text = "Por agotar";
                              }

                              // Calculamos si es un producto popular (>10 ventas)
                              $es_popular = isset($reg->num_ventas) && $reg->num_ventas > 10;
                          ?>
                              <tr>
                                <td class="text-center hidden-xs">
                                  <img src="<?php echo $imagen; ?>" alt="<?php echo $reg->nombre; ?>" class="img-thumbnail rounded-img" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td class="producto-nombre-celda">
                                  <strong class="producto-nombre"><?php echo $reg->nombre; ?></strong>
                                  <?php if (!empty($reg->descripcion)): ?>
                                    <br><small class="text-muted producto-descripcion"><?php echo substr($reg->descripcion, 0, 60); ?><?php echo (strlen($reg->descripcion) > 60) ? '...' : ''; ?></small>
                                  <?php endif; ?>
                                  <?php if ($es_popular): ?>
                                    <span class="label label-success popular-badge"><i class="fa fa-fire"></i> Popular</span>
                                  <?php endif; ?>
                                </td>
                                <td class="text-center hidden-xs hidden-sm"><?php echo $reg->codigo; ?></td>
                                <td class="text-center">
                                  <div class="stock-indicator">
                                    <span class="stock-number"><?php echo $reg->stock; ?></span>
                                    <div class="progress progress-xs">
                                      <div class="progress-bar progress-bar-<?php echo ($porcentaje_stock <= 30) ? 'danger' : (($porcentaje_stock <= 70) ? 'warning' : 'success'); ?>"
                                        style="width: <?php echo min($porcentaje_stock, 100); ?>%"></div>
                                    </div>
                                  </div>
                                </td>
                                <td class="text-right hidden-xs">Bs. <?php echo number_format($reg->precioventa, 2); ?></td>
                                <td class="text-center">
                                  <span class="label <?php echo $stock_class; ?>">
                                    <i class="fa <?php echo ($reg->stock <= 0) ? 'fa-times-circle' : 'fa-exclamation-circle'; ?>"></i>
                                    <?php echo $stock_text; ?>
                                  </span>
                                </td>
                              </tr>
                            <?php
                            } // Fin del while
                          } // Fin del if de validación

                          // Mensaje cuando no hay productos por agotar
                          if (!$hayProductosPorAgotar) {
                            ?>
                            <tr>
                              <td colspan="8" class="text-center">
                                <span class="text-success"><i class="fa fa-check-circle"></i> No hay artículos por agotar actualmente</span>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

<?php
  } else {
    require 'noacceso.php';
  }
  require 'footer.php';
}
ob_end_flush();
?>