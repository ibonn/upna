function [mejor_ind, fit_ind, medias_fitness] = viajante_genetico(distancias, num_individuos, prob_mut, generar_ind, seleccion_progenitores, op_cruzamiento, op_mutacion, max_iter, max_iter_igual, mostrar_grafico)
%ALGORITMO_GENETICO Summary of this function goes here
%   Detailed explanation goes here
    
    % Mostrar información de operadores usados, técnica de selección de
    % progenitores...
    disp('##############################################################');
    disp(['Número de individuos: ' num2str(num_individuos)]);
    disp(['Probabilidad de mutación: ' num2str(prob_mut)]);
    disp(['Método de generación de individuos: ' func2str(generar_ind)]);
    disp(['Método de selección de progenitores: ' func2str(seleccion_progenitores)]);
    disp(['Operador de cruzamiento: ' func2str(op_cruzamiento)]);
    disp(['Operador de mutación: ' func2str(op_mutacion)]);
    disp(['Límite de iteraciones: ' num2str(max_iter)]);
    disp(['Límite de iteraciones sin mejora: ' num2str(max_iter_igual)]);
    disp(['Mostrar grafico fitness en tiempo real: ' num2str(mostrar_grafico)]);
    disp('##############################################################');
    % Inicializar la población de forma aleatoria
    disp('Iniciando...');
    tic;
    disp('Generando población...');
    poblacion = generar_poblacion(num_individuos, distancias, generar_ind);

    disp(['Comenzado proceso iterativo. Máximo número de iteraciones: ' num2str(max_iter)]);
    % Calcular el fitness de la generación
    fitness_generacion = fitness(poblacion, distancias);
    % Mientras el fitness no sea suficientemente bueno y no se alcanze el
    % máximo de iteraciones
    iter = 0;
    iter_igual = 0;
    mejor_fit_ant = Inf;
    medias_fitness = [mean(fitness_generacion)];
    if mostrar_grafico
        hLine = plot(nan);
        title('Mejora del fitness');
        xlabel('Número de iteraciones');
        ylabel('Fitness promedio');
    end
    while iter < max_iter && iter_igual < max_iter_igual
        % Seleccionar progenitores
        [p1, p2] = seleccion_progenitores(poblacion, distancias);

        % Cruzarlos
        hijos = op_cruzamiento(p1, p2);

        % Mutar los hijos
        [~, n_hijos] = size(hijos);
        for i = 1:n_hijos
            hijos(:, i) = op_mutacion(hijos(:, i), prob_mut);
        end

        % Añadirlos a la nueva genercación
        poblacion = anadir_generacion(poblacion, hijos, distancias);

        % Calcular el fitness de la nueva generación
        fitness_generacion = fitness(poblacion, distancias);

        % Incrementar el contador de iteraciones
        iter = iter + 1;

        % Guardar la media para mostrarla mas tarde en la gráfica
        medias_fitness = [medias_fitness mean(fitness_generacion)];

        % Mostrar el gráfico del fitness en tiempo real
        if mostrar_grafico
            set(hLine, 'YData', medias_fitness);
            drawnow
        end

        % Obtener el fitness del mejor individuo
        [~, fit_ind] = mejor_individuo(poblacion, distancias);

        % Si el fitness del mejor individuo no cambia en un número determinado
        % de iteraciones parar
        if fit_ind < mejor_fit_ant
            mejor_fit_ant = fit_ind;
            iter_igual = 0;
        else
            iter_igual = iter_igual + 1;
        end;

        if mod(iter, 100) == 0
            disp([num2str(iter) ' iteraciones']);
            disp(['Mejor fitness: ' num2str(fit_ind)]);
        end
    end

    fin = toc;
    [mejor_ind, fit_ind] = mejor_individuo(poblacion, distancias);
    disp('##############################################################');
    disp(['finalizado en ' num2str(iter) ' iteraciones']);
    disp(['tiempo transcurrido: ' num2str(fin) ' segundos']);
    disp(['fitness: ' num2str(fit_ind)]);
    disp('Orden en el que se deben visitar las ciudades: ');
    disp(mejor_ind');
end

