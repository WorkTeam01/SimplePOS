var tabla;

//función que se ejecuta al inicio
function init() {
	listar();
}

//función listar
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
					title: 'Permisos del Sistema',
					messageTop: 'Listado de permisos del Sistema',
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
					title: 'Permisos del Sistema',
					filename: 'permisos_sistema_' + new Date().toISOString().slice(0, 10),
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
							text: 'PERMISOS DEL SISTEMA - SIMPLEPOS',
							style: {
								fontSize: 16,
								alignment: 'center',
								bold: true,
								margin: [0, 10, 0, 10]
							}
						});

						// Agregar titulo
						let tituloTexto = 'Permisos registrados';

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
			url: '../ajax/permiso.php?op=listar',
			type: "get",
			dataType: "json",
			error: function (e) {
				console.log(e.responseText);
			}
		},
		"bDestroy": true,
		"pageLength": 10,
		"lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
		"order": [[0, "asc"]],
		"language": {
			"sProcessing": "Procesando...",
			"sLengthMenu": "Mostrar _MENU_ registros",
			"sZeroRecords": "No se encontraron resultados",
			"sEmptyTable": "Ningún dato disponible en esta tabla",
			"sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ permisos",
			"sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 permisos",
			"sInfoFiltered": "(filtrado de un total de _MAX_ permisos)",
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

init();