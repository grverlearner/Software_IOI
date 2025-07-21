<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>
<div>
<!-- <link rel="stylesheet" href="style.css"> -->
<?php
function mostrarMatriz($matriz) {
    echo "<table border='1px'";
    foreach ($matriz as $fila) {
        echo "<tr>";
        foreach ($fila as $valor) {
            echo "<td>$valor</td>";
        }
        echo "</tr>";
    }
    echo "</table><br>";
}

class MetodoHungaro {
    private $matrizCostos;
    private $tamaño;
    private $asignaciones;

    public function __construct($matriz) {
        $this->matrizCostos = $matriz;
        $this->tamaño = count($matriz);
        $this->asignaciones = array_fill(0, $this->tamaño, -1);
    }

    public function resolver() {
        echo "<h3>Matriz Original</h3>";
        mostrarMatriz($this->matrizCostos);

        echo "<h3>Matriz reduccion por filas</h3>";
        $this->reducirFilas();
        mostrarMatriz($this->matrizCostos);

        echo "<h3>Matriz reduccion por columnas</h3>";
        $this->reducirColumnas();
        mostrarMatriz($this->matrizCostos);

        $this->asignarTareas();
        return $this->asignaciones;
    }

    private function reducirFilas() {
        for ($i = 0; $i < $this->tamaño; $i++) {
            $min = min($this->matrizCostos[$i]);
            for ($j = 0; $j < $this->tamaño; $j++) {
                $this->matrizCostos[$i][$j] -= $min;
            }
        }
    }

    private function reducirColumnas() {
        for ($j = 0; $j < $this->tamaño; $j++) {
            $min = INF;
            for ($i = 0; $i < $this->tamaño; $i++) {
                if ($this->matrizCostos[$i][$j] < $min) {
                    $min = $this->matrizCostos[$i][$j];
                }
            }
            for ($i = 0; $i < $this->tamaño; $i++) {
                $this->matrizCostos[$i][$j] -= $min;
            }
        }
    }

    private function asignarTareas() {
        $filasMarcadas = array_fill(0, $this->tamaño, false);
        $columnasMarcadas = array_fill(0, $this->tamaño, false);

        for ($i = 0; $i < $this->tamaño; $i++) {
            for ($j = 0; $j < $this->tamaño; $j++) {
                if ($this->matrizCostos[$i][$j] == 0 && !$filasMarcadas[$i] && !$columnasMarcadas[$j]) {
                    $this->asignaciones[$i] = $j;
                    $filasMarcadas[$i] = true;
                    $columnasMarcadas[$j] = true;
                    break;
                }
            }
        }
    }
}

// Procesamiento
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['resolver'])) {
    $matriz = $_POST['matriz'];
    $tamaño = intval($_POST['tamaño']);

    $hungaro = new MetodoHungaro($matriz);
    $asignaciones = $hungaro->resolver();

    echo "<h3>Asignaciones:</h3><ul>";
    $total = 0;
    for ($i = 0; $i < $tamaño; $i++) {
        $j = $asignaciones[$i];
        if ($j !== -1) {
            $costo = $matriz[$i][$j];
            echo "<li>Trabajador " . ($i + 1) . " → Tarea " . ($j + 1) . " (Costo: $costo)</li>";
            $total += $costo;
        } else {
            echo "<li>Trabajador " . ($i + 1) . " no fue asignado a ninguna tarea.</li>";
        }
    }
    echo "</ul>";
    echo "<strong>Costo total mínimo: $total</strong>";
}
?>

</div>
<?php
require_once '../Inicio/footer.php';
?>

