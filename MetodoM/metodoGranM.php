<?php
/*
session_start();

$variZeta = $_SESSION['variZeta'] ?? [];
$vari = $_SESSION['vari'] ?? [];
$condicion = $_SESSION['condicion'] ?? [];
$resul = $_SESSION['resul'] ?? [];
$cantVar = $_SESSION['cantVar'] ?? 2;
$cantRest = $_SESSION['cantRest'] ?? 2;
$objetivo = $_SESSION['objetivo'] ?? 'min';
$M = $_SESSION['M'] ?? 100;
?>

<div class="titulo">
  <h1>Método de la Gran M</h1>
</div>

<form method="post" action="resolucionGranM.php">
  <div class="cuerpo">
    <div class="ingresaDatos">
      <div class="tituloDatos">
        <h3>Ingresa datos iniciales:</h3>
      </div>
      <div class="maxmin">
        <label class="objetivo" id="label_min">
          <input type="radio" name="objetivo" value="min" <?= $objetivo === 'min' ? 'checked' : '' ?> onclick="opcionMin()" checked /> Minimizar
        </label>
        <label class="objetivo" id="label_max">
          <input type="radio" name="objetivo" value="max" <?= $objetivo === 'max' ? 'checked' : '' ?> onclick="opcionMax()" /> Maximizar
        </label>
      </div>

      <div class="datosIniciales">
        <table>
          <tr>
            <td><label for="M">Valor de M: </label></td>
            <td class="celdaInput"><input type="number" id="M" name="M" value="<?= $M ?>" required /></td>
          </tr>
          <tr>
            <td><label for="cantVar">Cantidad de variables: </label></td>
            <td class="celdaInput"><input type="number" id="cantVar" name="cantVar" value="<?= $cantVar ?>" min="2" required /></td>
          </tr>
          <tr>
            <td><label for="cantRest">Cantidad de restricciones: </label></td>
            <td class="celdaInput"><input type="number" id="cantRest" name="cantRest" value="<?= $cantRest ?>" min="2" required /></td>
          </tr>
        </table>
      </div>

      <div class="contBoton">
        <button class="botonIngresar" onclick="generarTabla()">Ingresar datos</button>
      </div>
    </div>
    <div class="contenedorTabla">
      <div class="tablaVariables" id="tablaContainer"></div>
      <div class="contBoton" id="submitBtn" style="margin-top: 10px;"></div>
    </div>
  </div>
</form>


<!-- Pasamos los datos PHP a JavaScript -->
<script>
  const datosGuardados = {
    cantVar: <?= $cantVar ?>,
    cantRest: <?= $cantRest ?>,
    variZeta: <?= json_encode($variZeta) ?>,
    vari: <?= json_encode($vari) ?>,
    condicion: <?= json_encode($condicion) ?>,
    resul: <?= json_encode($resul) ?>
  };

  window.onload = function() {
    if (datosGuardados.vari && datosGuardados.vari.length > 0) {
      generarTabla(datosGuardados);
    }
    <?php
    if ($objetivo == 'min') {
    ?> opcionMin();
    <?php
    } else {
    ?> opcionMax();
    <?php
    }

    ?>

  };
</script>
<script src="../Inicio/metodGranM.js"></script>