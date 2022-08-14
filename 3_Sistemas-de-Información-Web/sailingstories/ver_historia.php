<?php
    

    // Cargar modelos necesarios
    include_once "modelos/historia.php";             // Modelo para historia
    include_once "modelos/usuario.php";

    // Cargar archivos necesarios
    include_once "include/sesion.php";               // Gestión de sesión
    include_once "include/gestor_plantillas.php";    // Gestor de plantillas
    include_once "include/rastreador.php";

    // Cargar las vistas
    include "vistas.php";                       // Vistas

    if (isset($_GET["id"])) {
        // Buscar en la base de datos
        $historia = new Historia($_GET["id"]);

        // Si existe mostrar la historia
        if ($historia->existe) {
            vista_ver_historia($historia);
        }

        // Si no existe mostrar página de error
        else {
            vista_mensaje("Error", "La historia no existe", "La historia que has solicitado no existe o ha sido eliminada.");
        }   
    }

    // Valorar la historia (Se realiza la petición de forma asíncrona)
    else if (isset($_POST["estrellas"]) && isset($_POST["id"])) {
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_REGISTRADO) {
            $historia = new Historia($_POST["id"]);
            $historia->valorar($_SESSION["usuario"], $_POST["estrellas"]);
        }
    }

    // Si no se ha especificado ninguna historia, mostrar error
    else {
        vista_mensaje("Error", "No hay historia", "Debe especificar una historia.");
    }

    // Guardar estadísticas
    Rastreador::guardar_estadisticas();
?>