<?php
function linprog($c, $A, $b) {
    $results = [];
    $limitX = intval($b[0]);
    $limitY = intval($b[1]);
    for ($i = 0; $i <= $limitX; $i++) {
        for ($j = 0; $j <= $limitY; $j++) {
            $valid = true;
            for ($k = 0; $k < count($A); $k++) {
                $sum = $A[$k][0] * $i + $A[$k][1] * $j;
                if ($sum > $b[$k]) {
                    $valid = false;
                    break;
                }
            }
            if ($valid) {
                $val = $c[0] * $i + $c[1] * $j;
                $results[] = ['x' => [$i, $j], 'value' => $val];
            }
        }
    }
    return $results;
}

function is_integral_vector($x) {
    foreach ($x as $val) {
        if (abs($val - round($val)) > 1e-5) return false;
    }
    return true;
}

function branch_and_bound($c, $A, $b, &$tree, $parent = null) {
    static $id = 1;
    $best = null;
    $best_val = -INF;

    $solutions = linprog($c, $A, $b);
    foreach ($solutions as $sol) {
        if ($sol['value'] > $best_val) {
            $best = $sol;
            $best_val = $sol['value'];
        }
    }

    $current = [
        'id' => $id++,
        'parent' => $parent,
        'solution' => $best ? implode(",", $best['x']) : 'N/A',
        'value' => $best ? $best['value'] : 'Infeasible',
        'status' => 'pruned'
    ];

    if (!$best) {
        $tree[] = $current;
        return null;
    }

    if (is_integral_vector($best['x'])) {
        $current['status'] = 'optimal';
        $tree[] = $current;
        return $best;
    }

    $tree[] = $current;
    foreach ($best['x'] as $k => $v) {
        if (abs($v - round($v)) > 1e-5) {
            // Izquierda
            $A1 = $A;
            $b1 = $b;
            $new_row1 = array_fill(0, count($c), 0);
            $new_row1[$k] = 1;
            $A1[] = $new_row1;
            $b1[] = floor($v);
            branch_and_bound($c, $A1, $b1, $tree, $current['id']);

            // Derecha
            $A2 = $A;
            $b2 = $b;
            $new_row2 = array_fill(0, count($c), 0);
            $new_row2[$k] = -1;
            $A2[] = $new_row2;
            $b2[] = -ceil($v);
            branch_and_bound($c, $A2, $b2, $tree, $current['id']);
            break;
        }
    }
}

function parse_input_matriz($restricciones) {
    $matriz = [];
    foreach ($restricciones as $fila) {
        $matriz[] = array_map('floatval', $fila);
    }
    return $matriz;
}

$resultado = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $c = array_map('floatval', $_POST['objetivo']);
    $A = parse_input_matriz($_POST['restricciones']);
    $b = array_map('floatval', $_POST['rhs']);

    $tree = [];
    branch_and_bound($c, $A, $b, $tree);

    $resultado .= "<div class='container'>";
    $resultado .= "<h2>Resultado - Árbol de Decisión</h2>";
    $resultado .= "<table><tr><th>ID</th><th>Padre</th>";
    foreach ($c as $i => $_) {
        $resultado .= "<th>x" . ($i + 1) . "</th>";
    }
    $resultado .= "<th>Valor</th><th>Estado</th></tr>";
        foreach ($tree as $node) {
        $class = $node['status'] === 'optimal' ? 'node-optimal' : ($node['value'] === 'Infeasible' ? 'node-pruned' : '');
        $resultado .= "<tr class='$class'>";
        $resultado .= "<td>{$node['id']}</td>";
        $resultado .= "<td>" . ($node['parent'] ?? '-') . "</td>";

        $vars = explode(',', $node['solution']);
        foreach ($vars as $val) {
            $resultado .= "<td>$val</td>";
        }

        $resultado .= "<td>{$node['value']}</td>";
        $resultado .= "<td>{$node['status']}</td>";
        $resultado .= "</tr>";
    }
    $resultado .= "</table>";
    $resultado .= '<br><a href="index.php">Volver</a>';
    $resultado .= "</div>";

}
?>

<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>
<style>
    body {
        font-family: Arial;
        background: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    header {
        background-color: #2c2f33;
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 24px;
    }
    .container {
        max-width: 900px;
        margin: 30px auto;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #2c2f33;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th, td {
        border: 1px solid #aaa;
        padding: 8px;
        text-align: center;
    }
    th {
        background-color: #2c2f33;
        color: white;
    }
    .node-optimal {
        background-color: lightgreen;
    }
    .node-pruned {
        background-color: lightcoral;
    }
    a {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        text-decoration: none;
        color: #2c2f33;
        /* background-color: #ddd; */
        border-radius: 8px;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: background-color 0.3s ease;
    }

    a:hover {
        background-color: #25272aff;
    }

    header {
        background-color: #ddd;
        color: #2c2f33;
        padding: 20px;
        text-align: center;
        font-size: 24px;
        border-radius: 12px;
        margin: 20px auto;
        width: 90%;
        max-width: 900px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

</style>

<div class="titulo">
    <h1>Ramificación y Acotación - Programación Entera</h1>
</div>

<?= $resultado ?>

<?php
require_once '../Inicio/footer.php';
?>
