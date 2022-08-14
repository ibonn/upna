; Declaración de variables globales
(defparameter m 5)
(defparameter n 5)

; Representación de estados
(defstruct celda
	tablero		; Tablero
	posicion	; Posición del caballo
)

; Estado inicial
(defun crea-estado-inicial (x y)
	(make-celda
		:tablero (make-array (list m n))
		:posicion '(0 0)
	)
)

(defparameter *estado-inicial* (crea-estado-inicial m n))

; Estado final
(defun es-estado-final (estado)
	(equalp (make-array (list m n) :initial-element t) (celda-tablero estado))
)

; Operadores
(defparameter *operadores* '(
		mueve-caballo-1
		mueve-caballo-2
		mueve-caballo-3
		mueve-caballo-4
		mueve-caballo-5
		mueve-caballo-6
		mueve-caballo-7
		mueve-caballo-8
	)
)

(defun es-posible (estado)
	(setf x (first (celda-posicion estado)))
	(setf y (second (celda-posicion estado)))
	(setf nuevox (+ x horizontal))
	(setf nuevoy (+ y vertical))
	(and (and (< 0 nuevox) (> m nuevox)) (and (< 0 nuevoy) (> n nuevoy))
)

(defun mueve-caballo (vertical horizontal estado)
	(if (es-posible estado))
		
	)
)

(defun mueve-caballo-1 (estado)
	(mueve-caballo -2 1 estado)
)

(defun mueve-caballo-2 (estado)
	(mueve-caballo -1 2 estado)
)

(defun mueve-caballo-3 (estado)
	(mueve-caballo 1 2 estado)
)

(defun mueve-caballo-4 (estado)
	(mueve-caballo 2 1 estado)
)

(defun mueve-caballo-5 (estado)
	(mueve-caballo 2 -1 estado)
)

(defun mueve-caballo-6 (estado)
	(mueve-caballo 1 -2 estado)
)

(defun mueve-caballo-7 (estado)
	(mueve-caballo -1 -2 estado)
)

(defun mueve-caballo-8 (estado)
	(mueve-caballo -2 -1 estado)
)