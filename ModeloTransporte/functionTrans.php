<?php

function viewTable($demanda, $oferta, $costo, $asig) {
  ?>
    <div class="cont-table">
      <table class="r-table">
        <tr>
          <th></th>
          <?php for ($i = 0; $i < count($demanda); $i++) { ?>
            <th> Destino <?= $i + 1 ?></th>
          <?php } ?>
          <th>Oferta</th>
        </tr>
        <?php for ($i = 0; $i < count($oferta); $i++) { ?>
          <tr>
            <th>Fuente <?= $i + 1 ?> </th>
            <?php for ($j = 0; $j < count($demanda); $j++) { ?>
              <td class="celda-cost">
                <div class="td-cost"><?= $costo[$i][$j] ?></div>

                <div class="td-asig"><?= $asig[$i][$j] ?></div>
              </td>
            <?php } ?>
            <td class="td-ofer"><?= $oferta[$i] ?></td>
          </tr>
        <?php } ?>
        <tr>
          <th>Demanda</th>
          <?php for ($i = 0; $i < count($demanda); $i++) { ?>
            <td class="td-deman"><?= $demanda[$i] ?></td>
          <?php } ?>
          <td></td>
        </tr>

      </table>
    </div>
  <?php
}

function viewTableFormat($demanda, $oferta, $costo, $asig) {
  ?>
    <div class="cont-table">
      <table class="r-table">
        <tr>
          <th></th>
          <?php for ($i = 0; $i < count($demanda); $i++) { ?>
            <th> Destino <?= $i + 1 ?></th>
          <?php } ?>
          <th>Oferta</th>
        </tr>
        <?php for ($i = 0; $i < count($oferta); $i++) { ?>
          <tr>
            <th>Fuente <?= $i + 1 ?> </th>
            <?php for ($j = 0; $j < count($demanda); $j++) {
              if ($asig[$i][$j] > 0) {
                ?>
                  <td class="celda-cost td-res">
                    <div class="td-cost"><?= $costo[$i][$j] ?></div>

                    <div class="td-asig"><?= $asig[$i][$j] ?></div>
                  </td>
                <?php
              } else if ($demanda[$j] == 0 || $oferta[$i] == 0) { //$i < $iofer || $j < $ideman 
                ?>
                  <td class="celda-cost td-somb">
                    <div class="td-cost"><?= $costo[$i][$j] ?></div>

                    <div class="td-asig"><?= $asig[$i][$j] ?></div>
                  </td>
                <?php
              } else {
                ?>
                  <td class="celda-cost">
                    <div class="td-cost"><?= $costo[$i][$j] ?></div>

                    <div class="td-asig"><?= $asig[$i][$j] ?></div>
                  </td>
                <?php
              }
            } ?>
            <td class="td-ofer"><?= $oferta[$i] ?></td>
          </tr>
        <?php } ?>
        <tr>
          <th>Demanda</th>
          <?php for ($i = 0; $i < count($demanda); $i++) { ?>
            <td class="td-deman"><?= $demanda[$i] ?></td>
          <?php } ?>
          <td></td>
        </tr>

      </table>
    </div>
  <?php
}

function respTransporte($costo, $asig) {
  ?>
  <div class="tabla-resul">
    <table>
      <tr>
        <th>Fuente</th>
        <th> → </th>
        <th>Destino</th>
        <th>Costo</th>
        <th> x </th>
        <th>Cantidad</th>
        <th> = </th>
        <th>Total</th>
      </tr>
    <?php
      $sumTotal = 0;
      for ($i = 0; $i < count($costo); $i++) {
        for ($j = 0; $j < count($costo[0]); $j++) {
          if($asig[$i][$j] > 0) {
            $sumTotal += $costo[$i][$j]*$asig[$i][$j];
            ?>
              <tr>
                <td class="td-ofer"><?=$i+1?></td>
                <td class=""> → </td>
                <td class="td-deman"><?=$j+1?></td>
                <td class="td-somb"><?=$costo[$i][$j]?></td>
                <td> x </td>
                <td class="td-addi"><?=$asig[$i][$j]?></td>
                <td> = </td>
                <td class="td-ext"><?=$costo[$i][$j]*$asig[$i][$j]?></td>
              </tr>
            <?php
          }
        }
      }
    ?>
      <tr>
        <th colspan="6">Costo total: </th>
        <td> = </td>
        <td class="td-res"> <?=$sumTotal?> </td>
      </tr>
    </table>
  </div>
  <?php
}

function conteoAsignacion ($asig){
  $conteo = 0;
  for ($i = 0; $i < count($asig); $i++) {
    for ($j = 0; $j < count($asig[0]); $j++) {
      if($asig[$i][$j] > 0){
        $conteo++;
      }
    }
  }
  return $conteo;
}
/*
function dosMinimosFila($fila) {
  sort($fila); // Ordena la fila
  return [$fila[0], $fila[1]]; // Devuelve los dos menores
}

function dosMinimosColumna($matriz, $indiceColumna) {
    $columna = [];

    // Extraer la columna
    foreach ($matriz as $fila) {
        if (isset($fila[$indiceColumna])) {
            $columna[] = $fila[$indiceColumna];
        }
    }

    sort($columna); // Ordenar de menor a mayor

    return [$columna[0], $columna[1]]; // Devolver los 2 mínimos
}
*/
function viewTableVoguel($demanda, $oferta, $costo, $asig) {
  ?>
    <div class="cont-table">
      <table class="r-table">
        <tr>
          <th></th>
          <?php for ($i = 0; $i < count($demanda); $i++) { ?>
            <th> Destino <?= $i + 1 ?></th>
          <?php } ?>
          <th>Oferta</th>
        </tr>
        <?php for ($i = 0; $i < count($oferta); $i++) { ?>
          <tr>
            <th>Fuente <?= $i + 1 ?> </th>
            <?php for ($j = 0; $j < count($demanda); $j++) { ?>
              <td class="celda-cost">
                <div class="td-cost"><?= $costo[$i][$j] ?></div>

                <div class="td-asig"><?= $asig[$i][$j] ?></div>
              </td>
            <?php } ?>
            <td class="td-ofer"><?= $oferta[$i] ?></td>
          </tr>
        <?php } ?>
        <tr>
          <th>Demanda</th>
          <?php for ($i = 0; $i < count($demanda); $i++) { ?>
            <td class="td-deman"><?= $demanda[$i] ?></td>
          <?php } ?>
          <td></td>
        </tr>

      </table>
    </div>
  <?php
}
?>