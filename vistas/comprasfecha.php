<?php
// Incluimos verificación de sesión y permisos
require 'header.php';

if ($_SESSION['consultac'] == 1) {
?>
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <!-- Default box -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h1 class="box-title"><i class="fas fa-search-dollar"></i> Consulta de Compras por Fecha</h1>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <!--box-header-->
            <!--centro-->
            <div class="box-body">
              <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Fecha Inicio:</label>
                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d"); ?>">
                  </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Fecha Fin:</label>
                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d"); ?>">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="panel-body table-responsive">
                    <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                      <thead>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Proveedor</th>
                        <th>Total</th>
                        <th>Estado</th>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!--fin centro-->
          </div>
        </div>
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
<?php
} else {
  require 'noacceso.php';
}
require 'footer.php';
?>
<script src="scripts/comprasfecha.js"></script>