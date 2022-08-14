function x = mostrar_mapa(coordenadas_camino, coordenadas_opt, coordenadas_ciudades, titulo)
    figure;
    % Dibujar camino óptimo
    plot(coordenadas_opt(:, 2), coordenadas_opt(:, 1), 'LineWidth', 5);
    hold on;
    % Dibujar camino encontrado
    plot(coordenadas_camino(:, 2), coordenadas_camino(:, 1));
    % Dibujar ciudades
    scatter(coordenadas_ciudades(:, 2), coordenadas_ciudades(:, 1), '.');
    hold off;


    % Establecer título y leyenda
    title(titulo);
    legend('Camino óptimo', 'Camino encontrado', 'Ciudades');
end

