<?php
    // Conexión con la base de datos
    class BaseDatos {
        private static $instancia = null;

        // Códigos de error de MySQL para conocer el resultado de algunas consultas
        const CLAVE_DUPLICADA = 1062;

        public static function get() {
            if (!BaseDatos::$instancia) {
                BaseDatos::$instancia = new mysqli("dbserver", "grupo01", "aCh1ii3Eev", "db_grupo01");
                if (BaseDatos::$instancia->connect_error)
                    throw new Exception(BaseDatos::$instancia->connect_error);
            }
            return BaseDatos::$instancia;
        }

        public static function get_ultimo_id() {
            if (BaseDatos::$instancia != null)
                return BaseDatos::$instancia->insert_id;
            return null;
        }
    }
?>