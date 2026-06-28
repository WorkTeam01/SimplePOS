<?php
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['configuracion'] == 1) {
?>
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <!-- Default box -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h1 class="box-title">Gestión de Empresas</h1>
                <div class="box-tools pull-right">
                  <button class="btn btn-primary btn-sm" onclick="mostrarform(true)" id="btnagregar"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <!-- Listado de empresas -->
                <div class="panel-body table-responsive" id="listadoregistros">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                      <th>Opciones</th>
                      <th>Nombre</th>
                      <th>NIT</th>
                      <th>Dirección</th>
                      <th>Teléfono</th>
                      <th>Email</th>
                      <th>Estado</th>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>

                <!-- Formulario de registro -->
                <div class="panel-body" id="formularioregistros">
                  <form action="" name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="nombre">Nombre <span class="text-danger">(*)</span></label>
                      <input class="form-control" type="hidden" name="idempresa" id="idempresa">
                      <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Nombre de la empresa" required>
                    </div>

                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="nit">NIT <span class="text-danger">(*)</span></label>
                      <input class="form-control" type="text" name="nit" id="nit" maxlength="20" placeholder="Número de Identificación Tributaria" required>
                    </div>

                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="direccion">Dirección</label>
                      <input class="form-control" type="text" name="direccion" id="direccion" maxlength="255" placeholder="Dirección">
                    </div>

                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="telefono">Teléfono</label>
                      <input class="form-control" type="text" name="telefono" id="telefono" maxlength="15" placeholder="Teléfono">
                    </div>

                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="email">Email</label>
                      <input class="form-control" type="email" name="email" id="email" maxlength="100" placeholder="Email">
                    </div>

                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="imagen">Imagen</label>
                      <input class="form-control" type="file" name="imagen" id="imagen">
                      <input type="hidden" name="imagenactual" id="imagenactual">
                      <img src="" width="150px" height="120px" id="imagenmuestra" style="display: none; margin-top: 10px;">
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
  <?php
  } else {
    require 'noacceso.php';
  }
  require 'footer.php';
  ?>
  <script src="scripts/empresa.js"></script>
<?php
}
ob_end_flush();
?>