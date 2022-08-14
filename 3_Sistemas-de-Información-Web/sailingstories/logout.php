<?php
    include "include/sesion.php";

    // Destruir la sesión
    session_destroy();

    // Redirigir a la página de inicio
    header("Location: index.php");
?>