var tabla;
var cont = 0;
var detalles = 0;

function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    $.post("../ajax/venta.php?op=selectCliente", function (r) {
        $("#idcliente").html(r);
        $('#idcliente').selectpicker({
            liveSearch: true,
            liveSearchPlaceholder: 'Buscar cliente...',
            size: 5
        });
        $('#idcliente').selectpicker('refresh');
    });

    $("#metodopago").change(function () {
        mostrarCamposPago();
    });

    mostrarCamposPago();
}

function limpiar() {
    $("#idcliente").val("");
    $("#cliente").val("");
    $("#totalventa").val("");
    $(".filas").remove();
    $("#total").html("0");
    cont = 0;
    detalles = 0;

    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    $("#fechacreacion").val(now.getFullYear() + "-" + (month) + "-" + (day));

    $("#campos-pago").empty();
    mostrarCamposPago();
}

function calcularTotalPago() {
    var metodo = $("#metodopago").val();
    var totalVenta = parseFloat($("#totalventa").val()) || 0;
    var totalPagado = 0;
    var tieneEfectivo = false;
    var montoEfectivo = 0;
    var montoQR = 0;

    // Reiniciar campos
    $("#cambio").val("0.00");
    $("#total-pagado").text("0.00");
    $("#mensaje-validacion").text("");

    // Calcular montos según método seleccionado
    if (metodo.includes('+')) {
        var metodos = metodo.split('+');

        metodos.forEach(function (m) {
            m = m.trim();
            var inputId = m === 'EFECTIVO' ? "efectivo_recibido" : "monto_" + m.toLowerCase();
            var monto = parseFloat($("#" + inputId).val()) || 0;
            totalPagado += monto;

            if (m === 'EFECTIVO') {
                tieneEfectivo = true;
                montoEfectivo = monto;
            }
            if (m === 'QR') {
                montoQR = monto;
            }
        });
    } else {
        var inputId = metodo === 'EFECTIVO' ? "efectivo_recibido" : "monto_" + metodo.toLowerCase();
        totalPagado = parseFloat($("#" + inputId).val()) || 0;

        if (metodo === 'EFECTIVO') {
            tieneEfectivo = true;
            montoEfectivo = totalPagado;
        }
        if (metodo === 'QR') {
            montoQR = totalPagado;
        }
    }

    // Calcular cambio solo si hay efectivo
    if (tieneEfectivo) {
        var cambio = totalPagado - totalVenta;
        $("#cambio").val(cambio >= 0 ? cambio.toFixed(2) : "0.00");
    }

    // Actualizar visualización del total pagado
    $("#total-pagado").text(totalPagado.toFixed(2));

    // Validación y mensajes
    var mensaje = "";
    var esValido = true;

    if (totalPagado <= 0) {
        mensaje = "Debe ingresar al menos un monto";
        esValido = false;
    } else if (tieneEfectivo) {
        if (totalPagado < totalVenta) {
            mensaje = "El pago no cubre el total de la venta";
            esValido = false;
        } else {
            var cambio = totalPagado - totalVenta;
            if (cambio > 0) {
                mensaje = "Cambio: " + cambio.toFixed(2) + " Bs.";

                // Resaltar el cambio cuando es significativo
                if (cambio > totalVenta * 0.2) { // Si el cambio es más del 20% del total
                    $("#cambio").addClass('bg-warning');
                } else {
                    $("#cambio").removeClass('bg-warning');
                }
            }
        }
    } else {
        // Para métodos sin efectivo, debe coincidir exactamente
        if (Math.abs(totalPagado - totalVenta) > 0.01) {
            mensaje = "El total pagado debe coincidir exactamente con el total de la venta";
            esValido = false;
        }
    }

    $("#mensaje-validacion").text(mensaje);
    $("#btnGuardar").prop('disabled', !esValido);

    return esValido;
}

