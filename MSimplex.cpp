#include <iostream>
#include <iomanip>
#include <string>
#include <vector>
using namespace std;

int main() {
    int nRestriccion, nVariable;
    string tipoP;

    system("cls");
    cout << "Ingrese 'MAX' o 'MIN': ";
    cin >> tipoP;

    cout << "Numero de restricciones: ";
    cin >> nRestriccion;

    cout << "Numero de variables: ";
    cin >> nVariable;

    int filas = nRestriccion + 1;
    int columnas = nVariable + nRestriccion + 1;

    vector<vector<float>> tabla(filas, vector<float>(columnas, 0));
    vector<string> nomVariables(nVariable + nRestriccion);
    vector<string> base(nRestriccion);

    for (int j = 0; j < nVariable; j++) {
        nomVariables[j] = "X" + to_string(j + 1);
    }
    for (int j = 0; j < nRestriccion; j++) {
        nomVariables[nVariable + j] = "S" + to_string(j + 1);
    }

    for (int i = 0; i < nRestriccion; i++) {
        cout << "\nRestriccion " << i + 1 << ":\n";
        for (int j = 0; j < nVariable; j++) {
            cout << "Coeficiente de X" << j + 1 << ": ";
            cin >> tabla[i][j];
        }

        string signo;
        cout << "Signo: ";
        cin >> signo;

        if(signo == "<="){
            tabla[i][nVariable + i] = 1;
        }else if(signo == ">="){
            tabla[i][nVariable + i] = -1;
        }

        cout << "RHS  : ";
        cin >> tabla[i][columnas - 1];

        base[i] = nomVariables[nVariable + i];
    }

    cout << "\nFuncion Objetivo:\n";
    for (int j = 0; j < nVariable; j++) {
        float coef;
        cout << "Coeficiente de X" << j + 1 << ": ";
        cin >> coef;
        tabla[filas - 1][j] = (tipoP == "MIN" || tipoP == "min") ? coef : -coef;
    }

    int iter = 0;

    while (true) {
        cout << "\n--- Iteracion " << iter++ << " ---\n";
        for (int i = 0; i < filas; i++) {
            if (i < nRestriccion)
                cout << setw(5) << base[i] << " | ";
            else
                cout << "   Z  | ";
            for (int j = 0; j < columnas; j++) {
                cout << setw(8) << fixed << setprecision(2) << tabla[i][j];
            }
            cout << "\n";
        }

        int colPivote = -1;
        float minVal = 0;
        for (int j = 0; j < columnas - 1; j++) {
            if (tabla[filas - 1][j] < minVal) {
                minVal = tabla[filas - 1][j];
                colPivote = j;
            }
        }

        if (colPivote == -1) break;

        int filaPivote = -1;
        float minRatio = 1e9;
        for (int i = 0; i < filas - 1; i++) {
            float val = tabla[i][colPivote];
            if (val > 0) {
                float ratio = tabla[i][columnas - 1] / val;
                if (ratio < minRatio) {
                    minRatio = ratio;
                    filaPivote = i;
                }
            }
        }

        if (filaPivote == -1) {
            cout << "Solución no acotada.\n";
            return 0;
        }

        float pivote = tabla[filaPivote][colPivote];
        for (int j = 0; j < columnas; j++) {
            tabla[filaPivote][j] /= pivote;
        }

        for (int i = 0; i < filas; i++) {
            if (i != filaPivote) {
                float factor = tabla[i][colPivote];
                for (int j = 0; j < columnas; j++) {
                    tabla[i][j] -= factor * tabla[filaPivote][j];
                }
            }
        }

        base[filaPivote] = nomVariables[colPivote];
    }

    cout << "\n--- SOLUCION FINAL ---\n";
    vector<float> valores(nVariable, 0.0);
    for (int i = 0; i < nRestriccion; i++) {
        for (int j = 0; j < nVariable; j++) {
            if (base[i] == nomVariables[j]) {
                valores[j] = tabla[i][columnas - 1];
            }
        }
    }

    for (int j = 0; j < nVariable; j++) {
        cout << "X" << j + 1 << " = " << valores[j] << "\n";
    }

    float Z = tabla[filas - 1][columnas - 1];
    if (tipoP == "MIN" || tipoP == "min")
        Z = -Z;
    cout << "Z = " << Z << "\n";
    return 0;
}