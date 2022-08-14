$(document).ready(function() {
    // Oculta el visor grande cuando el documento se carga
    $('#navegador_grande').hide();
    $('#bloquear_fondo').hide();

    $("#selector_genero").change(function() {
        $.post("nueva_historia.php", {genero: $('#selector_genero :selected').text()}, function(data) {
            if (data != null) {
                tinymce.activeEditor.setContent(data);
            }
        });
    });
    
    // Iniciar las cuentas atrás en las votaciones
    setInterval(function() {

        var tarjetaVotacion = $(".tarjeta_votacion");
        var barra = tarjetaVotacion.find(".progress-bar");
        var texto = tarjetaVotacion.find(".cuenta_atras");

        // Obtener el valor
        var segundos = barra.attr("data-segundos");

        // Calcular el procentaje y el ancho
        var porcentaje = segundos / 604800;
        var w = porcentaje * barra.parent().width();

        // Calcular los valores para mostrarlos
        fSegundos = segundos % 60;
        minutos = (segundos - fSegundos) / 60;
        fMinutos = minutos % 60;
        horas = (minutos - fMinutos) / 60;
        fHoras = horas % 24;
        dias = (horas - fHoras) / 24;

        // Actualizar los valores
        segundos--;
        barra.attr("data-segundos", segundos);
        barra.attr("aria-valuenow", porcentaje * 100);
        texto.text(dias.toString().padStart(2, '0') + " dias " + fHoras.toString().padStart(2, '0') + " horas " + fMinutos.toString().padStart(2, '0') + " minutos " + fSegundos.toString().padStart(2, '0') + " segundos");

        // Ocultar la tarjeta si ha finalizado la votación
        if (segundos <= 0) {
            // Cerrar votación
            tarjetaVotacion.hide(function() {
                tarjetaVotacion.remove();
            });
        }
        barra.animate({ width: w }, 500);
    }, 1000);
});