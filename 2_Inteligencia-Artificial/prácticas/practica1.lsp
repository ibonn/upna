; 1. Hacer una función LISP que rote una lista hacia la izquierda incluyendo
; por la derecha los datos que salen por la izquierda.
(defun rotar-izda (lista)
	(if (equal lista nil)
		'()
		(append (cdr lista) (list (car lista)))
	)
)
(print (rotar-izda '(a b c d)))

; 2. Programar una función que busca en una lista el primer elemento 
; numérico y devolverlo.
(defun primer-num (lista)
	(if (equal lista nil)
		'() 
		(if (numberp (car lista))
			(car lista)
			(primer-num (cdr lista))
		)
	)
)
(print (primer-num '(a b c f 6 u 5 4)))

; 3. Hacer una función que cambie todas las apariciones de un elemento de 
; una lista por otro. Supondremos que la lista tiene un único nivel, es 
; decir, no contiene sublistas. Utilizar recursividad.
(defun cambiar (a b lista)
	(if (equal nil lista)
		'()
		(if (equal a (car lista))
			(cons b (cambiar a b (cdr lista)))
			(cons (car lista) (cambiar a b (cdr lista)))
		)
	)
)
(print (cambiar 'a 'b '(1 2 3 4 a 6 7 8)))

; 4. Hacer  una  función  que  devuelva una lista  formada por todos los 
; elementos de la lista de entrada mayores que un elemento dado. Utilizar 
; recursividad. Realizar una segunda versión utilizando mapcar.
(defun lista-mayores (elem lista)
	(if (equal nil lista)
		'()
		(if (> (car lista) elem)
			(cons (car lista) (lista-mayores elem (cdr lista)))
			(lista-mayores elem (cdr lista))
		)
	)
)
(print (lista-mayores '4 '(7 2 3 6 1)))
(print (mapcar #'lista-mayores '(4 3 2) '((7 2 3 6 1) (1 5 8 3) (2 4 33))))

; 5. Hacer una función que inserte en una lista de números ordenada un 
; nuevo elemento en la posición que le corresponda.
(defun insertar (elem lista)
	(if (equal lista nil)
		'()
		(if (> elem (car lista))
			(cons (car lista) (insertar elem (cdr lista)))
			(cons elem lista)
		)
	)
)
(print (insertar '9 '(3 6 33 77 88 100)))