function mostrarCamposPago() {
    var metodo = $("#metodopago").val();
    var html = '';
    var totalVenta = parseFloat($("#totalventa").val()) || 0;

    $("#campos-pago").empty().addClass('compact-container');

    if (metodo.includes('+')) {
        var metodos = metodo.split('+');

        metodos.forEach(function (m) {
            m = m.trim();
            if (m === 'EFECTIVO') {
                html += `
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ultra-compact">
                    <label class="compact-label">Efectivo (*):</label>
                    <input class="form-control ultra-compact-input" type="number" name="efectivo_recibido" id="efectivo_recibido" 
                           min="0" step="0.01" placeholder="0.00" required oninput="calcularTotalPago()">
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ultra-compact">
                    <label class="compact-label">Cambio:</label>
                    <input class="form-control ultra-compact-input" type="text" name="cambio" id="cambio" placeholder="0.00" readonly>
                </div>`;
            } else if (m === 'QR') {
                html += `
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ultra-compact">
                    <label class="compact-label">QR (*):</label>
                    <input class="form-control ultra-compact-input" type="number" name="monto_qr" id="monto_qr" 
                           min="0" step="0.01" placeholder="0.00" required oninput="calcularTotalPago()">
                </div>`;
            } else if (m === 'TARJETA') {
                html += `
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ultra-compact">
                    <label class="compact-label">Tarjeta (*):</label>
                    <input class="form-control ultra-compact-input" type="number" name="monto_tarjeta" id="monto_tarjeta" 
                           min="0" step="0.01" placeholder="0.00" required oninput="calcularTotalPago()">
                </div>`;
            } else if (m === 'TRANSFERENCIA') {
                html += `
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ultra-compact">
                    <label class="compact-label">Transf. (*):</label>
                    <input class="form-control ultra-compact-input" type="number" name="monto_transferencia" id="monto_transferencia" 
                           min="0" step="0.01" placeholder="0.00" required oninput="calcularTotalPago()">
                </div>`;
            }
        });
    } else {
        if (metodo === 'EFECTIVO') {
            html = `
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ultra-compact">
                <label class="compact-label">Efectivo (*):</label>
                <input class="form-control ultra-compact-input" type="number" name="efectivo_recibido" id="efectivo_recibido" 
                       min="${totalVenta}" step="0.01" placeholder="0.00" required oninput="calcularTotalPago()">
            </div>
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ultra-compact">
                <label class="compact-label">Cambio:</label>
                <input class="form-control ultra-compact-input" type="text" name="cambio" id="cambio" placeholder="0.00" readonly>
            </div>
          `;
        } else {
            html = `
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 ultra-compact">
                <label class="compact-label">${metodo} (*):</label>
                <input class="form-control ultra-compact-input" type="number" name="monto_${metodo.toLowerCase()}" id="monto_${metodo.toLowerCase()}" 
                       min="${totalVenta}" max="${totalVenta}" step="0.01" placeholder="0.00" value="${totalVenta.toFixed(2)}" required oninput="calcularTotalPago()">
            </div>`;
        }
    }

    $("#campos-pago").html(html);
    calcularTotalPago();
}

