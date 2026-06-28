var tabla;

// Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    // Cargamos los items a los selects
    $.post("../ajax/producto.php?op=selectCategoria", function (r) {
        $("#idcategoria").html(r);
        $("#idcategoria").selectpicker('refresh');
    });

    $.post("../ajax/producto.php?op=selectSucursal", function (r) {
        $("#idsucursal").html(r);
        $("#idsucursal").selectpicker('refresh');
    });

    $("#imagenmuestra").hide();
}

// Función limpiar
function limpiar() {
    $("#codigo").val("");
    $("#nombre").val("");
    $("#descripcion").val("");
    $("#stock").val("");
    $("#preciocompra").val("");
    $("#precioventa").val("");
    $("#imagenmuestra").attr("src", "");
    $("#imagenactual").val("");
    $("#print").hide();
    $("#idproducto").val("");
}

// Función mostrar formulario
function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

// Cancelar form
function cancelarform() {
    limpiar();
    mostrarform(false);
}

// Función listar
function listar() {
    tabla = $('#tbllistado').dataTable({
        "responsive": true,
        "autoWidth": false,
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'collection',
            text: 'Reportes',
            orientation: 'landscape',
            buttons: [
                {
                    text: 'Copiar',
                    extend: 'copy',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    text: 'Excel',
                    extend: 'excel',
                    title: 'Productos del Sistema',
                    messageTop: 'Registros de productos del Sistema',
                    messageBottom: 'Documento generado el ' + new Date().toLocaleDateString('es-BO'),
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    text: 'CSV',
                    extend: 'csv',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    title: 'Productos del Sistema',
                    filename: 'productos_sistema_' + new Date().toISOString().slice(0, 10),
                    pageSize: 'LETTER',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        // Estilo básico
                        doc.defaultStyle.fontSize = 10;
                        doc.styles.tableHeader.fontSize = 11;
                        doc.styles.tableHeader.fillColor = '#4b545c';
                        doc.styles.tableHeader.color = '#ffffff';

                        // Hacer que la tabla ocupe todo el ancho disponible
                        doc.content[doc.content.length - 1].table.widths = Array(doc.content[doc.content.length - 1].table.body[0].length + 1).join('*').split('');

                        // Agregar título con fecha
                        doc.content.splice(0, 1, {
                            text: 'PRODUCTOS DEL SISTEMA - SIMPLEPOS',
                            style: {
                                fontSize: 16,
                                alignment: 'center',
                                bold: true,
                                margin: [0, 10, 0, 10]
                            }
                        });

                        // Agregar titulo
                        let tituloTexto = 'Productos registrados';

                        doc.content.splice(1, 0, {
                            text: tituloTexto,
                            style: {
                                fontSize: 11,
                                alignment: 'center',
                                italic: true,
                                margin: [0, 0, 0, 10]
                            }
                        });

                        // Agregar fecha de generación
                        doc.content.splice(2, 0, {
                            text: 'Generado el: ' + new Date().toLocaleString('es-BO'),
                            style: {
                                fontSize: 9,
                                alignment: 'right',
                                margin: [0, 0, 0, 10]
                            }
                        });

                        // Pie de página
                        doc.footer = function (currentPage, pageCount) {
                            return {
                                columns: [{
                                    text: 'SimplePOS - Sistema de Ventas',
                                    alignment: 'left',
                                    fontSize: 8
                                },
                                {
                                    text: 'Página ' + currentPage + ' de ' + pageCount,
                                    alignment: 'center',
                                    fontSize: 8
                                },
                                {
                                    text: 'Confidencial',
                                    alignment: 'right',
                                    fontSize: 8
                                }
                                ],
                                margin: [40, 0]
                            };
                        };
                    }
                },
            ]
        },
        {
            extend: 'colvis',
            text: 'Visualización de columnas'
        }
        ],
        "ajax": {
            url: '../ajax/producto.php?op=listar',
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "pageLength": 10,
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        "order": [[0, "desc"]],
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    }).DataTable();
}

// Función para guardar o editar
function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/producto.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        },
        complete: function () {
            $("#btnGuardar").prop("disabled", false);
        }
    });
}

function mostrar(idproducto) {
    $.post("../ajax/producto.php?op=mostrar", { idproducto: idproducto },
        function (data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#idcategoria").val(data.idcategoria);
            $("#idcategoria").selectpicker('refresh');
            $("#idsucursal").val(data.idsucursal);
            $("#idsucursal").selectpicker('refresh');
            $("#codigo").val(data.codigo);
            $("#nombre").val(data.nombre);
            $("#stock").val(data.stock);
            $("#preciocompra").val(data.preciocompra);
            $("#precioventa").val(data.precioventa);
            $("#descripcion").val(data.descripcion);
            $("#imagenmuestra").show();
            $("#imagenmuestra").attr("src", data.imagen ? "../files/productos/" + data.imagen : "../public/img/default-50x50.gif");
            $("#imagenactual").val(data.imagen);
            $("#idproducto").val(data.idproducto);
            generarbarcode();
        });
}

// Función para desactivar
function desactivar(idproducto) {
    bootbox.confirm("¿Está seguro de desactivar este producto?", function (result) {
        if (result) {
            $.post("../ajax/producto.php?op=desactivar", { idproducto: idproducto }, function (e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

function activar(idproducto) {
    bootbox.confirm("¿Está seguro de activar este producto?", function (result) {
        if (result) {
            $.post("../ajax/producto.php?op=activar", { idproducto: idproducto }, function (e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

function generarbarcode() {
    codigo = $("#codigo").val();
    JsBarcode("#barcode", codigo);
    $("#print").show();
}

init();