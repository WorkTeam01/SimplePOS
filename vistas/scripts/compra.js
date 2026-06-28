var tabla;

$(document).on('hidden.bs.modal', '.modal', function () {
    // Corregir el problema de desplazamiento
    $('body').css('padding-right', '0');
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();

    // Asegurarse de que no haya scroll horizontal
    $('html, body').css({
        'overflow-x': 'hidden',
        'max-width': '100%'
    });
});

// Función que se ejecuta al inicio
function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    // Cargamos los items al select proveedor
    $.post("../ajax/compra.php?op=selectProveedor", function (r) {
        $("#idproveedor").html(r);
        $('#idproveedor').selectpicker('refresh');
    });
}

// Función limpiar
function limpiar() {
    $("#idproveedor").val("");
    $("#proveedor").val("");
    $("#total_compra").val("");
    $(".filas").remove();
    $("#total").html("0");

    // Obtenemos la fecha actual
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + (month) + "-" + (day);
    $("#fecha_hora").val(today);
}

// Función mostrar formulario
function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnagregar").hide();
        listarProductos();

        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        detalles = 0;
        $("#btnAgregarArt").show();
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
                    title: 'Compras del Sistema',
                    messageTop: 'Registros de compras del Sistema',
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
                    title: 'Compras del Sistema',
                    filename: 'compras_sistema_' + new Date().toISOString().slice(0, 10),
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
                            text: 'COMPRAS DEL SISTEMA - SIMPLEPOS',
                            style: {
                                fontSize: 16,
                                alignment: 'center',
                                bold: true,
                                margin: [0, 10, 0, 10]
                            }
                        });

                        // Agregar titulo
                        let tituloTexto = 'Compras registradas';

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
            url: '../ajax/compra.php?op=listar',
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
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    }).DataTable();
}

function listarProductos() {
    tabla = $('#tblproductos').dataTable({
        "responsive": true,
        "autoWidth": false,
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'colvis',
                text: 'Visualización de columnas'
            }
        ],
        "ajax": {
            url: '../ajax/compra.php?op=listarProductos',
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
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
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    }).DataTable();
}

// Función para guardaryeditar
function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/compra.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            bootbox.alert(datos);
            mostrarform(false);
            listar();
        }
    });

    limpiar();
}

function mostrar(idcompra) {
    $.post("../ajax/compra.php?op=mostrar", { idcompra: idcompra },
        function (data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#idproveedor").val(data.idproveedor);
            $("#idproveedor").selectpicker('refresh');
            $("#fecha_hora").val(data.fecha);
            $("#idcompra").val(data.idcompra);

            // Ocultar y mostrar los botones
            $("#btnGuardar").hide();
            $("#btnCancelar").show();
            $("#btnAgregarArt").hide();
        });

    $.post("../ajax/compra.php?op=listarDetalle&id=" + idcompra, function (r) {
        $("#detalles").html(r);
    });
}

// Variable de control global
var procesandoAccion = false;

// Función para anular
function anular(idcompra) {
    // Evitar múltiples clics
    if (procesandoAccion) return;
    procesandoAccion = true;

    bootbox.confirm({
        message: "¿Está seguro de anular esta compra?",
        buttons: {
            confirm: {
                label: 'Sí',
                className: 'btn-success'
            },
            cancel: {
                label: 'No',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.post("../ajax/compra.php?op=anular", { idcompra: idcompra }, function (e) {
                    bootbox.alert({
                        message: e,
                        callback: function () {
                            // Forzar recarga completa de la página en lugar de solo la tabla
                            location.reload();
                        }
                    });
                }).fail(function () {
                    procesandoAccion = false;
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                });
            } else {
                procesandoAccion = false;
            }
        }
    });
}

// Función para activar
function activar(idcompra) {
    // Evitar múltiples clics
    if (procesandoAccion) return;
    procesandoAccion = true;

    bootbox.confirm({
        message: "¿Está seguro de reactivar esta compra?",
        buttons: {
            confirm: {
                label: 'Sí',
                className: 'btn-success'
            },
            cancel: {
                label: 'No',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.post("../ajax/compra.php?op=activar", { idcompra: idcompra }, function (e) {
                    bootbox.alert({
                        message: e,
                        callback: function () {
                            // Forzar recarga completa de la página en lugar de solo la tabla
                            location.reload();
                        }
                    });
                }).fail(function () {
                    procesandoAccion = false;
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                });
            } else {
                procesandoAccion = false;
            }
        }
    });
}

// Variables para trabajar con las compras y sus detalles
var cont = 0;
var detalles = 0;

$("#btnGuardar").hide();

function agregarDetalle(idproducto, producto) {
    // Obtener el precio del producto via AJAX
    $.post("../ajax/compra.php?op=obtenerPrecioProducto", { idproducto: idproducto }, function (data) {
        if (data && data.preciocompra) {
            var cantidad = 1;
            var precio_compra = parseFloat(data.preciocompra);
            var subtotal = cantidad * precio_compra;

            var fila = '<tr class="filas" id="fila' + cont + '">' +
                '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')">X</button></td>' +
                '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>' +
                '<td><input type="number" name="cantidad[]" value="' + cantidad + '" onchange="modificarSubtotales()"></td>' +
                '<td><input type="number" name="precio_compra[]" value="' + precio_compra.toFixed(2) + '" onchange="modificarSubtotales()"></td>' +
                '<td><span name="subtotal">' + subtotal.toFixed(2) + '</span></td>' +
                '</tr>';

            cont++;
            detalles++;
            $('#detalles').append(fila);
            modificarSubtotales();
            $('#myModal').modal('hide');
        } else {
            alert("No se pudo obtener el precio del producto");
        }
    }, 'json');
}

function modificarSubtotales() {
    var cant = document.getElementsByName("cantidad[]");
    var prec = document.getElementsByName("precio_compra[]");
    var sub = document.getElementsByName("subtotal");

    for (var i = 0; i < cant.length; i++) {
        var inpC = cant[i];
        var inpP = prec[i];
        var inpS = sub[i];

        inpS.value = inpC.value * inpP.value;
        document.getElementsByName("subtotal")[i].innerHTML = inpS.value;
    }

    calcularTotales();
}

function calcularTotales() {
    var sub = document.getElementsByName("subtotal");
    var total = 0.0;

    for (var i = 0; i < sub.length; i++) {
        total += parseFloat(document.getElementsByName("subtotal")[i].innerHTML);
    }
    $("#total").html("S/." + total.toFixed(2));
    $("#total_compra").val(total.toFixed(2));
    evaluar();
}

function evaluar() {
    if (detalles > 0) {
        $("#btnGuardar").show();
    } else {
        $("#btnGuardar").hide();
        cont = 0;
    }
}

function eliminarDetalle(indice) {
    $("#fila" + indice).remove();
    calcularTotales();
    detalles = detalles - 1;
}

init();