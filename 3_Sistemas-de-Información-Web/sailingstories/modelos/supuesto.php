<?php

    include_once "include/db.php";

    class Supuesto {

        private $idComienzo;
        private $genero;
        private $comienzo;

        public function __construct($id=null) {
            if ($id == null) {
                $this->genero = null;
                $this->comienzo = null;
            }
            else {
                $db = BaseDatos::get();

                $stmt = $db->prepare("SELECT * FROM final_supuesto WHERE idComienzo = ?");
                $stmt->bind_param("i", $id);
                $res = $stmt->execute();
                $res = $stmt->get_result();
                if ($res->num_rows != 0)  {
                    $supuesto = Supuesto::cargar($res->fetch_assoc());
                    foreach (get_object_vars($supuesto) as $attr => $valor)
                        $this->$attr = $valor;
                }
            }
        }

        public function get_id() {
            return $this->idComienzo;
        }

        public function get_genero() {
            return $this->genero;
        }

        public function get_texto() {
            return $this->comienzo;
        }

        public function set_genero($genero) {
            $this->genero = $genero;
            return $this;
        }

        public function set_texto($texto) {
            $this->comienzo = $texto;
            return $this;
        }

        public static function get_supuesto_aleatorio($genero) {
            $db = BaseDatos::get();
            $stmt = $db->prepare("SELECT * FROM final_supuesto WHERE genero = ? ORDER BY RAND() LIMIT 1");
            $stmt->bind_param("s", $genero);
            $res = $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows != 0)  {
                return Supuesto::cargar($res->fetch_assoc());
            }
            return new Supuesto();
        }

        public static function get_supuestos() {

            $db = BaseDatos::get();

            $res = $db->query("SELECT * FROM final_supuesto");

            if ($res->num_rows == 0) {
                return array();
            }
            else {
                $capitulos = array();
                while ($fila = $res->fetch_assoc()) {
                    $capitulos[] = Supuesto::cargar($fila);
                }
                return $capitulos;
            }
        }

        private static function cargar($fila) {
            $supuesto = new Supuesto();

            $supuesto->idComienzo = $fila["idComienzo"];
            $supuesto->genero = $fila["genero"];
            $supuesto->comienzo = $fila["comienzo"];

            return $supuesto;
        }

        public function guardar() {
            $db = BaseDatos::get();

            if ($this->idComienzo == null) {
                $stmt = $db->prepare("INSERT INTO final_supuesto (genero, comienzo) VALUES (?, ?)");
                $stmt->bind_param("ss", $this->genero, $this->comienzo);
                $res = $stmt->execute();

                $this->idComienzo = BaseDatos::get_ultimo_id();
                return $res;
            }
            else {
                $stmt = $db->prepare("UPDATE final_supuesto SET genero = ?, comienzo = ? WHERE idComienzo = ?");
                $stmt->bind_param("ssi", $this->genero, $this->comienzo, $this->idComienzo);
                return $stmt->execute();
            }
        }

        public function __toString() {
            return $this->comienzo;
        }

        public function eliminar() {
            if ($this->id != null) {
                $db = BaseDatos::get();

                $stmt = $db->prepare("DELETE FROM final_supuesto WHERE idComienzo = ?");
                $stmt->bind_param("i", $this->id);

                return $stmt->execute();
            }
        }
    }
?>