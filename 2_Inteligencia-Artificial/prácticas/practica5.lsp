; Variables globales

(defparameter m 3)	; Número de misioneros
(defparameter c 3)	; Número de caníbales

(defparameter izquierda 0)
(defparameter derecha 1)

; Creación de estados
(defun crea-estado (m c b)
	(list m c b)
)

; Estados inicial y final
;	(numero-misioneros-izda numero-canibales-izda posicion-barca)
;	posicion-barca:
;		0 = izquierda
;		1 = derecha
(defparameter *estado-inicial* (crea-estado m c izquierda))
(defparameter *estado-final*   (crea-estado 0 0 derecha))

; Acceso a las componentes del estado
(defun num-misioneros-izda (estado)
	(nth 0 estado)
)

(defun num-canibales-izda (estado)
	(nth 1 estado)
)

(defun num-misioneros-dcha (estado)
	(- m (num-misioneros-izda estado))
)

(defun num-canibales-dcha (estado)
	(- c (num-canibales-izda estado))
)

(defun posicion-barca (estado)
	(nth 2 estado)
)

; Función es-seguro
(defun es-seguro (estado)
	(if (equal (posicion-barca estado) izquierda)
		(if (> (num-canibales-dcha estado) (num-misioneros-dcha estado))
			nil
			estado
		)
		(if (> (num-canibales-izda estado) (num-misioneros-izda estado))
			nil
			estado
		)
	)
)

; Operadores
(defparameter *operadores*
	'(
		pasar-canibal-izda
		pasar-canibal-dcha
		pasar-misionero-izda
		pasar-misionero-dcha
		pasar-canibal-misionero-dcha
		pasar-canibal-misionero-izda
		pasar-canibales-izda
		pasar-canibales-dcha
		pasar-misioneros-izda
		pasar-misioneros-dcha
	)
)

(defun pasar-canibal-izda (estado)
	(if (equal (posicion-barca estado) derecha)
		(progn
			(setf nuevo (crea-estado (num-misioneros-izda estado) (+ (num-canibales-izda estado) 1) izquierda))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-canibal-dcha (estado)
	(if (equal (posicion-barca estado) izquierda)
		(progn
			(setf nuevo (crea-estado (num-misioneros-izda estado) (- (num-canibales-izda estado) 1) derecha))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-misionero-izda (estado)
	(if (equal (posicion-barca estado) derecha)
		(progn
			(setf nuevo (crea-estado (+ (num-misioneros-izda estado) 1) (num-canibales-izda estado) izquierda))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-misionero-dcha (estado)
	(if (equal (posicion-barca estado) izquierda)
		(progn
			(setf nuevo (crea-estado (- (num-misioneros-izda estado) 1) (num-canibales-izda estado) derecha))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-canibal-misionero-izda (estado)
	(if (equal (posicion-barca estado) derecha)
		(progn
			(setf nuevo (crea-estado (+ (num-misioneros-izda estado) 1) (+ (num-canibales-izda estado) 1) izquierda))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-canibal-misionero-dcha (estado)
	(if (equal (posicion-barca estado) izquierda)
		(progn
			(setf nuevo (crea-estado (- (num-misioneros-izda estado) 1) (- (num-canibales-izda estado) 1) derecha))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-canibales-izda (estado)
	(if (equal (posicion-barca estado) derecha)
		(progn
			(setf nuevo (crea-estado (num-misioneros-izda estado) (+ (num-canibales-izda estado) 2) izquierda))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-canibales-dcha (estado)
	(if (equal (posicion-barca estado) izda)
		(progn
			(setf nuevo (crea-estado (num-misioneros-izda estado) (- (num-canibales-izda estado) 2) derecha))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-misioneros-izda (estado)
	(if (equal (posicion-barca estado) derecha)
		(progn
			(setf nuevo (crea-estado (+ (num-misioneros-izda estado) 2) (num-canibales-izda estado) izquierda))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

(defun pasar-misioneros-dcha (estado)
	(if (equal (posicion-barca estado) izda)
		(progn
			(setf nuevo (crea-estado (- (num-misioneros-izda estado) 2) (num-canibales-izda estado) derecha))
			(if (es-seguro nuevo)
				nuevo
				nil
			)
		)
		nil
	)
)

; Verifica
(verifica 
	'(
		pasar-canibales-dcha
		pasar-canibal-izda
		pasar-canibales-dcha
		pasar-canibal-izda
		pasar-misioneros-dcha
	)
)