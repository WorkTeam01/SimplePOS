$("#frmAcceso").on('submit', function (e) {
    e.preventDefault();
    var numdocumentoa = $("#numdocumentoa").val();
    var clavea = $("#clavea").val();

    // Validación simple de campos vacíos
    if (numdocumentoa === "") {
        bootbox.alert("Debe ingresar un número de documento");
        return false;
    }

    if (clavea === "") {
        bootbox.alert("Debe ingresar una contraseña");
        return false;
    }

    $.post("../ajax/usuario.php?op=verificar",
        { "numdocumentoa": numdocumentoa, "clavea": clavea },
        function (data) {
            if (data !== null && typeof data === 'object') {
                // Verificar si el usuario está activo
                if (data.estado === '0') {
                    bootbox.alert("Su cuenta está desactivada. Contacte al administrador.");
                    return;
                }

                // Redireccionar al escritorio
                $(location).attr("href", "escritorio.php");
            } else {
                bootbox.alert("Usuario y/o Password incorrectos");
                $("#clavea").val("");
                $("#clavea").focus();
            }
        }, 'json').fail(function () {
            bootbox.alert("Error al verificar credenciales");
        });
});