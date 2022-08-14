function mutante = sacudida(individuo, probabilidad)

    mutante = individuo;

    % Mutación por sacudida
    prob = rand();  % Mutar con una probabilidad determinada
    
    if prob <= probabilidad
        % Generar dos posiciones aleatorias
        [n_genes, ~] = size(mutante);
        
        p1 = randi(n_genes);
        p2 = p1;
        while p2 == p1
            p2 = randi(n_genes);
        end
        [pos, ~] = sort([p1 p2]);
        
        % intercambiar las posiciones de todos los elementos en el
        % intervalo
        indices = randperm(pos(2) - pos(1) + 1);
        intervalo = mutante(pos(1):pos(2));
        mutante(pos(1):pos(2)) = intervalo(indices);
    end
end

