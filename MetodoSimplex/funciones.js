function generarTabla(datos = null) {
    event.preventDefault();

    let cantVar, cantRest;

    if (datos) {
        cantVar = datos.cantVar;
        cantRest = datos.cantRest;
    } else {
        cantVar = parseInt(document.getElementById('cantVar').value);
        cantRest = parseInt(document.getElementById('cantRest').value);
    }

    const contenedor = document.getElementById('tablaContainer');
    const submitDiv = document.getElementById('submitBtn');

    let tabla = '<table><tr><th> </th>';

    for (let i = 0; i < cantVar; i++) {
        tabla += `<th><p>X${i + 1}</p></th>`;
    }

    tabla += `<th></th><th><p>Resultado</p></th></tr>`;

    tabla += `<tr><th><p> Z </p></th>`;
    for (let i = 0; i < cantVar; i++) {
        const valor = datos?.variZeta?.[i] ?? '';
        tabla += `
        <td>
            <input type="text" name="variZeta[${i}]" class="cell-input" value="${valor}" required>
        </td>`;
    }
    tabla += `<td></td><td></td></tr>`;

    for (let i = 0; i < cantRest; i++) {
        tabla += `<tr><th><p> Restricción ${i + 1}<p></th>`;
        
        for (let j = 0; j < cantVar; j++) {
            const valor = datos?.vari?.[i]?.[j] ?? '';
            tabla += `
            <td>
                <input type="text" name="variables[${i}][${j}]" class="cell-input" value="${valor}" required>
            </td>`;
        }

        const cond = datos?.condicion?.[i] ?? '<=';
        const resul = datos?.resul?.[i] ?? '';

        tabla += `
        <td class="condiciones">
            <select name="condicion[${i}]">
                <option value="<=" ${cond === '<=' ? 'selected' : ''}> ≤ </option>
                <option value="==" ${cond === '==' ? 'selected' : ''}> = </option>
                <option value=">=" ${cond === '>=' ? 'selected' : ''}> ≥ </option>
            </select>
        </td>
        <td>
            <input type="text" name="resul[${i}]" class="cell-input" value="${resul}" required>
        </td>
        </tr>`;
    }

    tabla += '</table>';
    contenedor.innerHTML = tabla;

    submitDiv.innerHTML = '<button type="submit">Aplicar Método Simplex</button>';
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
