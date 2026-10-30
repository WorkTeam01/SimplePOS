<?php
ob_start();
require_once "../config/auth.php";
iniciarSesionSegura();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.html");
} else {
    require 'header.php';
?>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h1 class="box-title">Mi Perfil</h1>
                        </div>
                        <!--box-header-->

                        <!--centro-->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="box box-primary">
                                        <div class="box-body box-profile">
                                            <img class="profile-user-img img-responsive" id="imagenmuestra" src="<?php echo !empty($_SESSION['imagen']) ? '../files/usuarios/'.$_SESSION['imagen'] : '../public/img/avatar.png'; ?>" alt="Foto de perfil">
                                            <h3 class="profile-username text-center"><?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellidopaterno'] . ' ' . $_SESSION['apellidomaterno']; ?></h3>
                                            <p class="text-muted text-center"><?php echo $_SESSION['cargo']; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="nav-tabs-custom">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#datos" data-toggle="tab">Datos Personales</a></li>
                                            <li><a href="#clave" data-toggle="tab">Cambiar Contraseña</a></li>
                                        </ul>
                                    </div>

                                    <div class="tab-content">
                                        <div class="active tab-pane" id="datos">
                                            <form action="" name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                                                <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                                    <label for="nombre">Nombres <span class="text-danger">(*)</span></label>
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
                                                    <label for="imagen">Imagen</label>
                                                    <input class="form-control" type="file" name="imagen" id="imagen">
                                                    <input type="hidden" name="imagenactual" id="imagenactual">
                                                </div>
                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <button class="btn btn-success" type="submit" id="btnGuardar"><i class="fa fa-save" style="margin-right: 5px;"></i> Actualizar</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane" id="clave">
                                            <form action="" name="formClave" id="formClave" method="POST">
                                                <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                                    <label for="claveActual">Contraseña Actual <span class="text-danger">(*)</span></label>
                                                    <input class="form-control" type="password" name="claveActual" id="claveActual" maxlength="64" placeholder="Contraseña Actual" required>
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                                    <label for="claveNueva">Nueva Contraseña <span class="text-danger">(*)</span></label>
                                                    <input class="form-control" type="password" name="claveNueva" id="claveNueva" maxlength="64" placeholder="Nueva Contraseña" required>
                                                </div>
                                                <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                                    <label for="claveConfirm">Confirmar Nueva Contraseña <span class="text-danger">(*)</span></label>
                                                    <input class="form-control" type="password" name="claveConfirm" id="claveConfirm" maxlength="64" placeholder="Confirmar Nueva Contraseña" required>
                                                </div>
                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <button class="btn btn-danger" type="submit" id="btnCambiarClave"><i class="fa fa-key" style="margin-right: 5px;"></i> Cambiar Contraseña</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--fin centro-->
                </div>
            </div>
        </section>
    </div>
    <?php
    require 'footer.php';
    ?>
    <script src="scripts/perfil.js"></script>
<?php
}
ob_end_flush();
?>