; 1. La variable *ESTADO-INICIAL* que contiene el estado inicial. 
; 2. La función (ES-ESTADO-FINAL ESTADO) que determina si el ESTADO es un  estado final. 
; 3. La variable *OPERADORES* que contiene la lista de operadores.
; 4. Para cada OPERADOR la función (OPERADOR ESTADO-ACTUAL) que devuelve 
;    el estado obtenido aplicando el OPERADOR al ESTADO-ACTUAL, si el OPERADOR es aplicable y 
;    NIL, en caso contrario.
; 5. La función (HEURISTICA ESTADO) que devuelve el valor de la función de evaluación 
;    heurística aplicada al ESTADO (es decir, el coste estimado para alcanzar una solución a 
;    partir del ESTADO).
; 6. La función (COSTE-DE-APLICAR-OPERADOR ESTADO OPERADOR) que devuelve el 
;    coste de aplicar el OPERADOR al ESTADO.     

; Estructura nodo
(defstruct nodo-ch
	estado					; Estado del nodo
	camino					; Camino
	coste					; Coste
	coste-mas-heuristica	; Coste + heurística
)

; Funciones auxiliares
(defun sucesor (nodo operador)
	(setf nuevo-estado (aplica operador (nodo-ch-estado nodo)))
	(if nuevo-estado
		(make-nodo-ch
			:estado 				nuevo-estado
			:camino 				(append (nodo-ch-camino nodo) operador)
			:coste 					(+ (coste-de-aplicar-operador (nodo-ch-estado nodo) operador) (nodo-ch-coste nodo))
			:coste-mas-heuristica	(+ (heuristica nuevo-estado) (nodo-ch-coste-mas-heuristica nodo))
		)
		nil
	)
)

(defun sucesores (nodo)
	(setf resultado '())
	(dolist (o *operadores*)
		(setf nodo-sucesor (sucesor nodo o))
		(if nodo-sucesor
			(push nodo-sucesor resultado)
		)
	)
	resultado
)

(defun esta-mejor (nodo lista)
	(dolist (n lista)
		(if (equalp (nodo-ch-estado nodo) (nodo-ch-estado n))
			(if (<= (nodo-ch-coste n) (nodo-ch-coste nodo))
				(return t)
			)
		)
	)
)
; Eliminar los peores, no seleccionar los mejores
(defun elimina-peores (lista0 lista1 lista2)
	(setf resultado '())
	(dolist (nodo lista0)
		(if (and (esta-mejor nodo lista1) (esta-mejor nodo lista2))
			(push nodo resultado)
		)
	)
	resultado
)

(defun nuevos-o-mejores-sucesores (nodo lista1 lista2)
	(elimina-peores (sucesores nodo) lista1 lista2)
)

(defun ordena-por-coste-mas-heur (lista)
	(sort lista #'> :key #'nodo-ch-coste-mas-heuristica)
)

(defun a-estrella ()
	(setf abiertos (list 
		(make-nodo-ch
			:estado 				*estado-inicial*
			:camino 				'()
			:coste 					0
			:coste-mas-heuristica	(heuristica *estado-inicial*)
		)
	))
	(setf cerrados '())
	(setf actual '())
	(setf sucesores '())
	(loop
		(if abiertos
			(progn
				(setf actual (list (car abiertos)))
				(setf abiertos (cdr abiertos))
				(setf cerrados (append cerrados actual))			
				(if (es-estado-final (nodo-ch-estado (first actual)))
					(return actual)
					(progn		
						(setf sucesores (nuevos-o-mejores-sucesores (first actual) abiertos cerrados))	
						(setf abiertos (append abiertos sucesores))
						(setf abiertos (ordena-por-coste-mas-heur abiertos))
						
						; (format t "~%Abiertos: ~S~%" abiertos)
						; (format t "Cerrados: ~S~%" cerrados)
						; (format t "Actual: ~S~%" actual)
						; (format t "Sucesores: ~S~%~%" sucesores)
					)
				)
			)
			(return nil)
		)
	)
)

(a-estrella)