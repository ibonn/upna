function [mejor, mejor_fitness] = mejor_individuo(individuos, distancias)
    fitness_individuos = fitness(individuos, distancias);
    [fitness_ord, idx] = sort(fitness_individuos);
    
    individuos = individuos(:, idx);
    
    mejor = individuos(:, 1);
    mejor_fitness = fitness_ord(1);
end

