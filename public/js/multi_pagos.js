var contador = 0;
var id_clientes = [];
var pos = 0;
var total_a_pagar = [];
var sumatoria = 0;
var tasa = 0;

async function showMainTable() {
    try {
        await fetch("/control_de_pago_remake/public/clientes")
            .then((response) => response.json())
            .then((data) => {
                table = $("#main").DataTable({
                    responsive: true,
                    destroy: true,
                    stateSave: false,
                    lengthMenu: [
                        [6, 10, -1],
                        ["6", "10", "Todos"],
                    ],
                    language: {
                        url: "/control_de_pago_remake/public/js/lenguaje.json",
                    },
                    data: data,
                    columns: [
                        { data: "id" },
                        {
                            data: "nombre",
                            render: function (data, type, row) {
                                if (row.almacen > 0) {
                                    return `${row.nombre} (Total abonado: ${row.almacen}$)`;
                                } else {
                                    return `${row.nombre}`;
                                }
                            },
                        },
                        {
                            data: "plan",
                            render: function (data, type, row) {
                                return `${row.plan} (Valor: ${row.valor}$)`;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                if (row.congelado == 0) {
                                    return `<img src="/control_de_pago_remake/public/img/multi_pago/agregar.png" id="img_add_${row.id}" class="img_add" onclick="agregar(${row.id}, '${row.nombre}', '${row.valor}', '${row.almacen}')">`;
                                } else {
                                    return `<img src="/control_de_pago_remake/public/img/multi_pago/congelado.png" class="img_add" title="Debes descongelar el cliente primero">`;
                                }
                            },
                        },
                    ],
                });

                document.querySelector("#customSearch").addEventListener("input", function () {
                    table.search(this.value).draw();
                });
            });
    } catch (err) {
        console.log(err);
    }
}

function verificar_agregados() {
    let sup = document.getElementById("sup");
    let main_form_multipagos = document.getElementById("main_form_multipagos");
    let dia_sel = document.getElementById("recurso").innerText;

    if (tasa == 0) {
        realizar_pago.disabled = true;
        realizar_pago.title = "Debes seleccionar la fecha de pago";
        realizar_pago.setAttribute("class", "desactivado");
        realizar_pago.removeAttribute("onclick");
        sup.style.display = "block";
        sup.innerText = "Debes seleccionar la fecha de pago*";
        sel_dia();
    } else {
        if (contador == 0) {
            realizar_pago.disabled = true;
            realizar_pago.title = "Debes agregar al menos dos clientes";
            realizar_pago.setAttribute("class", "desactivado");
            realizar_pago.removeAttribute("onclick");
            sup.style.display = "block";
            sup.innerText = "Debes agregar al menos dos clientes*";
        } else if (contador == 1) {
            realizar_pago.disabled = true;
            realizar_pago.title = "Debes seleccionar otro cliente";
            realizar_pago.setAttribute("class", "desactivado");
            realizar_pago.removeAttribute("onclick");
            sup.style.display = "block";
            sup.innerText = "Debes seleccionar otro cliente*";
        } else if (contador > 1) {
            realizar_pago.disabled = false;
            realizar_pago.removeAttribute("class");
            realizar_pago.setAttribute("class", "submit_multi");
            realizar_pago.title = "Realizar pago";
            realizar_pago.setAttribute("onclick", `formulario_de_pago('${dia_sel}')`);
            sup.style.display = "none";
        }
    }
}

function fecha_seleccionada() {
    let fecha = document.getElementById("resumen");
    let dia_sel = document.getElementById("fecha_pagomovil_input_dia").value;
    let recurso = document.getElementById("recurso");
    let fecha_formateada = moment(dia_sel).locale("es").format("DD/MM/YYYY");

    recurso.innerText = dia_sel;

    fecha.innerText = `Resumen y fecha ${fecha_formateada}`;

    devolver_tasa(dia_sel);

    swal.close();
}

async function verificar_multipago() {
    console.log("Verificando fecha...");
    const fecha = document.getElementById("fecha_pagomovil_input_dia").value;

    const response = await fetch(`/control_de_pago_remake/public/verificar_multipago/${fecha}`)
        .then((response) => response.json())
        .then((data) => {
            let alerta_tasa = document.getElementById("alerta_tasa");
            let correcta_tasa = document.getElementById("correcta_tasa");
            let desactivado = document.getElementById("desactivado");

            if (data) {
                console.log("fecha valida");
                alerta_tasa.style.display = "none";
                correcta_tasa.style.display = "block";
                desactivado.removeAttribute("disabled");
                desactivado.setAttribute("class", "submit_multi");
            } else {
                console.log("Fecha invalida");
                alerta_tasa.style.display = "block";
                correcta_tasa.style.display = "none";
                desactivado.setAttribute("disabled", "");
                desactivado.setAttribute("class", "desactivado");
            }
        });
}

