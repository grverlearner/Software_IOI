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
        fn($row) => array_map('floatval', $row),
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

// Función para comparar dos arrays de igual longitud y valores iguales
function arraysIguales(array $a, array $b) {
    if (count($a) !== count($b)) return false;
    for ($i = 0; $i < count($a); $i++) {
        if ($a[$i] !== $b[$i]) return false;
    }
    return true;
}

$combinaciones = generarCombinaciones($cantVar);
$mejorZ = $tipo == 'max' ? -INF : INF;
$mejorCombinacion = [];

?>


<div class="titulo">
    <h1>Resultados del Modelo Binario</h1>
</div>

<table border="1">
  <tr>
    <?php for ($i = 0; $i < $cantVar; $i++): ?>
      <th>x<?= $i + 1 ?></th>
    <?php endfor; ?>
    <th>¿Factible?</th>
    <th>Z</th>
  </tr>

  <?php foreach ($combinaciones as $combo): ?>
    <?php
      $z = array_sum(array_map(fn($c, $v) => $c * $v, $combo, $variZeta));
      $factible = true;
      for ($j = 0; $j < $cantRest; $j++) {
          $lhs = array_sum(array_map(fn($c, $v) => $c * $v, $combo, $variables[$j]));
          $cond = $signo[$j];
          $rhs = $RHS[$j];
          if (($cond === "<=" && $lhs > $rhs) ||
              ($cond === ">=" && $lhs < $rhs) ||
              ($cond === "="  && $lhs != $rhs)) {
              $factible = false;
              break;
          }
      }

      // Actualizar mejor solución
      if ($factible) {
          if (($tipo === 'max' && $z > $mejorZ) || ($tipo === 'min' && $z < $mejorZ)) {
              $mejorZ = $z;
              $mejorCombinacion = $combo;
          }
      }
    ?>
  <?php endforeach; ?>

  <?php foreach ($combinaciones as $combo): ?>
    <?php
      $z = array_sum(array_map(fn($c, $v) => $c * $v, $combo, $variZeta));
      $factible = true;
      for ($j = 0; $j < $cantRest; $j++) {
          $lhs = array_sum(array_map(fn($c, $v) => $c * $v, $combo, $variables[$j]));
          $cond = $signo[$j];
          $rhs = $RHS[$j];
          if (($cond === "<=" && $lhs > $rhs) ||
              ($cond === ">=" && $lhs < $rhs) ||
              ($cond === "="  && $lhs != $rhs)) {
              $factible = false;
              break;
          }
      }

      // Asignar clase según estado
      $clase = '';
      if ($factible && arraysIguales($combo, $mejorCombinacion)) {
          $clase = 'mejor-solucion'; // amarillo pastel para la mejor solución
      } elseif ($factible) {
          $clase = 'factible'; // celeste pastel para otras factibles
      }
    ?>
    <tr class="<?= $clase ?>">
      <?php foreach ($combo as $valor): ?>
        <td><?= $valor ?></td>
      <?php endforeach; ?>

      <?php if ($factible): ?>
        <td>Si</td>
        <td><?= $z ?></td>
      <?php else: ?>
        <td>No</td>
        <td>X</td>
      <?php endif; ?>
    </tr>
  <?php endforeach; ?>
</table>

<?php if (!empty($mejorCombinacion)): ?>
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
