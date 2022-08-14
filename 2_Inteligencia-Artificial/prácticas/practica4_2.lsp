; Definir el estado inicial.
(defparameter *estado-inicial* '(i i i i))

; Definir el estado final.
(defparameter *estado-final* '(d d d d))

;; Crear estado.
(defun crea-estado (granjero lobo cabra col)
	(list granjero lobo cabra col)
)

; Definir las funciones de acceso a cada componente de los estados.
(defun posicion-granjero (estado)
	(nth 0 estado)
)

(defun posicion-lobo (estado)
	(nth 1 estado)
)

(defun posicion-cabra (estado)
	(nth 2 estado)
)

(defun posicion-col (estado)
	(nth 3 estado)
)

; Definir la función ES-SEGURO, que comprueba que la posición es segura.
(defun es-seguro (estado)
	(if (equal (posicion-lobo estado) (posicion-cabra estado))
		(if (equal (posicion-granjero estado) (posicion-lobo estado))
			estado
			nil
		)
		(if (equal (posicion-cabra estado) (posicion-col estado))
			(if (equal (posicion-cabra estado) (posicion-granjero estado))
				estado
				nil
			)
		)
	)
)

; Definir el parámetro *operadores*.

(defparameter *operadores*
	'(
		mover-granjero-izda
		mover-granjero-dcha
		mover-lobo-izda
		mover-lobo-dcha
		mover-cabra-izda
		mover-cabra-dcha
		mover-col-izda
		mover-col-dcha
	)
)

; Definir las funciones asociadas a los operadores anteriormente definidos. 

(defun mover-granjero-izda (estado)
	(if (es-seguro (crea-estado 'i (posicion-lobo estado) (posicion-cabra estado) (posicion-col estado)))
		(crea-estado 'i (posicion-lobo estado) (posicion-cabra estado) (posicion-col estado))
		nil
	)
)

(defun mover-granjero-dcha (estado)
	(if (es-seguro (crea-estado 'd (posicion-lobo estado) (posicion-cabra estado) (posicion-col estado)))
		(crea-estado 'd (posicion-lobo estado) (posicion-cabra estado) (posicion-col estado))
		nil
	)
)

(defun mover-lobo-izda (estado)
	(if (es-seguro (crea-estado (posicion-granjero estado) 'i (posicion-cabra estado) (posicion-col estado)))
		(crea-estado (crea-estado (posicion-granjero estado) 'i (posicion-cabra estado) (posicion-col estado)))
		nil
	)
)

(defun mover-lobo-dcha (estado)
	(if (es-seguro (crea-estado (posicion-granjero estado) 'd (posicion-cabra estado) (posicion-col estado)))
		(crea-estado (crea-estado (posicion-granjero estado) 'd (posicion-cabra estado) (posicion-col estado)))
		nil
	)
)

(defun mover-cabra-izda (estado)
	(if (es-seguro (crea-estado (posicion-granjero estado) (posicion-lobo estado) 'i (posicion-col estado)))
		(crea-estado (crea-estado (posicion-granjero estado) (posicion-lobo estado) 'i (posicion-col estado)))
		nil
	)
)

(defun mover-cabra-dcha (estado)
	(if (es-seguro (crea-estado (posicion-granjero estado) (posicion-lobo estado) 'd (posicion-col estado)))
		(crea-estado (crea-estado (posicion-granjero estado) (posicion-lobo estado) 'd (posicion-col estado)))
		nil
	)
)

(defun mover-col-izda (estado)
	(if (es-seguro (crea-estado (posicion-granjero estado) (posicion-lobo estado) (posicion-cabra estado) 'i))
		(crea-estado (crea-estado (posicion-granjero estado) (posicion-lobo estado) (posicion-cabra estado) 'i))
		nil
	)
)

(defun mover-col-dcha (estado)
	(if (es-seguro (crea-estado (posicion-granjero estado) (posicion-lobo estado) (posicion-cabra estado) 'd))
		(crea-estado (crea-estado (posicion-granjero estado) (posicion-lobo estado) (posicion-cabra estado) 'd))
		nil
	)
)

; Comprobar tu solución con las funciones APLICA y VERIFICA.

(verifica 
	'(
		
	)
)