async function sel_dia(hoy) {
    let max = new Date().toISOString().split("T")[0];

    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        allowOutsideClick: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: `
            <div class="sel_date">
                <h1>Seleccione la fecha del pagomovil<h1>
                <div class="pay_date">
                    <input type="date" id="fecha_pagomovil_input_dia" value="${hoy}" max="${max}" onchange="verificar_multipago()" class="input fecha_pagomovil_aux" style="font-size: large; display: grid;">
                    <button id="desactivado" class="desactivado" title="seleccionar fecha del pagomovil" onclick="fecha_seleccionada()" disabled>Seleccionar</button>
                </div>
                <p id="alerta_tasa">La fecha seleccionada no tiene tasa registrada*</p>
                <p id="correcta_tasa">La fecha seleccionada es valida!*</p>
            </div>`,
        focusConfirm: false,
    });

    setTimeout(() => {
        verificar_multipago();
    }, 1500);
}

function agregar(id, nombre, valor, abono) {
    id_clientes.push(id);

    let valor_abono = parseFloat(valor - abono).toFixed(2);

    total_a_pagar.push(parseFloat(valor_abono));

    let realizar_pago = document.getElementById("realizar_pago");
    let agregados = document.getElementById("agregados");
    let img_add_id = document.getElementById(`img_add_${id}`);

    agregados.innerHTML += `<div class="item_agregado" id="item_agregado_${id}"><p>${id}</p><p style="word-break: break-all;">${nombre}</p><p>${
        valor - abono
    }$</p><img src="/control_de_pago_remake/public/img/multi_pago/menos.png" title="Eliminar de la lista" class="img_menos" onclick="quitar(${id}, '${nombre}', '${valor}', '${abono}')"></div>`;
    img_add_id.src = "/control_de_pago_remake/public/img/multi_pago/agregado.png";
    img_add_id.title = "Cliente agregado";
    img_add_id.removeAttribute("onclick");

    contador++;

    sumatoria = 0;

    for (let i = 0; i < total_a_pagar.length; i++) {
        sumatoria += total_a_pagar[i];
    }

    verificar_agregados();
}

function quitar(id, nombre, valor, abono) {
    let realizar_pago = document.getElementById("realizar_pago");
    let img_add_id = document.getElementById(`img_add_${id}`);
    document.getElementById(`item_agregado_${id}`).remove();
    pos = id_clientes.indexOf(id);
    id_clientes.splice(pos, 1);
    contador--;

    verificar_agregados();

    let restar = valor - abono;

    img_add_id.src = "/control_de_pago_remake/public/img/multi_pago/agregar.png";
    img_add_id.title = "Agregar cliente";
    img_add_id.setAttribute("onclick", `agregar(${id}, '${nombre}', '${valor}', '${abono}')`);

    let index = total_a_pagar.indexOf(parseFloat(restar));
    total_a_pagar.splice(index, 1);

    sumatoria = 0;

    for (let i = 0; i < total_a_pagar.length; i++) {
        sumatoria += total_a_pagar[i];
    }
}

async function devolver_tasa(fecha) {
    try {
        let fecha = document.getElementById("recurso").innerText;
        await fetch(`/control_de_pago_remake/public/consultar_tasa_fecha/${fecha}`)
            .then((response) => response.json())
            .then((data) => {
                tasa = data.tasa;
            });
    } catch (err) {
        console.log(err);
    }
}

