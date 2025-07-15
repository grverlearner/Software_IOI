<?php
	session_start();
	require_once '../inicio/header.php';
	require_once '../Inicio/sidebar.php';
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$tipo = $_POST['tipo'] ?? 'max';
        $cantRest = (int)($_POST['cantRest'] ?? 0);
        $cantVar = (int)($_POST['cantVar'] ?? 0);
        $variZeta = $_POST['variZeta'] ?? [];
        $variables = $_POST['variables'] ?? [];
        $signo = $_POST['condicion'] ?? [];
        $RHS = $_POST['resul'] ?? [];

        $_SESSION['tipo'] = $tipo;
        $_SESSION['cantRest'] = $cantRest;
        $_SESSION['cantVar'] = $cantVar;
        $_SESSION['variZeta'] = $variZeta;
        $_SESSION['variables'] = $variables;
        $_SESSION['signo'] = $signo;
        $_SESSION['RHS'] = $RHS;
    }
?>

<div class="titulo">
     <h1>Método Simplex</h1>
</div>

	<?php
        for ($i = 0; $i < $cantRest; $i++) {
            for ($j = 0; $j < $cantVar; $j++) {
                $variables[$i][$j] = floatval($variables[$i][$j] ?? 0);
            }
            $RHS[$i] = floatval($RHS[$i] ?? 0);
        }

        for ($i = 0; $i < $cantVar; $i++) {
            $variZeta[$i] = floatval($variZeta[$i] ?? 0);
        }

        $filas = $cantRest + 1;
        $columnas = $cantVar + $cantRest + 1;
        $tabla = array_fill(0, $filas, array_fill(0, $columnas, 0));
        $nombres = [];
        $base = [];

        for ($i = 0; $i < $cantVar; $i++) {
            $nombres[] = "X" . ($i + 1);
        }
        for ($i = 0; $i < $cantRest; $i++) {
            $nombres[] = "S" . ($i + 1);
            $base[$i] = "S" . ($i + 1);
        }

        for ($i = 0; $i < $cantRest; $i++) {
			for ($j = 0; $j < $cantVar; $j++) {
				$tabla[$i][$j] = $variables[$i][$j];
			}

			// Agregar variable de holgura según el signo de la restricción
			if ($signo[$i] === "<=") {
				$tabla[$i][$cantVar + $i] = 1;
			} elseif ($signo[$i] === ">=") {
				$tabla[$i][$cantVar + $i] = -1;
			} // Puedes agregar manejo de "=" si lo deseas

			$tabla[$i][$columnas - 1] = $RHS[$i];
		}
        for ($j = 0; $j < $cantVar; $j++) {
    if (strtolower($tipo) === 'min') {
        $tabla[$filas - 1][$j] = $variZeta[$j];  // coeficientes positivos para minimización
    } else {
        $tabla[$filas - 1][$j] = -$variZeta[$j]; // coeficientes negativos para maximización
    }
}

        $iter = 0;
        $filaPivote = -1;
        $colPivote = -1;
        do {
			echo '<div class="contResultado">';
			echo '<div class="subtitulo"><p>Iteración ' . $iter . '</p></div>';
            echo "<table cellpadding='5' cellspacing='1'>";
            echo "<tr><th>Base</th>";
   
            for ($j = 0; $j < $cantVar; $j++) {
                echo "<th>X" . ($j + 1) . "</th>";
            }
            for ($j = 0; $j < $cantRest; $j++) {
                echo "<th>S" . ($j + 1) . "</th>";
            }
            echo "<th>RHS</th></tr>";

            for ($i = 0; $i < $filas; $i++) {
                echo "<tr>";
                echo "<th>" . (($i < $cantRest) ? $base[$i] : "Z") . "</th>";
                for ($j = 0; $j < $columnas; $j++) {
                    $clase = '';
                    if ($filaPivote !== -1 && $colPivote !== -1) {
                        if ($i === $filaPivote && $j === $colPivote) {
                            $clase = 'sombreadoFull';
                        } elseif ($i === $filaPivote || $j === $colPivote) {
                            $clase = 'sombreado';
                        }
                    }
                    echo "<td class='$clase'>" . number_format($tabla[$i][$j], 2) . "</td>";
                }
                echo "</tr>";
            }
			
            echo "</table>";

            $colPivote = -1;
            $minVal = 0;
            for ($j = 0; $j < $columnas - 1; $j++) {
                if ($tabla[$filas - 1][$j] < $minVal) {
                    $minVal = $tabla[$filas - 1][$j];
                    $colPivote = $j;
                }
            }

            if ($colPivote == -1) break; // Óptimo

            $filaPivote = -1;
            $minRatio = INF;
            for ($i = 0; $i < $cantRest; $i++) {
                $val = $tabla[$i][$colPivote];
                if ($val > 0) {
                    $ratio = $tabla[$i][$columnas - 1] / $val;
                    if ($ratio < $minRatio) {
                        $minRatio = $ratio;
                        $filaPivote = $i;
                    }
                }
            }

            if ($filaPivote == -1) {
                echo "<p style='color:red;'>Solución no acotada.</p>";
                exit;
            }

            $pivote = $tabla[$filaPivote][$colPivote];
            for ($j = 0; $j < $columnas; $j++) {
                $tabla[$filaPivote][$j] /= $pivote;
            }

            for ($i = 0; $i < $filas; $i++) {
                if ($i != $filaPivote) {
                    $factor = $tabla[$i][$colPivote];
                    for ($j = 0; $j < $columnas; $j++) {
                        $tabla[$i][$j] -= $factor * $tabla[$filaPivote][$j];
                    }
                }
            }

            $base[$filaPivote] = $nombres[$colPivote];
            $iter++;
        } while (true);
		
		echo '<div class="contResultado">';
		echo '<div class="subtitulo"><p>SOLUCIÓN</p></div>';
		echo '<table cellpadding="5">';

		for ($j = 0; $j < $cantVar; $j++) {
			$nombre = "X" . ($j + 1);
			$valor = 0;
			for ($i = 0; $i < $cantRest; $i++) {
				if ($base[$i] == $nombre) {
					$valor = $tabla[$i][$columnas - 1];
				}
			}

			echo "<tr>";
			echo "<th><label>$nombre</label></th>";
			echo "<td><input type='text' value='" . number_format($valor, 3) . "' readonly></td>";
			echo "</tr>";
		}

		// Z final
		$z = $tabla[$filas - 1][$columnas - 1];
		if ($tipo === 'min') $z = -$z;

		echo "<tr>";
		echo "<th><label><strong>Z</strong></label></th>";
		echo "<td><input type='text' value='" . number_format($z, 2) . "' readonly></td>";
		echo "</tr>";

		echo "</table>";
		echo "</div>";
?>
 </div>
    <script src="funciones.js"></script>
