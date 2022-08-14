<?php
    include "include/fpdf.php";
    include "include/rastreador.php";

    include "modelos/historia.php";

    // Si se ha solicitado una historia, buscarla
    if (isset($_GET["id"])) {

        $historia = new Historia($_GET["id"]);

        // Si la historia existe se crea el documento. En caso contrario se muestra un a página en blanco
        if ($historia->existe) {

            // Crear un nuevo documento
            $pdf = new FPDF();

            $pdf->AddPage();

            // Codificar el texto. En la base de datos etá ne UTF-8. Si no se hace esto, las tildes y ñ no se ven correctamente en el pdf
            $titulo = iconv('UTF-8', 'windows-1252', $historia->get_titulo());

            // Añadir título y autor al documento
            $pdf->SetTitle($historia->get_titulo(), true);
            $pdf->SetAuthor("Sailing Stories");

            // Añadir el título
            $pdf->SetFont("Arial", "B", 20);
            $pdf->Cell(0, 10, $titulo, 0, 0, "C");
            $pdf->ln();

            // Añadir la imagen principal
            $imagen = $historia->get_imagen_principal();
            if ($imagen != null)
                $pdf->Image('uploads/' . $imagen->get_med());

            // Añadir los capítulos
            foreach ($historia->get_capitulos() as $capitulo) {

                // Codificar como antes
                $titulo_cap = iconv('UTF-8', 'windows-1252', $capitulo->get_titulo());
                $autor = iconv('UTF-8', 'windows-1252', $capitulo->get_autor()->get_id());
                $texto = iconv('UTF-8', 'windows-1252', $capitulo->get_texto());

                $pdf->SetFont("Arial", "BI", 16);
                $pdf->Cell(0, 10, $titulo_cap);
                $pdf->ln();

                $pdf->SetFont("Arial", "I", 12);
                $pdf->Cell(0, 10, "Escrito por " . $autor);
                $pdf->ln();
                
                $pdf->SetFont("Arial", "", 12);
                $pdf->MultiCell(0, 10, strip_tags($texto));
                $pdf->ln();
            }

            // Iniciar la descarga
            $pdf->Output("D", $titulo . ".pdf");
        }
    }

    // Guardar estadísiticas
    Rastreador::guardar_estadisticas();
?>