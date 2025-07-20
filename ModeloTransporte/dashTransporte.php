<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>
<?php
session_start();

$costo = $_SESSION['costo'] ?? [];
$demanda = $_SESSION['demanda'] ?? [];
$oferta = $_SESSION['oferta'] ?? [];
$numDestinos = $_SESSION['numDestinos'] ?? 2;
$numFuentes = $_SESSION['numFuentes'] ?? 2;
$objetivo = $_SESSION['objetivo'] ?? 'min';
?>
<div class="cont-all">
    <div class="titulo">
    <h1>Modelo Transporte</h1>
</div>

<form method="POST" action="resTransEN.php">
    <div class="cuerpoTransporte">
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
                        <td><label for="numDestinos">
                                <p>Cantidad de Destinos: </p>
                            </label></td>
                        <td class="celdaInput"><input type="number" id="numDestinos" name="numDestinos" value="<?= $numDestinos ?>" min="2" required /></td>
                    </tr>
                    <tr>
                        <td><label for="numFuentes">
                                <p>Cantidad de Fuentes: </p>
                            </label></td>
                        <td class="celdaInput"><input type="number" id="numFuentes" name="numFuentes" value="<?= $numFuentes ?>" min="2" required /></td>
                    </tr>
                </table>
            </div>

            <div class="contBoton">
                <button type="button" class="botonIngresar" onclick="tablaTransporte()">Ingresar datos</button>
            </div>
        </div>
        <div class="contenedorTabla">
            <div class="tablaVariables" id="tablaContainer"></div>
            <div class="contBoton" id="submitBtn" style="margin-top: 10px;"></div>
        </div>
    </div>
</form>
</div>




<!-- Pasamos los datos PHP a JavaScript -->
<script>
    const datosGuardados = {
        numDestinos: <?= $numDestinos ?>,
        numFuentes: <?= $numFuentes ?>,
        costo: <?= json_encode($costo) ?>,
        demanda: <?= json_encode($demanda) ?>,
        oferta: <?= json_encode($oferta) ?>
    };

    window.onload = function() {
        if (datosGuardados.costo && datosGuardados.costo.length > 0) {
            tablaTransporte(datosGuardados);
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
<script src="functionTrans.js"></script>

<?php
require_once '../Inicio/footer.php';
?>