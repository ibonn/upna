function city_position = leer_ruta(filename)
fid = fopen(filename, 'rt');
location = [];
A = [1 2];
tline = fgetl(fid);
while ischar(tline)
    if(strcmp(tline,'TOUR_SECTION'))
        while ~isempty(A)
            A = fscanf(fid,'%f',[1]);
            if isempty(A)
                break;
            end
            location = [location; A];
        end
    end
    tline = fgetl(fid); 
    if strcmp(tline, 'EOF')
        break;
    end
end
% Reemplazar el último elemento
location(end) = location(1);
city_position = location;
fclose(fid);
end
