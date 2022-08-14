class Caja {
  
  private ArrayList<Pelota> pelotas;    // Lista de pelotas en la caja
  private int ancho, alto;              // Dimensiones de la caja
  
  /*
  Crea una caja dadas unas dimensiones
  */
  public Caja(int ancho, int alto) {
    this.ancho = ancho;
    this.alto = alto;
    pelotas = new ArrayList();
  }
  
  /*
  Añade una nueva pelota a la caja
  */
  public void addPelota(Pelota p) {
    pelotas.add(p);
  }
  
  /*
  Incrementa el tiempo transcurrido el número de unidades que se le pase
  */
  public void incrementaTiempo(int unidades) {
    
    // Actualizar las posiciones de cada pelota
    for (Pelota p : pelotas) {
      p.actualizarPosicion(unidades);
      // Si la pelota choca contra los bordes de la caja, hacerla rebotar
      if (p.getX() >= ancho || p.getX() <= 0) {
        p.invertirX();
      }
      
      if (p.getY() >= alto || p.getY() <= 0) {
        p.invertirY();
      }
    }
  }
  
  /*
  Muestra la caja con todo su contenido
  */
  public void mostrar() {
    // Colorear el fondo de negro
    background(0);
    
    // Colorear cada pelota de blanco y hacerlas un poco mas gruesas
    stroke(255, 255, 255);
    strokeWeight(5);
    
    // Dibujar cada una de las pelotas
    for (Pelota p : pelotas) {
      point(p.getX(), p.getY());
    }
  }
}
