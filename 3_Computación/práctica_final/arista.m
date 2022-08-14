function hijo = arista(individuo1, individuo2)

    % Cruzamiento por aristas
    % OBTENER LAS ADYACENCIAS
    [fil, ~] = size(individuo1);
    adyacencias = zeros(fil);

    % buscar los vértices adyacentes
    pos_vecinos = [-1; 1];
    i1e = [individuo1(end); individuo1; individuo1(1)];
    i2e = [individuo2(end); individuo2; individuo2(1)];

    for i = 1:fil
        w1 = find(individuo1 == i);
        w2 = find(individuo2 == i);

        mascara1 = pos_vecinos + w1(1) + 1;
        mascara2 = pos_vecinos + w2(1) + 1;

        vecinos1 = i1e(mascara1);
        vecinos2 = i2e(mascara2);

        % Contar las adyacencias
        adyacencias(i, vecinos1) = adyacencias(i, vecinos1) + 1;
        adyacencias(i, vecinos2) = adyacencias(i, vecinos2) + 1;

    end

    % Marcar los comunes con -1
    adyacencias(adyacencias == 2) = -1;

    % CRUZAMIENTO
    % Reservar espacio para el hijo
    hijo = zeros(fil, 1);

    % Seleccionar el primer elemento aleatoriamente
    elem = randi(fil);
    hijo(1) = elem;
    pos = 2;

    % Seleccionar el resto de elementos
    while pos <= fil

        % Eliminar todas las referencias al elemento seleccionado de la tabla
        adyacencias(:, elem) = 0;

        % Si hay alguna arista común, elegirla como siguiente elemento
        comun = adyacencias(elem, :) == -1;
        adyacencias(elem, :) = Inf;

        if any(comun)
            elem = find(comun);
        % Si no, escoger la entrada con la lista mas corta
        else
            longitudes_lista = sum(abs(adyacencias), 2);
            cortas = longitudes_lista == min(longitudes_lista);
            empates = sum(cortas);
            % Los empates se deshacen al azar
            if empates > 1
                empatados = find(cortas);
                elem = empatados(randi(numel(empatados)));  
            else
                elem = find(cortas);
            end
        end 
        elem = elem(1);
        hijo(pos) = elem;
        pos = pos + 1;
    end
end
