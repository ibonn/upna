function poblacion = generar_poblacion(tamano, distancias, generar_individuo)
    
    [num_genes, ~] = size(distancias);

    poblacion = zeros(num_genes, tamano);
    
    for i = 1:tamano
        poblacion(:, i) = generar_individuo(distancias);
    end
end

