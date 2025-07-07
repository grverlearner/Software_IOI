#include <iostream>
#include <vector>
#include <string>
#include <iomanip>
#include <cmath>

using namespace std;

void ShowTableau(const vector<vector<double>> &A, const vector<int> &M, int m, int n) {
    int totalCols = n + m; // sin contar RHS
    vector<int> columnasUtiles;

    // Identificar columnas no triviales (ni canónicas ni ceros)
    for (int j = 0; j < totalCols; j++) {
        int unos = 0, ceros = 0;
        for (int i = 0; i <= m; i++) {  // incluye fila Z
            if (fabs(A[i][j]) < 1e-6) ceros++;
            else if (fabs(A[i][j] - 1.0) < 1e-6) unos++;
        }
        bool esCanonica = (unos == 1 && ceros == m);
        bool esCeroTotal = (ceros == m + 1); // todas las filas + Z son cero
        if (!esCanonica && !esCeroTotal)
            columnasUtiles.push_back(j);
    }

    // Mostrar encabezado
    cout << setw(8) << "Base";
    for (size_t idx = 0; idx < columnasUtiles.size(); ++idx) {
        int j = columnasUtiles[idx];
        if (idx < columnasUtiles.size() - 1) {
            string varName = (M[j] < n) ? "X" + to_string(M[j] + 1) : "S" + to_string(M[j] - n + 1);
            cout << setw(10) << varName;
        } else {
            // Última columna: dejar en blanco
            cout << setw(10) << "RHS\n";
        }
    }
    cout << "\n";

    // Mostrar filas
    for (int i = 0; i < m; i++) {
        string baseVar = (M[i + n] < n ? "X" : "S") + to_string(M[i + n] < n ? M[i + n] + 1 : M[i + n] - n + 1);
        cout << setw(8) << baseVar;
        for (int j : columnasUtiles)
            cout << setw(10) << fixed << setprecision(2) << A[i][j];
        cout << "\n";
    }

    // Fila Z
    cout << setw(8) << "Z";
    for (int j : columnasUtiles)
        cout << setw(10) << fixed << setprecision(2) << A[m][j];
    cout << "\n";
}

void Tablaeu(vector<vector<double>> &A, const vector<double> &Z, int m, int n) {
    cout << "\nIngresa restricciones (e.g. '1 2 3 <= 9'):\n";
    string line;
    
    for (int i = 0; i < m; i++) {
        getline(cin >> ws, line); // leer línea completa, ignorando espacios previos
        stringstream ss(line);

        for (int j = 0; j < n; j++) {
            if (!(ss >> A[i][j])) {
                cerr << "Error leyendo coeficiente de X" << (j+1) << " en restricción " << (i+1) << "\n";
                exit(1);
            }
        }

        string sign;
        double rhs;

        ss >> sign >> rhs;
        if (sign == ">=") {
            for (int j = 0; j <= n; j++) A[i][j] *= -1;
            A[i][n] = -rhs;
        } else if (sign == "<=" || sign == "=") {
            A[i][n] = rhs;
        } else {
            cerr << "Signo inválido en restricción: " << sign << "\n";
            exit(1);
        }
    }

    for (int i = 0; i < n; i++) A[m][i] = -Z[i];
    A[m][n] = 0;
}


void Operate(vector<vector<double>> &A, int m, int n, int pivotRow, int pivotCol) {
    double p = A[pivotRow][pivotCol];
    vector<vector<double>> B = A; // copia de respaldo

    A[pivotRow][pivotCol] = 1.0 / p;
    for (int i = 0; i <= m; i++)
        if (i != pivotRow) A[i][pivotCol] = -B[i][pivotCol] / p;
    for (int j = 0; j <= n; j++)
        if (j != pivotCol) A[pivotRow][j] = B[pivotRow][j] / p;

    for (int i = 0; i <= m; i++) {
        for (int j = 0; j <= n; j++) {
            if (i != pivotRow && j != pivotCol)
                A[i][j] = (B[pivotRow][pivotCol] * B[i][j] - B[pivotRow][j] * B[i][pivotCol]) / p;
        }
    }
}

