<?php
    include_once "include/sesion.php";
    include_once "modelos/usuario.php";
    include "vistas.php";

    // Si el usuario ya ha iniciado sesión, redirigir a la página de inicio
    if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_REGISTRADO) {
        header("Location: index.php");
    }
    // En caso contrario comprobar si se han enviado los datos de registro
    if (isset($_POST["nombre_usuario"]) && isset($_POST["correo_usuario"]) && isset($_POST["correo_usuario"]) && isset($_POST["pass_usuario"]) && isset($_POST["rep_pass_usuario"])) {

        // Validar la contraseña
        if ($_POST["pass_usuario"] == $_POST["rep_pass_usuario"]) {
            $nuevo_usuario = new Usuario();
            
            $nuevo_usuario->set_id($_POST["nombre_usuario"]);
            $nuevo_usuario->set_correo($_POST["correo_usuario"]);
            $nuevo_usuario->set_password($_POST["pass_usuario"]);

            // TODO comprobar que el usuario o el correo no existen una vez implementados los codigos de error de la base de datos
            if ($nuevo_usuario->guardar()) {
                // iniciar sesión
                $_SESSION["usuario"] = $nuevo_usuario;
                // Redirigir a inicio
                header("Location: index.php");
            }
            else {
                vista_crear_cuenta(true, "Ha ocurrido un error al crear el usuario. Vuelva a intentarlo más tarde.", $_POST["nombre_usuario"], $_POST["correo_usuario"]);
            }
        }
        else {
            vista_crear_cuenta(true, "Las contraseñas no coinciden", $_POST["nombre_usuario"], $_POST["correo_usuario"]);
        }
    }
    // Si no se han enviado datos, mostrar la vista de crear cuenta
    else {
        vista_crear_cuenta();
    }
?>