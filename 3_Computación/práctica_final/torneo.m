function [p1, p2] = torneo(generacion, distancias)
    
    % Método del torneo
    k = 20;
    n_progenitores = 2;
    
    [n_genes, n_individuos] = size(generacion);
   
    progenitores = zeros(n_genes, n_progenitores);
    
    for i = 1:n_progenitores
        torneo = zeros(n_genes, k);

        % Seleccionar k elementos al azar
        for j = 1:k
            torneo(:, j) = generacion(:, randi(n_individuos));
        end

        fitness_torneo = fitness(torneo, distancias);

        % Ordenar en función del fitness
        [~, idx] = sort(fitness_torneo);
        torneo = torneo(:, idx);

        % Seleccionar los dos mejores
        progenitores(:, i) = torneo(:, 1);
    end
    
    p1 = progenitores(:, 1);
    p2 = progenitores(:, 2);
end

