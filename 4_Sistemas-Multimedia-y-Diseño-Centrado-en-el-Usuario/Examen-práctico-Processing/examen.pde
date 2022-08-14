Caja caja;          // Caja donde rebotan las pelotas

void setup() {
  size(500, 500);   
  frameRate(30);
  caja = new Caja(width, height);
}

void draw() {
  // Cada iteracion se incrementa el tiempo para actualizar las posiciones de las pelotas
  // y se dibuja el contenido de la caja
  caja.incrementaTiempo(1);
  caja.mostrar();
}

void mouseClicked() {
  // Añadir 3 pelotas con direcciones aleatorias
  Vector v1 = new Vector(4,  random(0, 360));
  Vector v2 = new Vector(4,  random(0, 360));
  Vector v3 = new Vector(4,  random(0, 360));
  
  caja.addPelota(new Pelota(mouseX, mouseY, v1));
  caja.addPelota(new Pelota(mouseX, mouseY, v2));
  caja.addPelota(new Pelota(mouseX, mouseY, v3));
}
