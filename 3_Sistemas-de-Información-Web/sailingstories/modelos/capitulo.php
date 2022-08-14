<?php

    include_once "include/db.php";
    include_once "modelos/historia.php";
    include_once "modelos/votacion.php";

    /**
     * Clase Capítulo
     * 
     * Representa un capítulo de una historia
     */
    class Capitulo {

        // Constantes para el estado del capítulo
        const CAPITULO_ENVIADO = 0;     // El capítulo debe ser aprobado por los administradores para ser votado
        const CAPITULO_EN_VOTACION = 1; // El capítulo se puede votar
        const CAPITULO_FINALIZADO = 2;  // El capítulo no se puede votar (bien porque forma parte de la historia o ha sido descartado)

        private $id;            // Identificador del capítulo
        private $id_historia;   // Identificador de la historia a la que pertenece el capítulo
        private $titulo;        // Título del capítulo
        private $autor;         // Autor del capítulo
        private $texto;         // Texto del capítulo
        private $estado;        // Estado del capítulo
        private $es_final;      // ¿Es capítulo final?
        
        private function __construct() {
            
        }

        /**
         * Crea o un capítulo de una historia
         * 
         *      $titulo: El título del capítulo
         *      $texto: El texto del capítulo
         *      $id_historia: El identificador de la historia a la que pertenece el capítulo
         *      $autor: El autor del capítulo. instancia de la clase usuario
         */
        public static function nuevo_capitulo($titulo, $texto, $id_historia, $autor, $es_final=false) {
            $capitulo = new Capitulo();
            $capitulo->id = null;
            $capitulo->id_historia = $id_historia;
            $capitulo->titulo = $titulo;
            $capitulo->texto = $texto;
            $capitulo->autor = $autor;
            $capitulo->estado = Capitulo::CAPITULO_ENVIADO;
            $capitulo->es_final = $es_final;

            return $capitulo;
        }

        public static function buscar_capitulo($id) {
            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT * FROM final_capitulo WHERE idCapitulo = ?");
            $stmt->bind_param("i", $id);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows != 0)  {
                return Capitulo::cargar($res->fetch_assoc());
            }
            return null;
        }
    
        /**
         * Guarda el capítulo en la base de datos
         */
        public function guardar() {
            $db = BaseDatos::get();

            if ($this->id == null) {
                $stmt = $db->prepare("INSERT INTO final_capitulo (idHistoria, titulo, autor, texto, estado, es_final) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssii", $this->id_historia, $this->titulo, $this->autor, $this->texto, $this->estado, $this->es_final);
                $res = $stmt->execute();

                $this->id = BaseDatos::get_ultimo_id();
                return $res;
            }
            else {
                $stmt = $db->prepare("UPDATE final_capitulo SET idHistoria = ?, titulo = ?, autor = ?, texto = ?, estado = ?, es_final = ? WHERE idCapitulo = ?");
                $stmt->bind_param("isssiii", $this->id_historia, $this->titulo, $this->autor, $this->texto, $this->estado, $this->es_final, $this->id);
                $res = $stmt->execute();
                
                return $res;
            }
        }

        /**
         * Carga un capítulo a partir de una fila de la base de datos
         * 
         *      $fila: fila de la base de datos desde la que se va a cargar el capítulo
         * 
         *      Devuelve: El capítulo cargado desde la fila
         * 
         */
        public static function cargar($fila) {
            $cap = new Capitulo();

            $cap->id = $fila["idCapitulo"];
            $cap->id_historia = $fila["idHistoria"];
            $cap->titulo = $fila["titulo"];
            $cap->autor = new Usuario($fila["autor"]);
            $cap->texto = $fila["texto"];
            $cap->estado = $fila["estado"];
            $cap->es_final = $fila["es_final"];

            return $cap;
        }

        /**
         * Obtiene todos los capítulos finalizados que componen una historia
         * 
         *      $id: identificador de la historia
         * 
         *      Devuelve: Una lista de capítulos
         * 
         */
        public static function get_capitulos_historia($id) {
            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT * FROM final_capitulo WHERE idHistoria = ? AND estado = 2");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();

            $resultado = array();

            if ($res->num_rows != 0)  {
                while ($datos = $res->fetch_assoc()) {
                    $resultado[] = Capitulo::cargar($datos);
                }
            }
            return $resultado;
        }

        public static function get_capitulos_estado($estado) {
            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT * FROM final_capitulo WHERE estado = ?");
            $stmt->bind_param("i", $estado);
            $stmt->execute();
            $res = $stmt->get_result();

            $resultado = array();

            if ($res->num_rows != 0)  {
                while ($datos = $res->fetch_assoc()) {
                    $resultado[] = Capitulo::cargar($datos);
                }
            }
            return $resultado;
        }

        /**
         * Obtiene todos los capítulos no finalizados que se pueden votar para una historia
         * 
         *      $id: identificador de la historia
         * 
         *      Devuelve: Una lista de capítulos
         * 
         */
        public static function get_votaciones_historia($id) {
            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT * FROM final_capitulo NATURAL JOIN final_votacion WHERE idHistoria = ? AND estado = 1");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();

            $resultado = array();

            if ($res->num_rows != 0)  {
                while ($datos = $res->fetch_assoc()) {
                    $resultado[] = Votacion::cargar($datos);
                }
            }
            return $resultado;
        }

        /**
         * Obtiene el id del capítulo
         * 
         *      Devuelve: el identificador del capítulo
         * 
         */
        public function get_id() {
            return $this->id;
        }

        /**
         * Obtiene el id de la historia a la que pertenece el capítulo
         * 
         *      Devuelve: el identificador de la historia
         * 
         */
        public function get_id_historia() {
            return $this->id_historia;
        }

        public function set_id_historia($id) {
            $this->id_historia = $id;
            return $this;
        }

        /**
         * Obtiene el título del capítulo
         * 
         *      Devuelve: el título del capítulo
         * 
         */
        public function get_titulo() {
            return $this->titulo;
        }

        /**
         * Modifica el título del capítulo
         * 
         *      $titulo: el nuevo título del capítulo
         * 
         *      Devuelve: $this por si fuera necesario llamar a mas metodos en la misma línea
         * 
         */
        public function set_titulo($titulo) {
            $this->titulo = $titulo;
            return $this;
        }

        /**
         * Obtiene el autor del capítulo
         * 
         *      Devuelve: el autor del capítulo (instancia de la clase Usuario)
         * 
         */
        public function get_autor() {
            return $this->autor;
        }

        /**
         * Modifica el autor del capítulo
         * 
         *      $autor: el nuevo autor del capítulo (instancia de la clase Usuario)
         * 
         *      Devuelve: $this por si fuera necesario llamar a mas metodos en la misma línea
         * 
         */
        public function set_autor($autor) {
            $this->autor = $autor;
            return $this;
        }

        /**
         * Obtiene el texto que compone el capítulo
         * 
         *      Devuelve: el texto del capítulo
         * 
         */
        public function get_texto() {
            return $this->texto;
        }

        /**
         * Modifica el texto del capítulo
         * 
         *      $texto: el nuevo texto del capítulo
         * 
         *      Devuelve: $this por si fuera necesario llamar a mas metodos en la misma línea
         * 
         */
        public function set_texto($texto) {
            $this->texto = $texto;
            return $this;
        }

        /**
         * Obtiene el estado en el que se encuentra el capítulo
         * 
         *      Devuelve: 
         *          la constante CAPITULO_EN_PROGRESO si el capítulo está siendo redactado
         *          la constante CAPITULO_EN_VOTACION si el capítulo se está votando
         *          la constante CAPITULO_FINALIZADO si el capítulo ya se ha terminado de escribir
         * 
         */
        public function get_estado() {
            return $this->estado;
        }

        /**
         * Modifica el estado del capítulo
         * 
         *      $estado: el nuevo estado del capítulo (CAPITULO_EN_PROGRESO, CAPITULO_EN_VOTACION, CAPITULO_FINALIZADO)
         * 
         *      Devuelve: $this por si fuera necesario llamar a mas metodos en la misma línea
         * 
         */
        public function set_estado($estado) {
            $this->estado = $estado;
            return $this;
        }

        public function get_comienzo($longitud=100) {
            $texto = strip_tags($this->texto);
            if (strlen($texto) > $longitud) {
                return substr($texto, 0, $longitud) . "...";
            }
            return $texto;
        }

        /**
         * Permite a un usuario emitir un único voto para este capítulo
         */
        public function votar($usuario) {
            $db = BaseDatos::get();

            $id_usuario = $usuario->get_id();

            $stmt = $db->prepare("INSERT INTO final_voto (idUsuario, idCapitulo) VALUES (?, ?)");
            $stmt->bind_param("si", $id_usuario, $this->id);
            $res = $stmt->execute();

            // TODO comprobar el resultado de la consulta. Si es duplicado devolver una cosa, si no error otra y si error otra
            return $res;
        }

        public function get_es_final() {
            return $this->es_final;
        }

        public function set_es_final($val) {
            $this->es_final = $val;
            return $this;
        }

        public function eliminar() {
            if ($this->id != null) {
                $db = BaseDatos::get();

                $stmt = $db->prepare("DELETE FROM final_capitulo WHERE idCapitulo = ?");
                $stmt->bind_param("i", $this->id);
                $res = $stmt->execute();

                return $res;
            }
        }

        public function get_historia() {
            return new Historia($this->id_historia);
        }

        public static function get_capitulos() {

            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT * FROM final_capitulo");
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows == 0) {
                return array();
            }
            else {
                $capitulos = array();
                while ($fila = $res->fetch_assoc()) {
                    $capitulos[] = Capitulo::cargar($fila);
                }
                return $capitulos;
            }
        }
    }
?>