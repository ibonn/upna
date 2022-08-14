<?php
    // Cargar archivos necesarios
    include_once "include/sesion.php";	// Gestionar la sesión
    include_once "include/rastreador.php";
    include "vistas.php";	// Vistas
    
    if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_REGISTRADO) {
        if (isset($_POST["accion"])) {
            switch ($_POST["accion"]) {
                case "modificar_perfil":
                    if (isset($_POST["correo"])) {
                        $_SESSION["usuario"]->set_correo($_POST["correo"]);
                        if ($_SESSION["usuario"]->guardar()) {
                            vista_mensaje("Cambios guardados", "", "Se ha cambiado la dirección de correo");
                        }
                        else {
                            vista_mensaje("Error", "", "ha ocurrido un error al guardar los cambios");
                        }
                    }
                break;
    
                case "cambiar_contrasena":
                    if (isset($_POST["password_actual"]) && isset($_POST["password_nueva"]) && isset($_POST["password_rep"])) {
                        
                        if (Usuario::login($_SESSION["usuario"]->get_id(), $_POST["password_actual"])->get_tipo() == Usuario::USUARIO_REGISTRADO) {
                            if ($_POST["password_nueva"] == $_POST["password_rep"]) {

                                $_SESSION["usuario"]->set_password($_POST["password_nueva"]);

                                if ($_SESSION["usuario"]->guardar()) {
                                    vista_mensaje("Cambios guardados", "", "Se ha cambiado la contraseña");
                                }
                                else {
                                    vista_mensaje("Error", "", "ha ocurrido un error al guardar los cambios");
                                }
                            }
                            else {
                                vista_mensaje("Error", "", "Las contraseñas no coinciden");
                            }
                        }
                        else {
                            vista_mensaje("Error", "Contraseña incorrecta", "La contraseña actual es incorrecta");
                        }
                    }
                break;
    
                case "eliminar_cuenta":
                    if (isset($_POST["password"])) {
                        if (Usuario::login($_SESSION["usuario"]->get_id(), $_POST["password"])->get_tipo() == Usuario::USUARIO_REGISTRADO) {
                            if ($_SESSION["usuario"]->eliminar()) {
                                session_destroy();
                                header("Location: index.php");
                            }
                            else {
                                vista_mensaje("Error", "", "Ha ocurrido un error al eliminar la cuenta");
                            }
                        }
                        else {
                            vista_mensaje("Error", "Contraseña incorrecta", "La contraseña es incorrecta");
                        }
                    }

                break;
    
                default:
                    vista_mensaje("Error", "Acción desconocida", "Se ha solicitado una acción desconocida");
            }
        }
        else {
            vista_config_cuenta();
        }
    }
    else {
        // Redirigir a la página de login si el usuario no se ha identificado
        header("Location: login.php");
    }

    Rastreador::guardar_estadisticas();
?>