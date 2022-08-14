%% Prueba ejemplo Rumanía
distancias = [
%    Ora Zer Ara Sib Fag Rim Tim Pit Cra Buc
     000 071 Inf 151 165 Inf Inf Inf Inf Inf;    % Oradea
     071 000 075 Inf Inf Inf Inf Inf Inf Inf;    % Zerind
     Inf 075 000 140 Inf Inf 118 Inf Inf Inf;    % Arad
     151 Inf 140 000 099 080 120 Inf Inf Inf;    % Sibiu
     165 Inf Inf 099 000 Inf Inf 120 Inf 211;    % Fagaras
     Inf Inf Inf 080 Inf 000 Inf 097 146 Inf;    % Rimnicu
     Inf Inf 118 120 Inf Inf 000 Inf 218 Inf;    % Timisoara
     Inf Inf Inf Inf 120 097 Inf 000 105 101;    % Pitesi
     Inf Inf Inf Inf Inf 146 218 105 000 140;    % Craiova
     Inf Inf Inf Inf 211 Inf Inf 101 140 000;    % Bucarest
 ];

distancias(distancias == Inf) = intmax('int16'); % Para evitar valores infinitos en fitness

% Ejecutar el algoritmo
[~, ~, ~] = viajante_genetico(distancias, 500, 0.5, @individuo_permutacion, @torneo, @arista, @sacudida, 10000, 1000, 0);

%% Prueba matriz de distancias aleatoria (método de selección)
distancias = generar_distancias(10);

% Ejecutar el algoritmo
[mejor_ind_ruleta, fit_ind_ruleta, medias_ruleta] = viajante_genetico(distancias, 500, 0.5, @individuo_permutacion, @ruleta, @arista, @sacudida, 10000, 1000, 0);
[mejor_ind_torneo, fit_ind_torneo, medias_torneo] = viajante_genetico(distancias, 500, 0.5, @individuo_permutacion, @torneo, @arista, @sacudida, 10000, 1000, 0);

