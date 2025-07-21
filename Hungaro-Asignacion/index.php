<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>

<!-- <link rel="stylesheet" href="style.css"> -->

<div>
    <h2>Problema de Asignacion</h2>

    <?php if (!isset($_POST['tamaño'])): ?>
        <form method="post" action="index.php">
            <label>Seleccione el tamaño de la matriz:</label>
            <input type="number" name="tamaño" min="2" max="10" required>
            <!-- <input type="submit" value="Crear"> -->
            <button type="submit">Crear</button>
        </form>
    <?php else:
        $n = intval($_POST['tamaño']);
    ?>
        <form method="post" action="hungaro.php">
            <input type="hidden" name="tamaño" value="<?= $n ?>">
            <table>
                <?php for ($i = 0; $i < $n; $i++): ?>
                    <tr>
                        <?php for ($j = 0; $j < $n; $j++): ?>
                            <td><input type="number" name="matriz[<?= $i ?>][<?= $j ?>]" required></td>
                        <?php endfor; ?>
                    </tr>
                <?php endfor; ?>
            </table><br>
            <button type="submit" name="resolver">Resolver</button>
        </form>
    <?php endif; ?>
</div>
<?php
require_once '../Inicio/footer.php';
?>


