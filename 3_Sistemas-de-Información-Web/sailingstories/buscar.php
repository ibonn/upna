<?php
    include "vistas.php";

    include_once "include/rastreador.php";

    if (isset($_GET["consulta"])) {
        vista_inicio("Resultados para {$_GET["consulta"]}", Historia::buscar($_GET["consulta"]));
    }
    else {
        vista_mensaje("Error", "Busqueda incorrecta", "Para buscar historias es necesario realizar una consulta usando el buscador.");
    }

    Rastreador::guardar_estadisticas();
?>