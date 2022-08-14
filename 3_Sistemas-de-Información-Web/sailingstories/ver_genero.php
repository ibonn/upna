<?php 
	// Cargar archivos necesarios
    include_once "include/sesion.php";	// Gestionar la sesión
    include_once "include/rastreador.php";
    
	include "vistas.php";	// Vistas

    if (isset($_GET["genero"])) {
        // Mostrar la página de inicio con las historias del género corrspondiente
        vista_inicio("Mostrando historias de tipo: " . $_GET["genero"], Historia::historias_genero($_GET["genero"], 10));
    }
    else {
        vista_mensaje("Error", "Falta el género", "Debe seleccionar un género de historias de la barra lateral izquierda para ver historias de ese género.");
    }
    
    // Guardar estadísticas
    Rastreador::guardar_estadisticas();
?>