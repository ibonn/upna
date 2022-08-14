class Vector {

  private float r;      // Longitud del vector
  private float theta;  // Ángulo en radianes
  
  /*
  Vector a partir de coordenadas polares
  */
  Vector(float r, float theta) {
    this.r = r;
    this.theta = PI * theta / 180;
  }
  
  /*
  Vector a partir de coordenadas cartesianas
  */
  Vector(Punto pt) {
    float x = pt.getX();
    float y = pt.getY();
    
    // Calcular el módulo y el ángulo
    this.r = sqrt(pow(x, 2) + pow(y, 2));
    this.theta = atan2(y, x);
  }
  
  /*
    Módulo del vector
  */
  public float modulo() {
    return r;
  }
  
  /*
  Obtiene la coordenada horizontal cartesiana del vector
  */
  public float getX() {
    return r * cos(theta);
  }
  
  /*
  Obtiene la coordenada vertical cartesiana del vector
  */
  public float getY() {
    return r * sin(theta);
  }
  
  /*
  Obtiene el ángulo en grados
  */
  public float anguloGrad() {
    return theta * 180 / PI;
  }
}
