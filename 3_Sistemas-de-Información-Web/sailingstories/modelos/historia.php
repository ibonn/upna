<?php
    require_once "include/db.php";  // Conexión con la base de datos
    
    include_once "modelos/capitulo.php";    // Modelo para capítulo
    include_once "modelos/imagen.php";      // Modelo para imagen

    /**
     * Clase Historia
     * 
     * Representa una historia
     */
    class Historia {

        // Constantes para el estado de la historia
        const HISTORIA_EN_PROGRESO = 0; // La historia todavía se está escribiendo y se puede participar
        const HISTORIA_FINALIZADA = 1;  // No se puede participar en la escritura (ya está finalizada)

        private $titulo;    // Título de la historia
        private $estado;    // Estado de la historia
        private $id;        // Identificador de la historia
        private $categoria; // Categoría de la historia
        private $capitulos; // Listado de capitulos de la historia
        private $imagenes;  // Listado de imagenes de la historia
        private $en_votacion; // Listado de capitulos en votación de la historia 

        public $existe;     // ¿Existe la historia?

        /**
         * Crea o carga una historia
         * 
         *      $id: El id de la historia a cargar. Si es nulo, se creará una nueva historia
         */
        public function __construct($id=null) {

            // Crear historia nueva
            $this->existe = false;
            $this->titulo = "";
            $this->estado = Historia::HISTORIA_EN_PROGRESO;
            $this->id = null;
            $this->categoria = "Sin categoría";
            $this->capitulos = array();
            $this->imagenes = array();
            $this->en_votacion = array();

            // Cargar historia de la base de datos
            if ($id) {
                
                $db = BaseDatos::get();

                $stmt = $db->prepare("SELECT * FROM final_historia WHERE idHistoria = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows != 0)  {
                    $this->existe = true;
                    $hist = Historia::cargar($res->fetch_assoc());
                    foreach (get_object_vars($hist) as $attr => $valor)
                        $this->$attr = $valor;
                }
            }
        }

        public function get_valoracion() {
            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT ROUND(AVG(estrellas), 2) AS valoracion_media FROM final_valoracion WHERE idHistoria = ? GROUP BY idHistoria");
            $stmt->bind_param("i", $this->id);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows != 0)  {
                return $res->fetch_assoc()["valoracion_media"];
            }
            return 0;
        }

        public function get_valoracion_usuario($usuario) {
            $db = BaseDatos::get();

            $id_usuario = $usuario->get_id();
            $stmt = $db->prepare("SELECT estrellas FROM final_valoracion WHERE idHistoria = ? AND idUsuario = ?");
            $stmt->bind_param("is", $this->id, $id_usuario);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows != 0)  {
                return $res->fetch_assoc()["estrellas"];
            }
            return 0;
        }

        /**
         * Obtiene las historias de un género concreto ordenadas de menor a mayor
         * 
         *      $num: El número de historias que se desea obtener
         * 
         *      Devuelve: Una lista de historias
         */
        public static function historias_genero($cat, $num) {
            $db = BaseDatos::get();
            $stmt = $db->prepare("SELECT final_historia.idHistoria, final_historia.titulo, final_historia.estado, final_historia.categoria, AVG(estrellas) as valoracion_media FROM final_historia LEFT JOIN final_valoracion ON final_historia.idHistoria = final_valoracion.idHistoria WHERE categoria = ? GROUP BY final_historia.idHistoria ORDER BY valoracion_media DESC LIMIT ?");
            $stmt->bind_param("si", $cat, $num);
            $stmt->execute();
            $res = $stmt->get_result();

            $ret = array();
            if ($res->num_rows != 0)  {
                while ($fila = $res->fetch_assoc()) {
                    $ret[] = Historia::cargar($fila);
                }
            }

            return $ret;
        }

        /**
         * Obtiene las historias mejor valoradas populares
         * 
         *      $num: El número de historias que se desea obtener
         * 
         *      Devuelve: Una lista de historias
         */
        public static function top_historias($num) {
            $db = BaseDatos::get();
            $stmt = $db->prepare("SELECT final_historia.idHistoria, final_historia.titulo, final_historia.estado, final_historia.categoria, AVG(estrellas) as valoracion_media FROM final_historia LEFT JOIN final_valoracion ON final_historia.idHistoria = final_valoracion.idHistoria GROUP BY final_historia.idHistoria ORDER BY valoracion_media DESC LIMIT ?");
            $stmt->bind_param("i", $num);
            $stmt->execute();
            $res = $stmt->get_result();

            $ret = array();
            if ($res->num_rows != 0)  {
                while ($fila = $res->fetch_assoc()) {
                    $ret[] = Historia::cargar($fila);
                }
            }

            return $ret;
        }

        /**
         * 
         */
        public static function generos() {
            $db = BaseDatos::get();
            $res = $db->query("SELECT nombre from final_genero");
            $ret = array();
            while ($fila = $res->fetch_assoc()) {
                $ret[] = $fila["nombre"];
            }
            return $ret;
        }

        public static function get_num() {
            $db = BaseDatos::get();
            $res = $db->query("SELECT COUNT(*) AS num_historias FROM final_historia");
            $fila = $res->fetch_assoc();
            return $fila["num_historias"];
        }

        public static function crear_genero($nombre) {
            $db = BaseDatos::get();
            $stmt = $db->prepare("INSERT INTO final_genero (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombre);
            return $stmt->execute();
        }

        public static function eliminar_genero($nombre) {
            $db = BaseDatos::get();
            $stmt = $db->prepare("DELETE FROM final_genero WHERE nombre = ?");
            $stmt->bind_param("s", $nombre);
            return $stmt->execute();
        }

        /**
         * Carga una historia a partir de una fila de la base de datos
         * 
         *      $fila: La fila desde la que se cargará la historia
         * 
         *      Devuelve: Una historia
         */
        private static function cargar($fila) {
            $hist = new Historia();

            $hist->existe = true;

            $hist->id = $fila["idHistoria"];
            $hist->titulo = $fila["titulo"];
            $hist->estado = $fila["estado"];
            $hist->categoria = $fila["categoria"];

            $hist->capitulos = Capitulo::get_capitulos_historia($fila["idHistoria"]);
            $hist->imagenes = Imagen::get_imagenes_historia($fila["idHistoria"]);
            $hist->en_votacion = Capitulo::get_votaciones_historia($fila["idHistoria"]);

            return $hist;
        }

        public function add_capitulo($capitulo) {
            $this->capitulos[] = $capitulo;
        }


        /**
         * Guarda la historia en la base de datos
         */
        public function guardar() {

            $db = BaseDatos::get();

            if ($this->existe) {
                // Actualizar la historia
                $stmt = $db->prepare("UPDATE final_historia SET titulo = ?, estado = ?, categoria = ? WHERE idHistoria = ?");
                $stmt->bind_param("sisi", $this->titulo, $this->estado, $this->categoria, $this->id);
                $res = $stmt->execute();

                return $res;
            }
            else {
                // Crear una nueva historia
                $stmt = $db->prepare("INSERT INTO final_historia (titulo, estado, categoria) VALUES (?, ?, ?)");
                $stmt->bind_param("sis", $this->titulo, $this->estado, $this->categoria);
                $res = $stmt->execute();

                $this->id = BaseDatos::get_ultimo_id();

                // Insertar los capítulos
                foreach($this->capitulos as $capitulo) {
                    $capitulo->set_id_historia($this->id);
                    $capitulo->guardar();
                }

                return $res;
            }
        }

        /**
         * Obtiene el título de la historia
         * 
         *      Devuelve: El título de la historia
         */
        public function get_titulo() {
            return $this->titulo;
        }

        /**
         * Modifica el título de la historia
         * 
         *      $titulo: El nuevo título de la historia
         * 
         *      Devuelve: $this por si fuera necesario llamar a mas métodos en la misma línea
         * 
         */
        public function set_titulo($titulo) {
            $this->titulo = $titulo;
            return $this;
        }

        /**
         * Obtiene el estado de la historia
         * 
         *      Devuelve: 
         *          La constante HISTORIA_EN_PROGRESO si la historia no ha finalizado
         *          La constante HISTORIA_FINALIZADA si la histora ha terminado
         */
        public function get_estado() {
            return $this->estado;
        }

        /**
         * Modifica el estado de la historia
         * 
         *      $estado: El nuevo estado de la historia (HISTORIA_EN_PROGRESO o HISTORIA_FINALIZADA)
         * 
         *      Devuelve: $this por si fuera necesario llamar a mas métodos en la misma línea
         * 
         */
        public function set_estado($estado) {
            $this->estado = $estado;
            return $this;
        }

        /**
         * Obtiene el id de la historia
         * 
         *      Devuelve: El identificador de la historia
         * 
         */
        public function get_id() {
            return $this->id;
        }

        /**
         * Obtiene la categoría que se ha asignado a la historia
         * 
         *      Devuelve: La categoría de la historia
         * 
         */
        public function get_categoria() {
            return $this->categoria;
        }

        /**
         * Modifica la categoría de la historia
         * 
         *      $categoria: La nueva categoría de la historia
         * 
         *      Devuelve: $this por si fuera necesario llamar a mas métodos en la misma línea
         * 
         */
        public function set_categoria($categoria) {
            $this->categoria = $categoria;
            return $this;
        }

        /**
         * Obtiene todos los capitulos finalizados de la historia
         * 
         *      Devuelve: Lista de capítulos (Instancias de la clase Capitulo)
         * 
         */
        public function get_capitulos() {
            return $this->capitulos;
        }

        /**
         * Obtiene todos los capitulos no finalizados de la historia
         * 
         *      Devuelve: Lista de capítulos en votación (Instancias de la clase Votacion)
         * 
         */
        public function get_votaciones() {
            return $this->en_votacion;
        }

        /**
         * Obtiene todas las imagenes asociadas a la historia
         * 
         *      Devuelve: Lista de imagenes (Instancias de la clase Imagen)
         * 
         */
        public function get_imagenes() {
            return $this->imagenes;
        }

        public function get_comienzo($longitud=100) {
            if (count($this->capitulos) > 0) {
                return $this->capitulos[0]->get_comienzo($longitud);
            }
            return "";
        }

        public function get_imagen_principal() {
            if (count($this->imagenes) == 0)
                return null;
            return $this->imagenes[0];
        }

        public static function get_historias() {

            $db = BaseDatos::get();

            $res = $db->query("SELECT * FROM final_historia");

            if ($res->num_rows == 0) {
                return array();
            }
            else {
                $capitulos = array();
                while ($fila = $res->fetch_assoc()) {
                    $capitulos[] = Historia::cargar($fila);
                }
                return $capitulos;
            }
        }

        public static function buscar($consulta) {
            $db = BaseDatos::get();
            $stmt = $db->prepare("SELECT * FROM final_historia JOIN final_capitulo ON final_historia.idHistoria = final_capitulo.idHistoria WHERE final_historia.titulo LIKE ? OR final_capitulo.titulo LIKE ? OR texto LIKE ?");
            $con = "%$consulta%";
            $stmt->bind_param("sss", $con, $con, $con);
            $stmt->execute();
            $res = $stmt->get_result();

            $ret = array();
            if ($res->num_rows != 0)  {
                while ($fila = $res->fetch_assoc()) {
                    $ret[] = Historia::cargar($fila);
                }
            }

            return $ret;
        }

        public function valorar($usuario, $estrellas) {
            $db = BaseDatos::get();

            $id_usuario = $usuario->get_id();

            $stmt = $db->prepare("INSERT INTO final_valoracion (idUsuario, idHistoria, estrellas) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE estrellas = ?");
            $stmt->bind_param("sidd", $id_usuario, $this->id, $estrellas, $estrellas);

            return $stmt->execute();
        }

        public function eliminar() {
            $db = BaseDatos::get();

            $stmt = $db->prepare("DELETE FROM final_historia WHERE idHistoria = ?");
            $stmt->bind_param("i", $this->id);
            return $stmt->execute();
        }
    }
?>