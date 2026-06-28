<?php
// Activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.html");
} else {
    require 'header.php';

    if ($_SESSION['consultav'] == 1) {
?>
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h1 class="box-title">Consulta de Ventas por Fecha y Cliente</h1>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <!--box-header-->
                            <!--centro-->
                            <div class="panel-body table-responsive" id="listadoregistros">
                                <div class="row">
                                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <label>Fecha Inicio</label>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo date("Y-m-d"); ?>">
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <label>Fecha Fin</label>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo date("Y-m-d"); ?>">
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <label>Cliente</label>
                                        <select name="idcliente" id="idcliente" class="form-control selectpicker" data-live-search="true">
                                            <option value="">Todos los clientes</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12 box-pane-right" style="padding-top: 25px;">
                                        <button class="btn btn-success" onclick="listar()">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Método de Pago</th>
                                            <th>Opciones</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>Cliente</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Método de Pago</th>
                                            <th>Opciones</th>
                                        </tfoot>
                                    </table>
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
    <script src="scripts/ventasfechacliente.js"></script>
<?php
}

ob_end_flush();
?>