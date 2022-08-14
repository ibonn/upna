<?php
    /**
     * Clase PlantillaHTML
     * 
     * Permite gestionar fácilmente las plantillas
     * 
     */
    class PlantillaHTML {
        
        /**
         * Crea una nueva instancia del gestor de plantillas
         * 
         *      $nombre: nombre del archivo htm que se va a cargar para ser usado como plantilla
         */
        public function __construct($nombre) {
            $this->html = file_get_contents($nombre);
            $this->variables = array();
            $this->bloques = array();
            $this->ocultar = array();
        }

        /**
         * Establece un valor en la plantilla
         * 
         *      $nombre: nombre del valor a establecer
         *      $valor: valor que se desea establecer
         * 
         *      Devuelve: $this para poder volver a llamar a otro metodo de esta clase sobre el resultado si fuera necesario
         */
        public function set($nombre, $valor) {
            $this->variables[$nombre] = $valor;
            return $this;
        }

        /**
         * Establece múltiples valores
         * 
         *      $arr: diccionario clave-valor, donde cada clave es el nombre del valor que se desea establecer
         *            y el valor es el valor que se quere establecer para ese nombre. Es equivalente a llamar
         *            a set() para cada clave-valor. 
         * 
         *      Devuelve: $this para poder volver a llamar a otro metodo de esta clase sobre el resultado si fuera necesario
         */
        public function set_array($arr) {
            foreach ($arr as $nombre => $valor)
                $this->set($nombre, $valor);
            return $this;
        }

        /**
         * Establece múltiples valores que se repiten (Por ejemplo una lista), creando un nuevo bloque para cada item del array
         * 
         *      $nombre: nombre del bloque que se va a repetir tantas veces como elementos tenga $arr
         *      $arr: lista de diccionarios, en las que cada diccionario es un elemento que se quiere mostrar
         *            dentro del bloque, las claves del diccionario son los nombres de las variables que se
         *            quieren sustituir en la plantilla y los valores del diccionario los valores por los que serán
         *            sustituidas esas variables.
         * 
         * Ejemplo:
         * 
         *      <ul>
         *      ##bloque lista##
         *           <li class="##clase##">##elemento##</li>
         *      ##bloque lista##
         *      </ul>
         * 
         * Si a una plantilla con el código anterior le pasamos
         *      $valores = array(
         *           array("clase" => "clase1", "elemento" => "un elemento de la lista"),
         *           array("clase" => "clase2", "elemento" => "otro elemento de la lista"),
         *           array("clase" => "clase1", "elemento" => "el último elemento")
         *      );
         *      $plantilla->set_bloque("lista", $valores);
         * 
         * Se obtiene como resultado
         * 
         *      <ul>
         *          <li class="clase1">un elemento de la lista</li>
         *          <li class="clase2">otro elemento de la lista</li>
         *          <li class="clase1">el último elemento</li>
         *      </ul>
         */
        public function set_bloque($nombre, $arr) {
            $this->bloques[$nombre] = $arr;
            return $this;
        }

        // Oculta un bloque de el codigo
        public function ocultar_bloque($nombre) {
            $this->ocultar[] = $nombre;
        }

        /**
         * Prepara el html para ser visualizado, reemplazando todas las variables por sus valores
         * 
         *      Devuelve: El html resultante de reemplazar todos los valores en la plantilla
         */
        public function render() {
            $html = $this->html;

            // Oculta los bloques que se ha marcado como ocultos
            foreach($this->ocultar as $nombre) {
                $trozos = explode("##bloque $nombre##", $html);
                $html = $trozos[0] . $trozos[2];
            }

            // Se sustituyen los bloques
            foreach($this->bloques as $nombre => $contenido) {
                $trozos = explode("##bloque $nombre##", $html);
                $bloque = $trozos[1];
                
                $items = "";
                foreach($contenido as $item) {
                    $nuevo = $bloque;
                    foreach ($item as $nombre => $valor) {
                        $nuevo = str_replace("##$nombre##", $valor, $nuevo);
                    }
                    $items .= $nuevo;
                }

                $html = $trozos[0] . $items . $trozos[2];
            }
            
            // Se sustituyen los valores de las variables
            foreach($this->variables as $nombre => $valor)
                $html = str_replace("##$nombre##", $valor, $html);

            // Eliminar todos los marcadores de bloques que no se han ocultado
            $html = preg_replace("/##bloque .+##/", "", $html);

            return $html;
        }

        /**
         * Representación en string de la plantilla
         * 
         *      Devuelve: El html resultante de reemplazar todos los valores en la plantilla
         */
        public function __toString() {
            return $this->render();
        }
    }
?>