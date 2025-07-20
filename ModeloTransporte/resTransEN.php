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
$ideman = 0;
$iofer = 0;
?>

<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>

<div class="cont-trans-all">
    <div class="titulo">
        <h1>Moleo de transporte - Esquina Noroeste</h1>
    </div>

    <div class="cont-res-en">

        <?php
        viewTable($deman, $ofer, $cost, $asig);
        $cen = 0;
        while (($ideman < count($deman) && $iofer < count($ofer)) && $cen < 20) {
            if ($deman[$ideman] > $ofer[$iofer]) {
                $asig[$iofer][$ideman] = $ofer[$iofer];
                $deman[$ideman] -=  $asig[$iofer][$ideman];
                $ofer[$iofer] -=  $asig[$iofer][$ideman];
                $iofer++;
            } else {
                $asig[$iofer][$ideman] = $deman[$ideman];
                $deman[$ideman] -=  $asig[$iofer][$ideman];
                $ofer[$iofer] -=  $asig[$iofer][$ideman];
                $ideman++;
            }
            viewTable($deman, $ofer, $cost, $asig);
            $cen++;
            // echo $ideman . " ... " . $iofer . "<br>";
            // echo count($deman) . "---" . count($ofer);

        }
        ?>
    </div>
</div>

<div class="res-trans">
    
</div>



<?php
require_once '../Inicio/footer.php';
?>