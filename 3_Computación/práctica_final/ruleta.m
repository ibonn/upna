function [p1, p2] = ruleta(generacion, distancias)
    
    % Método de la ruleta
    n_progenitores = 2;
    [n_genes, ~] = size(generacion);
    progenitores = zeros(n_genes, n_progenitores);
    
    % Calcular el fitness
    fitness_generacion = fitness(generacion, distancias);
    
    probabilidades = fitness_generacion ./ sum(fitness_generacion);
    
    prob_acum = 1 - cumsum(probabilidades);
    
    % Crear los intervalos
    inf_intervalos = [prob_acum 1];
  
    for i = 1:n_progenitores
        % Generar un número aleatorio
        p = rand();

        % Seleccionar el intervalo en el que se encuentra
        pos = find(inf_intervalos < p);
        
        %
        
        pos = pos(1);

        % Seleccionar el progenitor correspondiente
        progenitores(:, i) = generacion(:, pos);
    end
    
    p1 = progenitores(:, 1);
    p2 = progenitores(:, 2);
end

