<?php
session_start();
require_once '../Inicio/header.php';
require_once '../Inicio/sidebar.php';

$variZeta = $_SESSION['variZeta'] ?? [];
$variables = $_SESSION['variables'] ?? [];
$signo = $_SESSION['signo'] ?? [];
$RHS = $_SESSION['RHS'] ?? [];
$cantVar = $_SESSION['cantVar'] ?? 2;
$cantRest = $_SESSION['cantRest'] ?? 2;
$tipo = $_SESSION['tipo'] ?? 'max';
?>

<div class="titulo">
    <h1>Modelo Binario</h1>
</div>

<form method="post" action="solucionBinario.php">
  <div class="cuerpo">
    <div class="ingresaDatos">
      <div class="tituloDatos">
        <h3>Ingresa datos iniciales:</h3>
      </div>
      <div class="maxmin">
        <label class="objetivo" id="label_min">
          <input type="radio" name="tipo" value="min" <?= $tipo === 'min' ? 'checked' : '' ?> onclick="opcionMin()"> Minimizar
        </label>
        <label class="objetivo" id="label_max">
          <input type="radio" name="tipo" value="max" <?= $tipo === 'max' ? 'checked' : '' ?> onclick="opcionMax()"> Maximizar
        </label>
      </div>
      <div class="datosIniciales">
        <table>
          <tr>
            <td><label for="cantVar"><p>Cantidad de variables: </p></label></td>
            <td><input type="number" id="cantVar" name="cantVar" value="<?= $cantVar ?>" min="2" required></td>
          </tr>
          <tr>
            <td><label for="cantRest"><p>Cantidad de restricciones: </p></label></td>
            <td><input type="number" id="cantRest" name="cantRest" value="<?= $cantRest ?>" min="1" required></td>
          </tr>
        </table>
      </div>
      <div class="contBoton">
        <button type="button" class="botonIngresar" onclick="generarTabla()">Ingresar datos</button>
      </div>
    </div>

    <div class="contenedorTabla">
      <div class="tablaVariables" id="tablaContainer"></div>
      <div class="contBoton" id="submitBtn" style="margin-top: 10px;"></div>
    </div>
  </div>
</form>

<script>
const datosGuardados = {
  cantVar: <?= $cantVar ?>,
  cantRest: <?= $cantRest ?>,
  variZeta: <?= json_encode($variZeta) ?>,
  variables: <?= json_encode($variables) ?>,
  signo: <?= json_encode($signo) ?>,
  RHS: <?= json_encode($RHS) ?>
};

window.onload = function () {
  if (datosGuardados.variables.length) {
    generarTabla(datosGuardados);
  }
  <?= $tipo == 'min' ? 'opcionMin();' : 'opcionMax();' ?>
};
</script>
<script src="funciones.js"></script>
