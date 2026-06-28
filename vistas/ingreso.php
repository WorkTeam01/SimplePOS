<?php
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['compras'] == 1) {
?>
    <style>
      html,
      body {
        overflow-x: hidden;
        max-width: 100%;
        position: relative;
      }

      /* Asegurarse de que los modales no afecten el scroll */
      .modal-open {
        padding-right: 0 !important;
        overflow: auto !important;
      }

      /* Evitar que el backdrop cause desplazamiento */
      .modal-backdrop {
        width: 100% !important;
        height: 100% !important;
      }
    </style>

    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <!-- Default box -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h1 class="box-title">Compras</h1>
                <div class="box-tools pull-right">
                  <button class="btn btn-primary btn-sm" onclick="mostrarform(true)" id="btnagregar"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <!-- Listado de ingresos -->
                <div class="panel-body table-responsive" id="listadoregistros">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                      <th>Opciones</th>
                      <th>Fecha</th>
                      <th>Proveedor</th>
                      <th>Usuario</th>
                      <th>Total Compra</th>
                      <th>Estado</th>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>

                <!-- Formulario de registro -->
                <div class="panel-body" id="formularioregistros">
                  <form action="" name="formulario" id="formulario" method="POST">
                    <div class="form-group col-lg-8 col-md-8 col-xs-12">
                      <label for="idproveedor">Proveedor(*):</label>
                      <input type="hidden" name="idcompra" id="idcompra">
                      <select name="idproveedor" id="idproveedor" class="form-control selectpicker" data-live-search="true" required></select>
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-xs-12">
                      <label for="fecha_hora">Fecha(*):</label>
                      <input class="form-control" type="date" name="fecha_hora" id="fecha_hora" required>
                    </div>

                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <div class="panel panel-primary">
                        <div class="panel-heading">
                          <div class="panel-title">
                            <button class="btn btn-info" id="btnAgregarArt" type="button" data-toggle="modal" data-target="#myModal">
                              <i class="fa fa-plus"></i> Agregar Productos
                            </button>
                          </div>
                        </div>
                        <div class="panel-body table-responsive">
                          <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                            <thead style="background-color:#A9D0F5">
                              <th>Opciones</th>
                              <th>Producto</th>
                              <th>Cantidad</th>
                              <th>Precio Compra</th>
                              <th>Subtotal</th>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                              <tr>
                                <th colspan="4">TOTAL</th>
                                <th>
                                  <h4 id="total">S/. 0.00</h4><input type="hidden" name="total_compra" id="total_compra">
                                </th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <hr>
                      <div class="text-right">
                        <button class="btn btn-primary btn-lg" type="submit" id="btnGuardar">
                          <i class="fa fa-save"></i> Guardar
                        </button>
                        <button class="btn btn-danger btn-lg" onclick="cancelarform()" type="button" id="btnCancelar">
                          <i class="fa fa-arrow-circle-left"></i> Cancelar
                        </button>
                      </div>
                    </div>
                  </form>
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

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Seleccione un Producto</h4>
          </div>
          <div class="modal-body table-responsive">
            <table id="tblproductos" class="table table-striped table-bordered table-condensed table-hover">
              <thead>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Código</th>
                <th>Stock</th>
                <th>Imagen</th>
              </thead>
              <tbody></tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Fin Modal -->
  <?php
  } else {
    require 'noacceso.php';
  }
  require 'footer.php';
  ?>
  <script src="scripts/compra.js"></script>
<?php
}
ob_end_flush();
?>