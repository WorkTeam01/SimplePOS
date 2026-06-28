var tabla;

// Función que se ejecuta al inicio
function init() {
    listar();
    // Cargamos los clientes en el select
    $.post("../ajax/venta.php?op=selectCliente", function (r) {
        $("#idcliente").html(r);
        $('#idcliente').selectpicker('refresh');
    });

    // Inicializamos componentes
    $('#fecha_inicio').change(function () {
        if ($('#fecha_inicio').val() > $('#fecha_fin').val() && $('#fecha_fin').val() != '') {
            alert("La fecha inicio no puede ser mayor a la fecha fin");
            $('#fecha_inicio').val('');
        }
    });

    $('#fecha_fin').change(function () {
        if ($('#fecha_inicio').val() > $('#fecha_fin').val() && $('#fecha_inicio').val() != '') {
            alert("La fecha fin no puede ser menor a la fecha inicio");
            $('#fecha_fin').val('');
        }
    });
}

// Función para listar las ventas según los filtros
function listar() {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();
    var idcliente = $("#idcliente").val();

    // Validar fechas
    if (fecha_inicio && fecha_fin && new Date(fecha_inicio) > new Date(fecha_fin)) {
        alert("La fecha de inicio no puede ser mayor a la fecha fin");
        return false;
    }

    // Destruir tabla existente si ya existe
    if ($.fn.DataTable.isDataTable('#tbllistado')) {
        tabla.destroy();
    }

    tabla = $('#tbllistado').dataTable({
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
                    title: 'Ventas por Cliente',
                    messageTop: 'Registros de ventas por cliente',
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
                    title: 'Ventas por Cliente',
                    filename: 'ventas_cliente_' + new Date().toISOString().slice(0, 10),
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
                            text: 'VENTAS POR CLIENTE - SIMPLEPOS',
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
            url: '../ajax/consultas.php?op=ventasfechacliente',
            data: {
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin,
                idcliente: idcliente
            },
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "pageLength": 10,
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
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
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;

            // Convertir a número si es necesario
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            // Total de todas las páginas
            var total = api
                .column(3)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // Total de la página actual
            var pageTotal = api
                .column(3, { page: 'current' })
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            // Actualizar el pie de página
            $(api.column(3).footer()).html(
                'Bs. ' + pageTotal.toFixed(2) + ' (Bs. ' + total.toFixed(2) + ' total)'
            );
        }
    }).DataTable();
}

// Función para ver detalles de una venta
function verDetalle(idventa) {
    window.open('../reportes/rptventas.php?id=' + idventa, '_blank');
}

init();