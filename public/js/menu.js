//Variables
let title = document.querySelector("title");
let nombre = title.innerHTML;
title.innerHTML = "Menu/Resumen " + nombre;
let sub_total_global = 0;
var contador_de_referencias = 0;
var multivar = false;
//Variables

function editar_servicio() {
    let servicios = document.getElementsByClassName("servicio_item");
    let valor = document.getElementById("select_servicios").value;

    for (i = 0; i <= servicios.length - 1; i++) {
        let div = document.getElementById("div_" + i);
        if (valor == "nulo") {
            div.style.display = "none";
        } else {
            div.style.display = "none";
            servicios[valor].style.display = "block";
        }
    }
}

$(".modificar_cliente").submit(function (e) {
    e.preventDefault();
    Swal.fire({
        title: "¿Estas seguro?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Editar",
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});

function switch_menu(val) {
    let resumen = document.getElementById("resumen");
    let servicios = document.getElementById("servicios");
    let facturacion = document.getElementById("facturacion_menu");

    let sec_1 = document.getElementById("sec_1");
    let sec_2 = document.getElementById("sec_2");
    let sec_3 = document.getElementById("sec_3");

    sec_1.style.display = "none";
    sec_2.style.display = "none";
    sec_3.style.display = "none";

    if (val == 1) {
        title.innerHTML = "";
        title.innerHTML = "Menu/Resumen " + nombre;
        resumen.style.backgroundColor = "#fff";
        resumen.style.color = "#000";

        servicios.style.backgroundColor = "#000";
        servicios.style.color = "#fff";
        facturacion.style.backgroundColor = "#000";
        facturacion.style.color = "#fff";

        sec_1.style.display = "grid";
        sec_2.style.display = "none";
        sec_3.style.display = "none";
    } else if (val == 2) {
        title.innerHTML = "";
        title.innerHTML = "Menu/Servicios " + nombre;
        servicios.style.backgroundColor = "#fff";
        servicios.style.color = "#000";

        resumen.style.backgroundColor = "#000";
        resumen.style.color = "#fff";
        facturacion.style.backgroundColor = "#000";
        facturacion.style.color = "#fff";

        sec_1.style.display = "none";
        sec_2.style.display = "grid";
        sec_3.style.display = "none";
    } else if (val == 3) {
        title.innerHTML = "";
        title.innerHTML = "Menu/Facturacion " + nombre;
        facturacion.style.backgroundColor = "#fff";
        facturacion.style.color = "#000";

        resumen.style.backgroundColor = "#000";
        resumen.style.color = "#fff";
        servicios.style.backgroundColor = "#000";
        servicios.style.color = "#fff";

        sec_1.style.display = "none";
        sec_2.style.display = "none";
        sec_3.style.display = "grid";
    } else if (val == 4) {
        title.innerHTML = "";
        title.innerHTML = "Menu/Tickets " + nombre;

        resumen.style.backgroundColor = "#000";
        resumen.style.color = "#fff";
        facturacion.style.backgroundColor = "#000";
        facturacion.style.color = "#fff";
        servicios.style.backgroundColor = "#000";
        servicios.style.color = "#fff";

        sec_1.style.display = "none";
        sec_2.style.display = "none";
        sec_3.style.display = "none";
    }
}

function borrar_cliente(id, cuenta, servicio_id) {
    if (cuenta == 1) {
        Swal.fire({
            title: "¿Eliminar cliente?",
            text: "Se generara una regla en el servidor",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Eliminar",
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(
                    "/control_de_pago_remake/public/clientes/eliminar/" + id,
                    { method: "DELETE" }
                )
                    .then(function (response) {
                        alert("Cliente eliminado. :c");

                        setTimeout(function () {
                            window.close();
                        }, 500);
                    })
                    .catch(function (error) {
                        alert("Error intente de nuevo");
                    });
            }
        });
    } else {
        Swal.fire({
            title: "Este menu es peligroso, ¿seguir?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            cancelButtonText: "No",
            confirmButtonText: "Si",
        }).then((result) => {
            if (result.isConfirmed) {
                $.get(
                    `/control_de_pago_remake/public/clientes/servicios/${servicio_id}`,
                    function (response) {
                        const { value: formValues } = Swal.fire({
                            showConfirmButton: false,
                            background: "rgba(0, 0, 0, 0)",
                            heightAuto: false,
                            customClass: {
                                container: "container_modal",
                                htmlContainer: "contenedor_add",
                            },
                            html: `<form action="/control_de_pago_remake/public/clientes/servicios_del" method="GET" class="nuevo_pago">
                    <h1 class="titulo">Antes de borrar, seleccione el nuevo servicio principal o elimine el cliente con todos sus servicios</h1>
                    <input type="hidden" name="identificacion" value="${id}" required>
                    <input type="hidden" name="ser_id" value="${servicio_id}" required>
                    <select name="servicio" class="input option">
                        <optgroup label="Servicios">
                            ${response}
                        </optgroup>
                        <optgroup label="CUIDADO!">
                            <option value="todo"><b>Eliminar todos los servicios</b></option>
                        </optgroup>
                    </select><br>
                    
                    <input type="submit" class="input_submit" value="Eliminar y asignar servicio principal">
                </form>`,
                            focusConfirm: false,
                        });
                    }
                );
            }
        });
    }
}

function agg_cantidad(id, cantidad, tasa) {
    let input = document.getElementById(id);
    input.value = cantidad;
    cambio(tasa);
}

function sumar_comprobacion() {
    return (contador_de_referencias = contador_de_referencias + 1);
}

function reiniciar_comprobacion() {
    return (contador_de_referencias = 0);
}

function sumar_comprobacion_ver() {
    return contador_de_referencias;
}

//ver como hacer que funcione xD
function fecha_pm() {
    let fecha = new Date();
    let fecha_formateada = moment(fecha).locale("es").format("Y-m-d");

    document.getElementById("fecha_pagomovil_input").value = fecha_formateada;
}

async function validar_referencia(id) {
    let datos_referencia = document.getElementById("input_7").value; //referencia.
    let datos_banco = document.getElementById("7").value; // banco.
    let pertenece = "";

    if (datos_referencia.length < 12) {
        console.log(`Referencia incompleta, faltan ${12 - datos_referencia.length} dígitos`);
    } else if (datos_referencia.length == 12) {
        console.log(`Referencia completa`);
        await fetch(`/control_de_pago_remake/public/referencias/0`)
            .then((response) => response.json())
            .then(function (response) {
                response.forEach(function (item) {
                    if (item["referencia"] == datos_referencia) {
                        pertenece = `- Referencia #: ${item["referencia"]}.<br>- Banco: ${item["banco"]}.<br>- Cliente: ${item["cliente"]}.<br>- Fecha: ${item["fecha_pago_movil"]}.`;
                        sumar_comprobacion();
                    }
                });

                let suma = sumar_comprobacion_ver();

                if (suma > 0) {
                    Swal.fire({
                        icon: "error",
                        title: `Referencia duplicada!<br><br>${pertenece}`,
                        showConfirmButton: false,
                        timer: 8000,
                        timerProgressBar: true,
                    });
                    setTimeout(function () {
                        document.getElementById(`pago_${id}`).click();
                    }, 8000);
                } else if (datos_referencia == "000000000000") {
                    Swal.fire({
                        icon: "error",
                        title: `Referencia incorrecta!`,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                    });
                } else {
                    console.log("Referencia correcta!");
                }

                reiniciar_comprobacion();
            });
    } else if (datos_referencia.length > 12) {
        console.log(`Referencia completa + dígitos de mas`);
    }
}

function validar(event) {
    let total_0 = parseFloat(document.getElementById("input_1").value); //dolar
    let valor_b = parseFloat(document.getElementById("input_2").value); //bolivar
    let total_1 = parseFloat(document.getElementById("input_3").value); //euros
    let total_2 = parseFloat(document.getElementById("input_4").value); // zelle a
    let total_3 = parseFloat(document.getElementById("input_5").value); // zelle b
    let valor_pm = parseFloat(document.getElementById("input_6").value); //pagomovil
    let valor_ref = document.getElementById("input_7").value;

    let mensaje = document.getElementById("mensaje");
    let submit_button = document.getElementById("submit");

    total = total_0 + valor_b + total_1 + total_2 + total_3 + valor_pm;

    if (total <= 0) {
        event.preventDefault();
        mensaje.style.display = "block";
    } else {
        setTimeout(() => {
            Swal.fire({
                icon: "success",
                title: `Pago realizado con éxito!`,
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
            });

            window.location.reload();
        }, 1500);

        console.log("Valido para pasar pago!");
        submit_button.setAttribute("disabled", "");
    }
}

function pagar(id, tasa, ticket, op, fecha) {
    $.get(
        `/control_de_pago_remake/public/datos_estructurados_pago/${id}`,
        function (response) {
            // variables para el formulario
            let nombre_formulario = response[0].nombre;
            let corte_formulario = moment(response[0].corte)
                .locale("es")
                .format("DD [de] MMMM [de] YYYY");
            let telefono_formulario = response[0].tlf;
            let cedula_formulario = response[0].cedula;
            let congelado_formulario = response[0].congelado;
            let valor_formulario = response[1].valor;
            let almacen_formulario = response[0].almacen;

            let multi_pago = "";

            if (op == 1) {
                multi_pago = `<option value="7" style="display:none;">Multiples pagos</option>`;
            }

            if (congelado_formulario == 0) {
                const { value: formValues } = Swal.fire({
                    showConfirmButton: false,
                    background: "rgba(0, 0, 0, 0)",
                    heightAuto: false,
                    customClass: {
                        container: "container_modal",
                        htmlContainer: "contenedor_add",
                    },
                    html: `<form action="/control_de_pago_remake/public/menu/pagar/${id}" method="POST" id="formulario_de_pago" onsubmit="validar(event)" class="nuevo_pago">
                <h1 class="titulo">Registrar pago</h1>
                <div class="servicio_small">
                    <p>Nombre: ${nombre_formulario}<br>Cedula: ${cedula_formulario}<br>Corte: ${corte_formulario}</p>
                    <small>(La factura sera emitida en ${ticket})
                        ¿imprimir factura?
                        <select name="imprimir_ticket" class="input option">
                            <option value="1">Si</option>
                            <option value="0">No</option>
                        </select>       
                    </small>
                </div>
                <h3 id="alerta_monto_v">VALIDO PARA SUBIR EL MES!</h3>
                <h3 id="alerta_monto">ALERTA! MONTO POR DEBAJO DE LA TASA!</h3>
                <p>El costo del plan es: ${parseFloat(valor_formulario).toFixed(
                    0
                )}$ / total abonado: ${parseFloat(almacen_formulario).toFixed(
                        2
                    )}$ <br> <span style="font-size: 15px"> monto a pagar: <span id="monto_a_pagar">${parseFloat(
                        valor_formulario - almacen_formulario
                    ).toFixed(2)}</span>$ (Al cambio: ${(
                        parseFloat(valor_formulario - almacen_formulario) *
                        parseFloat(tasa)
                    ).toFixed(2)}Bs.)<span></p>
                <select name="" id="tipo" class="input option" onchange="tipo_de_pago(${tasa})">
                    <option value="">Seleccione tipo de pago</option>
                    <option value="1">Dolares</option>
                    <option value="2">Bolivares</option>
                    <option value="3">Euros</option>
                    <option value="4">Zelle Vladimir</option>
                    <option value="5">Zelle Jesus Millan</option>
                    <option value="6">Pagomovil</option>
                </select>       
        
                <div class="tipo_de_pago">
                    <div class="item" id="1"> 
                        <input type="number" name="dolar" id="input_1" class="input input_type input_element" placeholder="Dolares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_1', ${parseFloat(
                        valor_formulario - almacen_formulario
                    ).toFixed(2)},${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(1,${tasa})">
                        <p>$</p>
                    </div>
                    
                    <div class="item" id="2">
                        <input type="number" name="bolivar" id="input_2" class="input input_type input_element" placeholder="Bolivares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_2',${(
                        parseFloat(valor_formulario - almacen_formulario) *
                        parseFloat(tasa)
                    ).toFixed(2)},${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(2,${tasa})">
                        <p>B</p>
                    </div>
        
                    <div class="item" id="3">
                        <input type="number" name="euro" id="input_3" class="input input_type input_element" placeholder="Euros" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_3', ${parseFloat(
                        valor_formulario - almacen_formulario
                    ).toFixed(2)},${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(3,${tasa})">
                        <p>€</p>
                    </div>
        
                    <div class="item" id="4">
                    <input type="number" name="zelle_v" id="input_4" class="input input_type input_element" placeholder="Zelle Vladimir" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_4', ${parseFloat(
                        valor_formulario - almacen_formulario
                    ).toFixed(2)},${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(4,${tasa})">
                        <p>Z V</p>
                    </div>
        
                    <div class="item" id="5">
                        <input type="number" name="zelle_j" id="input_5" class="input input_type input_element" placeholder="Zelle Jesus Millan" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_5', ${parseFloat(
                        valor_formulario - almacen_formulario
                    ).toFixed(2)},${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(5,${tasa})">
                        <p>Z J</p>
                    </div>
                </div>
        
                <div class="item_pm" id="6">
                    <input type="number" name="pagomovil" id="input_6" class="input input_type input_element" placeholder="Monto" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_6',${(parseFloat(valor_formulario - almacen_formulario) * parseFloat(tasa)).toFixed(2)},${tasa})" required>
                    <input type="text" class="input input_type input_element" placeholder="Referencia" pattern="^[0-9]{12}$" value="000000000000" name="referencia" id="input_7" style="display: block;" title="debe ingresar un numero de 12 dígitos" onkeyup="validar_referencia(${id})">
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(6,${tasa})">
                    <p>PM</p>
                </div>
                <div id="fecha_pagomovil"> 
                <input type="date" id="fecha_pagomovil_input" value="${fecha}" onchange="cambiar_tasa_pago('${id}', '${ticket}', 1)" class="input" style="display: none;" name="fecha_pago_movil">
                <select name="banco" id="7" class="input option item_pm_banco input_element" style="display: none;" onchange="validar_referencia(${id})" required>
                    <option value="provincial">Provincial</option>
                    <option value="venezuela">Banco de Venezuela</option>
                    <option value="banca amiga">Banca amiga</option>
                    <option value="mercantil">Mercantil</option>
                    <option value="bancaribe">Bancaribe</option>
                    <option value="banesco">Banesco</option>
                    <option value="bnc">BNC</option>
                    <option value="banfanb">BANFANB</option>
                    <option value="bangente">BANGENTE</option>
                    <option value="banplus">BANPLUS</option>
                    <option value="bfc">BFC</option>
                    <option value="sofitasa">SOFITASA</option>
                    <option value="BDC">Venezolana de Credito</option>
                    <option value="bicentenario">BICENTENARIO</option>
                    <option value="mi_banco">Mi Banco</option>
                    <option value="plaza">Plaza</option>
                    <option value="crecer">bancrecer</option>
                    <option value="plaza">Plaza</option>
                    <option value="100x100">100 X 100 Banco</option>
                    <option value="activo">Banco Activo</option>
                    <option value="agricola">Banco Agricola</option>
                    <option value="caroni">Caroni</option>
                    <option value="sur">Banco del Sur</option>
                    <option value="tesoro">Tesoro</option>
                    <option value="exterior">Exterior</option>
                    <option value="otros">Otros</option>
                </select>
                </div>
                <select id="banco_receptor" class="input" name="banco_receptor" style="display: none; margin-top: 6px;">
                    <option value="1">Provincial</option>
                    <option value="2">Banesco</option>
                    <option value="3">Venezuela</option>
                    <option value="4">Punto bicentenario</option>
                    <option value="5">Punto Venezuela</option>
                    <option value="6">Punto caroni</option>
                    <option value="7">Biópago (avícola)</option>
                </select>
                    </div>
                    <p id="cambio">tasa: ${tasa}bs / al cambio: 0.0$ / total : 0.0$</p>
                <button id="submit" class="input_submit" >Registrar pago</button>
                <small id="mensaje" style="display: none;">debes especificar algún monto*</small>
            </form>`,
                    focusConfirm: false,
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Debe DESCONGELAR el cliente primero.",
                    showConfirmButton: false,
                });
            }
        }
    );
}

function cambio(tasa) {
    let parrafo = document.getElementById("cambio");
    let pm = parseFloat(document.getElementById("input_6").value / tasa);
    let bs = parseFloat(document.getElementById("input_2").value / tasa);

    //Input para los totales

    let total_0 = parseFloat(document.getElementById("input_1").value); //Dolares
    let total_1 = parseFloat(document.getElementById("input_3").value); //Euros
    let total_2 = parseFloat(document.getElementById("input_4").value); //Zelle V
    let total_3 = parseFloat(document.getElementById("input_5").value); //Zelle J

    if (isNaN(total_0) || total_0 == "") {
        total_0 = 0;
    }

    if (isNaN(total_1) || total_1 == "") {
        total_1 = 0;
    }

    if (isNaN(total_2) || total_2 == "") {
        total_2 = 0;
    }

    if (isNaN(total_3) || total_3 == "") {
        total_3 = 0;
    }

    sub_total_global = pm + bs + total_0 + total_1 + total_2 + total_3;

    if (!multivar) {
        let monto_aux = parseFloat(document.getElementById("monto_a_pagar").innerText);

        let alerta_monto = document.getElementById("alerta_monto");
        let alerta_monto_v = document.getElementById("alerta_monto_v");

        if (monto_aux >= sub_total_global + 0.001) {
            alerta_monto.style.display = "block";
            alerta_monto_v.style.display = "none";
        } else {
            alerta_monto.style.display = "none";
            alerta_monto_v.style.display = "block";
        }
    }

    parrafo.innerText =`tasa: ${tasa}bs / al cambio: ${(pm + bs).toFixed(2)}$ / total: ${(pm + bs + total_0 + total_1 + total_2 + total_3).toFixed(2)}$`;
}

function nuevo_servicio(servicio_id) {
    //Nuevo servicio
    planes = "";
    servidores = "";
    $.get("/control_de_pago_remake/public/data", function (response) {
        planes += '<optgroup label="Inalambrico">';
        response[0].forEach(function (item) {
            if (item["tipo"] == 0) {
                planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
            }
        });
        planes += "</optgroup>";

        planes += '<optgroup label="Fibra">';
        response[0].forEach(function (item) {
            if (item["tipo"] == 1) {
                planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
            }
        });
        planes += "</optgroup>";

        planes += '<optgroup label="Empresariales">';
        response[0].forEach(function (item) {
            if (item["tipo"] == 2) {
                planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
            }
        });
        planes += "</optgroup>";

        planes += '<optgroup label="Planes especiales">';
        response[0].forEach(function (item) {
            if (item["tipo"] == 3) {
                planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
            }
        });
        planes += "</optgroup>";

        planes += '<optgroup label="Noria">';
        response[0].forEach(function (item) {
            if (item["tipo"] == 4) {
                planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
            }
        });
        planes += "</optgroup>";

        response[1].forEach(function (item) {
            servidores += `<option value="${item["id"]}">${item["nombre_de_servidor"]}</option> \n`;
        });
        planes += "</optgroup>";

        const { value: formValues } = Swal.fire({
            showConfirmButton: false,
            background: "rgba(0, 0, 0, 0)",
            heightAuto: false,
            customClass: {
                container: "container_modal",
                htmlContainer: "contenedor_add",
                popup: "popup_custom",
            },
            html: `<form action="/control_de_pago_remake/public/clientes/add/ser" method="POST" class="nuevo_servicio_sub" style="padding: 25px 15px 25px 0px;">
                <div class="">
                    <h1 class="titulo_servicio">Registrar Servicio</h1>
                    <div  class="nuevo_registro" id="datos_cliente">
                        <div class="item_input servicio">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" class="input" name="nombre" id="" placeholder="Nombre del servicio" required>
                            <input type="hidden" class="input" name="servicio_id" value="${servicio_id}" required>
                        </div>

                        <div class="item_input servicio">
                            <i class="fa-solid fa-phone"></i>
                            <input type="number" class="input" name="telefono" id="" placeholder="Numero telefónico" required>
                        </div>

                        <div class="item_input servicio">
                            <i class="fa-regular fa-id-card"></i>
                            <input type="text" class="input" name="cedula" id="" placeholder="Cédula" required>
                        </div>

                        <div class="item_input servicio">
                            <i class="fa-solid fa-network-wired"></i>
                            <input type="text" name="ip" id="" placeholder="Dirección ip" class="input" required="">
                        </div>

                        <div class="item_input servicio">
                            <i class="fa-solid fa-network-wired"></i>
                            <input type="text" name="mac" id="" placeholder="Dirección mac" class="input" required="">
                        </div>

                        <div class="item_input servicio">
                            <i class="fa-solid fa-map-location"></i>
                            <input type="text" class="input" name="direccion" id="" placeholder="Dirección" required>
                        </div>

                        <div class="item_input servicio">
                            <i class="fa-solid fa-tower-cell"></i>
                            <select name="plan" id="" class="input" required>${planes}</select>
                        </div>

                        <div class="item_input servicio">
                            <i class="fa-solid fa-tower-cell"></i>
                            <select name="servidor" id="" class="input" required>${servidores}</select>
                        </div>

                        <div class="item_input servicio">
                            <i class="fa-regular fa-clipboard"></i>
                            <input type="text" class="input" name="observacion" id="" placeholder="Información" required>
                        </div>
                    </div>
                    <input type="submit" class="input_submit" value="Registrar servicio">
                </div>
            </div>
                </div>
            </form>`,
            focusConfirm: false,
        });
    });
}

function msg() {
    Swal.fire({
        icon: "error",
        title: "Debe terminar de cancelar su mensualidad para poder cambiar los planes",
        showConfirmButton: false,
    });
}

function buscar_cliente(servicio_id) {
    let contenedor = document.querySelector("#sel_cliente");

    let cedula = document.getElementById("cedula_unir").value;
    let nombre = document.getElementById("nombre_unir").value;

    if (cedula == "") {
        cedula = "vacio";
    }

    if (nombre == "") {
        nombre = "vacio";
    }

    let url = ``;

    url = `/control_de_pago_remake/public/clientes/clientes_ss/${cedula}/${nombre}`;

    contenedor.innerHTML = "";

    if (nombre != "vacio" || cedula != "vacio") {
        fetch(`${url}`)
            .then((response) => response.json())
            .then(function (response) {
                let loop_iteration = 0;

                response.forEach(function (item) {
                    if (servicio_id != item["servicio_id"]) {
                        contenedor.style.display = "grid";
                        loop_iteration += 1;
                        contenedor.innerHTML += `<p>${item["id"]}</p><p>${item["nombre"]}</p><p>${item["cedula"]}</p><button class="boton_union" id="boton_union_${item["id"]}" onclick="union(${item["id"]}, ${servicio_id})">UNIR</button><img src="/control_de_pago_remake/public/img/menu/unir_check.png" id="unir_check_img_${item["id"]}" style="display:none; width: auto;" title="Servicio unido con éxito">`;
                    }
                });

                if (loop_iteration == 0) {
                    contenedor.style.display = "block";
                    contenedor.innerHTML += "<p>Sin resultados.</p>";
                }
                document.getElementById("error_unir").style.display = "none";
            });
    } else {
        document.getElementById("error_unir").style.display = "block";
    }
}

function union(id, servicio_id) {
    fetch(`/control_de_pago_remake/public/clientes/union/${id}/${servicio_id}`)
        .then(function (response) {
            if (response.ok) {
                document.getElementById("boton_union_" + id).style.display =
                    "none";
                document.getElementById("unir_check_img_" + id).style.display =
                    "block";
            }
        })
        .catch((err) => {
            console.error("ERROR: ", err.message);
        });
}

function unir_clientes(id) {
    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: `
        <div class="unir_servicio">
            <h1 class="titulo">Seleccione el cliente para unir</h1>
            <table id="main_ss" class="ui inverted celled table" style="text-align-last: center; width: 99%;">
                <div class="busqueda_cliente">
                    <input type="text" class="input_unir" name="nombre" id="nombre_unir" placeholder="Nombre">
                    <input type="number" class="input_unir" name="cedula" id="cedula_unir" placeholder="Cedula">
                    <button type="submit" onclick="buscar_cliente(${id})" class="boton_union">Buscar</button>
                </div><br>
                <div id="sel_cliente">

                </div>
                <p style="display: none; color: red;" id="error_unir">Debe ingresar al menos un dato.*</p>
            </table>
        </div>`,
        focusConfirm: false,
    });
}

