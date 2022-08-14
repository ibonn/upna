/*******************************************************************\
 *                                                                 *
 *                 Autor: Escartin Marcotegui, Ibon.               *
 *                                                                 *
 *******************************************************************
 *                                                                 *
 *                    Fecha: 20 de marzo de 2016.                  *
 *                                                                 *
 *******************************************************************
 *                                                                 *
 *      Descripción: Este algoritmo muestra tantas iteraciones     *
 *          de el juego de la vida como el usuario desee           *
 *           hasta que el número de células vivas sea 0            *
 *               o el usuario finalice la ejecución.               *
 *                                                                 *
\*******************************************************************/

#include <stdio.h>

#define DIM 50 //Dimensiones de la tabla: 50x50

typedef int tabla2D[DIM][DIM]; //Definimos el tipo tabla2D

//Accion InicializarTabla(e/s tabla:tabla2D);
//Descripción: Establece todos los valores de la tabla a 0.
//Requisitos: Ninguno
void InicializarTabla(tabla2D tabla) {
  int i, j;
  for (i = 0; i <= DIM; i++) {
    for (j = 0; j <= DIM; j++) {
      tabla[i][j] = 0;
    }
  }
  return;
}

//Accion CopiarTabla(e/s tabla1, tabla2:tabla2D);
//Descripción: Copia los valores de tabla1 en tabla2
//Requisitos: Ninguno
void CopiarTabla(tabla2D tabla1, tabla2D tabla2) {
  int i, j;
  for (i = 0; i <= DIM; i++) {
    for (j = 0; j <= DIM; j++) {
      tabla2[i][j] = tabla1[i][j];
    }
  }
  return;
}

//Accion MostrarTabla(ent tabla:tabla2D);
//Descripción: Representa en pantalla los valores de la tabla. En caso de que sea 0, imprime un espacio. En caso contrario, imprime una O (célula)
//Requisitos: Ninguno
void MostrarTabla(tabla2D tabla) {
  int i, j;
  for (i = 0; i <= DIM; i++) {
    for (j = 0; j <= DIM; j++) {
      if (tabla[i][j] == 0) {
        printf(" ");
      }
      else {
        printf("O");
      }
    }
    printf("\n");
  }
  return;
}

//Accion IntroducirPunto(ent x, y:entero; e/s tabla:tabla2D);
//Descripción: Introduce una célula en la tabla en las coordenadas (x, y). En caso de que x o y se salieran de los límites de la tabla, no se introduce ningún valor.
//Requisitos: 0 <= x <= DIM; 0 <= y <= DIM.
void IntroducirPunto(int x, int y, tabla2D tabla) {
  if (x <= DIM && x >= 0 && y <= DIM && y >= 0) {
    tabla[x][y] = 1;
  }
  return;
}

//Funcion SumarVecinos(ent x, y:entero; ent tabla:tabla2D) dev suma:entero;
//Descripción: Cuenta el número de células vivas alrededor de la célula situada en la coordenada (x, y)
//Requisitos: la coordenada (x, y) debe estar dentro de la tabla, en caso contrario, devuelve 0.
int SumarVecinos(int x, int y, tabla2D tabla) {
  int suma = 0;
  if (x <= DIM && x >= 0 && y <= DIM && y >= 0) {
    suma = tabla[x][y + 1] + tabla[x][y - 1] + tabla[x + 1][y] + tabla[x - 1][y] + tabla[x + 1][y + 1] + tabla[x - 1][y - 1] + tabla[x - 1][y + 1] + tabla[x + 1][y - 1];
  }
  return suma;
}

//Accion AlterarTabla(e/s tabla:tabla2D);
//Descripción: Modifica los valores de la tabla siguiendo las reglas de el juego de la vida.
//Requisitos: Ninguno
void AlterarTabla(tabla2D tabla) {
  int i, j, numvecinos;
  tabla2D tabla_aux;
  InicializarTabla(tabla_aux);
  for (i = 0; i <= DIM; i++) {
    for (j = 0; j <= DIM; j++) {
      numvecinos = SumarVecinos(i, j, tabla);
      if (tabla[i][j] == 1) {
        if (numvecinos < 2 || numvecinos > 3) {
          tabla_aux[i][j] = 0;
        }
        if (numvecinos == 2 || numvecinos == 3) {
          tabla_aux[i][j] = 1;
        }
      }
      else {
        if (numvecinos == 3) {
          tabla_aux[i][j] = 1;
        }
        else {
          tabla_aux[i][j] = 0;
        }
      }
    }
  }
  CopiarTabla(tabla_aux, tabla);
  return;
}

//Función CelulasVivas(ent tabla:tabla2D) dev suma:entero;
//Descripción: Cuenta el número de células vivas en la tabla
//Requisitos: Ninguno
int CelulasVivas(tabla2D tabla) {
  int i, j, suma;
  suma = 0;
  for (i = 0; i <= DIM; i++) {
    for (j = 0; j <= DIM; j++) {
      if (tabla[i][j] == 1) {
        suma++;
      }
    }
  }
  return suma;
}

int main(void) {
  char res;
  int x, y, vivas_ahora, vivas_antes, generacion;
  tabla2D tabla;
  
  printf("************************************************\n");
  printf("*                                              *\n");
  printf("*       Autor: Escartin Marcotegui, Ibon.      *\n");
  printf("*         Fecha: 20 de marzo de 2016.          *\n");
  printf("*                                              *\n");
  printf("************************************************\n");
  printf("*                                              *\n");
  printf("*             EL JUEGO DE LA VIDA              *\n");
  printf("*                                              *\n");
  printf("************************************************\n");
  printf("*                                              *\n");
  printf("*   Este algoritmo muestra tantas iteraciones  *\n");
  printf("* de el juego de la vida como el usuario desee *\n");
  printf("*  hasta que el número de células vivas sea 0. *\n");
  printf("*                                              *\n");
  printf("************************************************\n");
  printf("*                                              *\n");
  printf("*      Dimensiones de la tabla: %d x %d        *\n", DIM, DIM);
  printf("*                                              *\n");
  printf("************************************************\n\n");

  do {
  
    generacion = 0;
    InicializarTabla(tabla);
    
    do {
      printf("Escriba la posición (X, Y) donde quiera colocar una célula.\n");
      printf("X = ");
      scanf("%d", &x);
      printf("Y = ");
      scanf("%d", &y);
      IntroducirPunto(x, y, tabla);
      printf("¿Continuar introduciendo puntos? s/n\n");
      scanf(" %c", &res);
    } while (res == 's');

    do {
      MostrarTabla(tabla);
      vivas_antes = CelulasVivas(tabla);
      printf("**************************************************\n");
      printf("Hay %d células vivas.\n", vivas_antes);
      printf("Está viendo la generación nº %d.\n\n", generacion);
      printf("Pulse s para ver la siguiente generación.\n");
      printf("Pulse f para salir.\n");
      scanf(" %c", &res);
      AlterarTabla(tabla);
      vivas_ahora = CelulasVivas(tabla);
      generacion++;
    } while (res == 's' && vivas_ahora != 0);
    
    printf("El juego ha terminado.\n");
    printf("Desea volver a jugar? s/n.\n");
    scanf(" %c", &res);
  } while (res == 's');
  
  return 0;
}