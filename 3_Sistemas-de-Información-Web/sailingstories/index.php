<?php 
	// Cargar archivos necesarios
	include_once "include/sesion.php";	// Gestionar la sesión
	include_once "include/rastreador.php";

	include "vistas.php";	// Vistas

	// Mostrar la página de inicio
	vista_inicio("¡Bienvenid@ a Sailing Stories!", Historia::top_historias(10));

	// Guardar estadísticas
	Rastreador::guardar_estadisticas();
?>