function mostrarform(flag) {
    limpiar();
    if (flag) {
        $("#listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnagregar").hide();
        listarProductos();

        $("#btnGuardar").hide();
        $("#btnCancelar").show();
        $("#btnAgregarArt").show();
    } else {
        $("#listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

function cancelarform() {
    limpiar();
    mostrarform(false);
}

function listar() {
    tabla = $('#tbllistado').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "responsive": true,
        "autoWidth": false,
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
                    title: 'Ventas del Sistema',
                    messageTop: 'Registros de ventas del Sistema',
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
                    title: 'Ventas del Sistema',
                    filename: 'ventas_sistema_' + new Date().toISOString().slice(0, 10),
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
                            text: 'VENTAS DEL SISTEMA - SIMPLEPOS',
                            style: {
                                fontSize: 16,
                                alignment: 'center',
                                bold: true,
                                margin: [0, 10, 0, 10]
                            }
                        });

                        // Agregar titulo
                        let tituloTexto = 'Ventas registradas';

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
            url: '../ajax/venta.php?op=listar',
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
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ ventas",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 ventas",
            "sInfoFiltered": "(filtrado de un total de _MAX_ ventas)",
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

function listarProductos() {
    tabla = $('#tblproductos').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "responsive": true,
        "autoWidth": false,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'colvis',
            text: 'Visualización de columnas'
        }],
        "ajax": {
            url: '../ajax/venta.php?op=listarProductos',
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "pageLength": 5,
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        "order": [[1, "asc"]],
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún producto disponible",
            "sInfo": "Mostrando productos del _START_ al _END_ de un total de _TOTAL_ productos",
            "sInfoEmpty": "Mostrando productos del 0 al 0 de un total de 0 productos",
            "sInfoFiltered": "(filtrado de un total de _MAX_ productos)",
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

function guardaryeditar(e) {
    e.preventDefault();

    if (detalles == 0) {
        bootbox.alert("Debe agregar al menos un producto a la venta");
        return false;
    }

    if (!calcularTotalPago()) {
        bootbox.alert("Verifique los montos ingresados");
        return false;
    }

    var formData = new FormData($("#formulario")[0]);
    var metodo = $("#metodopago").val();

    // Procesar métodos de pago combinados
    if (metodo.includes('+')) {
        var metodos = metodo.split('+');
        metodos.forEach(function (m, index) {
            m = m.trim();
            var inputName = m === 'EFECTIVO' ? 'efectivo_recibido' : 'monto_' + m.toLowerCase();
            var monto = $("#" + inputName).val() || 0;

            formData.append('pagos_detalle[' + index + '][idmetodo]',
                m === 'EFECTIVO' ? 1 :
                    m === 'TARJETA' ? 2 :
                        m === 'TRANSFERENCIA' ? 3 : 4);
            formData.append('pagos_detalle[' + index + '][monto]', monto);

            if (m === 'EFECTIVO') {
                formData.append('pagos_detalle[' + index + '][efectivo_recibido]', $("#efectivo_recibido").val());
                formData.append('pagos_detalle[' + index + '][efectivo_cambio]', $("#cambio").val());
            }
        });
    } else {
        // Pago único
        formData.append('pagos_detalle[0][idmetodo]',
            metodo === 'EFECTIVO' ? 1 :
                metodo === 'TARJETA' ? 2 :
                    metodo === 'TRANSFERENCIA' ? 3 : 4);
        formData.append('pagos_detalle[0][monto]', metodo === 'EFECTIVO' ? $("#efectivo_recibido").val() : $("#monto_" + metodo.toLowerCase()).val());

        if (metodo === 'EFECTIVO') {
            formData.append('pagos_detalle[0][efectivo_recibido]', $("#efectivo_recibido").val());
            formData.append('pagos_detalle[0][efectivo_cambio]', $("#cambio").val());
        }
    }

    $.ajax({
        url: "../ajax/venta.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            if (datos == "Agotado") {
                bootbox.alert("Uno o más productos no tienen suficiente stock");
            } else if (datos && datos != "0") {
                bootbox.confirm({
                    message: "Venta registrada correctamente. ¿Desea imprimir el ticket?",
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
                            var win = window.open('../reportes/rptventas.php?id=' + datos, '_blank');
                            if (win) {
                                win.focus();
                            } else {
                                bootbox.alert("El navegador bloqueó la ventana emergente. Por favor, permita ventanas emergentes para este sitio.");
                            }
                        }
                        mostrarform(false);
                        listar();
                        limpiar();
                    }
                });
            } else {
                bootbox.alert("No se pudo registrar la venta");
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
            bootbox.alert("Ocurrió un error al procesar la venta");
        }
    });
}

