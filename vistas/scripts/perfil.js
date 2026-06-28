var tabla;

// Función inicial
function init() {
    obtenerPerfil();

    // Evento para el formulario de datos personales
    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    // Evento para el formulario de cambio de contraseña
    $("#formClave").on("submit", function (e) {
        cambiarClave(e);
    });

    // Mostrar imagen al seleccionarla
    $("#imagen").change(function () {
        var imagen = this.files[0];
        var imagenType = imagen.type;
        var match = ["image/jpeg", "image/png", "image/jpg"];

        if (!(imagenType == match[0] || imagenType == match[1] || imagenType == match[2])) {
            $("#imagen").val("");
            bootbox.alert("Por favor, selecciona un archivo de imagen válido (JPEG/JPG/PNG)");
        } else {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#imagenmuestra").attr("src", e.target.result);
            }
            reader.readAsDataURL(imagen);
        }
    });

    // Validación de contraseñas
    $("#claveConfirm").on("keyup", function () {
        var claveNueva = $("#claveNueva").val();
        var claveConfirm = $(this).val();

        if (claveNueva !== claveConfirm) {
            $(this).addClass("is-invalid");
        } else {
            $(this).removeClass("is-invalid");
        }
    });
}

// Función para obtener los datos del perfil
function obtenerPerfil() {
    $.post("../ajax/perfil.php?op=obtener", function (data) {
        data = JSON.parse(data);

        // Llenar formulario con datos del usuario
        $("#nombre").val(data.nombre);
        $("#apellidopaterno").val(data.apellidopaterno);
        $("#apellidomaterno").val(data.apellidomaterno);
        $("#tipodocumento").val(data.tipodocumento);
        $("#tipodocumento").selectpicker('refresh');
        $("#numdocumento").val(data.numdocumento);
        $("#direccion").val(data.direccion);
        $("#telefono").val(data.telefono);
        $("#correo").val(data.correo);
        $("#imagenactual").val(data.imagen);
    });
}

// Función para guardar o editar
function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/perfil.php?op=actualizar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (datos) {
            bootbox.alert(datos);
            $("#btnGuardar").prop("disabled", false);
            obtenerPerfil();
            // Recargar la página para actualizar la imagen en el header
            setTimeout(function () {
                location.reload();
            }, 1500);
        }
    });
}

// Función para cambiar la contraseña
function cambiarClave(e) {
    e.preventDefault();

    // Validar que las contraseñas coincidan
    var claveNueva = $("#claveNueva").val();
    var claveConfirm = $("#claveConfirm").val();

    if (claveNueva !== claveConfirm) {
        bootbox.alert("Las contraseñas no coinciden");
        return;
    }

    $("#btnCambiarClave").prop("disabled", true);
    var formData = new FormData($("#formClave")[0]);

    $.ajax({
        url: "../ajax/perfil.php?op=cambiarClave",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,

        success: function (datos) {
            bootbox.alert(datos);
            $("#btnCambiarClave").prop("disabled", false);
            $("#formClave").trigger("reset");
        }
    });
}

init();