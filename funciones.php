<?php


function mostrarMatrizResult($matriz, $arreglo){
    ?>
    <table>
    <?php
    ?> <?php
    $numFilas = count($matriz);
    $numColumnas = count($matriz[0]);
    ?> 
    <tr>
        <th><?=$arreglo[1][$numFilas-1]?></th>
        <?php
        for($i=0;$i<$numColumnas;$i++){
            echo "<th>{$matriz[$numFilas-1][$i]}</th>";  
        }
        ?>
        <th></th>
        <th><?=$arreglo[0][$numFilas-1]?></th>
    </tr>
    <?php

    for ($i = 0; $i < $numFilas-1   ; $i++) {
        echo "<tr>";
        $celdaIzquierda = $arreglo[1][$i];
        echo "<th> $celdaIzquierda </th>";
        if($numFilas-2 != $i){
            for ($j = 0; $j < $numColumnas; $j++) {
                if(str_starts_with($celdaIzquierda, "X")){
                    ?> <td class="result"><?=formNum($matriz[$i][$j],3)?></td><?php
                }else{
                    ?> <td><?=formNum($matriz[$i][$j],3)?></td><?php
                }
                
            }
        }else{
            for ($j = 0; $j < $numColumnas; $j++) {
                ?> <td class="resultZeta"><?= formNum($matriz[$i][$j],3)?></td><?php
            }
        }
        $celdaDerecha = $celdaDerecha =formNum($arreglo[0][$i],3);
        if($numFilas-2 != $i){
            if(str_starts_with($celdaIzquierda, "X")){
                ?>
                    <td class="result"> = </td> 
                    <td class="result"><?=$celdaDerecha?></td>
                    </tr>
                <?php
            }else{
                ?>
                    <td> = </td> 
                    <td><?=$celdaDerecha?></td>
                    </tr>
                <?php
            }
        }else{
            ?>
                <td class="resultZeta"> = </td> 
                <td class="resultZeta"><?=$celdaDerecha?></td>
                </tr>
            <?php
        }
    }
    echo "</table>";
    echo "<br>";
}

function imprimirMatriz($matriz, $arreglo,$posFil,$posColum){
    ?>
    <table>
    <?php
    ?> <?php
    $numFilas = count($matriz);
    $numColumnas = count($matriz[0]);
    ?> 
    <tr>
        <th><?= $arreglo[1][$numFilas-1]?></th>
        <?php
        for($i=0;$i<$numColumnas;$i++){
            echo "<th>{$matriz[$numFilas-1][$i]}</th>";  
        }
        ?>
        <th></th>
        <th><?=$arreglo[0][$numFilas-1]?></th>
        <th>/</th>
    </tr>
    <?php

    for ($i = 0; $i < $numFilas-1   ; $i++) {
        echo "<tr>";
        $celdaIzquierda = $arreglo[1][$i];
        echo "<th> $celdaIzquierda </th>";
        
        if($numFilas-2 != $i){
            for ($j = 0; $j < $numColumnas; $j++) {
                if($j == $posColum){
                    $dividendo = $matriz[$i][$j];
                }
                if($i == $posFil || $j == $posColum){
                    if($i == $posFil && $j == $posColum){
                       ?> <td class="sombreadoFull"><?=formNum($matriz[$i][$j],3)?></td><?php 
                    }else{
                        ?> <td class="sombreado"><?=formNum($matriz[$i][$j],3)?></td><?php
                    }
                    
                }else{
                    ?> <td><?= formNum($matriz[$i][$j],3)?></td><?php
                }
                
            }
        }else{
            for ($j = 0; $j < $numColumnas; $j++) {
                ?> <td class="zeta"><?=formNum($matriz[$i][$j],3)?></td><?php
            }
        }
        $celdaDerecha = $arreglo[0][$i];
        
        if($numFilas-2 != $i){
            if($i == $posFil){
                ?>
                    <td class="sombreado"> = </td> 
                    <td class="sombreado"><?=formNum($celdaDerecha,3) ?></td>
                    <?php
                    if($dividendo == 0){
                        ?> <td class="sombreado" ><?="---"?></td> <?php
                    }else{
                        ?> <td class="sombreado" ><?=formNum($celdaDerecha/$dividendo,3)?></td> <?php
                    }
                    ?>                    
                </tr>
                <?php
            }else{
                ?>
                    <td> = </td> 
                    <td><?=formNum($celdaDerecha,3)?></td>
                    <?php
                    if($dividendo == 0){
                        ?> <td ><?="---"?></td> <?php
                    }else{
                        ?> <td><?=formNum($celdaDerecha/$dividendo,3)?></td> <?php
                    }
                    ?> 
                    
                    </tr>
                <?php
            }
            
        }else{
            ?>
                <td class="zeta"> = </td> 
                <td class="zeta"><?=formNum($celdaDerecha,3)?></td>
                </tr>
            <?php
        }
    }
    echo "</table>";
    echo "<br>";
}

