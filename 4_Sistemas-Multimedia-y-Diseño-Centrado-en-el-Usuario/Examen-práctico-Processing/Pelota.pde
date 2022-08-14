class Pelota {
  
  private Vector velocidad;  // Velocidad de la pelota
  private float x, y;        // Posición dentro de la caja
  
  /*
  Crea una pelota en las coordenadas especificadas con una velocidad incial dada
  */
  public Pelota(float x, float y, Vector velocidad) {
    this.x = x;
    this.y = y;
    this.velocidad = velocidad;
  }
  
  /*
  Obtiene la coordenada X de la posición de la pelota
  */
  public float getX() {
    return x;
  }
  
  /*
  Obtiene la coordenada Y de la posición de la pelota
  */
  public float getY() {
    return y;
  }
  
  /*
  Actualiza la posición de la pelota en función del tiempo transcurrido
  */
  public void actualizarPosicion(int t) {
    x = x + velocidad.getX() * t;
    y = y + velocidad.getY() * t;
  }
  
  /*
  Invierte el sentido de la velocidad en el eje X
  */
  public void invertirX() {
    float x = -this.velocidad.getX();
    float y = this.velocidad.getY();
    this.velocidad = new Vector(new Punto(x, y));
  }
  
  /*
  Invierte el sentido de la velocidad en el eje Y
  */
  public void invertirY() {
    float x = this.velocidad.getX();
    float y = -this.velocidad.getY();
    this.velocidad = new Vector(new Punto(x, y));
  }
  
}
