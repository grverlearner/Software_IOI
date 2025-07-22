<?php
session_start();
require_once '../Inicio/header.php';
require_once '../Inicio/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['tipo'] = $_POST['tipo'] ?? 'max';
    $_SESSION['cantRest'] = (int) ($_POST['cantRest'] ?? 0);
    $_SESSION['cantVar'] = (int) ($_POST['cantVar'] ?? 0);
    $_SESSION['variZeta'] = array_map('floatval', $_POST['variZeta'] ?? []);
    $_SESSION['variables'] = array_map(
        function($row) { return array_map('floatval', $row);
        },
        $_POST['variables'] ?? []
    );
    $_SESSION['signo'] = $_POST['condicion'] ?? [];
    $_SESSION['RHS'] = array_map('floatval', $_POST['resul'] ?? []);
}

$tipo = $_SESSION['tipo'];
$cantVar = $_SESSION['cantVar'];
$cantRest = $_SESSION['cantRest'];
$variZeta = $_SESSION['variZeta'];
$variables = $_SESSION['variables'];
$signo = $_SESSION['signo'];
$RHS = $_SESSION['RHS'];

function generarCombinaciones($n) {
    $combinaciones = [];
    for ($i = 0; $i < pow(2, $n); $i++) {
        $bin = str_pad(decbin($i), $n, "0", STR_PAD_LEFT);
        $combinaciones[] = array_map('intval', str_split($bin));
    }
    return $combinaciones;
}

function esMejorCombinacion(array $a, array $b) {
  if (count($a) !== count($b)) return false;
  for ($i = 0; $i < count($a); $i++) {
    if ($a[$i] !== $b[$i]) return false;
  }
  return true;
}

$combinaciones = generarCombinaciones($cantVar);
$mejorZ = $tipo == 'max' ? -INF : INF;
$mejorCombinacion = [];
$resultados = [];

foreach ($combinaciones as $combo) {
  $z = 0;
  for ($i = 0; $i < count($combo); $i++) {
      $z += $combo[$i] * $variZeta[$i];
  }
  $factible = true;

  for ($j = 0; $j < $cantRest; $j++) {
    $lhs = 0;
    for ($i = 0; $i < count($combo); $i++) {
        $lhs += $combo[$i] * $variables[$j][$i];
    }
    $cond = $signo[$j];
    $rhs = $RHS[$j];

    if (($cond === "<=" && $lhs > $rhs) ||
        ($cond === ">=" && $lhs < $rhs) ||
        ($cond === "="  && $lhs != $rhs)) {
      $factible = false;
      break;
    }
  }

  if ($factible) {
    if (($tipo === 'max' && $z > $mejorZ) || ($tipo === 'min' && $z < $mejorZ)) {
      $mejorZ = $z;
      $mejorCombinacion = $combo;
    }
  }

  $resultados[] = [
    'combo' => $combo,
    'z' => $z,
    'factible' => $factible
  ];
}
?>

<div class="titulo">
    <h1>Resultados del Modelo Binario</h1>
</div><br>
<table border="1" style="border-collapse: collapse;border: 1px solid black; border-radius: 8px;">
  <tr>
    <?php for ($i = 0; $i < $cantVar; $i++): ?>
      <th>x<?= $i + 1 ?></th>
    <?php endfor; ?>
    <th>¿Factible?</th>
    <th>Z</th>
  </tr>

  <?php foreach ($resultados as $res): 
    $combo = $res['combo'];
    $z = $res['z'];
    $factible = $res['factible'];

    $clase = '';
    if ($factible && esMejorCombinacion($combo, $mejorCombinacion)) {
        $clase = 'mejor-solucion';
    } elseif ($factible) {
        $clase = 'factible';
    }
  ?>
    <tr class="<?= $clase ?>">
      <?php foreach ($combo as $valor): ?>
        <td><?= $valor ?></td>
      <?php endforeach; ?>
      <td><?= $factible ? 'Si' : 'No' ?></td>
      <td><?= $factible ? $z : 'X' ?></td>
    </tr>
  <?php endforeach; ?>
</table>

<?php if (!empty($mejorCombinacion)): ?><br>
<div class="subtitulo"><p>SOLUCIÓN</p></div>
  <table>
    <tr>
      <th><label for="z">Z</label></th>
      <td><input type="text" id="z" value="<?= htmlspecialchars($mejorZ) ?>" readonly></td>
    </tr>
    <?php foreach ($mejorCombinacion as $i => $valor): ?>
      <tr>
        <th><label for="x<?= $i + 1 ?>">x<?= $i + 1 ?></label></th>
        <td><input type="text" id="x<?= $i + 1 ?>" value="<?= htmlspecialchars($valor) ?>" readonly></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php endif; ?>
<script src="funciones.js"></script>