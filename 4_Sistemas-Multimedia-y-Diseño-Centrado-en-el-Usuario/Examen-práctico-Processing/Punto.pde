class Punto {
  private float x, y;  // Coordenadas
  
  /*
  Crea un punto a partir de unas coordenadas
  */
  Punto(float x, float y) {
    this.x = x;
    this.y = y;
  }
  
  /*
  Obtiene la coordenada X
  */
  public float getX() {
    return x;
  }
  
  /*
  Obtiene la coordenada Y
  */
  public float getY() {
    return y;
  }
}
