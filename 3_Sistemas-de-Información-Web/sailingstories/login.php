<?php
    include_once "include/sesion.php";
    include_once "include/rastreador.php";

    include "vistas.php";
    
    include_once "modelos/usuario.php";

    // Si el usuario ya ha iniciado sesión redirigir a la página de inicio
    if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_REGISTRADO) {
        header("Location: index.php");
    }
    // En caso contrario comprobar si se han enviado las credenciales
    if (isset($_POST["nombre_usuario"]) && isset($_POST["pass_usuario"])) {

        // Si se han enviado, validarlas
        $usuario = Usuario::login($_POST["nombre_usuario"], $_POST["pass_usuario"]);

        if ($usuario->get_tipo() == Usuario::USUARIO_ANONIMO) {
            vista_login(true);
        }
        else {
            // Establecer el usuario
            $_SESSION["usuario"] = $usuario;

            // Redirigir a inicio
            header("Location: index.php");
        }
    }
    // Si no se han enviado credenciales, mostrar la vista de login
    else {
        vista_login();
    }

    Rastreador::guardar_estadisticas();
?>