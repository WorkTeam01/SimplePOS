var tabla;

// Función que se ejecuta al inicio
function init() {
    // Configurar fechas por defecto (últimos 7 días)
    var fechaFin = new Date();
    var fechaInicio = new Date();
    fechaInicio.setDate(fechaInicio.getDate() - 7);

    $("#fecha_inicio").val(fechaInicio.toISOString().split('T')[0]);
    $("#fecha_fin").val(fechaFin.toISOString().split('T')[0]);

    // Inicializar la tabla
    listar();

    // Agregar eventos para detectar cambios en las fechas
    $("#fecha_inicio").change(function () {
        listar();
    });

    $("#fecha_fin").change(function () {
        listar();
    });
}

// Función para listar las compras según el rango de fechas
function listar() {
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();

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
            url: '../ajax/consultas.php?op=comprasfecha',
            data: {
                fecha_inicio: fecha_inicio,
                fecha_fin: fecha_fin
            },
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "pageLength": 10,
        "lenghtMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
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

init();