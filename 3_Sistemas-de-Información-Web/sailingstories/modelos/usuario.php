<?php
    /**
     * Clase Usuario
     * 
     * Representa a un usuario de la página. Existen dos tipos de usuario:
     *      - Usuario anónimo:    El usuario que no se ha registrado en la página o todavía no se ha identificado.
     *                            Al guardar se crea un nuevo usuario.
     *      - Usuario registrado: El usuario posee una cuenta en la página. Al guardar se actualiza en la base de datos 
     *                            si el usuario está identificado. En caso de no estar identificado, se lanza una excepción.
     * 
     * Ejemplo:
     * $usuario_anon = new Usuario();
     * $usuario_login = Usuario::login("usuario@servidor.tld", "contraseña");
     * $usuario = new Usuario("nombre_usuario");
     * 
     * $usuario_anon->guardar();        // Crea un nuevo usuario en la base de datos (El usuario se registra)
     * $usuario_login->guardar();       // Se guardan los cambios que el usuario haya realizado en la base de datos
     */

    // Cargar los archivos necesarios
    require_once "include/db.php";  // Conexión con la base de datos

    class Usuario {

        // Constantes de tipos de usuario
        const USUARIO_ANONIMO = 0;
        const USUARIO_REGISTRADO = 1;

        private $idUsuario;      // Nombre de usuario
        private $correo;         // Correo del usuario
        private $password;       // Hash SHA-256 de la contraseña del usuario
        private $salt;           // Caracteres añadidos al final de la contraseña para formar el hash anterior

        private $tipo;          // Tipo de usuario (Anónimo o registrado)

        /**
         * Constructor de la clase. Carga un usuario de la base de datos
         * 
         *      $id: Identificador de usuario. Si es nulo, se instancia un usuario anónimo.
         * 
         *      Tanto si el usuario es anónimo como si no, no tendrá permisos de escritura. Para ello debe
         *      iniciar sesión mediante el método login()
         */
        public function __construct($id=null) {

            // Establecer valores por defecto
            $this->idUsuario = null;
            $this->correo = null;
            $this->password = null;
            $this->salt = null;

            $this->tipo = Usuario::USUARIO_ANONIMO;

            // Cargar el usuario de la base de datos
            if ($id) {
                $db = BaseDatos::get();

                $stmt = $db->prepare("SELECT * FROM final_usuario WHERE idUsuario = ?");
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows != 0) {
                    $usuario = Usuario::cargar($res->fetch_assoc());
                    foreach (get_object_vars($usuario) as $attr => $valor)
                        $this->$attr = $valor;
                }
            }
        }

        public static function get_num() {
            $db = BaseDatos::get();
            $res = $db->query("SELECT COUNT(*) AS num_usuarios FROM final_usuario");
            $fila = $res->fetch_assoc();
            return $fila["num_usuarios"];
        }

        /**
         * Iniciar sesión. Permite realizar modificaciones
         * 
         *      $nombre: Nombre del usuario
         *      $pass: Contraseña del usuario
         * 
         *      Si el inicio de sesión es satisfactorio, se devolverá el usuario con permisos de escritura.
         *      En otro caso se devolverá un usuario anónimo sin permisos.
         */
        public static function login($email, $pass) {
            $db = BaseDatos::get();

            $stmt = $db->prepare("SELECT * FROM final_usuario WHERE idUsuario = ? AND SHA2(CONCAT(?, salt), 256) = password");
            $stmt->bind_param("ss", $email, $pass);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows == 0)
                return new Usuario();
            else
                return Usuario::cargar($res->fetch_assoc());
        }

        /**
         * Carga un usuario dada una fila de la base de datos
         * 
         *      $fila: Fila de la base de datos
         * 
         *      Devuelve: Instancia de la clase Usuario
         */
        private static function cargar($fila) {
            $usuario = new Usuario();

            // Establecer los atributos de tipo y permisos para guardar
            $usuario->tipo = Usuario::USUARIO_REGISTRADO;

            // Cargar los datos
            foreach ($fila as $attr => $valor)
                $usuario->$attr = $valor;

            return $usuario;
        }

        /**
         * Genera una cadena de caracteres para hacer la contraseña mas segura y dificil de recuperar
         * a partir del hash
         * 
         *      $longitud: Longitud de la cadena. Por defecto es 32
         * 
         *      Devuelve: Una cadena de caracteres aleatoria
         */
        private static function genera_salt($longitud=32) {
            $caracteres = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ!?+-*/=<>()[]{}_.,:;@$%&";
            $salt = "";
            for ($i = 0; $i < $longitud; $i++) {
                $salt .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }
            return $salt;
        }

        /**
         * Obtiene el ID de usuario
         */
        public function get_id() {
            return $this->idUsuario;
        }

        /**
         * Establece el ID del usuario
         * 
         *      $nombre: El nuevo identificador de usuario
         * 
         *      Devuelve: $this por si fuera necesario realizar mas modificaciones en la misma línea 
         */
        public function set_id($nombre) {
            $this->idUsuario = $nombre;
            return $this;
        }

        /**
         * Obtiene el email del usuario si es válido
         */
        public function get_correo() {
            return $this->correo;
        }

        /**
         * Modifica el email del usuario si es válido
         * 
         *      $email: La nueva dirección de email
         * 
         *      Devuelve: $this por si fuera necesario realizar mas modificaciones en la misma línea 
         */
        public function set_correo($email) {
            $this->correo = $email;
            return $this;
        }

        /**
         * Modifica la contraseña del usuario
         */
        public function set_password($password) {
            $this->salt = Usuario::genera_salt();
            $this->password = hash("sha256", $password . $this->salt);
            return $this;
        }

        /**
         * Obtiene el tipo de usuario (USUARIO_REGISTRADO, USUARIO_ANONIMO)
         */
        public function get_tipo() {
            return $this->tipo;
        }
        
        /**
         * Guarda el usuario en la base de datos
         */
        public function guardar() {
            $db = BaseDatos::get();

            // Si el usuario está registado, acutaliza los datos. En caso contrario, crea un nuevo usuario
            if ($this->tipo == Usuario::USUARIO_REGISTRADO) {
                $stmt = $db->prepare("UPDATE final_usuario SET idUsuario = ?, correo = ?, password = ?, salt = ? WHERE idUsuario = ?");
                $stmt->bind_param("sssss", $this->idUsuario, $this->correo, $this->password, $this->salt, $this->idUsuario);
            }
            else {
                $stmt = $db->prepare("INSERT INTO final_usuario (idUsuario, correo, password, salt) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $this->idUsuario, $this->correo, $this->password, $this->salt);
            }

            // Ejecutar la consulta y devolver el resultado
            return $stmt->execute();
        }

        /**
         * Devuelve el identificador del usuario
         */
        public function __toString() {
            return $this->idUsuario;
        }

        public static function get_usuarios() {

            $db = BaseDatos::get();

            $res = $db->query("SELECT * FROM final_usuario");

            if ($res->num_rows == 0) {
                return array();
            }
            else {
                $usuarios = array();
                while ($fila = $res->fetch_assoc()) {
                    $usuarios[] = Usuario::cargar($fila);
                }
                return $usuarios;
            }
        }

        public function eliminar() {
            $db = BaseDatos::get();

            $stmt = $db->prepare("DELETE FROM final_usuario WHERE idUsuario = ?");
            $stmt->bind_param("s", $this->idUsuario);
            return $stmt->execute();
        }
    }
?>