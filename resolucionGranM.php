<?php
session_start();
include('funciones.php')
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Metodo M Resolución</title>
    <link rel="stylesheet" href="style1.css" />
    <link rel="stylesheet" href="style2.css" />
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $objetivo = $_POST['objetivo']; //Maximizar o Minimizar
        $cantrest = $_POST['cantRest']; //Cantidad de restricciones
        $cantVar = $_POST['cantVar'];   //Cantidad de variables
        $M = $_POST['M'];               //Valor de M

        $funObjetivo = $_POST['variZeta'];  //Variables de la fila Z    [ARRAY]
        $restricciones = $_POST['vari'];    //Variables de restriccion (parte izquierda de la ecuacion) [MATRIZ]
        $condiciones = $_POST['condicion']; //igual, mayor, menor       [ARRAY]
        $resultados = $_POST['resul'];      //Resultados (derecha de la ecuacion)          [ARRAY]
        

        $_SESSION['objetivo'] = $objetivo;
        $_SESSION['cantRest'] = $cantrest;
        $_SESSION['cantVar'] = $cantVar;
        $_SESSION['variZeta'] = $funObjetivo;
        $_SESSION['vari'] = $restricciones;
        $_SESSION['condicion'] = $condiciones;
        $_SESSION['resul'] = $resultados;
        $_SESSION['M'] = $M;
    }
    ?>
</head>

