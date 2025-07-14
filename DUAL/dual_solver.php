
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .bloque {
            background: white;
            padding: 10px;
            margin: 20px auto;
            width: 90%;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        .titulo {
            background-color: #2c2f33;
            color: white;
            padding: 10px;
            font-weight: bold;
            text-align: center;
            border-radius: 8px 8px 0 0;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #2c2f33;
            color: white;
        }
        .iteracion-1 td {
            background-color: #e3faff;
        }
        .iteracion-2 td {
            background-color: #f9f9f9;
        }
        .iteracion-3 td {
            background-color: #ffd8e0;
        }
        .resultado {
            text-align: center;
            background: #2c2f33;
            color: white;
            padding: 10px;
            font-weight: bold;
            border-radius: 8px;
            margin: 20px auto;
        }
        .solucion-box {
            background-color: #fff;
            padding: 15px;
            width: 200px;
            float: right;
            margin-right: 10%;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(13, 13, 13, 0.15);
        }
        .solucion-box div {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 6px;
            background-color: #918e8eff;
            border-radius: 6px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>

<?php
function parse_input($str) {
    return array_map('floatval', explode(',', trim((string)$str)));
}
function parse_lines($str) {
    return array_map('trim', explode("\n", trim($str)));
}

function mostrarTabla($tabla, $base, $nVars, $nRestricciones, $iteracion) {
    static $iteracion_count = 1;
    $claseIter = "iteracion-$iteracion_count";
    echo "<div class='bloque'>";
    echo "<div class='titulo'>ITERACION $iteracion_count</div>";
    echo "<table class='$claseIter'>";
    echo "<tr><th>VB</th>";
    for ($j = 0; $j < count($tabla[0]) - 1; $j++) echo "<th>X" . ($j + 1) . "</th>";
    echo "<th>RHS</th></tr>";
    for ($i = 0; $i < count($tabla); $i++) {
        echo "<tr>";
        echo "<td>" . ($i < $nRestricciones ? $base[$i] : "Z") . "</td>";
        foreach ($tabla[$i] as $val) {
            echo "<td>" . round($val, 2) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table></div>";
    $iteracion_count++;
}

function generarDual($tipo, $nVars, $nRestricciones, $obj, $restricciones, $signos, $rhs) {
    $aDual = [];
    $zDual = $rhs;
    if ($tipo == 'max'){
        for ($i = 0; $i < $nVars; $i++) {
            $fila = [];
            for ($j = 0; $j < $nRestricciones; $j++) {
                $fila[] = -floatval($restricciones[$j][$i]);
            }
            $aDual[] = $fila;
        }
        $zDual = array_map(fn($v) => -$v, $rhs);
    } else {
        for ($i = 0; $i < $nVars; $i++) {
            $fila = [];
            for ($j = 0; $j < $nRestricciones; $j++) {
                $fila[] = floatval($restricciones[$j][$i]);
            }
            $aDual[] = $fila;
        }
    }
    $rhsDual = $tipo === 'max' ? array_map(fn($v) => -$v, $obj) : $obj;
    return [$aDual, $zDual, $rhsDual];
}

// Entrada de datos
$tipo = $_POST['tipo'];
$nVars = intval($_POST['n_vars']);
$nRestricciones = intval($_POST['n_cons']);

$obj = array_map('floatval', $_POST['objetivo']);
$restricciones = $_POST['restricciones'];
$signos = $_POST['signos'];
$rhs = array_map('floatval', $_POST['rhs']);


list($aDual, $zDual, $rhsDual) = generarDual($tipo, $nVars, $nRestricciones, $obj, $restricciones, $signos, $rhs);

$tabla = [];
$base = [];
for ($i = 0; $i < count($aDual); $i++) {
    $fila = $aDual[$i];
    for ($j = 0; $j < count($aDual); $j++) {
        $fila[] = ($i == $j) ? 1 : 0;
    }
    $fila[] = $rhsDual[$i];
    $tabla[] = $fila;
    $base[] = "Y" . ($i + 1);
}

$filaZ = array_fill(0, count($tabla[0]), 0);
for ($j = 0; $j < count($zDual); $j++) {
    $filaZ[$j] = -$zDual[$j];
}
$tabla[] = $filaZ;

$filas = count($tabla);
$columnas = count($tabla[0]);

mostrarTabla($tabla, $base, $nVars, count($aDual), 1);

while (true) {
    $filaPiv = -1;
    $minRHS = 0;
    for ($i = 0; $i < $filas - 1; $i++) {
        if ($tabla[$i][$columnas - 1] < $minRHS) {
            $minRHS = $tabla[$i][$columnas - 1];
            $filaPiv = $i;
        }
    }
    if ($filaPiv == -1) break;

    $colPiv = -1;
    $minRatio = PHP_INT_MAX;
    for ($j = 0; $j < $columnas - 1; $j++) {
        $coef = $tabla[$filaPiv][$j];
        if ($coef < 0) {
            $ratio = abs($tabla[$filas - 1][$j] / $coef);
            if ($ratio < $minRatio) {
                $minRatio = $ratio;
                $colPiv = $j;
            }
        }
    }
    if ($colPiv == -1) {
        echo "<p><strong>El problema no tiene solución dual factible.</strong></p>";
        exit;
    }

    $pivote = $tabla[$filaPiv][$colPiv];
    for ($j = 0; $j < $columnas; $j++) {
        $tabla[$filaPiv][$j] /= $pivote;
    }

    for ($i = 0; $i < $filas; $i++) {
        if ($i != $filaPiv) {
            $factor = $tabla[$i][$colPiv];
            for ($j = 0; $j < $columnas; $j++) {
                $tabla[$i][$j] -= $factor * $tabla[$filaPiv][$j];
            }
        }
    }

    $base[$filaPiv] = "X" . ($colPiv + 1);
    mostrarTabla($tabla, $base, $nVars, count($aDual), 2);
}

$hayNegativosEnZ = false;
for ($j = 0; $j < $columnas - 1; $j++) {
    if ($tabla[$filas - 1][$j] < 0) {
        $hayNegativosEnZ = true;
        break;
    }
}

if ($hayNegativosEnZ) {
    echo "<div class='resultado'>Se aplica método primal (simplex estándar) al finalizar el dual</div>";
    
    while (true) {
        $colPivote = -1;
        $minVal = 0;
        for ($j = 0; $j < $columnas - 1; $j++) {
            if ($tabla[$filas - 1][$j] < $minVal) {
                $minVal = $tabla[$filas - 1][$j];
                $colPivote = $j;
            }
        }

        if ($colPivote == -1) break;

        $filaPivote = -1;
        $minRatio = PHP_FLOAT_MAX;
        for ($i = 0; $i < $filas - 1; $i++) {
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
            echo "<p><strong>Solución no acotada.</strong></p>";
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

        $base[$filaPivote] = "X" . ($colPivote + 1);
        mostrarTabla($tabla, $base, $nVars, count($aDual), 3);
    }
}


echo "<div class='resultado'><< RESULTADO >></div>";
echo "<div class='bloque clearfix'>";
echo "<div class='titulo'>ITERACION FINAL</div>";
echo "<table class='iteracion-3'>";
echo "<tr><th>VB</th>";
for ($j = 0; $j < $columnas - 1; $j++) echo "<th>X" . ($j + 1) . "</th>";
echo "<th>RHS</th></tr>";
for ($i = 0; $i < count($tabla); $i++) {
    echo "<tr>";
    echo "<td>" . ($i < count($aDual) ? $base[$i] : "Z") . "</td>";
    foreach ($tabla[$i] as $val) echo "<td>" . round($val, 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<div class='solucion-box'>";
echo "<div class='titulo'>SOLUCIÓN</div>";
// <<<<<<<< HEAD:MetodoDual/dual_solver.php
// for ($j = 0; $j < $nVars; $j++) {
//     $valor = 0;
//     for ($i = 0; $i < count($aDual); $i++) {
//         if ($base[$i] == "X" . ($j + 1)) {
//             $valor = $tabla[$i][$columnas - 1];
//             break;
// ========

if ($tipo === 'min') {
    for ($j = 0; $j < $nRestricciones; $j++) {
        $valor = 0;
        for ($i = 0; $i < count($aDual); $i++) {
            if ($base[$i] == "X" . ($j + 1)) {
                $valor = $tabla[$i][$columnas - 1];
                break;
            }
// >>>>>>>> origin/main:DUAL/dual_solver.php
        }
        echo "<div><span>X" . ($j + 1) . "</span><span>" . round($valor, 2) . "</span></div>";
    }
} else {
    for ($j = 0; $j < $nVars-1; $j++) {
        $valor = 0;
        for ($i = 0; $i < count($aDual); $i++) {
            if ($base[$i] == "X" . ($j + 1)) {
                $valor = $tabla[$i][$columnas - 1];
                break;
            }
        }
        echo "<div><span>X" . ($j + 1) . "</span><span>" . round($valor, 2) . "</span></div>";
    }
}

$z = $tabla[$filas - 1][$columnas - 1];
echo "<div><span>Z</span><span>" . round(abs($z), 2) . "</span></div>";
echo "</div>";

?>

<?php
require_once '../Inicio/footer.php';
?>