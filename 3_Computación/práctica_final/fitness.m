function x = fitness(poblacion, distancias)

    [n_genes, n_cromosomas] = size(poblacion);
    
    % Reservar espacio para los valores de fitness de cada cromosoma
    x = zeros(1, n_cromosomas);
    
    % Preparar una máscara que se va a usar dentro del bucle
    mascara = logical(eye(n_genes));
    
    % Calcular el fitness para cada cromosoma
    for i = 1:n_cromosomas
        
        cromosoma = poblacion(:, i);
        
        % Calcular los movimientos para obtener los indices de la tabla de
        % distancias
        c1 = [0 cromosoma']';
        c2 = [cromosoma' cromosoma(1)]';    % se tiene en cuenta el regreso al punto de origen para calcular el fitness

        % Concatenar las columnas
        movimientos = [c1 c2];

        % Ignorar la primera y ultima fila porque no contienen índices válidos
        movimientos = movimientos(2:end, :);
        
        % Obtener la distancia de cada desplazamiento
        mat = distancias(movimientos(:, 1), movimientos(:, 2));
        
        % Sumar las distancias
        x(i) = sum(mat(mascara));
    end
end

