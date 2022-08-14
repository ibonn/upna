; 1. Construir los estados.
(defun crear-estado (x y)
	(list x y)
)

; 2. Acceso a las componentes de cada estado.
(defun contenido-jarra-3 (estado)
	(second estado)
)

(defun contenido-jarra-4 (estado)
	(first estado)
)

; 3. Declarar el estado inicial.
(defparameter *estado-inicial* (crear-estado 0 0))

; 4. Declarar el  estado final  o la función de comprobación de si es estado final. 
;    ¿Se necesitan funciones auxiliares?
(defun es-estado-final (estado)
	(equal (contenido-jarra-4 estado) 2)
)

; 6. Definir las funciones de cada elemento de la lista de operadores.
(defparameter *operadores*
	'(
		llenar-jarra-4
		llenar-jarra-3
		volcar-jarra-4
		volcar-jarra-3
		vaciar-jarra-4
		vaciar-jarra-3
	)
)

; 5. Declarar la lista de operadores. 
(defun llenar-jarra-3 (estado)
	(if (< (contenido-jarra-3 estado) 3)
		(crear-estado (contenido-jarra-4 estado) 3)
		estado
	)
)

(defun llenar-jarra-4 (estado)
	(if (< (contenido-jarra-4 estado) 4)
		(crear-estado 4 (contenido-jarra-3 estado))
		estado
	)
)

(defun volcar-jarra-3 (estado)
	(if (and (> (contenido-jarra-3 estado) 0) (< (contenido-jarra-4 estado) 4))
		(if (> (+ (contenido-jarra-3 estado) (contenido-jarra-4 estado)) 4)
			(crear-estado 4 (- (+ (contenido-jarra-3 estado) (contenido-jarra-4 estado)) 4))
			(crear-estado (+ (contenido-jarra-3 estado) (contenido-jarra-4 estado)) 0)
		)
		estado
	)
)

(defun volcar-jarra-4 (estado)
	(if (and (> (contenido-jarra-4 estado) 0) (< (contenido-jarra-3 estado) 3))
		(if (> (+ (contenido-jarra-3 estado) (contenido-jarra-4 estado)) 3)
			(crear-estado (- (+ (contenido-jarra-4 estado) (contenido-jarra-3 estado)) 3) 3)
			(crear-estado 0 (+ (contenido-jarra-3 estado) (contenido-jarra-4 estado)))
		)
		estado
	)
)

(defun vaciar-jarra-3 (estado)
	(if (> (contenido-jarra-3 estado) 0)
		(crear-estado (contenido-jarra-4 estado) 0)
		estado
	)
)

(defun vaciar-jarra-4 (estado)
	(if (> (contenido-jarra-4 estado) 0)
		(crear-estado 0 (contenido-jarra-3 estado))
		estado
	)
)

; 7. Construir la función "Aplica".
(defun aplica (operador estado)
	(funcall (symbol-function operador) estado)
)

; 8. Construir el procedimiento de verificación.
(defun verifica (operadores)
	(setf estado-actual *estado-inicial*)
	(loop
		(if (equal operadores nil)
			(return nil)
			(if (es-estado-final estado-actual)
				(return estado-actual)
				(setf estado-actual (aplica (pop operadores) estado-actual))
			)
		)
	)
)

; (print (verifica '(llenar-jarra-4 volcar-jarra-4 vaciar-jarra-3 volcar-jarra-4 llenar-jarra-4 volcar-jarra-4)))