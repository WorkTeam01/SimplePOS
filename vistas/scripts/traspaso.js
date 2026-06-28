var tabla;

// Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });
}

// Función para limpiar el formulario
function limpiar() {
    $("#idtraspaso").val("");
    $("#motivo").val("");
    $("#observaciones").val("");
    $("#btnGuardar").prop("disabled", false);

    // Limpiar tabla de detalles
    $("#detalles tbody").empty();
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

// Función cancelar form
function cancelarform() {
    limpiar();
    mostrarform(false);
}

// Función listar traspasos
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
                        columns: [1, 2, 3, 4]
                    }
                },
                {
                    text: 'Excel',
                    extend: 'excel',
                    title: 'Traspasos de Stock',
                    messageTop: 'Registros de traspasos de stock del Sistema',
                    messageBottom: 'Documento generado el ' + new Date().toLocaleDateString('es-BO'),
                    exportOptions: {
                        columns: [1, 2, 3, 4]
                    }
                },
                {
                    text: 'CSV',
                    extend: 'csv',
                    exportOptions: {
                        columns: [1, 2, 3, 4]
                    }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    title: 'Traspasos de Stock',
                    filename: 'traspasos_stock_' + new Date().toISOString().slice(0, 10),
                    pageSize: 'LETTER',
                    exportOptions: {
                        columns: [1, 2, 3, 4]
                    },
                    customize: function (doc) {
                        // Estilo básico
                        doc.defaultStyle.fontSize = 10;
                        doc.styles.tableHeader.fontSize = 11;
                        doc.styles.tableHeader.fillColor = '#4b545c';
                        doc.styles.tableHeader.color = '#ffffff';

                        // Hacer que la tabla ocupe todo el ancho disponible
                        doc.content[doc.content.length - 1].table.widths = ['25%', '25%', '25%', '25%'];

                        // Agregar título con fecha
                        doc.content.splice(0, 1, {
                            text: 'TRASPASOS DE STOCK - SIMPLEPOS',
                            style: {
                                fontSize: 16,
                                alignment: 'center',
                                bold: true,
                                margin: [0, 10, 0, 10]
                            }
                        });

                        // Agregar titulo
                        let tituloTexto = 'Traspasos registrados';

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
            url: '../ajax/traspaso.php?op=listar',
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "pageLength": 10,
        "order": [[1, "desc"]],
        "columns": [
            { "data": "opciones" },
            { "data": "fecha_solicitud" },
            { "data": "motivo" },
            { "data": "usuario" },
            { "data": "estado" }
        ]
    }).DataTable();
}

// Función para guardar o editar
function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/traspaso.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            bootbox.alert(datos);
            mostrarform(false);
            listar();
        },
        error: function (error) {
            console.error("Error al guardar:", error);
            $("#btnGuardar").prop("disabled", false);
            bootbox.alert("Error al guardar el traspaso. Intente nuevamente.");
        }
    });
}

// Función para mostrar los datos de un traspaso
function mostrar(idtraspaso) {
    $.post("../ajax/traspaso.php?op=mostrar", { idtraspaso: idtraspaso }, function (data) {
        try {
            data = JSON.parse(data);

            // Mostrar datos del traspaso en el modal
            var estado_clase = '';
            if (data.estado == 'Pendiente') estado_clase = 'label-warning';
            else if (data.estado == 'Completado') estado_clase = 'label-success';
            else estado_clase = 'label-danger';

            var html = '<div class="row">' +
                '<div class="col-md-6"><strong>Solicitado por:</strong> ' + data.usuario + '</div>' +
                '<div class="col-md-6"><strong>Fecha solicitud:</strong> ' + data.fecha_solicitud + '</div>' +
                '</div><div class="row">' +
                '<div class="col-md-6"><strong>Motivo:</strong> ' + data.motivo + '</div>' +
                '<div class="col-md-6"><strong>Estado:</strong> <span class="label ' + estado_clase + '">' + data.estado + '</span></div>' +
                '</div>';

            if (data.observaciones) {
                html += '<div class="row">' +
                    '<div class="col-md-12"><strong>Observaciones:</strong> ' + data.observaciones + '</div>' +
                    '</div>';
            }

            if (data.fecha_completado) {
                html += '<div class="row">' +
                    '<div class="col-md-12"><strong>Fecha de completado:</strong> ' + data.fecha_completado + '</div>' +
                    '</div>';
            }

            $("#traspaso_info").html(html);

            // Cargar los detalles del traspaso
            $.post("../ajax/traspaso.php?op=listarDetalle", { idtraspaso: idtraspaso }, function (r) {
                try {
                    r = JSON.parse(r);

                    $("#tbldetalles tbody").empty();
                    if (r.aaData && r.aaData.length > 0) {
                        r.aaData.forEach(function (item) {
                            var estado_clase = '';
                            if (item.estado == 'Pendiente') estado_clase = 'label-warning';
                            else if (item.estado == 'Completado') estado_clase = 'label-success';
                            else estado_clase = 'label-danger';

                            var row = '<tr>' +
                                '<td>' + item.producto + '</td>' +
                                '<td>' + item.codigo + '</td>' +
                                '<td>' + item.stock_actual + '</td>' +
                                '<td>' + item.cantidad + '</td>' +
                                '<td><span class="label ' + estado_clase + '">' + item.estado + '</span></td>' +
                                '</tr>';

                            $("#tbldetalles tbody").append(row);
                        });
                    } else {
                        $("#tbldetalles tbody").append('<tr><td colspan="5" class="text-center">No hay detalles disponibles</td></tr>');
                    }
                } catch (error) {
                    console.error("Error al procesar detalles:", error);
                    $("#tbldetalles tbody").append('<tr><td colspan="5" class="text-center">Error al cargar detalles</td></tr>');
                }
            });

            $("#modalDetalles").modal("show");
        } catch (error) {
            console.error("Error al procesar datos del traspaso:", error);
            bootbox.alert("Error al cargar los datos del traspaso");
        }
    });
}

