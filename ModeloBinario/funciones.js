function generarTabla(datos = null) {
    event.preventDefault();

    let cantVar = datos?.cantVar ?? parseInt(document.getElementById('cantVar').value);
    let cantRest = datos?.cantRest ?? parseInt(document.getElementById('cantRest').value);

    const contenedor = document.getElementById('tablaContainer');
    const submitDiv = document.getElementById('submitBtn');
    let tabla = '<table><tr><th></th>';

    for (let i = 0; i < cantVar; i++) {
        tabla += `<th><p>X${i + 1}</p></th>`;
    }
    tabla += `<th></th><th><p>Resultado</p></th></tr>`;

    // Z row
    tabla += `<tr><th><p>Z</p></th>`;
    for (let i = 0; i < cantVar; i++) {
        const val = datos?.variZeta?.[i] ?? '';
        tabla += `<td><input type="text" name="variZeta[${i}]" value="${val}" required></td>`;
    }
    tabla += `<td></td><td></td></tr>`;

    // Constraints
    for (let i = 0; i < cantRest; i++) {
        tabla += `<tr><th><p>Restricción ${i + 1}</p></th>`;
        for (let j = 0; j < cantVar; j++) {
            const val = datos?.variables?.[i]?.[j] ?? '';
            tabla += `<td><input type="text" name="variables[${i}][${j}]" value="${val}" required></td>`;
        }
        const cond = datos?.signo?.[i] ?? '<=';
        const resul = datos?.RHS?.[i] ?? '';
        tabla += `
            <td>
                <select name="condicion[${i}]">
                    <option value="<=" ${cond === '<=' ? 'selected' : ''}>≤</option>
                    <option value="=" ${cond === '=' ? 'selected' : ''}>=</option>
                    <option value=">=" ${cond === '>=' ? 'selected' : ''}>≥</option>
                </select>
            </td>
            <td><input type="text" name="resul[${i}]" value="${resul}" required></td>
        </tr>`;
    }

    tabla += '</table>';
    contenedor.innerHTML = tabla;
    submitDiv.innerHTML = '<button type="submit">Aplicar Modelo Binario</button>';
}


function opcionMin() {
    document.getElementById('label_min').style.backgroundColor = "#2b302f";
    document.getElementById('label_min').style.color = "#fff";
    document.getElementById('label_max').style.backgroundColor = "#fff";
    document.getElementById('label_max').style.color = "#2b302f";
}

function opcionMax() {
    document.getElementById('label_max').style.backgroundColor = "#2b302f";
    document.getElementById('label_max').style.color = "#fff";
    document.getElementById('label_min').style.backgroundColor = "#fff";
    document.getElementById('label_min').style.color = "#2b302f";
}


function pintarCasos(soluciones, mejorSolucionIndex) {
  const tabla = document.getElementById("tablaContainer");
  tabla.innerHTML = ""; // Limpiar si ya existe algo

  soluciones.forEach((sol, index) => {
    const fila = document.createElement("div");
    fila.classList.add("casilla-todas"); // Todos los casos tienen esta clase

    if (sol.esPosible) {
      fila.classList.add("casilla-posible");
    }

    if (index === mejorSolucionIndex) {
      fila.classList.add("casilla-mejor");
    }

    fila.innerHTML = `
      <p>Solución ${index + 1}: Valor = ${sol.valor}, Peso = ${sol.peso}, Items: ${sol.items.join(", ")}</p>
    `;

    tabla.appendChild(fila);
  });
}

