$(document).ready(function() {

    // Función auxiliar para hacer las peticiones a admin_json.php
    function peticionAdmin(url, datos, fun = null) {
        $.post(url, datos, function(data) {
            if (data["ok"]) {

                if (fun != null)
                    fun();
                    
                $(document).Toasts('create', {
                    class: 'bg-success',
                    title: data["titulo"],
                    autohide: true,
                    delay: 4000,
                    body: data["mensaje"]
                });
            }
            else {
                $(document).Toasts('create', {
                    class: 'bg-danger',
                    title: data["titulo"],
                    autohide: true,
                    delay: 4000,
                    body: data["mensaje"]
                });
            }
        });
    }

    // Vaciar estadísticas
    $("#boton_vaciar_estadisticas").click(function() {
        peticionAdmin("admin_json.php?accion=vaciar_estadisticas", {});
    });

    // Aprobar capítulo
    $(".boton_aceptar_capitulo").click(function() {
        var id_capitulo = $(this).parent().attr("data-id");

        peticionAdmin("admin_json.php?accion=gestionar_capitulos_enviados", {
            id: id_capitulo, 
            accion: "aceptar"
        }, function () {
            // Ocultar la fila
            var selector = ".fila[data-id='" + id_capitulo + "']";
            $(selector).fadeOut(function() {
                $(selector).remove();
            });
        });
    });

    // Rechazar capítulo
    $(".boton_rechazar_capitulo").click(function() {
        var id_capitulo = $(this).parent().attr("data-id");

        peticionAdmin("admin_json.php?accion=gestionar_capitulos_enviados", {
            id: id_capitulo, 
            accion: "rechazar"
        }, function () {
            // Ocultar la fila
            var selector = ".fila[data-id='" + id_capitulo + "']";
            $(selector).fadeOut(function() {
                $(selector).remove();
            });
        });
    });

    // Aceptar votación
    $(".boton_aceptar_votacion").click(function() {
        var id_capitulo = $(this).parent().attr("data-id");

        peticionAdmin("admin_json.php?accion=gestionar_votaciones", {
            id: id_capitulo, 
            accion: "aceptar"
        }, function () {
            // Ocultar la fila
            var selector = ".fila[data-id='" + id_capitulo + "']";
            $(selector).fadeOut(function() {
                $(selector).remove();
            });
        });
    });

    // Rechazar votación
    $(".boton_rechazar_votacion").click(function() {
        var id_capitulo = $(this).parent().attr("data-id");

        peticionAdmin("admin_json.php?accion=gestionar_votaciones", {
            id: id_capitulo, 
            accion: "rechazarr"
        }, function () {
            // Ocultar la fila
            var selector = ".fila[data-id='" + id_capitulo + "']";
            $(selector).fadeOut(function() {
                $(selector).remove();
            });
        });
    });

    // Crear usuario
    $(".boton_crear_usuario").click(function(e) {
        e.preventDefault();
        var pass = $("#password_cu").val();
        var repPass = $("#rep_password_cu").val();
        if (pass == repPass) {

            peticionAdmin("admin_json.php?accion=crear_usuario", {
                id: $("#id_usuario_cu").val(),
                correo: $("#correo_cu").val(),
                pass: $("#password_cu").val(),
            }, function () {
                // Vaciar los campos
                $("#id_usuario_cu").val("");
                $("#correo_cu").val("");
                $("#password_cu").val("");
                $("#rep_password_cu").val("");
            });
        }
        else {
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Error',
                autohide: true,
                delay: 4000,
                body: 'Las contraseñas no coinciden'
            });
        }
    });

    // Modificar usuario
    $(".boton_modificar_usuario").click(function(e) {
        e.preventDefault();
        var data = $(this).closest("tr").find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=modificar_usuario", data);
    });

    // Eliminar usuario
    $(".boton_eliminar_usuario").click(function(e) {
        e.preventDefault();
        var id_usuario = $(this).parent().attr("data-id");

        peticionAdmin("admin_json.php?accion=eliminar_usuario", {
            id: id_usuario
        }, function () {
            // Ocultar la fila
            var selector = ".fila[data-id='" + id_usuario + "']";
            $(selector).fadeOut(function() {
                $(selector).remove();
            });
        });
    });

    // Guardar cambios en el capítulo
    $(".boton_guardar_capitulo").click(function(e) {
        e.preventDefault();
        var data = $(this).closest("tr").find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=modificar_capitulo", data);
    });

    // Eliminar el capítulo
    $(".boton_eliminar_capitulo").click(function(e) {
        e.preventDefault();
        var fila = $(this).closest("tr");
        var data = fila.find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=eliminar_capitulo", data, function() {
            // Ocultar la fila
            fila.fadeOut(function() {
                fila.remove();
            });
        });
    });

    // Modificar una historia
    $(".boton_modificar_historia").click(function(e) {
        e.preventDefault();
        var data = $(this).closest("tr").find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=modificar_historia", data);
    });

    // Eliminar una historia
    $(".boton_eliminar_historia").click(function(e) {
        e.preventDefault();
        var fila = $(this).closest("tr");
        var data = fila.find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=eliminar_historia", data, function() {
            // Ocultar la fila
            fila.fadeOut(function() {
                fila.remove();
            });
        });
    });

    // Modificar una imagen
    $(".boton_modificar_imagen").click(function(e) {
        e.preventDefault();
        var data = $(this).closest("tr").find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=modificar_imagen", data);
    });

    // Eliminar una imagen
    $(".boton_eliminar_imagen").click(function(e) {
        e.preventDefault();
        var fila = $(this).closest("tr");
        var data = fila.find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=eliminar_imagen", data, function() {
            // Ocultar la fila
            fila.fadeOut(function() {
                fila.remove();
            });
        });
    });

    // Crear un nuevo género
    $(".boton_crear_genero").click(function(e) {
        e.preventDefault();
        var fila = $(this).closest("tr");
        var data = fila.find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=crear_genero", data, function() {
            $("#nombre_nuevo_genero").val("");
        });
    });

    // Eliminar un género
    $(".boton_eliminar_genero").click(function(e) {
        e.preventDefault();
        var fila = $(this).closest("tr");
        peticionAdmin("admin_json.php?accion=eliminar_genero", {
            genero: $(this).attr("data-genero")
        }, function() {
            // Ocultar la fila
            fila.fadeOut(function() {
                fila.remove();
            });
        });
    });

    // Crear un supuesto
    $(".boton_crear_supuesto").click(function(e) {
        e.preventDefault();
        var fila = $(this).closest("tr");
        var data = fila.find("form").serializeArray();
        console.log(data);
        peticionAdmin("admin_json.php?accion=crear_supuesto", data, function() {
            $("#nombre_nuevo_genero").val("");
        });
    });

    // Modificar un supuesto
    $(".boton_modificar_supuesto").click(function(e) {
        e.preventDefault();
        var data = $(this).closest("tr").find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=modificar_supuesto", data);
    });

    // Eliminar un supuesto
    $(".boton_eliminar_supuesto").click(function(e) {
        e.preventDefault();
        var fila = $(this).closest("tr");
        var data = fila.find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=eliminar_supuesto", data, function() {
            // Ocultar la fila
            fila.fadeOut(function() {
                fila.remove();
            });
        });
    });

    // Eliminar un administrador
    $(".boton_eliminar_admin").click(function(e) {
        e.preventDefault();
        var fila = $(this).closest("tr");
        var data = fila.find("form").serializeArray();
        peticionAdmin("admin_json.php?accion=eliminar_admin", data, function() {
            // Ocultar la fila
            fila.fadeOut(function() {
                fila.remove();
            });
        });
    });
});