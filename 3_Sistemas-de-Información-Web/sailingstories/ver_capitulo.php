<?php
    // Cargar archivos necesarios
    include_once "include/sesion.php";               // Gestión de sesión
    include_once "include/gestor_plantillas.php";    // Gestor de plantillas
    include_once "include/rastreador.php";

    // Cargar modelos necesarios
    include_once "modelos/capitulo.php";             // Modelo para historia
    include_once "modelos/usuario.php";              // Modelo para usuario

    // Cargar las vistas
    include "vistas.php";                       // Vistas

    // Si se ha pedido ver un capítulo tratar de servirlo
    if (isset($_GET["id"])) {
        // Buscar en la base de datos
        $capitulo = Capitulo::buscar_capitulo($_GET["id"]);

        // Si existe mostrar el capítulo
        if ($capitulo != null) {
            vista_ver_capitulo($capitulo);
        }

        // Si no existe mostrar página de error
        else {
            vista_mensaje("Error", "El capítulo no existe", "El capítulo que has solicitado no existe o ha sido eliminado.");
        }   
    }
    // Si se ha enviado un voto, registrarlo y mostrar mensaje
    else if (isset($_POST["id_capitulo"])) {
        // Asegurarse de que el usuario haya iniciado sesión
        if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_REGISTRADO) {
            // Buscar el capítulo
            $capitulo = Capitulo::buscar_capitulo($_POST["id_capitulo"]);

            // Si existe tratar de registar el voto
            if ($capitulo != null) {
                if ($capitulo->votar($_SESSION["usuario"])) {
                    vista_mensaje("Voto registrado", "", "El voto ha sido registrado correctamente");
                }
                else {
                    vista_mensaje("Error", "", "Ha ocurrido un error al registrar el voto, o puede que ya hayas votado por este capítulo.");
                }
            }
            // Mostrar error en caso de que el capítulo no exista
            else {
                vista_mensaje("Error", "El capítulo no existe", "El capítulo que has solicitado no existe o ha sido eliminado.");
            }
        }
        // En caso contrario mostrar mensaje
        else {
            vista_mensaje("Debes inicar sesión", "", "Para poder votar un capítulo es necesario ser un usuario registrado.");
        }
        
    }

    // Si no se ha especificado ninguna historia, mostrar error
    else {
        vista_mensaje("Error", "No hay capítulo", "Debes especificar un capítulo.");
    }

    // Guardar estadísticas
    Rastreador::guardar_estadisticas();
?>