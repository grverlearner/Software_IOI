
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #2c2f33;
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 24px;
    }

    .container {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin: 30px auto;
        max-width: 1200px;
        flex-wrap: wrap;
    }

    .panel {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background-color: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0,0,0,0.15);
        flex: 1;
        min-width: 300px;
    }

    h3 {
        margin-top: 0;
        background: #2c2f33;
        color: white;
        padding: 10px;
        border-radius: 8px 8px 0 0;
        text-align: center;
    }

    .restriccion-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
        gap: 10px;
        margin-bottom: 10px;
    }

    .restriccion-label {
        font-weight: bold;
        margin-top: 15px;
    }

    input[type="number"], select, textarea {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 100%;
        box-sizing: border-box;
    }

    button, input[type="submit"] {
        background-color: #2c2f33;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
    }

    th, td {
        border: 1px solid #aaa;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: #2c2f33;
        color: white;
    }

    .node-optimal {
        background-color: lightgreen;
    }

    .node-pruned {
        background-color: lightcoral;
    }
</style>

<?php
require_once '../inicio/header.php';
require_once '../Inicio/sidebar.php';
?>


<div class="titulo">
    <h1>Ramificación y Acotación - Programación Entera</h1>
</div>


<div class="container">
    <div class="panel">
        <h3>Ingresa datos iniciales:</h3>

        <div>
            <label>Tipo de problema:</label>
            <select id="tipoProblema">
                <option value="max">Maximizar</option>
                <option value="min">Minimizar</option>
            </select>
        </div>

        <div>
            <label>Cantidad de variables:</label>
            <input type="number" id="numVars" value="2">
        </div>

        <div>
            <label>Cantidad de restricciones:</label>
            <input type="number" id="numRestricciones" value="2">
        </div>

        <div>
            <button onclick="generarTabla()">Ingresar datos</button>
        </div>
    </div>


    <div class="panel matrix-container">
        <form action="ramificacion.php" method="post" id="formulario">
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
