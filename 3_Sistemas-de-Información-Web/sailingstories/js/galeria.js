$(document).ready(function() {

    // Función para centrar el visor de imagenes grande
    jQuery.fn.center = function () {
        this.css("position","absolute");
        this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + 
                                                    $(window).scrollTop()) + "px");
        this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + 
                                                    $(window).scrollLeft()) + "px");
        return this;
    }

    // Obtenemos la lista de fotos pequeñas
    var fotosPeq = $('#navegador_peq div').children('img');

    // Guardamos la posición de la foto que se está viendo en el visor grande
    var indice = 0;

    // Carga la primera imagen en el visor mediano
    if (fotosPeq.length > 0) {
    
        $('#visor_mediano').attr('src', 'uploads/' + fotosPeq[0].attributes['data-nombre'].nodeValue + '_med.jpg');
        $('#visor_mediano').attr('data-nombre', fotosPeq[0].attributes['data-nombre'].nodeValue);
        $('#visor_mediano').attr('alt', fotosPeq[0].attributes['alt'].nodeValue);
        $('#navegador_peq p').text(fotosPeq[0].attributes['alt'].nodeValue);

        // Marcamos la primera imagen como seleccionada
        $('#navegador_peq div img:first').addClass('seleccionado');

        // Carga la anterior foto en el visor grande
        $('#flecha_ant').click(function() {
            if (--indice < 0) {
                indice = fotosPeq.length - 1;
            }
            $('#visor_grande').attr('src', 'uploads/' + fotosPeq[indice].attributes['data-nombre'].nodeValue + '.jpg');
            $('#visor_grande').attr('alt', fotosPeq[indice].attributes['alt'].nodeValue);
            $('#navegador_grande div').text(fotosPeq[indice].attributes['alt'].nodeValue);
        });

        // Carga la siguiente foto en el visor grande
        $('#flecha_sig').click(function() {
            if (++indice >= fotosPeq.length) {
                indice = 0;
            }
            $('#visor_grande').attr('src', 'uploads/' + fotosPeq[indice].attributes['data-nombre'].nodeValue + '.jpg');
            $('#visor_grande').attr('alt', fotosPeq[indice].attributes['alt'].nodeValue);
            $('#navegador_grande div').text(fotosPeq[indice].attributes['alt'].nodeValue);
        });

        // Oculta el visor grande cuando se pincha fuera
        $(document).mouseup(function(e) {
            if ($(e.target).closest('#navegador_grande').length === 0) { 
                $('#navegador_grande').hide();
                $('#bloquear_fondo').hide();
                $('html, body').css({
                    overflow: 'auto',
                    height: 'auto'
                });
            }
        });

        // Carga la imagen de la lista al visor mediano
        $('img.peq').click(function() {
            $('#visor_mediano').attr('src', 'uploads/' + $(this).attr('data-nombre') + '_med.jpg');
            $('#visor_mediano').attr('data-nombre', $(this).attr('data-nombre'));
            $('#visor_mediano').attr('alt', $(this).attr('alt'));
            
            $('img.peq').removeClass('seleccionado');
            $(this).addClass('seleccionado');

            $('#navegador_peq p').text($(this).attr('alt'));

            indice = $(this).attr('data-indice');
        });

        // Carga la imagen del visor mediano en el visor grande
        $('img.med').click(function() {

            // Centrar el visor grande
            
            $('#visor_grande').attr('src', 'uploads/' + $(this).attr('data-nombre') + '.jpg');
            $('#visor_grande').attr('alt', $(this).attr('alt'));

            $('#navegador_grande div').text($(this).attr('alt'));

            $('#navegador_grande').show(function() {
                $('#navegador_grande').center();
            });
            
            $('#bloquear_fondo').show();

            $('html, body').css({
                overflow: 'hidden',
                height: '100%'
            });
        });
    }
});