<?php

    include_once "include/db.php";  // Conexión con la base de datos

    /**
     * Esta clase representa un administrador
     */
    class Admin {

        private $nombre;    // Nombre del administrador

        // Constructor privado para evitar acceso a admin sin usar login()
        private function __construct($nombre) {
            $this->nombre = $nombre;
        }

        public static function login($usuario, $password) {
            $con = BaseDatos::get();

            //recogemos del formulario las variables para la llamada en sql
            $password = md5($password);
        
            $consulta = $con->prepare("select * from final_admin where usuario = ?");
            $consulta->bind_param("s", $usuario);

            if ($consulta->execute()) {
                $resultado = $consulta->get_result();
                if ($datos = $resultado->fetch_assoc()) {
                    if($password == $datos["password"]){
                        return new Admin($usuario);
                    }else{
                        return null;
                    }				
                }else{
                    return null;
                }
            } else {
                throw new Exception("Ha ocurrido un problema con la base de datos. Por favor, inténtalo de nuevo mas tarde.");
            }
        }

        public static function crear($usuario, $password) {
            $con = BaseDatos::get();
            $consulta = $con->prepare("insert into final_admin (usuario, password) values (?, ?)");
            $password = md5(password);
            $consulta->bind_param("ss", $usuario, $password);
            return $consulta->execute();
        }

        public static function eliminar($usuario) {
            $con = BaseDatos::get();
            $consulta = $con->prepare("DELETE FROM final_admin WHERE usuario = ?");
            $consulta->bind_param("s", $usuario);
            return $consulta->execute();
        }

        public static function get_usuarios() {
            $con = BaseDatos::get();
            $consulta = $con->prepare("select * from final_admin");
            $consulta->execute();
            $res = $consulta->get_result();

            $admins = array();

            while ($data = $res->fetch_assoc()) {
                $admins[] = new Admin($data["usuario"]);
            }

            return $admins;
        }

        public function get_nombre() {
            return $this->nombre;
        }
    }
?>