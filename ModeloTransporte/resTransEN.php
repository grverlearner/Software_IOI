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
for($i = 0; $i < count($deman); $i++){
    for($j = 0; $j < count($cost); $j++){
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

<div class="Cont-reso">

<?php
viewTable($deman,$ofer,$cost,$asig);
?>
    <div>

    </div>
</div>


<?php
require_once '../Inicio/footer.php';
?>