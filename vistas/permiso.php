<?php
//activamos almacenamiento en el buffer
ob_start();
require_once "../config/auth.php";
iniciarSesionSegura();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
} else {
  require 'header.php';
  if ($_SESSION['acceso'] == 1) {
?>
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <!-- Default box -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h1 class="box-title">Permisos</h1>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <!-- Listado de permisos -->
                <div class="panel-body table-responsive" id="listadoregistros">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                      <th>Nombre</th>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->
    </div>
  <?php
  } else {
    require 'noacceso.php';
  }
  require 'footer.php';
  ?>
  <script src="scripts/permiso.js"></script>
<?php
}
ob_end_flush();
?>