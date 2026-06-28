<?php
// Activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.html");
} else {
    require 'header.php';
    if ($_SESSION['almacen'] == 1) {
?>
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h1 class="box-title">Lista de Productos</h1>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-primary btn-sm" onclick="mostrarform(true)" id="btnagregar"><i class="fa fa-plus-circle"></i> Agregar</button>
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="panel-body table-responsive" id="listadoregistros">
                                    <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                                        <thead>
                                            <th>Opciones</th>
                                            <th>Nombre</th>
                                            <th>Categoría</th>
                                            <th>Código</th>
                                            <th>Stock</th>
                                            <th>Sucursal</th>
                                            <th>P. Compra</th>
                                            <th>P. Venta</th>
                                            <th>Imagen</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="panel-body" style="height: 400px;" id="formularioregistros">
                                    <form action="" name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="nombre">Nombre <span class="text-danger">(*)</span></label>
                                            <input class="form-control" type="hidden" name="idproducto" id="idproducto">
                                            <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Nombre" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="idcategoria">Categoría <span class="text-danger">(*)</span></label>
                                            <select name="idcategoria" id="idcategoria" class="form-control selectpicker" data-live-search="true" required></select>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="idsucursal">Sucursal <span class="text-danger">(*)</span></label>
                                            <select name="idsucursal" id="idsucursal" class="form-control selectpicker" data-live-search="true" required></select>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="stock">Stock <span class="text-danger">(*)</span></label>
                                            <input class="form-control" type="number" name="stock" id="stock" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="preciocompra">Precio Compra <span class="text-danger">(*)</span></label>
                                            <input class="form-control" type="number" step="0.01" name="preciocompra" id="preciocompra" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="precioventa">Precio Venta <span class="text-danger">(*)</span></label>
                                            <input class="form-control" type="number" step="0.01" name="precioventa" id="precioventa" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="descripcion">Descripción</label>
                                            <input class="form-control" type="text" name="descripcion" id="descripcion" maxlength="256" placeholder="Descripción">
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="imagen">Imagen</label>
                                            <input class="form-control" type="file" name="imagen" id="imagen">
                                            <input type="hidden" name="imagenactual" id="imagenactual">
                                            <img src="" alt="" width="150px" height="120" id="imagenmuestra">
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="codigo">Código <span class="text-danger">(*)</span></label>
                                            <div class="input-group">
                                                <input class="form-control" type="text" name="codigo" id="codigo" placeholder="Código del producto" required>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-success" type="button" onclick="generarbarcode()">Generar código</button>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label>Código de barras</label>
                                            <div id="print" class="text-center">
                                                <svg id="barcode"></svg>
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
    <?php
    } else {
        require 'noacceso.php';
    }
    require 'footer.php';
    ?>

    <script src="../public/js/JsBarcode.all.min.js"></script>
    <script src="scripts/producto.js"></script>
<?php
}
ob_end_flush();
?>