% Mostrar los resultados
disp(['Mejor fitness (ruleta): ' num2str(fit_ind_ruleta)]);
disp(['Mejor camino (ruleta): ' num2str(mejor_ind_ruleta')]);
disp(['Mejor fitness (torneo): ' num2str(fit_ind_torneo)]);
disp(['Mejor camino (torneo): ' num2str(mejor_ind_torneo')]);

% Mostrar los gráficos
plot(medias_ruleta);
hold on;
plot(medias_torneo);
hold off;
xlabel('Número de iteraciones');
ylabel('Fitness promedio');
title('Variación del fitness a lo largo de la ejecución');
legend('Método de la ruleta', 'Método del torneo');

%% Prueba matriz de distancias aleatoria (operador de cruzamiento)

% Ejecutar el algoritmo
[mejor_ind_pmx, fit_ind_pmx, medias_pmx] =          viajante_genetico(distancias, 500, 0.5, @individuo_permutacion, @torneo, @pmx,    @sacudida, 10000, 1000, 0);
[mejor_ind_arista, fit_ind_arista, medias_arista] = viajante_genetico(distancias, 500, 0.5, @individuo_permutacion, @torneo, @arista, @sacudida, 10000, 1000, 0);

% Mostrar los resultados
disp(['Mejor fitness (pmx): ' num2str(fit_ind_pmx)]);
disp(['Mejor camino (pmx): ' mejor_ind_pmx']);
disp(['Mejor fitness (arista): ' num2str(fit_ind_arista)]);
disp(['Mejor camino (arista): ' mejor_ind_arista']);

% Mostrar los gráficos
plot(medias_pmx);
hold on;
plot(medias_arista);
hold off;
xlabel('Número de iteraciones');
ylabel('Fitness promedio');
title('Variación del fitness a lo largo de la ejecución');
legend('PMX', 'Cruzamiento por arista');

%% Prueba matriz de distancias aleatoria (operador de mutación)

% Ejecutar el algoritmo
[mejor_ind_intercambio, fit_ind_intercambio, medias_intercambio] = viajante_genetico(distancias, 500, 0.5, @individuo_permutacion, @torneo, @arista, @intercambio, 10000, 1000, 0);
[mejor_ind_sacudida, fit_ind_sacudida, medias_sacudida] =          viajante_genetico(distancias, 500, 0.5, @individuo_permutacion, @torneo, @arista, @sacudida, 10000, 1000, 0);

% Mostrar los resultados
disp(['Mejor fitness (intercambio): ' num2str(fit_ind_intercambio)]);
disp(['Mejor camino (intercambio): ' mejor_ind_intercambio']);
disp(['Mejor fitness (sacudida): ' num2str(fit_ind_sacudida)]);
disp(['Mejor camino (sacudida): ' mejor_ind_sacudida']);

% Mostrar los gráficos
plot(medias_intercambio);
hold on;
plot(medias_sacudida);
hold off;
xlabel('Número de iteraciones');
ylabel('Fitness promedio');
title('Variación del fitness a lo largo de la ejecución');
legend('Intercambio', 'Sacudida');

%% Pruebas cambiando tamaño de población

%distancias = generar_distancias(50); % Descomentar para la prueba de 50 ciudades

% Ejecutar el algoritmo
[mejor_ind1000, fit_ind1000, medias1000] = viajante_genetico(distancias, 1000, 0.5, @individuo_permutacion, @torneo, @arista, @sacudida, 10000, 1000, 0);
[mejor_ind500, fit_ind500, medias500] =    viajante_genetico(distancias,  500, 0.5, @individuo_permutacion, @torneo, @arista, @sacudida, 10000, 1000, 0);
[mejor_ind100, fit_ind100, medias100] =    viajante_genetico(distancias,  100, 0.5, @individuo_permutacion, @torneo, @arista, @sacudida, 10000, 1000, 0);

% Mostrar los resultados
disp(['Mejor fitness (1000 individuos): ' num2str(fit_ind1000)]);
disp(['Mejor camino (1000 individuos): ' mejor_ind1000']);
disp(['Mejor fitness (500 individuos): ' num2str(fit_ind500)]);
disp(['Mejor camino (500 individuos): ' mejor_ind500']);
disp(['Mejor fitness (100 individuos): ' num2str(fit_ind100)]);
disp(['Mejor camino (100 individuos): ' mejor_ind100']);

% Mostrar los gráficos
plot(medias1000);
hold on;
plot(medias500);
hold on;
plot(medias100);
hold off;
xlabel('Número de iteraciones');
ylabel('Fitness promedio');
title('Variación del fitness a lo largo de la ejecución');
legend('1000 individuos', '500 individuos', '100 individuos');

%% Prueba ejemplo real

% Cargar las coordenadas de las ciudades
[n_ciudades, posiciones] = leer('datos/gr96.tsp');

% Obtener la matriz de distancias a partir de las coordenadas
distancias = pos_a_dist(posiciones, n_ciudades);

% Ejecutar el algoritmo
% Descomentar para generar los individuos empleando las ciudades mas próximas
%[mejor_ind, fit_ind, medias] = viajante_genetico(distancias, 200, 0.5, @individuo_proximidad, @torneo, @arista, @sacudida, 10000, 1000, 0);
[mejor_ind, fit_ind, medias] = viajante_genetico(distancias, 200, 0.5, @individuo_permutacion, @torneo, @arista, @sacudida, 10000, 1000, 0);

% Una vez finalizada la ejecución mostrar el resultado
camino = [mejor_ind; mejor_ind(1)]; % Se regresa al punto de partida

% Leer el camino óptimo del fichero
camino_opt = leer_ruta('datos/gr96.opt.tour');

% Obtener las coordenadas de cada uno de los caminos
coordenadas_opt = posiciones(camino_opt, :);
coordenadas_camino = posiciones(camino, :);

% Mostrar grafico de fitness
figure;
plot(medias);
title('Mejora del fitness');
xlabel('Número de iteraciones');
ylabel('Fitness promedio');

% Mostrar el mapa
mostrar_mapa(coordenadas_camino, coordenadas_opt, posiciones, 'Mapa gr96');

% Mostrar el valor del fitness óptimo
fitness_optimo = fitness(camino_opt, distancias);
disp(['Fitness camino óptimo: ' num2str(fitness_optimo)]);