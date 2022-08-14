<?php
    require_once "include/db.php";  // Conexión con la base de datos

    include_once "modelos/capitulo.php";

    /**
     * Clase Votacion
     * Representa la votación de un capítulo
     */
    class Votacion {
        private $id; 
        private $capitulo;  // El capitulo que se esta votando
        private $fecha_fin; // Fecha en la que finaliza la votación

        /**
         * Crear una nueva votación
         */
        public function __construct() {

        }

        public static function crear($capitulo) {
            $votacion = new Votacion();

            $votacion->capitulo = $capitulo;

            $capitulo->set_estado(Capitulo::CAPITULO_EN_VOTACION);

            if ($capitulo->guardar()) {
                $votacion->fecha_fin = date("Y/m/d", strtotime('+1 Week'));

                $db = BaseDatos::get();
    
                $id = $capitulo->get_id();
    
                $stmt = $db->prepare("INSERT INTO final_votacion (idCapitulo, fecha_fin) VALUES (?, ?)");
                $stmt->bind_param("is", $id, $votacion->fecha_fin);
                $res = $stmt->execute();
    
                $votacion->id = BaseDatos::get_ultimo_id();
                
                if ($res)
                    return $votacion;
            }
            return null;
        }

        public static function cargar($fila) {
            $votacion = new Votacion();
            $votacion->capitulo = Capitulo::cargar($fila);
            $votacion->id = $fila["idVotacion"];
            $votacion->fecha_fin = new DateTime($fila["fecha_fin"]);
            return $votacion;
        }

        /**
         * Calcula el tiempo restante en segundos para que finalice la votación
         */
        public function get_tiempo_restante() {
            return $this->fecha_fin->getTimestamp() - (new DateTime())->getTimestamp();
        }

        /**
         * Devuelve el numero de votos recibidos
         */
        public function get_numero_votos() {
            $db = BaseDatos::get();

            $id_capitulo = $this->capitulo->get_id();

            $stmt = $db->prepare("SELECT COUNT(*) AS num_votos FROM final_voto WHERE idCapitulo = ? GROUP BY idCapitulo");
            $stmt->bind_param("i", $id_capitulo);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows == 0)  {
                return 0;
            }
            else {
                return $res->fetch_assoc()["num_votos"];
            }
        }

        public function get_capitulo() {
            return $this->capitulo;
        }

        public static function get_votacion_capitulo($id) {
            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT * FROM final_votacion NATURAL JOIN final_capitulo WHERE idCapitulo = ? AND fecha_fin > NOW()");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows == 0)  {
                return array();
            }
            else {
                return Votacion::cargar($res->fetch_assoc());
            }
        }

        public static function get_votaciones() {
            $db = BaseDatos::get();

            $res = $db->query("SELECT final_votacion.idCapitulo, idVotacion, fecha_fin, idHistoria, titulo, autor, texto, estado, es_final, COUNT(idUsuario) AS num_votos FROM final_votacion NATURAL JOIN final_capitulo LEFT JOIN final_voto ON final_voto.idCapitulo = final_capitulo.idCapitulo GROUP BY idCapitulo, idVotacion ORDER BY num_votos DESC");

            if ($res->num_rows == 0)  {
                return array();
            }
            else {
                $votaciones = array();
                while ($fila = $res->fetch_assoc()) {
                    $votaciones[] = Votacion::cargar($fila);
                }
                return $votaciones;
            }
        }

        public function aprobar() {
            $capitulo = $this->get_capitulo();
            $capitulo->set_estado(Capitulo::CAPITULO_FINALIZADO);
            if ($capitulo->guardar()) {
                if ($capitulo->get_es_final()) {
                    if($capitulo->get_historia()->set_estado(Historia::HISTORIA_FINALIZADA)->guardar()) {
                        return $this->eliminar();
                    }
                }
                else {
                    return $this->eliminar();
                }
            }
            return false;
        }

        public function eliminar() {
            $db = BaseDatos::get();

            $stmt = $db->prepare("DELETE FROM final_votacion WHERE idVotacion = ?");
            $stmt->bind_param("i", $this->id);
            return $stmt->execute();
        }
    }
?>