function mostrar(idventa) {
    $.post("../ajax/venta.php?op=mostrar", { idventa: idventa },
        function (data, status) {
            data = JSON.parse(data);
            mostrarform(true);

            $("#idcliente").val(data.idcliente);
            $("#idcliente").selectpicker('refresh');
            $("#fechacreacion").val(data.fecha);
            $("#idventa").val(data.idventa);

            $("#btnGuardar").hide();
            $("#btnCancelar").show();
            $("#btnAgregarArt").hide();
        });
    $.post("../ajax/venta.php?op=listarDetalle&id=" + idventa, function (r) {
        $("#detalles").html(r);
    });
}

function anular(idventa) {
    bootbox.confirm("¿Está seguro de anular esta venta?", function (result) {
        if (result) {
            $.post("../ajax/venta.php?op=anular", { idventa: idventa }, function (e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

function agregarDetalle(idproducto, producto, precioventa, stock) {
    var productos = document.getElementsByName("idproducto[]");
    for (var i = 0; i < productos.length; i++) {
        if (productos[i].value == idproducto) {
            bootbox.alert("Este producto ya está agregado");
            return false;
        }
    }

    if (stock <= 0) {
        bootbox.alert("Producto agotado");
        return false;
    }

    var cantidad = 1;
    var descuento = 0;

    if (idproducto != "") {
        var subtotal = cantidad * precioventa;
        var fila = '<tr class="filas" id="fila' + cont + '">' +
            '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ')">X</button></td>' +
            '<td><input type="hidden" name="idproducto[]" value="' + idproducto + '">' + producto + '</td>' +
            '<td><input type="number" name="cantidad[]" id="cantidad[]" value="' + cantidad + '" min="1" max="' + stock + '" onchange="modificarSubtotales()"></td>' +
            '<td><input type="number" name="precioventa[]" id="precioventa[]" value="' + precioventa + '" onchange="modificarSubtotales()"></td>' +
            '<td><input type="number" name="descuento[]" value="' + descuento + '" min="0" onchange="modificarSubtotales()"></td>' +
            '<td><span id="subtotal' + cont + '" name="subtotal">' + subtotal.toFixed(2) + '</span></td>' +
            '</tr>';
        cont++;
        detalles++;
        $('#detalles').append(fila);
        modificarSubtotales();
    } else {
        bootbox.alert("Error al ingresar el detalle, revise los datos del producto");
    }
}

function modificarSubtotales() {
    var cant = document.getElementsByName("cantidad[]");
    var prev = document.getElementsByName("precioventa[]");
    var desc = document.getElementsByName("descuento[]");
    var sub = document.getElementsByName("subtotal");

    for (var i = 0; i < cant.length; i++) {
        var cantidad = parseFloat(cant[i].value);
        var precio = parseFloat(prev[i].value);
        var descuento = parseFloat(desc[i].value || 0);

        var maxStock = parseInt($(cant[i]).attr('max'));
        if (cantidad > maxStock) {
            bootbox.alert("No hay suficiente stock para este producto");
            $(cant[i]).val(maxStock);
            cantidad = maxStock;
        }

        var subtotal = (precio - descuento) * cantidad;
        sub[i].innerHTML = subtotal.toFixed(2);
    }
    calcularTotales();
}

function calcularTotales() {
    var sub = document.getElementsByName("subtotal");
    var total = 0.0;

    for (var i = 0; i < sub.length; i++) {
        total += parseFloat(sub[i].innerHTML);
    }

    $("#total").html("S/. " + total.toFixed(2));
    $("#totalventa").val(total.toFixed(2));

    mostrarCamposPago();
    evaluar();
}

function evaluar() {
    if (detalles > 0) {
        $("#btnGuardar").show();
    } else {
        $("#btnGuardar").hide();
    }
}

function eliminarDetalle(indice) {
    $("#fila" + indice).remove();
    calcularTotales();
    detalles = detalles - 1;
    evaluar();
}

init();
