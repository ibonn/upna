function individuo = individuo_permutacion(distancias)
    [num_genes, ~] = size(distancias);
    individuo = randperm(num_genes);
end

