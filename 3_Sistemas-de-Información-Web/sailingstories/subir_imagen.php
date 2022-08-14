<?php

    require "include/sesion.php";  // Iniciar la sesión
    include "include/rastreador.php";
    include "modelos/imagen.php";  // Modelo de imagen

    /**
     * Redimensiona y guarda una imagen. Soporta imágenes en JPEG.
     * 
     *      $nombre_origen: Nombre de la imagen original
     *      $nombre_destino: Nombre con el que se guardará la imagen redimensionada
     *      $ancho_destino: Ancho al que redimensionar la imagen. Si no se especifica se calcula
     *                      a partir de $alto_destino de forma que la imagen mantenga la proporción
     *                      original
     *      $alto_destino: Altura a la que redimensionar la imagen. Si no se especifica se calcula
     *                      a partir de $ancho_destino de forma que la imagen mantenga la proporción
     *                      original
     *    
     */
    function redimensionar_imagen($nombre_origen, $nombre_destino, $ancho_destino=null, $alto_destino=null) {
        list($ancho, $alto) = getimagesize($nombre_origen);

        if (($ancho_destino == null) && ($alto_destino == null)) {
            throw new Exception("No se puede redimensionar la imagen");
        }
        else if ($ancho_destino == null) {
            // Calcular el ancho
            $ancho_destino = round($alto_destino * $ancho / $alto);
        }
        else if ($alto_destino == null) {
            // Calcular el alto
            $alto_destino = round($ancho_destino * $alto / $ancho);
        }
        
        $origen = imagecreatefromjpeg($nombre_origen);
        $destino = imagecreatetruecolor($ancho_destino, $alto_destino);
        imagecopyresampled($destino, $origen, 0, 0, 0, 0, $ancho_destino, $alto_destino, $ancho, $alto);

        imagejpeg($destino, $nombre_destino);
    }

    // Nombre de la carpeta en la que se guardarán las imagenes subidas
    $ds = DIRECTORY_SEPARATOR;
    $carpeta_imagenes = 'uploads';
    
    // Si se ha enviado una imagen, proceder
    if (!empty($_FILES) && isset($_POST["id_historia"]) && isset($_POST["descripcion"])) {

        // Obtener el nombre y la extensión de la imagen
        $nombre_temp = $_FILES['file']['tmp_name'];
        $extension_imagen = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        // Si la imagen no es una imagen jpg, mostrar error y no subir la imagen
        if (($extension_imagen != "jpg") && ($extension_imagen != "jpeg")) {
            http_response_code(400);
            die("Solo se admiten imágenes en formato jpg");
        }

        // Generar un nombre único para la imagen
        $nombre_destino = uniqid($more_entropy=TRUE);

        // Asociar la imagen a la historia
        $img = new Imagen();
        $img->set_nombre($nombre_destino)->set_historia($_POST["id_historia"])->set_autor($_SESSION["usuario"]);

        // Añadir la descripción
        $img->set_texto($_POST["descripcion"]);
        
        // Guardar la imagen en la base de datos. Si no se ha podido guardar la imagen mostrar el error
        if (!$img->guardar()) {
            http_response_code(400);
            die("Ha ocurrido un error al subir la imagen");
        }

        // Mover la imagen a la carpeta de imagenes subidas
        $carpeta_destino = dirname(__FILE__) . $ds. $carpeta_imagenes . $ds;
        $archivo_destino =  $carpeta_destino . $nombre_destino . ".jpg";
        move_uploaded_file($nombre_temp, $archivo_destino);

        // Redimensionar la imagen
        list($ancho, $alto) = getimagesize($archivo_destino);
        $proporcion = $ancho / $alto;
        
        if ($proporcion >= 1) {
            // Imagen apaisajada o cuadrada. Ajustar al ancho máximo
            $ancho_peq = 100;    // La anchura máxima de una imagen pequeña es 100px
            $ancho_med = 640;    // La anchura máxima de una imagen mediana es 640px
            $nombre_imagen = pathinfo($nombre_temp, PATHINFO_EXTENSION);

            // Crear las imágenes pequeña y mediana
            redimensionar_imagen($archivo_destino, $carpeta_destino . $nombre_destino . "_peq.jpg", $ancho_destino=$ancho_peq);
            redimensionar_imagen($archivo_destino, $carpeta_destino . $nombre_destino . "_med.jpg", $ancho_destino=$ancho_med);
        }
        else {
            // Imagen vertical. Ajustar al alto máximo
            $alto_peq = 100;    // La altura máxima de una imagen pequeña es 100px
            $alto_med = 420;    // La altura máxima de una imagen mediana es 420px

            // Crear las imágenes pequeña y mediana
            redimensionar_imagen($archivo_destino, $carpeta_destino . $nombre_destino . "_peq.jpg", $alto_destino=$alto_peq);
            redimensionar_imagen($archivo_destino, $carpeta_destino . $nombre_destino . "_med.jpg", $alto_destino=$alto_med);
        }
    }

    // Guardar estadísticas
    Rastreador::guardar_estadisticas();
?>    