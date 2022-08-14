<?php
    include_once "include/sesion.php";
    include_once "include/rastreador.php";

    include_once "modelos/usuario.php";
    include_once "modelos/capitulo.php";

    include "vistas.php";

    // Aceptar únicamente peticiones de usuarios registrados
    if (isset($_SESSION["usuario"]) && ($_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_REGISTRADO)) {
        
        // Procesar la petición si se han enviado los datos correctos
        if (isset($_POST["titulo_capitulo"]) && isset($_POST["texto_capitulo"]) && isset($_POST["id_historia"])) {

            // Crear el nuevo capítulo
            $capitulo = Capitulo::nuevo_capitulo($_POST["titulo_capitulo"], $_POST["texto_capitulo"], $_POST["id_historia"], $_SESSION["usuario"], isset($_POST["es_capitulo_final"]));

            // Guardar el capítulo
            // TODO comprobar el error una vez implementados los códigos de error de la base de datos
            if ($capitulo->guardar()) {
                vista_mensaje("Capítulo enviado", "", "El capítulo se ha enviado con éxito. Deberá ser aprobado por los administradores antes de pasar a la fase de votación.");
            }
            else {
                vista_mensaje("Error", "Error al enviar el capítulo", "Ha ocurrido un error al enviar el capítulo. Vuelva a intentarlo más tarde.");
            }
        }
        else {
            // Redirigir: petición incorrecta
            header("Location: index.php");
        }
    }
    else {
        // Redirigir: petición incorrecta
        header("Location: index.php");
    }

    Rastreador::guardar_estadisticas();
?>