function individuo = individuo_proximidad(distancias)
    [num_genes, ~] = size(distancias);
    
    % Reservar espacio
    individuo = zeros(num_genes, 1);
    
    % Seleccionar una ciudad aleatoriamente
    ciudad = randi(num_genes);
    individuo(1) = ciudad;
    i = 2;
    
    % Seleccionar la ciudad a distancia mas cercana
    while i <= num_genes
        [~, ciudades_dist] = sort(distancias(ciudad, :));

        n = 1;
        while any(ciudades_dist(n) == individuo)
            n = n + 1;
        end

        ciudad = ciudades_dist(n);
        individuo(i) = ciudad;
        i = i + 1;
    end
end

