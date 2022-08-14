function nueva_poblacion = anadir_generacion(poblacion, hijo1, distancias)
    % Reemplazamiento basado en fitness
    nueva_poblacion = [poblacion, hijo1];
    
    % Calcular el fitness
    fitness_generacion = fitness(nueva_poblacion, distancias);
    [~, idx] = sort(fitness_generacion);
    nueva_poblacion = nueva_poblacion(:, idx);
    
    % Eliminar los peores
    nueva_poblacion(:, end) = [];
end

