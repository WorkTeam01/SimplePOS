var tabla;

// Función que se ejecuta al inicio
function init() {
   mostrarform(false);
   listar();

   $("#formulario").on("submit", function (e) {
      guardaryeditar(e);
   });
}

// Función limpiar formulario
function limpiar() {
   $("#nombre").val("");
   $("#apellidopaterno").val("");
   $("#apellidomaterno").val("");
   $("#tipodocumento").val("");
   $("#tipodocumento").selectpicker('refresh');
   $("#numdocumento").val("");
   $("#direccion").val("");
   $("#telefono").val("");
   $("#email").val("");
   $("#idpersona").val("");
}

// Mostrar/ocultar formulario
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

// Cancelar formulario
function cancelarform() {
   limpiar();
   mostrarform(false);
}

// Función para listar registros
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
               title: 'Proveedores del Sistema',
               messageTop: 'Registros de proveedores del Sistema',
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
               title: 'Proveedores del Sistema',
               filename: 'proveedores_sistema_' + new Date().toISOString().slice(0, 10),
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
                     text: 'PROVEEDORES DEL SISTEMA - SIMPLEPOS',
                     style: {
                        fontSize: 16,
                        alignment: 'center',
                        bold: true,
                        margin: [0, 10, 0, 10]
                     }
                  });

                  // Agregar titulo
                  let tituloTexto = 'Proveedores registrados';

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
         url: '../ajax/persona.php?op=listarp',
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

   // Validar formato de correo si no está vacío
   var email = $("#email").val();
   if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      bootbox.alert("El formato del correo electrónico no es válido");
      return false;
   }

   $("#btnGuardar").prop("disabled", true);
   var formData = new FormData($("#formulario")[0]);

   $.ajax({
      url: "../ajax/persona.php?op=guardaryeditar",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (datos) {
         if (datos == "El número de documento ya está registrado") {
            bootbox.alert(datos);
         } else if (datos == "El correo electrónico ya está registrado") {
            bootbox.alert(datos);
         } else {
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
         }
      },
      error: function (xhr, status, error) {
         bootbox.alert("Ha ocurrido un error al procesar la solicitud");
         console.error(error);
      },
      complete: function () {
         $("#btnGuardar").prop("disabled", false);
      }
   });
}

// Mostrar datos de una persona
function mostrar(idpersona) {
   $.post("../ajax/persona.php?op=mostrar", { idpersona: idpersona },
      function (data, status) {
         data = JSON.parse(data);
         mostrarform(true);

         $("#nombre").val(data.nombre);
         $("#apellidopaterno").val(data.apellidopaterno);
         $("#apellidomaterno").val(data.apellidomaterno || "");
         $("#tipodocumento").val(data.tipodocumento);
         $("#tipodocumento").selectpicker('refresh');
         $("#numdocumento").val(data.numdocumento);
         $("#direccion").val(data.direccion || "");
         $("#telefono").val(data.telefono || "");
         $("#email").val(data.email || "");
         $("#idpersona").val(data.idpersona);
      }
   );
}

// Función para desactivar persona
function desactivar(idpersona) {
   bootbox.confirm("¿Está seguro de desactivar esta persona?", function (result) {
      if (result) {
         $.post("../ajax/persona.php?op=desactivar", { idpersona: idpersona }, function (e) {
            bootbox.alert(e);
            tabla.ajax.reload();
         });
      }
   });
}

// Función para activar persona
function activar(idpersona) {
   bootbox.confirm("¿Está seguro de activar esta persona?", function (result) {
      if (result) {
         $.post("../ajax/persona.php?op=activar", { idpersona: idpersona }, function (e) {
            bootbox.alert(e);
            tabla.ajax.reload();
         });
      }
   });
}

init();