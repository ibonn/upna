<?php
    require_once "include/db.php";          // Conexión con la base de datos
    include_once "modelos/usuario.php";     // Modelo de usuario

    /**
     * Clase Imagen
     * Representa una imagen
     */
    class Imagen {

        private $id;            // ID de la imagen
        private $id_historia;   // ID de historia
        private $nombre;        // Nombre de la imagen
        private $autor;         // Autor de la imagen
        private $texto;         // Texto asociado a la imagen

        /**
         * Crea una nueva imagen o la carga de la base de datos en caso de existir
         * 
         *      $id_imagen: Identificador de la imagen
         * 
         * Si $id_historia y $nombre son nulos se crea una nueva imagen
         */
        public function __construct($id_imagen=null) {

            if ($id_imagen == null) {
                $this->id = null;
                $this->id_historia = null;
                $this->nombre = "";
                $this->autor = null;
                $this->texto = null;
            }
            else {
                $db = BaseDatos::get();
                $stmt = $db->prepare("SELECT * FROM final_imagen WHERE idImagen = ? ");
                $stmt->bind_param("i", $id_imagen);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows != 0) {
                    $img = Imagen::cargar($res->fetch_assoc());
                    foreach (get_object_vars($img) as $attr => $valor)
                        $this->$attr = $valor;
                }
            }
        }

        /**
         * Carga una imagen dada una fila de la tabla
         * 
         *      $fila: Fila de la base de datos desde la que se cargará la imagen
         * 
         *      Devuelve: instancia de la clase Imagen
         */
        private static function cargar($fila) {
            $img = new Imagen();

            $img->id = $fila["idImagen"];
            $img->id_historia = $fila["idHistoria"];
            $img->nombre = $fila["imagen"];
            $img->autor = new Usuario($fila["idUsuario"]);
            $img->texto = $fila["texto"];

            return $img;
        }

        /**
         * Obtiene todas las imagenes asociadas a una historia
         * 
         *      $id: Identificador de la historia
         * 
         *      Devuelve: Lista de imagenes (Instancias de la clase Imagen)
         * 
         */
        public static function get_imagenes_historia($id) {
            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT * FROM final_imagen WHERE idHistoria = ?");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $res = $stmt->get_result();

            $resultado = array();

            if ($res->num_rows != 0)  {
                while ($datos = $res->fetch_assoc()) {
                    $resultado[] = Imagen::cargar($datos);
                }
            }
            return $resultado;
        }

        /**
         * Guarda la imagen en la base de datos
         */
        public function guardar() {
            if ($this->id == null) {
                $id_autor = $this->autor->get_id();
                $db = BaseDatos::get();
                $stmt = $db->prepare("INSERT INTO final_imagen (idHistoria, imagen, texto, idUsuario) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $this->id_historia, $this->nombre, $this->texto, $id_autor);

                $resultado = $stmt->execute();

                $this->id = BaseDatos::get_ultimo_id();

                return $resultado;
            }
            else {
                $id_autor = $this->autor->get_id();
                $db = BaseDatos::get();
                $stmt = $db->prepare("UPDATE final_imagen SET idHistoria = ?, imagen = ?, texto = ?, idUsuario = ? WHERE idImagen = ?");
                $stmt->bind_param("isssi", $this->id_historia, $this->nombre, $this->texto, $id_autor, $this->id);

                return $stmt->execute();
            }
        }

        /**
         * Establece el nombre de una nueva imagen
         * 
         *      $nombre: Nombre de la imagen
         * 
         *      Devuelve: $this para realizar mas cambios en la misma línea si fuera necesario
         */
        public function set_nombre($nombre) {
            $this->nombre = $nombre;
            return $this;
        }

        /**
         * Establece el id de historia de una nueva imagen
         * 
         *      $historia: identificador de la historia
         * 
         *      Devuelve: $this para realizar mas cambios en la misma línea si fuera necesario
         */
        public function set_historia($historia) {
            $this->id_historia = $historia;
            return $this;
        }

        /**
         * Establece el texto de una imagen
         * 
         *      $texto: descripción de la imagen
         * 
         *      Devuelve: $this para realizar mas cambios en la misma línea si fuera necesario
         */
        public function set_texto($texto) {
            $this->texto = $texto;
            return $this;
        }

        /**
         * Establece el autor de una imagen
         * 
         *      $autor: autor de la imagen (Instancia de la clase Usuario)
         * 
         *      Devuelve: $this para realizar mas cambios en la misma línea si fuera necesario
         */
        public function set_autor($autor) {
            $this->autor = $autor;
            return $this;
        }

        /**
         * Devuelve el nombre de la imagen
         */
        public function get_nombre() {
            return $this->nombre;
        }

        /**
         * Devuelve el identificador de la historia a la que pertenece la imagen
         */
        public function get_historia() {
            return $this->id_historia;
        }

        /**
         * Devuelve el autor de la imagen
         */
        public function get_autor() {
            return $this->autor;
        }

        /**
         * Devuelve el texto asociado a la imagen
         */
        public function get_texto() {
            return $this->texto;
        }

        /**
         * Devuelve un enlace a la imagen en tamaño pequeño
         */
        public function get_peq() {
            return $this->nombre . "_peq.jpg";
        }

        /**
         * Devuelve un enlace a la imagen en tamaño mediano
         */
        public function get_med() {
            return $this->nombre . "_med.jpg";
        }

        /**
         * Devuelve un enlace a la imagen en tamaño grande
         */
        public function get_grande() {
            return $this->nombre . ".jpg";
        }

        public function get_id() {
            return $this->id;
        }

        public function eliminar() {
            if ($this->id != null) {
                $db = BaseDatos::get();

                $stmt = $db->prepare("DELETE FROM final_imagen WHERE idImagen= ?");
                $stmt->bind_param("i", $this->id);
                $res = $stmt->execute();

                if ($res) {
                    unlink("uploads/" . $this->get_peq());
                    unlink("uploads/" . $this->get_med());
                    unlink("uploads/" . $this->get_grande());
                }

                return $res;
            }
        }

        public static function get_imagenes() {

            $db = BaseDatos::get();

            $res = $db->query("SELECT * FROM final_imagen");

            if ($res->num_rows == 0) {
                return array();
            }
            else {
                $imagenes = array();
                while ($fila = $res->fetch_assoc()) {
                    $imagenes[] = Imagen::cargar($fila);
                }
                return $imagenes;
            }
        }
    }
?>