double Primal(vector<vector<double>> &A, vector<int> &M, int m, int n, bool calcularIndice = false) {
    int col = -1, row;
    double minVal = 0.0;
    for (int j = 0; j < n; j++)
        if (A[m][j] < minVal) { minVal = A[m][j]; col = j; }

    double ratio, bestRatio = -1;
    for (int i = 0; i < m; i++) {
        if (A[i][col] > 0) {
            ratio = A[i][n] / A[i][col];
            if (bestRatio < 0 || ratio < bestRatio) {
                bestRatio = ratio;
                row = i;
            }
        }
    }
    if (calcularIndice) {
        double PI = abs(A[row][n] * A[m][col] / A[row][col]);
        cout << "PI=" << PI << "\n";
        return PI;
    }
    Operate(A, m, n, row, col);
    swap(M[row + n], M[col]);
    return 0.0;
}

double Dual(vector<vector<double>> &A, vector<int> &M, int m, int n, bool calcularIndice = false) {
    int row = -1, col;
    double minVal = 0.0;
    for (int i = 0; i < m; i++)
        if (A[i][n] < minVal) { minVal = A[i][n]; row = i; }

    double ratio, bestRatio = -1;
    for (int j = 0; j < n; j++) {
        if (A[row][j] < 0) {
            ratio = A[m][j] / A[row][j];
            if (bestRatio < 0 || ratio < bestRatio) {
                bestRatio = ratio;
                col = j;
            }
        }
    }
    if (calcularIndice) {
        double DI = abs(A[row][n] * A[m][col] / A[row][col]);
        cout << "DI=" << DI << "\n";
        return DI;
    }
    Operate(A, m, n, row, col);
    swap(M[row + n], M[col]);
    return 0.0;
}

void simplex(vector<vector<double>> &A, vector<int> &M, int m, int n) {
    while (true) {
        bool necesitaPrimal = false, necesitaDual = false;
        for (int i = 0; i < m; i++) if (A[i][n] < 0) necesitaDual = true;
        for (int j = 0; j < n; j++) if (A[m][j] < 0) necesitaPrimal = true;

        if (!necesitaDual && !necesitaPrimal) return;
        if (!necesitaDual) Primal(A, M, m, n);
        else if (!necesitaPrimal) Dual(A, M, m, n);
        else {
            double PI = Primal(A, M, m, n, true);
            double DI = Dual(A, M, m, n, true);
            if (PI >= DI) Primal(A, M, m, n);
            else Dual(A, M, m, n);
        }
    }
}

void Result(const vector<vector<double>> &A, const vector<int> &M, int m, int n) {
    int totalVars = n + m;
    vector<double> valores(totalVars, 0.0);

    for (int i = 0; i < m; i++) {
        int varIndex = M[i + n];
        if (varIndex < totalVars)
            valores[varIndex] = A[i][n];
    }

    cout << "\n--- SOLUCION ---\n";
    cout << "Valor optimo Z = " << A[m][n] << "\n\n";

    cout << "Solucion:\n";
    for (int i = 0; i < n; i++)
        cout << "X" << (i + 1) << " = " << valores[i] << "\n";
    for (int i = n; i < totalVars; i++)
        cout << "S" << (i - n + 1) << " = " << valores[i] << "\n";
    cout << "\n";
}

int main() {

    char valor;

    do{
    system ("cls");
    int m, n, tipo;
    cout<<"\t\tDual Simplex \n\n";
    cout<<"NOTA: si su ejercicio es Primal, entonces pasarlo a DUAL e ingresar valores.\n\n";
    cout << "Ingresa 0 para MAX, 1 para MIN: ";
    cin >> tipo;
    cout << "Ingresa numero de variables: ";
    cin >> n;
    cout << "Ingresa numero de restricciones: ";
    cin >> m;

    vector<double> Z(n);
    vector<vector<double>> A(m + 1, vector<double>(n + m + 1, 0.0));
    vector<int> M(n + m);
    for (int i = 0; i < n + m; i++) M[i] = i;

cout << "\nIngrese coeficientes de funcion objetivo (e.g. '1 2 3'):\n";
    for (int i = 0; i < n; i++) cin >> Z[i];
    if (tipo == 1)
        for (int i = 0; i < n; i++) Z[i] *= -1;

    Tablaeu(A, Z, m, n);
	
    simplex(A, M, m, n);
    if (tipo == 1) A[m][n] *= -1;

    cout << "\nTabla Final:\n";
    ShowTableau(A, M, m, n);
    cout << "\n";
    Result(A, M, m, n);
    cout<<"Deseas Ingresar otro ejercicio? (S/N) : "; cin>>valor;
    }while(valor=='S' || valor=='s');
    return 0;
}