async function formulario_de_pago(dat) {
    multivar = true;
    var bancos = "";

    await $.get("/control_de_pago_remake/public/bancos", function (response) {
        response.forEach(function (item) {
            bancos += `<option value="${item["value"]}">${item["banco"]}</option>`;
        });
    });


    valor_formulario = sumatoria;

    console.log(dat);

    let max = new Date().toISOString().split("T")[0];

    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: `<form action="/control_de_pago_remake/public/pagar/multi_pago" method="POST" id="formulario_de_pago" onsubmit="validar(event)" class="nuevo_pago">
        <h1 class="titulo">Registrar pago</h1>
        <input type="hidden" name="clientes" value="${id_clientes.toString()}">
        <p>El total a pagar es: ${parseFloat(sumatoria)}$</p>

        <input type="hidden" name="fecha_pagomovil" value="${dat}">

        <div class="tipo_de_pago">
            <div class="item" id="1"> 
                <input type="number" name="dolar" id="input_1" class="input input_type input_element" placeholder="Dolares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_1', ${parseFloat(valor_formulario).toFixed(2)},${tasa})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(1,${tasa})">
                <p>$</p>
            </div>
            
            <div class="item" id="2">
                <input type="number" name="bolivar" id="input_2" class="input input_type input_element" placeholder="Bolivares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_2',${(parseFloat(valor_formulario) * parseFloat(tasa)).toFixed(2)},${tasa})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(2,${tasa})">
                <p>B</p>
            </div>

            <div class="item" id="3">
                <input type="number" name="euro" id="input_3" class="input input_type input_element" placeholder="Euros" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_3', ${parseFloat(valor_formulario).toFixed(2)},${tasa})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(3,${tasa})">
                <p>€</p>
            </div>

            <div class="item" id="4">
            <input type="number" name="zelle_v" id="input_4" class="input input_type input_element" placeholder="Zelle Vladimir" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_4', ${parseFloat(valor_formulario).toFixed(2)},${tasa})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(4,${tasa})">
                <p>Z V</p>
            </div>

            <div class="item" id="5">
                <input type="number" name="zelle_j" id="input_5" class="input input_type input_element" placeholder="Zelle Jesus Millan" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_5', ${parseFloat(valor_formulario).toFixed(2)},${tasa})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(5,${tasa})">
                <p>Z J</p>
            </div>
        </div>

        <div class="item_pm div_pagomovil" id="6">
            <input type="number" name="pagomovil" id="input_6" class="input input_type input_element" placeholder="Monto" value="" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa}), verificar()" onkeyup="cambio(${tasa}), verificar()" ondblclick="agg_cantidad('input_6',${(parseFloat(valor_formulario) * parseFloat(tasa)).toFixed(2)},${tasa}), verificar()" required>
            <input type="text" class="input input_type input_element" placeholder="Referencia" pattern="^[0-9]{12}$" value="" name="referencia" id="input_7" style="display: block;" title="debe ingresar un numero de 12 dígitos" onkeyup="validar_referencia(1)" required>
            <p>PM</p>
        </div>
        <div id="fecha_pagomovil"> 
            Emisor
            <select name="banco" id="7" class="input option item_pm_banco input_element" style="display: grid;" required>
                ${bancos}
            </select>
        </div>
            Receptor
            <select id="banco_receptor" class="input" name="banco_receptor" style="margin: 0; margin-top: 5px;">
                <option value="1">Provincial</option>
                <option value="2">Banesco</option>
                <option value="3">Venezuela</option>
                <option value="4">Punto bicentenario</option>
                <option value="5">Punto Venezuela</option>
                <option value="6">Punto caroni</option>
                <option value="7">Biópago (avícola)</option>
            </select>
        </div>
            <p id="cambio">Tasa: ${tasa}bs / Al cambio: 0.0$ / Total : 0.0$</p>
        <button id="submit" class="desactivado" title="Debes pagar la cantidad especificada." disabled>Registrar pago</button>
        <small id="mensaje" style="display: none;">debes especificar algún monto*</small>
    </form>`,
        focusConfirm: false,
    });
    
    bancos = "";
}

function verificar() {
    let entrada = document.getElementById("input_6").value;
    let tasa = parseFloat(document.getElementById("tasa").innerText);
    let dolares_al_cambio = entrada / tasa;
    let realizar_pago_submit = document.getElementById("submit");

    console.log(`Sumatoria: ${sumatoria} y input: ${dolares_al_cambio + 1}`);

    if (dolares_al_cambio + 1 >= sumatoria) {
        realizar_pago_submit.removeAttribute("class");
        realizar_pago_submit.setAttribute("class", "submit_multi");
        realizar_pago_submit.title = "Valido para pasar pago!";
        realizar_pago_submit.disabled = false;
    } else {
        realizar_pago_submit.removeAttribute("class");
        realizar_pago_submit.setAttribute("class", "desactivado");
        realizar_pago_submit.title = "No valido para pasar pago!";
        realizar_pago_submit.disabled = true;
    }
}