async function comprobar_referencia(event) {
    let datos_referencia = document.getElementById("input_7").value; //referencia.
    let datos_banco = document.getElementById("7").value; // banco.

    await fetch(`/control_de_pago_remake/public/referencias/0`)
        .then((response) => response.json())
        .then(function (response) {
            response.forEach(function (item) {
                if (
                    item["referencia"] == datos_referencia &&
                    item["banco"] == datos_banco
                ) {
                    sumar_comprobacion();
                }
            });

            datos_referencia = "";
            datos_banco = "";
        });
}

function eliminar_pago(id) {
    Swal.fire({
        title: "¿Estas seguro?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "No",
        confirmButtonText: "Si",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/control_de_pago_remake/public/eliminar_pago/${id}`)
                .then(() => {
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Pago ELIMINADO con éxito!",
                        showConfirmButton: false,
                        timer: 3000,
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                })
                .catch((err) => {
                    console.error("ERROR: ", err.message);
                    Swal.fire({
                        position: "top-end",
                        icon: "error",
                        title: "error!",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                });
        }
    });
}

function img_conducta(val) {
    let imagen = document.getElementById("imagen_conducta");

    if (val == 0) {
        imagen.src = "/control_de_pago_remake/public/img/inicio/good.png";
    } else {
        imagen.src = "/control_de_pago_remake/public/img/inicio/demon.png";
    }
}

function reiniciar_prorroga(id) {
    Swal.fire({
        title: "¿Reiniciar prorroga?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si",
    }).then(async (result) => {
        if (result.isConfirmed) {
            await fetch(
                `/control_de_pago_remake/public/clientes/reiniciar_prorroga/${id}`
            );

            Swal.fire({
                title: "Prorroga reiniciada",
                icon: "success",
                showCancelButton: false,
                showConfirmButton: false,
            });

            setTimeout(() => {
                window.location.reload();
            }, 1500);
        }
    });
}

function modificar_conducta(id) {
    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            htmlContainer: "",
        },
        html: `
                <form action="/control_de_pago_remake/public/clientes/conducta" method="POST" class="contenedor_conducta">
                <div class="titulo_conducta">
                    <h1 class="titulo">Modificar conducta</h1> 
                </div>
                <input type="hidden" name="id" value="${id}">
                <img src="/control_de_pago_remake/public/img/inicio/good.png" id="imagen_conducta" class="estrellita" title="uwu">

                <div class="conducta">
                    <label class="label_conducta">
                        <p>Buena conducta</p><br> 
                        <input type="radio" name="conducta" value="0" onchange="img_conducta(0)" checked>
                    </label>
                    
                    <label class="label_conducta">
                        <p>Mala conducta</p><br> 
                        <input type="radio" name="conducta" value="1" onchange="img_conducta(1)">
                    </label>
                </div>

                <div class="conducta">
                <input type="text" class="input_unir" name="motivo" id="" placeholder="Motivo" required>
                <input type="submit" class="boton_pago" value="Modificar">
                
                </div>
                </form>
                `,
        focusConfirm: false,
    });
}

function separar(id) {
    Swal.fire({
        title: "¿Separar servicio?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "No",
        confirmButtonText: "Si",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `/control_de_pago_remake/public/clientes/separar/${id}`;
        }
    });
}

function ult_pago() {
    Swal.fire({
        title: "Debes eliminar el ultimo pago.",
        icon: "error",
        confirmButtonColor: "#d33",
    });
}

async function cambiar_tasa_pago(id, ticket, op) {
    let fecha = document.getElementById("fecha_pagomovil_input").value;
    let tasa = "";
    let tasa_aux = "";
    let verificar = 0;

    await fetch(`/control_de_pago_remake/public/consultar_tasa`)
        .then((response) => response.json())
        .then(function (response) {
            response.forEach(function (item) {
                if (item["fecha"] == fecha) {
                    tasa = item["tasa"];
                    verificar += 1;
                }

                tasa_aux = item["tasa"];
            });
        });

    if (verificar > 0) {
        pagar(id, `${tasa}`, ticket, op, fecha);
    } else {
        pagar(id, `${tasa_aux}`, ticket, op, fecha);
    }
}