<body>
    <nav>
        <a href="index.html">Inicio</a>
        <a href="metodoGranM.php">Método de la gran M</a>
    </nav>

    <main>
        <div class="titulo">
            <h1>Método de la Gran M</h1>
        </div>

        <?php
        $bandera = 0;
        $iterMaxima = 20;

        //LIMPIAR DATOS INICIALES DE LA TABLA
        $restricciones = limpiarNumeros($restricciones);
        $resultados = limpiarNumeros($resultados);
        $funObjetivo = limpiarNumeros($funObjetivo);
        
        //PREPARANDO VARIABLES
        $exceso = array();
        $artificial = array();
        $holgura = array();

        $nexc = $nart = $nhol = 0;
        $iexc = $iart = $ihol = 0;

        $restCuadro = $restricciones;
        $zetaCuadro = $funObjetivo;
        $soluCuadro = $resultados;
        $titCuadro = array();
        $basiCuadro = array();

        //CREANDO MATRICES HOLGURA,EXCESO,ARTIFICIAL
        for ($i = 0; $i < $cantrest; $i++) {
            array_push($holgura, array());
            array_push($exceso, array());
            array_push($artificial, array());

            if ($condiciones[$i] == '>=') {
                $nexc++;
                $nart++;
                array_push($basiCuadro, 'A');
            } else if ($condiciones[$i] == '==') {
                $nart++;
                array_push($basiCuadro, 'A');
            } else {
                $nhol++;
                array_push($basiCuadro, 'H');
            }
        }
        $boolexc = $boolart = $boolhol = 0;

        for ($i = 0; $i < $cantrest; $i++) {

            if ($condiciones[$i] == '>=') {
                $boolexc = 1;
                $boolart = 1;
            } else if ($condiciones[$i] == '==')
                $boolart = 1;
            else
                $boolhol = 1;

            for ($j = 0; $j < $nexc; $j++) {
                if ($j == $iexc && $boolexc == 1) {
                    array_push($exceso[$i], -1);
                    $iexc++;
                    $boolexc = 0;
                } else
                    array_push($exceso[$i], 0);

                array_push($restCuadro[$i], $exceso[$i][$j]);
            }

            for ($j = 0; $j < $nart; $j++) {
                if ($j == $iart && $boolart == 1) {
                    array_push($artificial[$i], 1);
                    $iart++;
                    $boolart = 0;
                } else
                    array_push($artificial[$i], 0);

                array_push($restCuadro[$i], $artificial[$i][$j]);
            }


            for ($j = 0; $j < $nhol; $j++) {
                if ($j == $ihol && $boolhol == 1) {
                    array_push($holgura[$i], 1);
                    $ihol++;
                    $boolhol = 0;
                } else
                    array_push($holgura[$i], 0);

                array_push($restCuadro[$i], $holgura[$i][$j]);
            }
        }

        //zetaCuadro //Variables básicas
        for ($i = 0; $i < $cantVar; $i++)
            array_push($titCuadro, 'X' . $i + 1);
        for ($i = 0; $i < $nexc; $i++) {
            array_push($zetaCuadro, 0);
            array_push($titCuadro, 'R' . $i + 1);
        }

        for ($i = 0; $i < $nart; $i++) {
            if ($objetivo == 'min')
                array_push($zetaCuadro, $M);
            else
                array_push($zetaCuadro, -$M);

            array_push($titCuadro, 'A' . $i + 1);
        }
        for ($i = 0; $i < $nhol; $i++) {
            array_push($zetaCuadro, 0);
            array_push($titCuadro, 'H' . $i + 1);
        }

        for ($i = 0; $i < count($zetaCuadro); $i++) {
            $zetaCuadro[$i] = $zetaCuadro[$i] * -1;
        }


        array_push($basiCuadro, 'Z');
        array_push($basiCuadro, 'VB');
        array_push($soluCuadro, 0);
        array_push($soluCuadro, 'S');

        //Estandarizar
        $variTabla = $restCuadro;
        array_push($variTabla, $zetaCuadro);
        array_push($variTabla, $titCuadro);

        $soluTabla = array();
        array_push($soluTabla, $soluCuadro);
        array_push($soluTabla, $basiCuadro);
        ?>

        <div class="cuerpoMetodo">
            <div class="primerCuadro">
                <div class="subtitulo">
                    <p>PRIMER CUADRO</p>
                </div>

                <div>
                    <?php mostrarMatriz($variTabla, $soluTabla); ?>
                </div>
            </div>

            <?php

            //CORRECION FILA Z
            for ($i = 0; $i < count($variTabla[0]); $i++) {
                $sumaArti = 0;
                for ($j = 0; $j < $cantrest; $j++) {
                    if ($soluTabla[1][$j] == 'A') {
                        $sumaArti += $variTabla[$j][$i];
                    }
                }
                if ($objetivo == 'min')
                    $variTabla[$cantrest][$i] += $M * $sumaArti;
                else
                    $variTabla[$cantrest][$i] -= $M * $sumaArti;
            }
            $sumaArti = 0;
            for ($i = 0; $i < $cantrest; $i++) {
                if ($soluTabla[1][$i] == 'A') {
                    $sumaArti += $soluTabla[0][$i];
                }
            }
            if ($objetivo == 'min')
                $soluTabla[0][$cantrest] += $M * $sumaArti;
            else
                $soluTabla[0][$cantrest] = -$M * $sumaArti;

            ?>
            <div class="segundoCuadro">
                <div class="subtitulo">
                    <p>CONVERSION DE M</p>
                </div>

                <div>
                    <?php
                    $posfilcol = posFilColum($variTabla, $soluTabla, $objetivo, $cantrest);
                    $posColum = $posfilcol['poscolum'];
                    $posFil = $posfilcol['posfil'];

                    imprimirMatriz($variTabla, $soluTabla, $posFil, $posColum);

                    ?>
                </div>
            </div>
        </div>
        <?php
            //ALGORITMO SIMPLEX METODO M
            $cen = 0;
            $contador = 0;
            do {
        ?>
        <?php


            //Algoritmo SIMPLEX

            //Volver el pivote a la unidad
            $valorPivote = $variTabla[$posFil][$posColum];
            for ($i = 0; $i < count($variTabla[0]); $i++) {

                $variTabla[$posFil][$i] /= $valorPivote;
            }
            $soluTabla[0][$posFil] /= $valorPivote;

            //Volver la columna pivote 0
            $soluTabla[1][$posFil] = $variTabla[$cantrest + 1][$posColum];
            for ($i = 0; $i < $cantrest + 1; $i++) {
                if ($i != $posFil) {
                    $valorFactorPiv = $variTabla[$i][$posColum];
                    for ($j = 0; $j < count($variTabla[0]); $j++) {
                        $variTabla[$i][$j] -= $valorFactorPiv * $variTabla[$posFil][$j];
                    }
                    $soluTabla[0][$i] -= $valorFactorPiv * $soluTabla[0][$posFil];
                }
            }

            //Verificar si se desea continuar con las iteraciones
            $cen = 0;
            for ($i = 0; $i < count($variTabla[$cantrest]); $i++) {
                if ($objetivo == 'min') {
                    if ($variTabla[$cantrest][$i] > 0) {
                        $cen = 1;
                    }
                }
                if ($objetivo == 'max') {
                    if ($variTabla[$cantrest][$i] < 0) {
                        $cen = 1;
                    }
                }
            }
            
            $contador++;
            if ($cen == 1) {
                $posfilcol = posFilColum($variTabla, $soluTabla, $objetivo, $cantrest);
                $posColum = $posfilcol['poscolum'];
                $posFil = $posfilcol['posfil'];
            ?>
                <div class="cuadroIteraciones">
                    <div class="subtitulo">
                        <p>ITERACION <?= $contador ?></p>
                    </div>

                    <div>
                        <?php
                        imprimirMatriz($variTabla, $soluTabla, $posFil, $posColum);
                        ?>
                    </div>
                </div>
        <?php
            }
        } while ($cen != 0 && $contador < $iterMaxima);
        ?>


        <!-- Verificacion de la ieracion máxima -->
        <?php
        if($cen == 0){
        ?>
            <div class="subtitulo">
                <p>
                    << RESULTADO >>
                </p>
            </div>
            <div class="cuerpoMetodo">
                <div class="finalCuadro">
                    <div class="subtitulo">
                        <p>ITERACION <?= $contador ?></p>
                    </div>

                    <div>
                        <?php mostrarMatrizResult($variTabla, $soluTabla); ?>
                    </div>
                </div>
                <div class="contResultado">
                    <div class="subtitulo">
                        <p>SOLUCIÓN</p>
                    </div>
                    <div class="cuadroMuestra">
                        <table>
                            <?php
                            for ($i = 0; $i < $cantVar; $i++) {
                                $bandera = 0;
                                $variable = "X" . $i + 1;
                                for ($j = 0; $j < count($soluTabla[1]); $j++) {
                                    if ($variable == $soluTabla[1][$j]) {
                                        $bandera = 1;
                                        $indice = $j;
                                    }
                                }
                                if ($bandera == 1) {
                                    ?>
                                        <tr>
                                            <th><label for="<?= $variable ?>">X<sub><?= $i + 1 ?></sub></label></th>
                                            <td><input type="text" id="<?= $variable ?>" value=" <?= formNum($soluTabla[0][$indice],3) ?> " readonly></td>
                                        </tr>
                                    <?php
                                } else {
                                    ?>
                                        <tr>
                                            <th><label for="<?= $variable ?>"> X<sub><?= $i + 1 ?></sub> </label></th>
                                            <td><input type="text" id="<?= $variable ?>" value='0' readonly></td>

                                        </tr>
                                    <?php
                                }
                            }
                            ?>
                            <tr>
                                <th><label for="Z"> Z </label></th>
                                <td><input type="text" id="Z" value=" <?= formNum($soluTabla[0][$cantrest],3) ?> " readonly></td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>

            <?php
        } else{
            echo "Llego a su máxima iteración: ". $iterMaxima. "<br>";
            
        }
        ?>


    </main>
    <script src="metodGranM.js"></script>
</body>

</html>