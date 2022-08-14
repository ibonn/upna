<?php
    include_once "modelos/historia.php";            // Modelo para historia. Se usa para obtener los géneros disponibles
    include_once "modelos/admin.php";
    include_once "modelos/usuario.php";
    include_once "modelos/votacion.php";
    include_once "modelos/supuesto.php";

    include_once "include/rastreador.php";          // Rastreador
    include_once "include/gestor_plantillas.php";   // Gestor de plantillas
    include_once "include/sesion.php";              // Sesión

    /**
     * Carga el contenido en la vista principal, añadiendole la barra de navegación, la barra lateral...
     * 
     *      $contenido: El contenido que se desea mostrar
     *      $titulo: El título de la página
     *
     */
    function vista_principal($contenido, $titulo) {
        // Cargar el html
        $plantilla = new PlantillaHTML("html/principal.html");

        // Añadir el contenido
        $plantilla->set("titulo", $titulo);
        $plantilla->set("contenido", $contenido);
        $plantilla->set("year", date("Y"));

        // Obtener los géneros para añadirlos a la barra lateral
        $array_generos = [];
        foreach (Historia::generos() as $genero) {
            $array_generos[] = array(
                "url" => $genero,
                "genero" => $genero
            );
        }

        // Añadir los géneros disponibles
        $plantilla->set_bloque("generos", $array_generos);

        // Obtener el usuario
        $usuario = $_SESSION["usuario"];

        // Si el usuario está identificado, mostrar el menú de configuración
        if ($usuario->get_tipo() == Usuario::USUARIO_REGISTRADO) {
            $plantilla->set("nombre_usuario", $usuario->get_id());
            $plantilla->set("link_cuenta", "cuenta.php");
            $plantilla->set("accion_usuario", "Cerrar sesión");
            $plantilla->set("link_accion_usuario", "logout.php");
        }
        // en caso contrario mostrar el formulario de inicio de sesión
        else {
            $plantilla->set("nombre_usuario", "Usuario anónimo");
            $plantilla->set("link_cuenta", "");
            $plantilla->set("accion_usuario", "Identifícate");
            $plantilla->set("link_accion_usuario", "login.php");
        }

        // Mostrar la vista
        echo $plantilla;
    }

    /**
     * Muestra la página de inicio
     * 
     *      $titulo: El titulo del mensaje de bienvenda
     *      $mensaje: El mensaje de bienvenda
     *      $historias: Lista de historias a mostrar en la página principal
     *
     */
    function vista_inicio($titulo, $historias) {
        $plantilla = new PlantillaHTML("html/inicio.html");

        // Añadir el contenido
        $plantilla->set("titulo_mensaje_bienvenida", $titulo);

        // Añadir las historias a cada una de las columnas
        $columna1 = "";
        $columna2 = "";
        for ($i = 0; $i < count($historias); $i++) {

            // Cargar la plantilla correspondiente
            if ($historias[$i]->get_estado() == Historia::HISTORIA_FINALIZADA) {
                $tarjeta = new PlantillaHTML("html/tarjeta_historia_finalizada.html");
            }
            else {
                $tarjeta = new PlantillaHTML("html/tarjeta_historia.html");
            }

            $imagen = $historias[$i]->get_imagen_principal();
            if ($imagen == null) {
                $tarjeta->set("imagen", "");
            }
            else {
                $tarjeta->set("imagen", "uploads/" . $imagen->get_med());
            }

            $tarjeta->set("id", $historias[$i]->get_id());
            $tarjeta->set("titulo_historia", $historias[$i]->get_titulo());
            $tarjeta->set("fragmento_historia", $historias[$i]->get_comienzo(200));

            if ($i % 2 == 0) {
                $columna1 .= $tarjeta;
            }
            else {
                $columna2 .= $tarjeta;
            }
        }
        $plantilla->set("columna1", $columna1);
        $plantilla->set("columna2", $columna2);

        // Mostrar la vista
        vista_principal($plantilla, "Página de inicio");
    }

    /**
     * Muestra la historia especificada
     * 
     *      $historia: la historia que se va a mostrar. Es un objeto de la clase Historia
     *
     */
    function vista_ver_historia($historia) {
        $plantilla = new PlantillaHTML("html/ver_historia.html");

        // Establecer los valores
        $plantilla->set("id", $historia->get_id());
        $plantilla->set("titulo", $historia->get_titulo());
        $plantilla->set("categoria", $historia->get_categoria());

        // Establecer la valoración
        $plantilla->set("valoracion_media", $historia->get_valoracion());
        $plantilla->set("valoracion_usuario", $historia->get_valoracion_usuario($_SESSION["usuario"]));

        // Establecer el estado
        if ($historia->get_estado() == Historia::HISTORIA_EN_PROGRESO) {
            $plantilla->set("estado", "Historia en progreso");
        }
        else {
            $plantilla->set("estado", "Historia finalizada");
        }            

        // Mostrar los capítulos
        $array_capitulos = array();
        foreach ($historia->get_capitulos() as $capitulo) {
            $array_capitulos[] = array(
                "titulo_capitulo" => $capitulo->get_titulo(),
                "texto_capitulo" => $capitulo->get_texto(),
                "autor" => $capitulo->get_autor()->get_id()
            );
        }
        $plantilla->set_bloque("capitulo", $array_capitulos);

        // Establecer las fotos para la galería
        $array_fotos = array();
        foreach ($historia->get_imagenes() as $imagen) {
            $array_fotos[] = array(
                "nombre" => $imagen->get_nombre(), 
                "descripcion" => $imagen->get_texto(), 
                "autor" => $imagen->get_autor()->get_id()
            );
        }
        $plantilla->set("array_fotos", json_encode($array_fotos));

        // Si el usuario no ha iniciado sesión o la historia está finalizada, ocultar las votaciones, el envío de capítulo y la subida de imágenes
        if (($_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_ANONIMO) || ($historia->get_estado() == Historia::HISTORIA_FINALIZADA)) {
            $plantilla->ocultar_bloque("registrado");
        }
        else {
            // Establecer las votaciones
            
            $votaciones = $historia->get_votaciones();

            if (count($votaciones) == 0) {
                $plantilla->ocultar_bloque("votaciones");
            }
            else {
                $columna1 = "";
                $columna2 = "";
                for ($i = 0; $i < count($votaciones); $i++) {

                    $cap = $votaciones[$i]->get_capitulo();
    
                    $votacion = new PlantillaHTML("html/tarjeta_votacion.html");
                    $votacion->set("titulo", $cap->get_titulo());
                    $votacion->set("texto", $cap->get_comienzo());
                    $votacion->set("id", $cap->get_id());
                    $votacion->set("segundos", $votaciones[$i]->get_tiempo_restante());
    
                    if ($i % 2 == 0) {
                        $columna1 .= $votacion;
                    }
                    else {
                        $columna2 .= $votacion;
                    }
                }
                $plantilla->set("columna1", $columna1);
                $plantilla->set("columna2", $columna2);
            }
        }

        vista_principal($plantilla, $historia->get_titulo());
    }

    function vista_login($error=false) {
        $plantilla = new PlantillaHTML("html/login.html");
        if (!$error) {
            $plantilla->ocultar_bloque("error");
        }
        echo $plantilla;
    }

    function vista_crear_cuenta($error=false, $mensaje="", $nombre_usuario="", $correo="") {
        $plantilla = new PlantillaHTML("html/crear_cuenta.html");
        if (!$error) {
            $plantilla->ocultar_bloque("error");
        }
        else {
            $plantilla->set("mensaje_error", $mensaje);
        }
        $plantilla->set("nombre_usuario", $nombre_usuario);
        $plantilla->set("correo_usuario", $correo);
        echo $plantilla;
    }

    function vista_ver_capitulo($capitulo) {
        $plantilla = new PlantillaHTML("html/ver_capitulo.html");

        $plantilla->set("id", $capitulo->get_id());
        $plantilla->set("titulo_capitulo", $capitulo->get_titulo());
        $plantilla->set("texto", $capitulo->get_texto());
        $plantilla->set("texto", $capitulo->get_texto());

        $votacion = Votacion::get_votacion_capitulo($capitulo->get_id());
        if ($votacion == null) {
            
        }
        else {
            $plantilla->set("segundos", $votacion->get_tiempo_restante());
        }
        

        vista_principal($plantilla, $capitulo->get_titulo());
    }

    function vista_mensaje($titulo, $subtitulo, $texto) {
        $plantilla = new PlantillaHTML("html/mensaje.html");

        $plantilla->set("titulo", $titulo);
        $plantilla->set("subtitulo", $subtitulo);
        $plantilla->set("texto", $texto);

        vista_principal($plantilla, $titulo);
    }

    function vista_crear_historia() {
        $plantilla = new PlantillaHTML("html/nueva_historia.html");

        $lista_generos = array();
        foreach (Historia::generos() as $genero) {
            $lista_generos[] = array("nombre" => $genero);
        }

        $plantilla->set_bloque("generos", $lista_generos);

        vista_principal($plantilla, "Escribir una nueva historia");
    }

    function vista_login_admin() {
        echo file_get_contents("html/login_admin.html");
    }

    function vista_principal_admin($contenido) {
        $plantilla = new PlantillaHTML("html/principal_admin.html");

        $admin = $_SESSION["admin"];

        $plantilla->set("titulo", "Panel de administración");
        $plantilla->set("contenido", $contenido);
        $plantilla->set("nombre_usuario", $admin->get_nombre());
        $plantilla->set("accion_usuario", "Cerrar sesión");
        $plantilla->set("link_accion_usuario", "logout.php");
        $plantilla->set("year", date("Y"));

        echo $plantilla;
    }

    function vista_admin() {
        $plantilla = new PlantillaHTML("html/inicio_admin.html");

        $navegadores = Rastreador::get_navegadores();
        $visitas_dias = Rastreador::get_visitas_dia();

        $plantilla->set("num_usuarios", Usuario::get_num());
        $plantilla->set("num_historias", Historia::get_num());
        $plantilla->set("num_visitas", Rastreador::get_num_visitas());
        $plantilla->set("num_visitas_u", Rastreador::get_num_visitas_unicas());

        $plantilla->set("navegadores_labels", json_encode($navegadores["labels"]));
        $plantilla->set("navegadores_data", json_encode($navegadores["data"]));
        $plantilla->set("len_nav_data", count($navegadores["data"]));

        $plantilla->set("visitas_dias_labels", json_encode($visitas_dias["labels"]));
        $plantilla->set("visitas_dias_data", json_encode($visitas_dias["data"]));

        return vista_principal_admin($plantilla);
    }

    function vista_gestion_cap_enviados() {
        $plantilla = new PlantillaHTML("html/gestion_cap_enviados.html");
        $votaciones = array();
        foreach(Capitulo::get_capitulos_estado(Capitulo::CAPITULO_ENVIADO) as $votacion) {
            $votaciones[] = array(
                "id" => $votacion->get_id(),
                "titulo" => $votacion->get_titulo(),
                "autor" => $votacion->get_autor(),
                "texto" => $votacion->get_comienzo(),
            );
        }
        $plantilla->set_bloque("votaciones", $votaciones);
        
        return vista_principal_admin($plantilla);
    }

    function vista_gestion_votaciones() {
        $plantilla = new PlantillaHTML("html/gestion_votaciones.html");
        $votaciones = array();
        foreach(Votacion::get_votaciones() as $votacion) {
            $votaciones[] = array(
                "id" => $votacion->get_capitulo()->get_id(),
                "titulo" => $votacion->get_capitulo()->get_titulo(),
                "autor" => $votacion->get_capitulo()->get_autor(),
                "num_votos" => $votacion->get_numero_votos(),
                "texto" => $votacion->get_capitulo()->get_comienzo(),
                "es_final" => $votacion->get_capitulo()->get_es_final() ? "Sí" : "No"
            );
        }
        $plantilla->set_bloque("votaciones", $votaciones);
        
        return vista_principal_admin($plantilla);
    }

    function vista_gestion_usuarios() {
        $plantilla = new PlantillaHTML("html/gestion_usuarios.html");
        $usuarios = array();
        foreach(Usuario::get_usuarios() as $usuario) {
            $usuarios[] = array(
                "id" => $usuario->get_id(),
                "correo" => $usuario->get_correo()
            );
        }
        $plantilla->set_bloque("usuarios", $usuarios);
        
        return vista_principal_admin($plantilla);
    }

    function vista_gestion_historias() {
        $plantilla = new PlantillaHTML("html/gestion_historias.html");
        $historias = array();
        foreach(Historia::get_historias() as $historia) {
            $historias[] = array(
                "id" => $historia->get_id(),
                "titulo" => $historia->get_titulo(),
                "tematica" => $historia->get_categoria(),
                "intro" => $historia->get_comienzo(),
            );
        }

        $lista_generos = array();
        foreach (Historia::generos() as $genero) {
            $lista_generos[] = array("nombre" => $genero);
        }

        $plantilla->set_bloque("generos", $lista_generos);
        $plantilla->set_bloque("historias", $historias);
        
        return vista_principal_admin($plantilla);
    }

    function vista_gestion_capitulos() {
        $plantilla = new PlantillaHTML("html/gestion_capitulos.html");
        $capitulos = array();
        foreach(Capitulo::get_capitulos() as $capitulo) {
            $capitulos[] = array(
                "id" => $capitulo->get_id(),
                "titulo" => $capitulo->get_titulo(),
                "autor" => $capitulo->get_autor()->get_id(),
                "texto" => $capitulo->get_texto()
            );
        }
        $plantilla->set_bloque("capitulos", $capitulos);
        
        return vista_principal_admin($plantilla);
    }

    function vista_gestion_imagenes() {
        $plantilla = new PlantillaHTML("html/gestion_imagenes.html");
        $imagenes = array();
        foreach(Imagen::get_imagenes() as $imagen) {
            $imagenes[] = array(
                "id" => $imagen->get_id(),
                "imagen" => "uploads/" . $imagen->get_peq(),
                "autor" => $imagen->get_autor()->get_id(),
                "descripcion" => $imagen->get_texto()
            );
        }
        $plantilla->set_bloque("imagenes", $imagenes);
        
        return vista_principal_admin($plantilla);
    }

    function vista_gestion_administradores() {
        $plantilla = new PlantillaHTML("html/gestion_admin.html");
        $admins = array();
        foreach(Admin::get_usuarios() as $admin) {
            $admins[] = array(
                "id" => $admin->get_nombre()
            );
        }
        $plantilla->set_bloque("admins", $admins);
        
        return vista_principal_admin($plantilla);
    }

    function vista_config_cuenta() {
        $plantilla = new PlantillaHTML("html/cuenta.html");

        $plantilla->set("nombre_usuario", $_SESSION["usuario"]->get_id());
        $plantilla->set("correo_usuario", $_SESSION["usuario"]->get_correo());

        vista_principal($plantilla, "Mi cuenta");
    }

    function vista_gestion_generos() {
        $plantilla = new PlantillaHTML("html/gestion_generos.html");

        $lista_generos = array();
        foreach (Historia::generos() as $genero) {
            $lista_generos[] = array("nombre" => $genero);
        }

        $plantilla->set_bloque("generos", $lista_generos);
        $plantilla->set_bloque("lista_generos", $lista_generos);
        $plantilla->set_bloque("lista_generos_sup", $lista_generos);


        $lista_supuestos = array();
        foreach (Supuesto::get_supuestos() as $supuesto) {
            $lista_supuestos[] = array(
                "id" => $supuesto->get_id(),
                "supuesto" => $supuesto->get_texto()
            );
        }

        $plantilla->set_bloque("supuestos", $lista_supuestos);

        return vista_principal_admin($plantilla);
    }

    // Función que muestra los resultados de las acciones del administrador en formato json para ser decodificados por admin.js
    function mostrar_resultado($ok, $titulo, $mensaje) {
        die(json_encode(
            array(
                "ok" => $ok,
                "titulo" => $titulo,
                "mensaje" => $mensaje
            )
        ));
    }
?>