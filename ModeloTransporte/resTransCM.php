<?php
session_start();
include('functionTrans.php');
$_SESSION['costo'] = $_POST['costo'];
$_SESSION['demanda'] = $_POST['demanda'];
$_SESSION['oferta'] = $_POST['oferta'];
$_SESSION['numDestinos'] = $_POST['numDestinos'];
$_SESSION['numFuentes'] = $_POST['numFuentes'];
$_SESSION['objetivo'] = $_POST['objetivo'];

$costo = $_SESSION['costo'] ?? [];
$demanda = $_SESSION['demanda'] ?? [];
$oferta = $_SESSION['oferta'] ?? [];
$numDestinos = $_SESSION['numDestinos'] ?? 2;
$numFuentes = $_SESSION['numFuentes'] ?? 2;
$objetivo = $_SESSION['objetivo'] ?? 'min';

//Exclusivo para la resolucion
$cost = $costo;
$deman = $demanda;
$ofer = $oferta;
$asig = [];
for ($i = 0; $i < count($ofer); $i++) {
  for ($j = 0; $j < count($deman); $j++) {
    $asig[$i][$j] = 0;
  }
}
//Variables agregadas

?>

<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>

<div class="cont-trans-all">
  <div class="titulo">
    <h1>Modelo de transporte - Costos Mínimos</h1>
  </div>

  <div class="cont-res-en">

    <?php
    viewTable($deman, $ofer, $cost, $asig);
    $cen = 0;
    
    $numVerifi = $numDestinos+$numFuentes-1;
    while (($numVerifi > conteoAsignacion($asig)) && $cen < 20) {
      $ideman = 0;
      $iofer = 0;
      $costMenor = 10000000;
      for ($i = 0; $i < count($ofer); $i++) {
        for ($j = 0; $j < count($deman); $j++) {          
          if ($costMenor > $cost[$i][$j] && $asig[$i][$j] == 0 && ($ofer[$i] > 0 && $deman[$j] > 0)) {
            $costMenor = $cost[$i][$j];
            $iofer = $i;
            $ideman = $j;
          }
        }
      }
      if ($deman[$ideman] > $ofer[$iofer]) {
        $asig[$iofer][$ideman] = $ofer[$iofer];
      } else {
        $asig[$iofer][$ideman] = $deman[$ideman];
      }

      $deman[$ideman] -=  $asig[$iofer][$ideman];
      $ofer[$iofer] -=  $asig[$iofer][$ideman];
      viewTableFormat($deman, $ofer, $cost, $asig);
      
      $cen++;
    }
    ?>
  </div>
  <div class="res-en-end">

  </div>
</div>

<div class="res-trans">
  <div class="subtitulo">
    <h3>Resultado</h3>
  </div>
  <?= respTransporte($cost, $asig); ?>
</div>



<?php
require_once '../Inicio/footer.php';
?>