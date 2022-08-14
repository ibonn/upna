<?php

    include_once "modelos/capitulo.php";
    include_once "modelos/votacion.php";
    include_once "modelos/usuario.php";
    include_once "modelos/supuesto.php";
    include_once "modelos/admin.php";

    include "vistas.php";

    include_once "include/sesion.php";
    include_once "include/rastreador.php";

    // Mandar cabeceras
    header("Content-Type: application/json");


    // Comprobar que el administrador haya iniciado sesión
    if (isset($_SESSION["admin"])) {

        // Asegurarse de que se ha solicitado alguna acción
        if (isset($_GET["accion"])) {

            switch ($_GET["accion"]) {

                // Subir csv con datos de administradores
                case "subir_csv":
                    if (!empty($_FILES)) {

                        // Obtener el nombre y la extensión
                        $nombre_temp = $_FILES['file']['tmp_name'];
                        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                
                        // Si no es csv, ignorar
                        if (($extension != "csv")) {
                            http_response_code(400);
                            mostrar_resultado(false, "Error", "Solo se admiten ficheros csv");
                        }
                        else {
                            if (($handle = fopen($nombre_temp, "r")) !== false) {
                                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                                    $admin = Admin::crear($data[0], $data[1]);
                                }
                                fclose($handle);
                            }
                        }
                    }
                break;

                // Gestionar capítulos enviados (aprobar para votación o rechazar y eliminar)
                case "gestionar_capitulos_enviados":
                    if (isset($_POST["id"]) && isset($_POST["accion"])) {

                        if ($_POST["accion"] == "aceptar") {

                            $capitulo = Capitulo::buscar_capitulo($_POST["id"]);

                            $votacion = Votacion::crear($capitulo);
                        
                            if ($votacion != null) {
                                mostrar_resultado(true, "Aceptado", "El capítulo ha sido aceptado y ha pasado a la fase de votación");
                            }
                            else {
                                mostrar_resultado(false, "Error", "No se ha podido crear la votación en la base de datos");
                            }
                        }
                        else {

                            $capitulo = Capitulo::buscar_capitulo($_POST["id"]);

                            if ($capitulo->eliminar()) {
                                mostrar_resultado(true, "Rechazado", "Se ha rechazado el capítulo");
                            }
                            else {
                                mostrar_resultado(false, "Error", "Ha ocurrido un error al tratar de rechazar el capítulo");
                            }
                        }
                    }
                break;

                case "gestionar_votaciones":
                    if (isset($_POST["id"]) && isset($_POST["accion"])) {

                        if ($_POST["accion"] == "aceptar") {

                            $votacion = Votacion::get_votacion_capitulo($_POST["id"]);

                            if ($votacion->aprobar()) {
                                mostrar_resultado(true, "Votación aprobada", "El capítulo ha pasado a formar parte de la historia");
                            }
                            else {
                                mostrar_resultado(false, "Error", "Ha ocurrido un error al aprobar la votación");
                            }
                        }
                        else {

                            $capitulo = Capitulo::buscar_capitulo($_POST["id"]);

                            if ($capitulo->eliminar()) {
                                mostrar_resultado(true, "Votación rechazada", "El capítulo y la votación han sido eliminados");
                            }
                            else {
                                mostrar_resultado(false, "Error", "Ha ocurrido un error al rechazar la votación");
                            }
                        }
                    }
                break;

                case "crear_usuario":
                    if (isset($_POST["id"]) && isset($_POST["correo"]) && isset($_POST["pass"])) {

                        $nuevo_usuario = new Usuario();
            
                        $nuevo_usuario->set_id($_POST["id"]);
                        $nuevo_usuario->set_correo($_POST["correo"]);
                        $nuevo_usuario->set_password($_POST["pass"]);

                        if ($nuevo_usuario->guardar()) {
                            mostrar_resultado(true, "Usuario creado", "El usuario se ha creado satisfactoriamente");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Ha ocurrido un error al crear el usuario");
                        }
                    }
                break;

                case "modificar_usuario":
                    if (isset($_POST["id_usuario"]) && isset($_POST["correo"]) && isset($_POST["password"])) {

                        $usuario = new Usuario($_POST["id_usuario"]);

                        $usuario->set_correo($_POST["correo"]);
                        $usuario->set_password($_POST["password"]);

                        if ($usuario->guardar()) {
                            mostrar_resultado(true, "Usuario modificado", "Se ha modificado el usuario");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Ha ocurrido un error al modificar los valores del usuario");
                        }
                    }
                break;

                case "eliminar_usuario":
                    if (isset($_POST["id"])) {

                        $usuario = new Usuario($_POST["id"]);

                        if ($usuario->eliminar()) {
                            mostrar_resultado(true, "Usuario eliminado", "Se ha eliminado el usuario");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Ha ocurrido un error al eliminar el usuario");
                        }
                    }
                break;

                case "modificar_capitulo":
                    if (isset($_POST["id"]) && isset($_POST["titulo"]) && isset($_POST["autor"]) && isset($_POST["texto"])) {
                        $capitulo = Capitulo::buscar_capitulo($_POST["id"]);

                        if ($capitulo == null) {
                            mostrar_resultado(false);
                        }
                        else {
                            $capitulo->set_titulo($_POST["titulo"]);
                            $capitulo->set_autor($_POST["autor"]);
                            $capitulo->set_texto($_POST["texto"]);

                            if ($capitulo->guardar()) {
                                mostrar_resultado(true, "Capítulo modificado", "Se ha modificado el capítulo con éxito");
                            }
                            else {
                                mostrar_resultado(false, "Error", "Ha ocurrido un error al modificar el capítulo");
                            }
                        }
                    }
                break;

                case "eliminar_capitulo":
                    if (isset($_POST["id"])) {
                        $capitulo = Capitulo::buscar_capitulo($_POST["id"]);

                        if ($capitulo == null) {
                            mostrar_resultado(false, "Error", "No se ha encontrado el capítulo en la base de datos");
                        }
                        else {
                            if ($capitulo->eliminar()) {
                                mostrar_resultado(true, "Capítulo eliminado", "El capítulo se ha eliminado satisfactoriamente");
                            }
                            else {
                                mostrar_resultado(false, "Error", "Ha ocurrido un error al eliminar el capítulo");
                            }
                        }
                    }
                break;

                case "modificar_historia":
                    if (isset($_POST["id_historia"]) && isset($_POST["titulo_historia"]) && isset($_POST["genero_historia"])) {
                        $historia = new Historia($_POST["id_historia"]);

                        if (!$historia->existe) {
                            mostrar_resultado(false, "Error", "La historia no existe");
                        }
                        else {
                            $historia->set_titulo($_POST["titulo_historia"]);
                            $historia->set_categoria($_POST["genero_historia"]);

                            if ($historia->guardar()) {
                                mostrar_resultado(true, "Historia modificada", "La historia se ha modificado correctamente");
                            }
                            else {
                                mostrar_resultado(false, "Error", "Ha ocurrido un error al modificar la historia");
                            }
                        }
                    }
                break;

                case "eliminar_historia":
                    if (isset($_POST["id_historia"])) {
                        $historia = new Historia($_POST["id_historia"]);

                        if ($historia->eliminar()) {
                            mostrar_resultado(true, "Historia modificada", "Historia eliminada satisfactoriamente");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Error al eliminar la historia");
                        }
                    }
                break;

                case "modificar_imagen":
                    if (isset($_POST["id_imagen"]) && isset($_POST["autor_imagen"]) && isset($_POST["descripcion_imagen"])) {
                        $imagen = new Imagen($_POST["id_imagen"]);

                        if ($imagen == null) {
                            mostrar_resultado(false, "Error", "La imagen no existe");
                        }
                        else {
                            $imagen->set_autor(new Usuario($_POST["autor_imagen"]));
                            $imagen->set_texto($_POST["descripcion_imagen"]);

                            if ($imagen->guardar()) {
                                mostrar_resultado(true, "Error", "La imagen se ha modificado correctamente");
                            }
                            else {
                                mostrar_resultado(false, "Error", "Ha ocurrido un error al modificar la imagen");
                            }
                        }
                    }
                break;

                case "eliminar_imagen":
                    if (isset($_POST["id_imagen"])) {
                        $imagen = new Imagen($_POST["id_imagen"]);

                        if ($imagen == null) {
                            mostrar_resultado(false, "Error", "La imagen no existe");
                        }
                        else {
                            if ($imagen->eliminar()) {
                                mostrar_resultado(true, "Error", "La imagen se ha eliminado correctamente");
                            }
                            else {
                                mostrar_resultado(false, "Error", "Ha ocurrido un error al eliminar la imagen");
                            }
                        }
                    }
                break;

                case "crear_genero":
                    if (isset($_POST["genero"])) {
                        if (Historia::crear_genero($_POST["genero"])) {
                            mostrar_resultado(true, "Género creado", "Se ha creado un nuevo género");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Ha ocurrido un error al crear el género");
                        }
                    }
                break;

                case "eliminar_genero":
                    if (isset($_POST["genero"])) {
                        if (Historia::eliminar_genero($_POST["genero"])) {
                            mostrar_resultado(true, "Género eliminado", "Se ha eliminado el género '{$_POST["genero"]}'");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Ha ocurrido un error al eliminar el género");
                        }
                    }
                break;

                case "crear_supuesto":
                    if (isset($_POST["genero"]) && isset($_POST["supuesto"])) {
                        $supuesto = new Supuesto();

                        $supuesto->set_genero($_POST["genero"]);
                        $supuesto->set_texto($_POST["supuesto"]);

                        if ($supuesto->guardar()) {
                            mostrar_resultado(true, "Supuesto creado", "Se ha creado un nuevo supuesto");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Ha ocurrido un error al crear el supuesto");
                        }
                    }
                break;

                case "modificar_supuesto":
                    if (isset($_POST["id_supuesto"]) && isset($_POST["genero"]) && isset($_POST["supuesto"])) {
                        $supuesto = new Supuesto($_POST["id_supuesto"]);

                        $supuesto->set_genero($_POST["genero"]);
                        $supuesto->set_texto($_POST["supuesto"]);

                        if ($supuesto->guardar()) {
                            mostrar_resultado(true, "Supuesto modificado", "Se ha modificado el supuesto");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Ha ocurrido un error al modificar el supuesto");
                        }
                    }
                break;

                case "eliminar_supuesto":
                    if (isset($_POST["id_supuesto"])) {
                        $supuesto = new Supuesto($_POST["id_supuesto"]);
                        if ($supuesto->eliminar()) {
                            mostrar_resultado(true, "Supuesto modificado", "Se ha eliminado el supuesto");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Ha ocurrido un error al eliminar el supuesto");
                        }
                    }
                break;
                
                case "eliminar_admin":
                    if (isset($_POST["admin"])) {
                        if (Admin::eliminar($_POST["admin"])) {
                            mostrar_resultado(true, "Administrador eliminado", "Se ha eliminado el administrador");
                        }
                        else {
                            mostrar_resultado(false, "Error", "Error al eliminar el administrador");
                        }
                    }
                break;

                case "vaciar_estadisticas":
                    if (Rastreador::vaciar_estadisticas()) {
                        mostrar_resultado(true, "Tabla vaciada", "Se han eliminado todos los datos de la tabla de estadísticas");
                    }
                    else {
                        mostrar_resultado(false, "Error", "Ha ocurrido un error al vaciar las estadísticas");
                    }

                default:
                    mostrar_resultado(false, "Acción desconocida", "Se ha solicitado una acción desconocida");
            }
        }
    }
?>