function mostrarMatriz($matriz, $arreglo){
    ?>
    <table>
    <?php
    ?> <?php
    $numFilas = count($matriz);
    $numColumnas = count($matriz[0]);
    ?> 
    <tr>
        <th><?=$arreglo[1][$numFilas-1]?></th>
        <?php
        for($i=0;$i<$numColumnas;$i++){
            echo "<th>{$matriz[$numFilas-1][$i]}</th>";  
        }
        ?>
        <th></th>
        <th><?=$arreglo[0][$numFilas-1]?></th>
    </tr>
    <?php

    for ($i = 0; $i < $numFilas-1   ; $i++) {
        echo "<tr>";
        $celdaIzquierda = $arreglo[1][$i];
        echo "<th> $celdaIzquierda </th>";
        if($numFilas-2 != $i){
            for ($j = 0; $j < $numColumnas; $j++) {
                ?> <td><?=formNum($matriz[$i][$j],3)?></td><?php
            }
        }else{
            for ($j = 0; $j < $numColumnas; $j++) {
                ?> <td class="zeta"><?=formNum($matriz[$i][$j],3)?></td><?php
            }
        }
        $celdaDerecha = formNum($arreglo[0][$i],3);
        if($numFilas-2 != $i){
            ?>
                <td> = </td> 
                <td><?=$celdaDerecha?></td>
                </tr>
            <?php
        }else{
            ?>
                <td class="zeta"> = </td> 
                <td class="zeta"><?=$celdaDerecha?></td>
                </tr>
            <?php
        }
    }
    echo "</table>";
    echo "<br>";
}
function posFila(){

}
function posColumna(){
    
}
function posFilColum($matriz,$arreglo,$objetivo,$cantrest){
    $variTabla = $matriz;
    $soluTabla = $arreglo;
    $mayorMininimizar = 0;
    $menorMaximinizar = 1000000000;
    //Busqueda de la columna
    for ($i = 0; $i < count($variTabla[$cantrest]); $i++) {
        $valor = $variTabla[$cantrest][$i];
        if ($objetivo == 'min') {
            if ($mayorMininimizar < $valor) {
                $mayorMininimizar = $valor;
                $posColum = $i;
            }
        }
        if ($objetivo == 'max') {
            if ($menorMaximinizar > $valor) {
                $menorMaximinizar = $valor;
                $posColum = $i;
            }
        }
    }
    //Busqueda de la fila
    $menorCociente = 1000000;
    for ($i = 0; $i < $cantrest; $i++) {
        if ($variTabla[$i][$posColum] > 0) {
            $valor = $soluTabla[0][$i] / $variTabla[$i][$posColum];
        }

        if ($menorCociente > $valor && $valor > 0) {
            $menorCociente = $valor;
            $posFil = $i;
        }
    }
    return [
        'posfil' => $posFil,
        'poscolum' => $posColum
    ];
}
//FUNCIONES ESPECIALES GRAN M


//FUNCIONES GENERALES
function formNum ($num,$dec){ 
    $regreso = rtrim(rtrim(number_format($num, $dec, '.', ''), '0'), '.');
    return $regreso;
}

//Convertir los datos introducidos a numero [1/2 -> 0.5]
function limpiarNumeros($matriz){ //Ingresa matriz o array
    if(esMatriz($matriz)){
        for($i = 0;$i < count($matriz); $i++){
            for($j = 0; $j < count($matriz[0]);$j++){
                $valor = 0;
                $expresion = '$valor = '.$matriz[$i][$j] . ';';
                eval($expresion);
                $matriz[$i][$j] = $valor;
            }
        }
    }else{
        for($i = 0;$i < count($matriz);$i++){
            $valor = 0;
            $expresion = '$valor = '.$matriz[$i] . ';';
            eval($expresion);
            $matriz[$i] = $valor;
        }
    }
    return $matriz;
}

//Verifica si el array es una matriz
function esMatriz($array){ //Ingresa matriz o array
    foreach($array as $valor){
        if(is_array($valor)){
            return true;
        }
    }
    return false;
}