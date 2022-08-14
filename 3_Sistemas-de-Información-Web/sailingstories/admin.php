<?php
    include "vistas.php";
    include_once "modelos/admin.php";

    include_once "include/sesion.php";
    include_once "include/rastreador.php";

    // Si se ha enviado informacion de login validarla
    if (isset($_POST["escritor"]) && isset($_POST["password"])) {
        $admin = Admin::login($_POST["escritor"], $_POST["password"]);
        if ($admin == null) {
            vista_mensaje("Error", "Credenciales incorrectas", "El usuario o la contraseña no son correctos");
        }
        else {
            // Login correcto
            $_SESSION["admin"] = $admin;
            vista_admin();
        }
    }
    else {
        // Comprobar que el administrador haya iniciado sesión y mostrar su vista
        if (isset($_SESSION["admin"])) {

            // Obtener la acción y mostrar la vista correspondiente
            if (isset($_GET["accion"])) {
                $accion = $_GET["accion"];

                switch ($accion) {
                    case "gestionar_capitulos_enviados":
                        vista_gestion_cap_enviados();
                    break;

                    case "gestionar_votaciones":
                        vista_gestion_votaciones();
                    break;

                    case "gestionar_usuarios":
                        vista_gestion_usuarios();
                    break;

                    case "gestionar_capitulos":
                        vista_gestion_capitulos();
                    break;

                    case "gestionar_historias":
                        vista_gestion_historias();
                    break;

                    case "gestionar_imagenes":
                        vista_gestion_imagenes();
                    break;

                    case "gestionar_generos":
                        vista_gestion_generos();
                    break;

                    case "gestionar_administradores":
                        vista_gestion_administradores();
                    break;

                    case "exportar_datos":
                        $estadisticas = Rastreador::get_estadisticas();
                        file_put_contents("estadisticas.json", json_encode($estadisticas));
                        $file_url = "estadisticas.json";
                        header('Content-Type: application/octet-stream');
                        header("Content-Transfer-Encoding: Binary"); 
                        header("Content-disposition: attachment; filename=\"estadisticas.json\""); 
                        readfile($file_url);
                    break;

                    default:
                        vista_mensaje("Error", "La página que buscas no existe", "Estás tratando acceder a una página que no existe");
                }
            }
            else {
                vista_admin();
            }
        }
        // Mostrar la vista de login
        else {
            vista_login_admin();
        }
    }
?>