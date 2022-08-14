<?php

    include_once "modelos/usuario.php";	// Carga el modelo usuario

    session_name("sailingstories");		// Nombre de la sesión
    session_start();

    // Iniciar sesión como un usuario anónimo por defecto
    if (!isset($_SESSION["usuario"]))
        $_SESSION["usuario"] = new Usuario();
?>