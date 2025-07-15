<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>

<div class="titulo">
    <h1>Método Simplex Dual</h1>

</div>

<div class="container">
    <div class="panel">
        <h3>Ingresa datos iniciales:</h3>

        <div>
            <label>Tipo de problema:</label>
            <select id="tipoProblema">
                <option value="min">Minimizar</option>
                <option value="max">Maximizar</option>
            </select>
        </div>

        <div>
            <label>Cantidad de variables:</label>
            <input type="number" id="numVars" value="2">
        </div>

        <div>
            <label>Cantidad de restricciones:</label>
            <input type="number" id="numRestricciones" value="3">
        </div>

        <div>
            <button onclick="generarTabla()">Ingresar datos</button>
        </div>
    </div>


    <div class="panel matrix-container">
        <form action="dual_solver.php" method="post" id="formulario">
            <div id="tablaGenerada"></div>
        </form>
    </div>
</div>

<script>
function generarTabla() {
    const n = parseInt(document.getElementById("numVars").value);
    const m = parseInt(document.getElementById("numRestricciones").value);
    const tipo = document.getElementById("tipoProblema").value;
    const contenedor = document.getElementById("tablaGenerada");
    const form = document.getElementById("formulario");

    let html = "<h3>Función Objetivo:</h3><div class='restriccion-grid'>";
    for (let i = 0; i < n; i++) {
        html += `<input type="number" name="objetivo[]" step="any" placeholder="X${i + 1}" required>`;
    }
    html += "</div>";

    html += "<h3>Restricciones:</h3>";
    for (let i = 0; i < m; i++) {
        html += `<div class='restriccion-label'>Restricción ${i + 1}:</div>`;
        html += `<div class='restriccion-grid'>`;
        for (let j = 0; j < n; j++) {
            html += `<input type="number" name="restricciones[${i}][]" step="any" placeholder="X${j + 1}" required>`;
        }
        html += `
            <select name="signos[]" required>
                <option value="<=">&le;</option>
                <option value="=">=</option>
                <option value=">=">&ge;</option>
            </select>
            <input type="number" name="rhs[]" step="any" placeholder="RHS" required>
        `;
        html += `</div>`;
    }
    html += `<input type="submit" value="Aplicar">`;

    contenedor.innerHTML = html;

    ['tipo', 'n_vars', 'n_cons'].forEach(name => {
        const old = form.querySelector(`input[name="${name}"]`);
        if (old) old.remove();
    });

    form.insertAdjacentHTML('beforeend', `<input type="hidden" name="tipo" value="${tipo}">`);
    form.insertAdjacentHTML('beforeend', `<input type="hidden" name="n_vars" value="${n}">`);
    form.insertAdjacentHTML('beforeend', `<input type="hidden" name="n_cons" value="${m}">`);
}
</script>

<?php
require_once '../Inicio/footer.php';
?>