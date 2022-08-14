; Crear una función el Lisp que dado una lista, que puede contener listas, y un elemento, 
; busque  el  elemento  recursivamente  y  si  lo  encuentra  escribe  en  pantalla  el  nivel  de 
; anidamiento de la sublista en la que se encuentra, si no lo encuentra escribe un cero.

(defun busca-rec (elem lista profundidad)
	(if (equal lista nil)
		0
		(if (listp (car lista))
			(if (equal (busca-rec elem (car lista) (+ profundidad 1)) 0)
				(if (equal elem (car lista))
					profundidad
					(busca-rec elem (cdr lista) profundidad)
				)
				(busca-rec elem (car lista) (+ profundidad 1))
			)	
		)
	)
)

;(print (busca-rec 9 '(2 f h (j k (j u 9) j u y) g j u) 1))
(print (busca-rec 5 '((1 2) 5) 1))

; Realizar  una  función  el  Lisp  que  dado  una  lista,  que  puede  contener  listas,  y  un 
; elemento,  busque  el  elemento  recursivamente  y  si  lo  encuentra  escribe  en  pantalla 
; “Elemento  X  esta  en  el  nivel  Y”  siendo  X  el  elemento  buscado  e  Y  el  nivel  de 
; imbricación de la sublista en la que se encuentra, si no lo encuentra escribe 
; “Elemento no encontrado”. Utilizar busca-rec.

(defun obtener-nivel (elem lista)
	(if (equal lista nil)
		0
		(progn
			(setf a (busca-rec elem lista 1))
			(if (equal a 0)
				(print "Elemento no encontrado")
				(print (concatenate 'string "El elemento " elem "esta en el nivel " a))
			)
		)	
	)
)

;(print (obtener-nivel '9 '(2 f h (j k (j u 9) j u y) g j u)))

; Dada  una  lista  que  puede  contener  número,  strings  y  símbolos  anidados  a  cualquier 
; nivel,  por  ejemplo  ‘(hola  (“mundo”  5)),  construye  dos  versiones  de  la  función
; (sustituye-tipo  lista)  para  que  devuelva  (symbol  (string  number)).  La  primera  versión 
; será   puramente   recursiva   y   la   segunda   utilizando   mapcar,   funciones   lambda   y 
; recursividad.



; Sea  L una lista que representa  a una matriz de números de dimensión MxN, construye
; dos  versiones  de  la  función  (suma-matriz  L)  para  que  devuelva  la  suma  de  todos  los 
; elementos de la matriz representada por L. La primera versión es puramente recursiva y 
; la segunda utilizando apply, mapcar, funciones lambda y recursividad.


