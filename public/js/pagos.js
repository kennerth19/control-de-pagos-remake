function tipo_de_pago(tasa) {

    let tipo = document.getElementById('tipo').value;

    if (tipo == 7) {
        console.log('Bazinga');
    } else {
        let div = document.getElementById(tipo);

        if (tipo != 0 && tipo != 6) {
            let input = document.getElementById(`input_${tipo}`)
            div.style.display = "grid";
            if (input.value == 0) {
                input.value = "0";
            }

        } else if (tipo == 6) {
            let div_fecha_pagomovil = document.getElementById('fecha_pagomovil').style.display = "grid";
            let input_fecha_pagomovil = document.getElementById('fecha_pagomovil_input');
            let banco_receptor = document.getElementById('banco_receptor').style.display = "grid";

            let div_pm = document.getElementById(tipo).style.display = "grid";
            let div_banco = document.getElementById('7').style.display = "grid";
            let pm = document.getElementById('input_6');
            let ref = document.getElementById('input_7');

            pm.value = "";
            ref.value = "";
            input_fecha_pagomovil.style.display = "block"
            input_fecha_pagomovil.setAttribute('required', '');
            ref.setAttribute('required', '');
        }
    }
}

function menos(item, tasas, ops) {
    console.log(ops)
    let op = document.getElementById('tipo').selectedIndex = 0;
    let div = document.getElementById(item);
    let input = document.getElementById(`input_${item}`);

    let total_0 = parseFloat(document.getElementById('input_1').value); //dolar
    let valor_b = parseFloat(document.getElementById('input_2').value / tasas); //bolivar
    let total_1 = parseFloat(document.getElementById('input_3').value); //euros
    let total_2 = parseFloat(document.getElementById('input_4').value); // zelle a
    let total_3 = parseFloat(document.getElementById('input_5').value); // zelle b
    let valor_pm = parseFloat(document.getElementById('input_6').value / tasas); //pagomovil

    let pm_req = document.getElementById('input_7'); //referencia del pagomovil

    let total_t = total_0 + total_1 + total_2 + total_3;

    let cambio = document.getElementById('cambio');

    let total = valor_b + valor_pm;

    if (ops == 1) {
        let cambio_calcular = document.getElementById('cambio_calcular');
    }

    if (item == 1) {
        sub_total_global = sub_total_global - total_0;
        if (ops == 1) {
            cambio_calcular.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_0 + total).toFixed(1) + "$";
        } else {
            cambio.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_0 + total).toFixed(1) + "$";
        }
    }

    if (item == 2) {

        let total_2 = valor_b + valor_pm - valor_b;

        sub_total_global = sub_total_global - valor_b;

        if (ops == 1) {
            cambio_calcular.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_0 + total).toFixed(1) + "$";
        } else {
            cambio.innerText = "Al cambio: " + total_2.toFixed(1) + "$ / total: " + (total_2 + total_t).toFixed(1) + "$";
        }
    }

    if (item == 3) {

        sub_total_global = sub_total_global - total_1;

        if (ops == 1) {
            cambio_calcular.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_0 + total).toFixed(1) + "$";
        } else {
            cambio.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_1).toFixed(1) + "$";
        }
    }

    if (item == 4) {
        sub_total_global = sub_total_global - total_2;

        if (ops == 1) {
            cambio_calcular.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_0 + total).toFixed(1) + "$";
        } else {
            cambio.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_2).toFixed(1) + "$";
        }
    }

    if (item == 5) {
        sub_total_global = sub_total_global - total_3;

        if (ops == 1) {
            cambio_calcular.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_0 + total).toFixed(1) + "$";
        } else {
            cambio.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_3).toFixed(1) + "$";
        }
    }

    if (item == 6) {

        document.getElementById('7').style.display = 'none';
        let fecha_pagomovil = document.getElementById('fecha_pagomovil_input').style.display = 'none';
        let banco_receptor = document.getElementById('banco_receptor').style.display = 'none';

        fecha_pagomovil_input.removeAttribute('required', '');

        let total = valor_b + valor_pm - valor_pm;

        pm_req.required = false;

        sub_total_global = sub_total_global - valor_pm;

        if (ops == 1) {
            cambio_calcular.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (total_t - total_0 + total).toFixed(1) + "$";
        } else {
            cambio.innerText = "Al cambio: " + total.toFixed(1) + "$ / total: " + (valor_b + valor_pm - valor_pm + total_t).toFixed(1) + "$";
        }
    }

    div.style.display = "none";
    input.value = 0;
}