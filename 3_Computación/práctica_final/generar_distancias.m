function distancias = generar_distancias(num_ciudades)
    rand('seed', 1234567890);
    matD = ceil(rand(num_ciudades) * 100);
    a = triu(matD);
    b = a';
    distancias = a + b;
end

