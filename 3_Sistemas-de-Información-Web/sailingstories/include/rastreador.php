<?php

    include_once "include/db.php";

    include_once "modelos/usuario.php";

    class Rastreador {

        public static function guardar_estadisticas() {
            $db = BaseDatos::get();

            // Obtener el nombre de usuario
            $usuario = isset($_SESSION["usuario"]) && ($_SESSION["usuario"]->get_tipo() == Usuario::USUARIO_REGISTRADO) ? $_SESSION["usuario"]->get_id() : "Anónimo";

            // Obtener la dirección IP
            $ip = $_SERVER["REMOTE_ADDR"];

            // Obtener la página que se está visitando
            $pagina = $_SERVER["REQUEST_URI"];

            // Obtener la página de origen
            $origen = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null;

            // Obtener la hora actual
            $hora = date("H:i:s");

            // Obtener del día de la semana
            $dia = date("N");

            // Obtener el navegador
            $navegador = $_SERVER["HTTP_USER_AGENT"];

            // Guardar los datos
            $stmt = $db->prepare("INSERT INTO final_estadisticas (usuario, ip, origen, pagina, navegador, hora, dia) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssi", $usuario, $ip, $origen, $pagina, $navegador, $hora, $dia);
            return $stmt->execute();
        }

        public static function get_estadisticas() {
            $db = BaseDatos::get();
            $res = $db->query("SELECT * FROM final_estadisticas");
            $filas = array();

            while ($fila = $res->fetch_assoc()) {
                $filas[] = $fila;
            }

            return $filas;
        }

        public static function get_navegadores() {
            $db = BaseDatos::get();
            $res = $db->query("SELECT navegador, COUNT(*) as num_visitas FROM final_estadisticas GROUP BY navegador");
            $filas = array(
                "navegadores" => array(),
                "num_visitas" => array()
            );

            while ($fila = $res->fetch_assoc()) {
                $filas["navegadores"][] = $fila["navegador"];
                $filas["num_visitas"][] = $fila["num_visitas"];
            }

            return Rastreador::get_valor($filas["navegadores"], $filas["num_visitas"]);
        }

        public static function get_num_visitas() {
            $db = BaseDatos::get();
            $res = $db->query("SELECT COUNT(*) as num_visitas FROM final_estadisticas");
            $fila = $res->fetch_assoc();
            return $fila["num_visitas"];
        }

        public static function get_num_visitas_unicas() {
            $db = BaseDatos::get();
            $res = $db->query("SELECT COUNT(*) AS num_visitas FROM (SELECT usuario, COUNT(*) FROM final_estadisticas GROUP BY usuario, ip) AS visitas_usuario");
            $fila = $res->fetch_assoc();
            return $fila["num_visitas"];
        }

        public static function get_visitas_dia() {
            $db = BaseDatos::get();
            $res = $db->query("SELECT dia, COUNT(*) AS visitas_dia FROM final_estadisticas GROUP BY dia");
            $filas = array(
                "dia" => array(),
                "num_visitas" => array()
            );

            while ($fila = $res->fetch_assoc()) {
                $filas["dia"][] = $fila["dia"];
                $filas["num_visitas"][] = $fila["visitas_dia"];
            }

            return Rastreador::get_valor($filas["dia"], $filas["num_visitas"]);
        }

        private static function get_valor($labels, $data) {
            return array(
                "labels" => $labels,
                "data" => $data
            );
        }

        public static function vaciar_estadisticas() {
            $db = BaseDatos::get();
            return $db->query("TRUNCATE TABLE final_estadisticas");
        }
    }
?>