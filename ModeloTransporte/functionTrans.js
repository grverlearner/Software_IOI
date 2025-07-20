
function tablaTransporte(datos = null) {
    event.preventDefault();

    let numDestinos, numFuentes;
    if (datos) {
        numDestinos = datos.numDestinos;
        numFuentes = datos.numFuentes;
    } else {
        numDestinos = parseInt(document.getElementById('numDestinos').value);
        numFuentes = parseInt(document.getElementById('numFuentes').value);
    }

    const contenedor = document.getElementById('tablaContainer');
    const submitDiv = document.getElementById('submitBtn');
    let tabla = '<table><tr><th> </th>';

    for (let i = 0; i < numDestinos; i++) {
        tabla += `<th><p>Destino ${i + 1}</p></th>`;
    }
    tabla += `<th><p>Oferta</p></th></tr>`;


    // Restricciones
    for (let i = 0; i < numFuentes; i++) {
        tabla += `<tr> <th><p> Fuente ${i + 1}<p> </th>`;
        for (let c = 0; c < numDestinos; c++) {
            const valor = datos?.costo?.[i]?.[c] ?? '';
            tabla += `
            <td>
                <input type="text" name="costo[${i}][${c}]" class="cell-input" value="${valor}" required>
            </td>`;
        }

        const demanda = datos?.demanda?.[i] ?? '';
        const oferta = datos?.oferta?.[i] ?? '';
        
        tabla += `
        <td>
            <input type="text" name="oferta[${i}]" class="cell-input" value="${oferta}">
        </td>`;
        tabla += '</tr>';
    }
    
        tabla += `<tr> <th><p>Demanda<p> </th>`;
        for (let c = 0; c < numDestinos; c++) {
            const valor = datos?.demanda?.[c] ?? '';
            tabla += `
            <td>
                <input type="text" name="demanda[${c}]" class="cell-input" value="${valor}" required>
            </td>`;
        }
    

    tabla += '</table>';
    contenedor.innerHTML = tabla;
    submitDiv.innerHTML = '<button type="submit">Aplicar Modelo</button>';
}

function opcionMin() {
    console.log("Minimo");

    //ACTUALIZACION
    document.getElementById('label_min').style.backgroundColor = "#2b302f";
    document.getElementById('label_min').style.color = "#fff";
    document.getElementById('label_max').style.backgroundColor = "#fff";
    document.getElementById('label_max').style.color = "#2b302f";
    //actualizaTipoTransaccion()
}
function opcionMax() {
    console.log("Máximo");
    //ACTUALIZACION
    document.getElementById('label_max').style.backgroundColor = "#2b302f";
    document.getElementById('label_max').style.color = "#fff";
    document.getElementById('label_min').style.backgroundColor = "#fff";
    document.getElementById('label_min').style.color = "#2b302f";
    //actualizaTipoTransaccion();
}
