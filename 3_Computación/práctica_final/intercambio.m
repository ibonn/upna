function mutante = intercambio(individuo, probabilidad)

    mutante = individuo;

    % Mutación por intercambio
    prob = rand();  % Mutar con una probabilidad determinada
    
    if prob <= probabilidad
        % Generar dos posiciones aleatorias
        [n_genes, ~] = size(mutante);
        
        p1 = randi(n_genes);
        p2 = randi(n_genes);
        
        % intercambiarlas
        aux = mutante(p1);
        mutante(p1) = mutante(p2);
        mutante(p2) = aux;
    end
end

