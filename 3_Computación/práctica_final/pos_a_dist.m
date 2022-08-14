function dist = pos_a_dist(posiciones, num_ciudades)
    dist = zeros(num_ciudades);
    
    for i = 1:num_ciudades
        for j = 1:num_ciudades
            ciudad_i = posiciones(i, :);
            ciudad_j = posiciones(j, :);
            dif = ciudad_i - ciudad_j;
            cuad = dif .^2;
            s = sqrt(sum(cuad));
            
            dist(i, j)  = s;
        end
    end
end

