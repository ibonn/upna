; Definir el estado inicial, la posición I la tomamos como (72 72).
(defparameter *estado-inicial* '(72 72))

; Definir el estado final, la posición F la tomamos como (324 216).
(defparameter *estado-final* '(324 144))

; Definir la función es-estado-final
(defun es-estado-final (estado)
	(equal estado *estado-final*)
)

; Definir  la  lista  de  posiciones  no  válidas,  definir  un  parámetro  llamado  *no-válidas*
; consistente  en  una  lista  con  las  posiciones  en  las  que  hay  obstáculos, por ejemplo 
; ((36 36) (36 72)).
(defparameter *no-validas* '((36 36) (36 72)))

; Definir el parámetro *operadores*.
(defparameter *operadores*
	'(
		avanzarR1
		retrocederR1
		avanzarR2
		retrocederR2
	)
)

; Definir  la  función:  es-posible  ESTADO,  que  comprueba  si  una  posición  del tablero es válida, es decir, no hay obstáculos.
(defun es-posible (estado)
	(not (member estado *no-validas* :test #'equal))
)

; Definir   las   funciones   asociadas   a   los   operadores   anteriormente   definidos. 
(defun avanzarR1 (estado)
	(if (es-posible (list (+ 36 (first estado)) (second estado)))
		(if (< (first estado) 360)
			(list (+ 36 (first estado)) (second estado))
			nil
		)
		nil
	)
)

(defun retrocederR1 (estado)
	(if (es-posible (list (- (first estado) 36) (second estado)))
		(if (> (first estado) 0)
			(list (- (first estado) 36) (second estado))
			nil
		)
		nil
	)
)

(defun avanzarR2 (estado)
	(if (es-posible (list (first estado) (+ 36 (second estado))))
		(if (< (second estado) 360)
			(list (first estado) (+ 36 (second estado)))
			nil
		)
		nil
	)
)

(defun retrocederR2 (estado)
	(if (es-posible (list (first estado) (- (second estado) 36)))
		(if (> (second estado) 0)
			(list (first estado) (- (second estado) 36))
			nil
		)
		nil
	)
)

; Comprobar tu solución con las funciones APLICA y VERIFICA.
(defun aplica (operador estado)
	(funcall (symbol-function operador) estado)
)

(defun verifica (plan &optional (estado *estado-inicial*))
	(cond 	((null estado) (format t "~&Movimiento no permitido~&") nil)
		((null plan) (cond ((es-estado-final estado) (format t "~&~a estado final~&" estado) t)
				    (t (format t "~&~a no es estado final~&" estado) nil)))
		 (t (format t "~&~a~a" estado (first plan))
			(verifica (rest plan) (aplica (first plan) estado)))))
			
; (print (verifica '(
	; avanzarR2
	; avanzarR2
	; avanzarR2
	; avanzarR2
	; avanzarR1
	; avanzarR2
	; avanzarR2
	; avanzarR1
	; avanzarR1
	; avanzarR1
	; retrocederR2
	; retrocederR2
	; retrocederR1
	; retrocederR2
	; retrocederR2
	; avanzarR1
	; avanzarR1
	; avanzarR1
	; avanzarR1
; )))

(defun heuristica (estado)
	(+ (abs (- (first estado) (first *estado-final*))) (abs (- (second estado) (second *estado-final*))))
)

(defun coste-de-aplicar-operador (estado operador)
	0
)