// Función para completar un traspaso
function completar(idtraspaso) {
    $.post("../ajax/traspaso.php?op=listarDetalle", { idtraspaso: idtraspaso }, function (r) {
        try {
            r = JSON.parse(r);

            if (r.aaData && r.aaData.length > 0) {
                var iddetalletraspaso = [];
                r.aaData.forEach(function (item) {
                    iddetalletraspaso.push(item.iddetalletraspaso);
                });

                bootbox.confirm("¿Está seguro de completar este traspaso?", function (result) {
                    if (result) {
                        $.post("../ajax/traspaso.php?op=completar",
                            { idtraspaso: idtraspaso, iddetalletraspaso: iddetalletraspaso },
                            function (e) {
                                bootbox.alert(e);
                                listar();
                            }
                        );
                    }
                });
            } else {
                bootbox.alert("No hay detalles para completar en este traspaso");
            }
        } catch (error) {
            console.error("Error al procesar detalles para completar:", error);
            bootbox.alert("Error al cargar los detalles del traspaso");
        }
    });
}

// Función para cancelar un traspaso
function cancelar(idtraspaso) {
    bootbox.confirm("¿Está seguro de cancelar este traspaso? Esta acción no se puede deshacer.", function (result) {
        if (result) {
            $.post("../ajax/traspaso.php?op=cancelar", { idtraspaso: idtraspaso }, function (e) {
                bootbox.alert(e);
                listar();
            });
        }
    });
}

// Función para agregar productos al detalle
function agregarDetalle(idproducto, producto, stock) {
    var cantidad = 1;

    if (stock > 0) {
        var fila = '<tr class="filas" id="fila' + idproducto + '">' +
            '<td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarDetalle(' + idproducto + ')"><i class="fa fa-trash"></i></button></td>' +
            '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>' +
            '<td>' + stock + '</td>' +
            '<td><input type="number" name="cantidad[]" min="1" max="' + stock + '" value="' + cantidad + '" class="form-control" required></td>' +
            '</tr>';

        // Verificar si el producto ya fue agregado
        if ($("#fila" + idproducto).length === 0) {
            $("#detalles tbody").append(fila);
        } else {
            bootbox.alert("El producto ya ha sido agregado");
        }
    } else {
        bootbox.alert("El producto seleccionado no tiene stock disponible");
    }

    $('#myModal').modal('hide');
}

// Función para eliminar un detalle
function eliminarDetalle(idproducto) {
    $("#fila" + idproducto).remove();
}

// Función para abrir el modal de productos
$(document).on('click', '#btnAgregarProducto', function () {
    $('#myModal').modal('show');

    // Inicializar la tabla de productos
    try {
        if (!$.fn.DataTable.isDataTable('#tblproductos')) {
            $('#tblproductos').DataTable({
                "aProcessing": true,
                "aServerSide": true,
                "responsive": true,
                "autoWidth": false,
                "ajax": {
                    url: '../ajax/traspaso.php?op=listarProductos',
                    type: "get",
                    dataType: "json",
                    error: function (e) {
                        console.error("Error en la solicitud Ajax de productos:", e.responseText);
                    }
                },
                "bDestroy": true,
                "iDisplayLength": 5,
                "columns": [
                    { "data": "opciones" },
                    { "data": "codigo" },
                    { "data": "nombre" },
                    { "data": "stock" },
                    { "data": "imagen" }
                ]
            });
        } else {
            $('#tblproductos').DataTable().ajax.reload();
        }
    } catch (error) {
        console.error("Error al inicializar DataTable:", error);
    }
});

init();