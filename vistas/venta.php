<?php
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['ventas'] == 1) {
?>
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <!-- Default box -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h1 class="box-title">Ventas</h1>
                <div class="box-tools pull-right">
                  <button class="btn btn-primary btn-sm" onclick="mostrarform(true)" id="btnagregar"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <!-- Listado de ventas -->
                <div class="panel-body table-responsive" id="listadoregistros">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                      <th>Opciones</th>
                      <th>Fecha</th>
                      <th>Cliente</th>
                      <th>Usuario</th>
                      <th>Método Pago</th>
                      <th>Total Venta</th>
                      <th>Estado</th>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>

                <!-- Formulario de registro -->
                <div class="panel-body" id="formularioregistros">
                  <form action="" name="formulario" id="formulario" method="POST">
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="idcliente">Cliente <span class="text-danger">(*)</span></label>
                      <input class="form-control" type="hidden" name="idventa" id="idventa">
                      <select name="idcliente" id="idcliente" class="form-control selectpicker"
                        data-live-search="true" data-size="5" required>
                      </select>
                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-xs-6">
                      <label for="fechacreacion">Fecha <span class="text-danger">(*)</span></label>
                      <input class="form-control" type="date" name="fechacreacion" id="fechacreacion" required>
                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-xs-6">
                      <label for="metodopago">Método Pago <span class="text-danger">(*)</span></label>
                      <select name="metodopago" id="metodopago" class="form-control selectpicker" required>
                        <option value="EFECTIVO">EFECTIVO</option>
                        <option value="QR">QR</option>
                        <option value="TARJETA">TARJETA</option>
                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                        <option value="EFECTIVO+QR">EFECTIVO + QR</option>
                        <option value="EFECTIVO+TARJETA">EFECTIVO + TARJETA</option>
                        <option value="EFECTIVO+TRANSFERENCIA">EFECTIVO + TRANSFERENCIA</option>
                        <option value="QR+TARJETA">QR + TARJETA</option>
                      </select>
                    </div>

                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <div class="panel panel-primary">
                        <div class="panel-heading">
                          <div class="panel-title">
                            <button class="btn btn-primary" id="btnAgregarArt" type="button" data-toggle="modal" data-target="#myModal">
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
                              <th>Precio Venta</th>
                              <th>Descuento</th>
                              <th>Subtotal</th>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                              <tr>
                                <th colspan="5">TOTAL</th>
                                <th>
                                  <h4 id="total">Bs. 0.00</h4><input type="hidden" name="totalventa" id="totalventa">
                                </th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-lg-12 col-md-12 col-xs-12" id="campos-pago">
                      <!-- Campos de pago dinámicos -->
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

    <!-- Modal para productos -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Seleccione un Producto</h4>
          </div>
          <div class="modal-body">
            <table id="tblproductos" class="table table-striped table-bordered table-condensed table-hover">
              <thead>
                <th>Opciones</th>
                <th>Nombre</th>
                <th>Categoria</th>
                <th>Código</th>
                <th>Stock</th>
                <th>Precio Venta</th>
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
  <script src="scripts/venta.js"></script>
<?php
}
ob_end_flush();
?>