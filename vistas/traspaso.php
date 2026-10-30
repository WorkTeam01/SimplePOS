<?php
//Activar el almacenamiento en el buffer
ob_start();
require_once "../config/auth.php";
iniciarSesionSegura();

if (!isset($_SESSION["nombre"])) {
    header("Location: login.html");
} else {
    require 'header.php';

    if ($_SESSION['almacen'] == 1) {
?>
        <div class="content-wrapper">
            <section class="content-header">
                <h1>Traspasos de Stock</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h1 class="box-title">Traspasos</h1>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-primary btn-sm" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Nuevo</button>
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <!-- centro -->
                            <div class="panel-body table-responsive" id="listadoregistros">
                                <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                                    <thead>
                                        <th>Opciones</th>
                                        <th>Fecha</th>
                                        <th>Motivo</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <div class="panel-body" id="formularioregistros">
                                <form name="formulario" id="formulario" method="POST">
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Motivo:</label>
                                        <input type="hidden" name="idtraspaso" id="idtraspaso">
                                        <input type="text" class="form-control" name="motivo" id="motivo" maxlength="100" placeholder="Motivo del traspaso" required>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Observaciones:</label>
                                        <textarea class="form-control" name="observaciones" id="observaciones" rows="3" placeholder="Observaciones adicionales"></textarea>
                                    </div>

                                    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                        <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                                            <thead style="background-color:#A9D0F5">
                                                <th>Opciones</th>
                                                <th>Producto</th>
                                                <th>Stock Actual</th>
                                                <th>Cantidad a Traspasar</th>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <button class="btn btn-primary" type="button" id="btnAgregarProducto"><i class="fa fa-plus"></i> Agregar Productos</button>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <hr>
                                        <div class="text-right">
                                            <button class="btn btn-primary btn-lg" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
                                            <button class="btn btn-danger btn-lg" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" style="width: 70% !important;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Seleccione un Producto</h4>
                                        </div>
                                        <div class="modal-body">
                                            <table id="tblproductos" class="table table-striped table-bordered table-condensed table-hover">
                                                <thead>
                                                    <th>Opciones</th>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Stock</th>
                                                    <th>Imagen</th>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin modal -->

                            <!-- Modal Detalles -->
                            <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" style="width: 70% !important;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">Detalles del Traspaso</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div id="traspaso_info">
                                                <!-- Aquí se cargarán los datos del traspaso -->
                                            </div>
                                            <table id="tbldetalles" class="table table-striped table-bordered table-condensed table-hover">
                                                <thead>
                                                    <th>Producto</th>
                                                    <th>Código</th>
                                                    <th>Stock Actual</th>
                                                    <th>Cantidad</th>
                                                    <th>Estado</th>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fin modal detalles -->
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
    ?>

    <script src="scripts/traspaso.js"></script>

<?php
}
ob_end_flush();
?>