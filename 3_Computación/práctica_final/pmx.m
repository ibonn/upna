function [hijo1, hijo2] = pmx(individuo1, individuo2)
    
    % Cruzamiento parcialmente mapeado
    % Obtener el número de genes
    [n_genes, ~] = size(individuo1);
    
    % Seleccionar las posiciones
    p1 = randi(n_genes);
    p2 = randi(n_genes);
    
    % Crear los hijos
    hijo1 = zeros(n_genes, 1);
    hijo2 = zeros(n_genes, 1);

    % Obtener los segmentos y copiarlos a los hijos
    hijo1(p1:p2) = individuo1(p1:p2);
    hijo2(p1:p2) = individuo2(p1:p2);
    
    % Obtener mapeos
    mapeo = zeros(n_genes, 2);
    for i = 1:n_genes
        en_segmento1 = i == hijo1;
        en_segmento2 = i == hijo2;
        if any(en_segmento1)
            mapeo(i, 1) = hijo2(en_segmento1);
        end
        if any(en_segmento2)
            mapeo(i, 2) = hijo1(en_segmento2);
        end
    end
    
    % Comprobar si se ha copiado el elemento
    for i = p1:n_genes
        p = individuo1(i) == hijo2;
        if sum(p) == 0
            p = individuo1(i);
            while mapeo(p, 1) ~= 0
                p = mapeo(p, 1);
            end
            hijo2(individuo1 == p) = individuo1(i);
        end
        
        p = individuo2(i) == hijo1;
        if sum(p) == 0
            p = individuo2(i);
            while mapeo(p, 2) ~= 0
                p = mapeo(p, 2);
            end
            hijo1(individuo2 == p) = individuo2(i);
        end
    end
    
    % Copiar el resto de elementos
    m1 = hijo1 == 0;
    m2 = hijo2 == 0;
    
    hijo1(m1) = individuo2(m1);
    hijo2(m2) = individuo1(m2);
    
end

