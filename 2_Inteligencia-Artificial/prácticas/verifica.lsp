(defun aplica (operador estado)
	(funcall (symbol-function operador) estado)
)

(defun verifica (plan &optional (estado *estado-inicial*))
	(cond 	((null estado) (format t "~&Movimiento no permitido~&") nil)
		((null plan) (cond ((es-estado-final estado) (format t "~&~a estado final~&" estado) t)
				    (t (format t "~&~a no es estado final~&" estado) nil)))
		 (t (format t "~&~a~a" estado (first plan))
			(verifica (rest plan) (aplica (first plan) estado)))))