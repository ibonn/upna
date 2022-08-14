<?php
    include_once "include/sesion.php";
    include_once "modelos/usuario.php";
    include_once "modelos/historia.php";
    include_once "modelos/capitulo.php";
    include_once "modelos/supuesto.php";
    include_once "include/rastreador.php";

    include "vistas.php";

    // Comprobar que el usuario esté registrado y mostrar la vista correspondiente en cada caso
    if (isset($_SESSION["usuario"]) && ($_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_REGISTRADO)) {

        // Comrobar si el usuario ha mandado los datos para una nueva historia
        if (isset($_POST["titulo_historia"]) && isset($_POST["genero_historia"]) && isset($_POST["titulo_capitulo"]) && isset($_POST["texto_capitulo"])) {
            
            // Crear la nueva historia
            $historia = new Historia();

            // Crear el primer capítulo
            $capitulo = Capitulo::nuevo_capitulo($_POST["titulo_capitulo"], $_POST["texto_capitulo"], null, $_SESSION["usuario"]);
            
            // Establecer el primer capítulo como finalizado para que pueda formar parte de la historia
            $capitulo->set_estado(Capitulo::CAPITULO_FINALIZADO);
            
            // Establecer los valores
            $historia->set_titulo($_POST["titulo_historia"]);
            $historia->set_categoria($_POST["genero_historia"]);
            $historia->add_capitulo($capitulo);

            // Guardar la historia
            if ($historia->guardar()) {
                vista_mensaje("Historia creada", "", "La historia se ha creado correctamente.");
            }
            else {
                vista_mensaje("Error", "Error al crear la historia", "Ha ocurrido un error al crear la historia. Inténtalo de nuevo más tarde.");
            }


        }
        // Comprobar si se ha cambiado de género y entregar un supuesto
        elseif (isset($_POST["genero"])) {
            die(Supuesto::get_supuesto_aleatorio($_POST["genero"])->get_texto());
        }
        else {
            // Mostrar la vista de crear una nueva historia
            vista_crear_historia();
        }
    }
    else {
        vista_mensaje("Error", "Debes iniciar sesión", "Para poder escribir una historia es necesario ser un usuario registrado.");
    }

    // Guardar estadísticas
    Rastreador::guardar_estadisticas();
?>