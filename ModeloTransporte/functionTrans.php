<?php

function viewTable($demanda,$oferta,$costo,$asig) {
    ?>
    <div class="cont-table">
        <table class="r-table">
            <tr>
                <th></th>
                <?php for($i = 0; $i < count($demanda); $i++){ ?>
                    <th> Destino <?=$i+1?></th>   
                <?php } ?>
                <th>Oferta</th>
            </tr>
            <?php for($i = 0; $i < count($oferta); $i++){ ?>
                <tr>
                    <th>Fuente <?=$i+1?> </th>
                    <?php for($j = 0; $j < count($demanda); $j++){ ?>
                        <td class="celda-cost">
                            <div class="td-cost"><?=$costo[$i][$j]?></div>
                            
                            <div class="td-asig"><?=$asig[$i][$j]?></div>
                        </td>
                    <?php } ?>
                    <td><?=$oferta[$i]?></td>
                </tr>
            <?php } ?>
            <tr>
                <th>Demanda</th>
                <?php for($i = 0; $i < count($demanda); $i++){ ?>
                    <td><?=$demanda[$i]?></td>   
                <?php } ?>
                <td></td>
            </tr>

        </table>
    </div>
    <?php
}


?>