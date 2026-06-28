<?php
//activamos almacenamiento en el buffer
ob_start();
session_start();
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
                <h1 class="box-title">Usuarios</h1>
                <div class="box-tools pull-right">
                  <button class="btn btn-primary btn-sm" onclick="mostrarform(true)" id="btnagregar"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <!-- Listado de usuarios -->
                <div class="panel-body table-responsive" id="listadoregistros">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                    <thead>
                      <th>Opciones</th>
                      <th>Nombre Completo</th>
                      <th>Tipo Documento</th>
                      <th>Número Documento</th>
                      <th>Teléfono</th>
                      <th>Email</th>
                      <th>Cargo</th>
                      <th>Foto</th>
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
                      <input class="form-control" type="hidden" name="idusuario" id="idusuario">
                      <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Nombre" required>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="apellidopaterno">Apellido Paterno <span class="text-danger">(*)</span></label>
                      <input class="form-control" type="text" name="apellidopaterno" id="apellidopaterno" maxlength="100" placeholder="Apellido Paterno" required>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="apellidomaterno">Apellido Materno</label>
                      <input class="form-control" type="text" name="apellidomaterno" id="apellidomaterno" maxlength="100" placeholder="Apellido Materno">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="tipodocumento">Tipo Documento <span class="text-danger">(*)</span></label>
                      <select name="tipodocumento" id="tipodocumento" class="form-control selectpicker" required>
                        <option value="DNI">DNI</option>
                        <option value="RUC">RUC</option>
                        <option value="CEDULA">Cédula</option>
                        <option value="PASAPORTE">Pasaporte</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="numdocumento">Número de Documento <span class="text-danger">(*)</span></label>
                      <input type="text" class="form-control" name="numdocumento" id="numdocumento" placeholder="Número de documento" maxlength="20" required>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="direccion">Dirección</label>
                      <input class="form-control" type="text" name="direccion" id="direccion" maxlength="70" placeholder="Dirección">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="telefono">Teléfono</label>
                      <input class="form-control" type="text" name="telefono" id="telefono" maxlength="20" placeholder="Teléfono">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="correo">Email</label>
                      <input class="form-control" type="email" name="correo" id="correo" maxlength="70" placeholder="Email">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="cargo">Cargo</label>
                      <input class="form-control" type="text" name="cargo" id="cargo" maxlength="50" placeholder="Cargo">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="clave">Clave <span class="text-danger">(*)</span></label>
                      <input class="form-control" type="password" name="clave" id="clave" maxlength="64" placeholder="Clave">
                      <input type="hidden" name="claveactual" id="claveactual">
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                      <label for="imagen">Imagen</label>
                      <input class="form-control" type="file" name="imagen" id="imagen">
                      <input type="hidden" name="imagenactual" id="imagenactual">
                      <img src="" alt="" width="150px" height="120px" id="imagenmuestra" style="display: none; margin-top: 10px;">
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-xs-12">
                      <label>Permisos</label>
                      <ul id="permisos" style="list-style: none; column-count: 3;"></ul>
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
  <script src="scripts/usuario.js"></script>
<?php
}
ob_end_flush();
?>