var valor_global = 0;
var sel = 0;
var seleccionado = [0];

function agregar_cliente() {
    //agregar cliente manualmente
    planes = "";
    servidores = "";
    $.get("/control_de_pago_remake/public/data", function (response) {
        planes += '<optgroup label="Inalambrico">';
        response[0].forEach(function (item) {
            if (item["tipo"] == 0) {
                planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
            }
        });
        planes += "</optgroup>";

        planes += '<optgroup label="Fibra">';
        response[0].forEach(function (item) {
            if (item["tipo"] == 1) {
                planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
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
            html: `<form action="/control_de_pago_remake/public/clientes/add" method="POST" class="nuevo_cliente">
                <h1 class="titulo">Registrar cliente</h1>
                <div class="item_input">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" class="input" name="nombre" id="" placeholder="Nombre del cliente" required>
                </div>
                
                <div class="item_input">
                    <i class="fa-solid fa-phone"></i>
                    <input type="number" class="input" name="telefono" id="" placeholder="Numero telefonico" required>
                </div>
        
                <div class="item_input">
                    <i class="fa-regular fa-id-card"></i>
                    <input type="text" class="input" name="cedula" id="" placeholder="Cedula" required>
                </div>
                
                <div class="item_input">
                    <i class="fa-solid fa-map-location"></i>
                    <input type="text" class="input" name="direccion" id="" placeholder="Dirección" required>
                </div>
                
                <div class="item_input" required>
                    <i class="fa-solid fa-server"></i>
                    <select name="servidor" id=""class="input">${servidores}</select>
                </div>
                
                <div class="item_input">
                    <i class="fa-solid fa-tower-cell"></i>
                    <select name="plan" id="" class="input">${planes}</select>
                </div>
                
                <div class="item_input">
                    <i class="fa-solid fa-network-wired"></i>
                    <input type="text" name="ip" id="" placeholder="Dirección ip" class="input" required>
                </div>

                <div class="item_input">
                    <i class="fa-solid fa-network-wired"></i>
                    <input type="text" name="mac" id="" placeholder="Dirección MAC" class="input" required>
                </div>
                
                <div class="item_input">
                    <i class="fa-regular fa-clipboard"></i>
                    <textarea name="observacion" id="" cols="10" rows="1" class="input"></textarea>
                </div>
        
                <input type="submit" class="input_submit" value="Registrar cliente">
            </form>`,
            focusConfirm: false,
        });
    });

    setTimeout(() => {
        let nuevo_cliente = document.getElementsByClassName("swal2-container");
        nuevo_cliente[0].setAttribute("id", "nuevo");

        let nuevo_cliente_id = document.getElementById("nuevo");
        nuevo_cliente_id.removeAttribute("style");
        nuevo_cliente_id.classList.add("styles_nuevo");
        nuevo_cliente_id.style.overflow = "hidden";
    }, "1000");
}

function facturacion(int) {
    let facturacion = document.getElementById("facturacion");
    let datos_cliente = document.getElementById("datos_cliente");
    let flecha = document.getElementById("flecha_siguiente");
    let paso_01 = document.getElementById("paso_1");
    let registrado = document.getElementById("registrado");

    document.getElementById("datetimelocal").value = toLocalISOString(new Date());

    flecha.removeAttribute("onclick");

    if (int == 1) {
        facturacion.style.display = "grid";
        datos_cliente.style.display = "none";
        paso_01.style.display = "none";
        registrado.style.display = "none";
        flecha.style.transform = "rotate(180deg)";
        flecha.style.transition = "transform 0.5s ease 0s";
        flecha.setAttribute("onclick", "facturacion(0)");
    } else if (int != 1 && int != 2) {
        facturacion.style.display = "none";
        paso_01.style.display = "block";
        registrado.style.display = "block";
        datos_cliente.style.display = "grid";
        flecha.style.transform = "rotate(0deg)";
        flecha.style.transition = "transform 0.5s ease 0s";
        flecha.setAttribute("onclick", "facturacion(1)");
    }
}

function facturacion_servicio(int) {
    let facturacion_servicio = document.getElementById("facturacion_servicio");
    let datos_cliente_servicio = document.getElementById("datos_cliente_servicio");
    let flecha_servicio = document.getElementById("flecha_siguiente_servicio");
    let paso_01_servicio = document.getElementById("paso_1_servicio");

    document.getElementById("datetimelocal").value = toLocalISOString(new Date());

    flecha_servicio.removeAttribute("onclick");

    if (int == 1) {
        facturacion_servicio.style.display = "grid";
        datos_cliente_servicio.style.display = "none";
        paso_01_servicio.style.display = "none";
        flecha_servicio.style.transform = "rotate(180deg)";
        flecha_servicio.style.transition = "transform 0.5s ease 0s";
        flecha_servicio.setAttribute("onclick", "facturacion_servicio(0)");
    } else {
        facturacion_servicio.style.display = "none";
        paso_01_servicio.style.display = "block";
        datos_cliente_servicio.style.display = "grid";
        flecha_servicio.style.transform = "rotate(0deg)";
        flecha_servicio.style.transition = "transform 0.5s ease 0s";
        flecha_servicio.setAttribute("onclick", "facturacion_servicio(1)");
    }
}

function agregar(id, valor, nombre, cedula) {
    let boton_agregar = document.getElementById(`boton_union_${id}`);
    let check = document.getElementById(`unir_check_img_${id}`);
    let valores = document.getElementById(`valores`);
    let seleccionados = document.getElementById(`seleccionados`);
    sel += 1;
    seleccionados.innerHTML += `<p>${id}</p><p>${nombre}</p><p>${cedula}</p><p>${valor}$</p><p>X</p>`;

    seleccionado.push(id);
    valores.value = valores.value += `,${id}`;
    check.style.display = "block";
    boton_agregar.style.display = "none";
    valor_global += valor;
    total_pago.innerHTML = `Total a pagar: ${valor_global}$`;
}

function mensaje_deuda(id, nombre, deuda, motivo, tasa, factura, op) {
    let funcion = "";

    if (op == 0) {
        funcion = `<button class="boton_pago" onclick="pagar('${id}', '${tasa}', '${factura}', 1)">Pagar mensualidad</button> `;
    }

    Swal.fire({
        icon: "error",
        title: `El servicio de ${nombre} tiene una deuda pendiente de ${deuda}$.<br><br>motivo: ${motivo}.`,
        showConfirmButton: false,
        footer: `${funcion}<button class="boton_pago" onclick="pagar_deuda(${id},'${motivo}',${deuda}, ${tasa})">Pagar deuda</button>`,
    });
}

function pagar_deuda(id, motivo, deuda, tasa) {
    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
            popup: "popup_custom",
        },
        html: `<form action="/control_de_pago_remake/public/pagar_deuda/${id}" method="POST" id="formulario_pagar" class="nuevo_registro_sub">
                <h1>Registrar pago de deudas</h1>

                <div id="facturacion">
                    <h1 class="titulo">Facturación, <span id="total_pago">Total a pagar: ${deuda}$</span></h1>
                    <p id="cambio">Al cambio: ${tasa}bs / total : 0.0$ / total: 0.0$</p>
        
                    <select name="" id="tipo" class="input option" onchange="tipo_de_pago()">
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
                        <input type="number" name="dolar" id="input_1" class="input input_type input_element" placeholder="Dolares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(1,${tasa})">
                        <p>$</p>
                    </div>

                    <div class="item" id="2">
                        <input type="number" name="bolivar" id="input_2" class="input input_type input_element" placeholder="Bolivares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(2,${tasa})">
                        <p>B</p>
                    </div>

                    <div class="item" id="3">
                        <input type="number" name="euro" id="input_3" class="input input_type input_element" placeholder="Euros" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(3,${tasa})">
                        <p>€</p>
                    </div>
        
                    <div class="item" id="4">
                    <input type="number" name="zelle_v" id="input_4" class="input input_type input_element" placeholder="Zelle Vladimir" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(4,${tasa})">
                        <p>Z V</p>
                    </div>
        
                    <div class="item" id="5">
                        <input type="number" name="zelle_j" id="input_5" class="input input_type input_element" placeholder="Zelle Jesus Millan" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(5,${tasa})">
                        <p>Z J</p>
                    </div>
                </div>
        
                <div class="item_pm" id="6">
                    <input type="number" name="pagomovil" id="input_6" class="input input_type input_element" placeholder="Monto" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                    <input type="text" class="input input_type input_element" placeholder="Referencia" pattern="^[0-9]{12}$" value="000000000000" name="referencia" id="input_7" style="display: block;" onkeyup="validar_referencia(${id})" title="debe ingresar un numero de 12 dígitos">
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(6,${tasa})">
                    <p>PM</p>
                </div>
        
                <div id="fecha_pagomovil"> 
                <input type="date" value="" id="fecha_pagomovil_input" class="input" style="display: none;" name="fecha_pago_movil">
                <select name="banco" id="7" class="input option item_pm_banco input_element" onchange="validar_referencia(${id})" style="display: none;" required>
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
        
                <input type="submit" class="input_submit" value="Registrar pago">
            </div>
                    
                </div>
            </form>`,
        focusConfirm: false,
    });
}
var router_aux = "";
async function agregar_instalacion(tasa, id, op) {
    //pre registro
    planes = "";
    servidores = "";
    router = "";
    titulo = "REGISTRAR INSTALACIÓN";
    hidden = `<input type='hidden' name='cliente_id' value='0'>`;
    var bancos = "";

    if (op == 1) {
        titulo = "REGISTRAR MUDANZA O MIGRACIÓN";
        hidden = `<input type='hidden' name='cliente_id' value='${id}'>`;
    }

    await $.get("/control_de_pago_remake/public/bancos", function (response) {
        response.forEach(function (item) {
            bancos += `<option value="${item["value"]}">${item["banco"]}</option>`;
        });
    });

    await $.get("/control_de_pago_remake/public/data", function (response) {
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

        router += '<optgroup label="Instalación sencilla">';
        response[2].forEach(function (item) {
            if (item["categoria"] == 0) {
                if (item["active"] == 1) {
                    router += `<option value="${item["id"]}">${item["router"]} - ${item["valor"]}$</option> \n`;
                }
            }
        });
        router += "</optgroup>";

        router += '<optgroup label="Instalación intermedia">';
        response[2].forEach(async function (item) {
            if (item["categoria"] == 1) {
                if (item["active"] == 1) {
                    router += `<option value="${item["id"]}">${item["router"]} - ${item["valor"]}$ (Quedan: ${item["total_existencias"]} en el inventario)</option> \n`;
                }
            }
        });
        router += "</optgroup>";

        router += '<optgroup label="Instalación avanzada">';
        response[2].forEach(function (item) {
            if (item["categoria"] == 2) {
                if (item["active"] == 1) {
                    router += `<option value="${item["id"]}">${item["router"]} - ${item["valor"]}$</option> \n`;
                }
            }
        });
        router += "</optgroup>";

        const { value: formValues } = Swal.fire({
            showConfirmButton: false,
            background: "rgba(0, 0, 0, 0)",
            heightAuto: false,
            width: 600,
            customClass: {
                container: "container_modal",
                htmlContainer: "contenedor_add",
                popup: "popup_custom",
            },
            html: `<form action="/control_de_pago_remake/public/pre_registro/nuevo" method="POST" class="nuevo_registro_sub">
                <div class="">
                    <h1 class="titulo_pre">${titulo} <img src="/control_de_pago_remake/public/img/inicio/flecha.png" id="flecha_siguiente" onclick="facturacion(1)"></h1>
                    <p id="paso_1">PASO #1: DATOS DEL CLIENTE</p>
                    <div  class="nuevo_registro" id="datos_cliente">
                    <input type="hidden" name="tasa" value="${tasa}">
                    ${hidden}
                        <div class="item_input" style="padding: 10px 0px 10px 0px;">
                            <i class="fa-solid fa-wifi"></i>
                            <div>
                                <label for="fibra">Fibra</label>
                                <input type="radio" name="tipo_de_instalacion" value="0" id="fibra" class="input_type_insta" checked required>
                                <label for="inalambrico">Inalámbrico</label>
                                <input type="radio" name="tipo_de_instalacion" value="1" id="inalambrico" required><br>
                            </div>
                        </div>
                        <div class="item_input">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" class="input" name="nombre" id="" placeholder="Nombre del cliente" required>
                        </div>

                        <div class="item_input">
                            <i class="fa-solid fa-phone"></i>
                            <input type="number" class="input" name="telefono" id="" placeholder="Numero telefónico" required>
                        </div>

                        <div class="item_input">
                            <i class="fa-regular fa-id-card"></i>
                            <input type="text" class="input" name="cedula" id="" placeholder="Cédula" required>
                        </div>

                        <div class="item_input">
                            <i class="fa-solid fa-map-location"></i>
                            <input type="text" class="input" name="direccion" id="" placeholder="Dirección" required>
                        </div>

                        <div class="item_input">
                            <i class="fa-solid fa-tower-cell"></i>
                            <select name="plan" id="" class="input" required>${planes}</select>
                        </div>

                        <div class="item_input">
                            <i class="fa-solid fa-server"></i>
                            <select name="router" id="" class="input">${router}</select>
                        </div>

                        <div class="item_input">
                            <i class="fa-regular fa-clipboard"></i>
                            <input type="text" class="input" name="observacion" id="" placeholder="Información">
                        </div>
                    </div>
                    <div id="registrado">
                        ¿Agregar al pre-registro?<br>
                        <label for="no">No</label>
                        <input type="radio" name="instalado" value="0" id="no" required>
                        <label for="si">Si</label>
                        <input type="radio" name="instalado" value="1" id="si" required>
                    </div>
                </div>
                <div style="display: none;" id="facturacion">
                    <p class="">PASO #2: FACTURACIÓN</p>
                    <p id="cambio">Al cambio: 0.0$ / total : 0.0$</p>

                    <input type="datetime-local" value="" class="input_pre_reg" id="datetimelocal" name="fecha_preg" id="">
        
                <select name="" id="tipo" class="input option" onchange="tipo_de_pago()">
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
                        <input type="number" name="dolar" id="input_1" class="input input_type input_element" placeholder="Dolares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(1,${tasa})">
                        <p>$</p>
                    </div>

                    <div class="item" id="2">
                        <input type="number" name="bolivar" id="input_2" class="input input_type input_element" placeholder="Bolivares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(2,${tasa})">
                        <p>B</p>
                    </div>

                    <div class="item" id="3">
                        <input type="number" name="euro" id="input_3" class="input input_type input_element" placeholder="Euros" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(3,${tasa})">
                        <p>€</p>
                    </div>
        
                    <div class="item" id="4">
                    <input type="number" name="zelle_v" id="input_4" class="input input_type input_element" placeholder="Zelle Vladimir" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(4,${tasa})">
                        <p>Z V</p>
                    </div>
        
                    <div class="item" id="5">
                        <input type="number" name="zelle_j" id="input_5" class="input input_type input_element" placeholder="Zelle Jesus Millan" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(5,${tasa})">
                        <p>Z J</p>
                    </div>
                </div>
        
                <div class="item_pm" id="6">
                    <input type="number" name="pagomovil" id="input_6" class="input input_type input_element" placeholder="Monto" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                    <input type="text" class="input input_type input_element" placeholder="Referencia" pattern="^[0-9]{12}$" value="000000000000" name="referencia" id="input_7" style="display: block;" onkeyup="validar_referencia(${id})" title="debe ingresar un numero de 12 dígitos">
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(6,${tasa})">
                    <p>PM</p>
                </div>
        
                <div id="fecha_pagomovil"> 
                <input type="date" value="" id="fecha_pagomovil_input" class="input" style="display: none;" name="fecha_pago_movil">
                <select name="banco" id="7" class="input option item_pm_banco input_element" onchange="validar_referencia(${id})" style="display: none;" required>
                    ${bancos}
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
                <input type="submit" class="input_submit" value="Registrar cliente">
            </div>
                    
                </div>
            </form>`,
            focusConfirm: false,
        });

        bancos = "";
    });
}

function servicio_cliente(id) {
    window.open("/control_de_pago_remake/public/clientes/menu/" + id);
}

function error_prorroga(id) {
    $.get(`/control_de_pago_remake/public/datos_estructurados_modificar/${id}`, function (response) {
        let fecha = moment(response.ult_prorroga).locale("es").format("DD [de] MMMM [de] YYYY");

        Swal.fire({
            icon: "error",
            title: `Error al dar prorroga`,
            text: `Tienes que esperar hasta el dia ${fecha}`,
            showConfirmButton: false,
        });
    });
}

function modificar_cliente(id) {
    $.get(`/control_de_pago_remake/public/datos_estructurados_modificar/${id}`, function (response) {
        let planes = "";
        let servidores = "";
        let checked_active = "";
        let ticket_active = "";
        let tipo_cliente = "";
        let iptvs = "";

        // variables para el formulario
        let id_formulario = response.id;
        let nombre_formulario = response.nombre;
        let telefono_formulario = response.tlf;
        let cedula_formulario = response.cedula;
        let direccion_formulario = response.direccion;
        let ip_formulario = response.ip;
        let mac_formulario = response.mac;
        let corte_formulario = response.corte;
        let observacion_formulario = response.observacion;
        let plan_formulario = response.plan_id;
        let servidor_formulario = response.servidor;
        let active_formulario = response.active;
        let congelado_formulario = response.congelado;
        let ticket_formulario = response.ticket;
        let tipo_formulario = response.tipo_cliente;
        let iptv_formulario = response.iptv;
        let prorroga_formulario = response.prorroga;
        let prorroga_hasta_formulario = response.prorroga_hasta;
        let prorroga_nota_formulario = response.nota_prorroga;
        let ult_prorroga_dada = response.ult_prorroga;
        let conducta = response.conducta;
        let asignacion = response.asignacion ? response.asignacion : "";

        let fecha_formateada_prorroga = moment(ult_prorroga_dada).locale("es").format("DD-MMMM-YYYY");
        let fecha_hoy = moment(new Date()).locale("es").format("DD-MMMM-YYYY");

        $.get("/control_de_pago_remake/public/data", function (response) {
            planes += '<optgroup label="Inalambrico">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 0) {
                    if (item["id"] == plan_formulario) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} - ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Fibra">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 1) {
                    if (item["id"] == plan_formulario) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} - ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Empresariales">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 2) {
                    if (item["id"] == plan_formulario) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} - ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Planes especiales">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 3) {
                    if (item["id"] == plan_formulario) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} - ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Noria">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 4) {
                    if (item["id"] == plan_formulario) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} - ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} - ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            response[1].forEach(function (item) {
                if (item["id"] == servidor_formulario) {
                    servidores += `<option value="${item["id"]}" selected>${item["nombre_de_servidor"]}</option> \n`;
                } else {
                    servidores += `<option value="${item["id"]}">${item["nombre_de_servidor"]}</option> \n`;
                }
            });
            planes += "</optgroup>";

            if (active_formulario != "0") {
                if (congelado_formulario == 1) {
                    checked_active = `<p>Activar: <input type="radio" disabled name="act_des" value="1" id="" checked></p>    <p>desactivar: <input type="radio" disabled name="act_des" value="0" id=""></p>`;
                } else {
                    checked_active = `<p>Activar: <input type="radio" name="act_des" value="1" id="" checked></p>    <p>desactivar: <input type="radio" name="act_des" value="0" id=""></p>`;
                }
            } else {
                checked_active = `<p>Activar: <input type="radio" name="act_des" value="1" id=""></p><p>desactivar: <input type="radio" name="act_des" value="0" id="" checked></p>`;
            }

            if (ticket_formulario != "0") {
                ticket_active = `
            <p>Bolivares: <input type="radio" name="ticket" value="0" id=""></p>
            <p>Dolares: <input type="radio" name="ticket" value="1" id="" checked></p>`;
            } else {
                ticket_active = `
            <p>Bolivares: <input type="radio" name="ticket" value="0" id="" checked></p>
            <p>Dolares: <input type="radio" name="ticket" value="1" id=""></p>`;
            }

            let congelador = "";

            if (congelado_formulario == 0) {
                if (active_formulario == 1) {
                    congelador = `<img src="/control_de_pago_remake/public/img/inicio/congelar.png" class="congelar" title="copito :3" onclick="congelar(${id},'${corte_formulario}')"></img>`;
                } else {
                    congelador = `<img src="/control_de_pago_remake/public/img/inicio/congelar.png" class="congelar" title="copito :3" onclick="alert('debes activar primero')"></img>`;
                }
            } else {
                congelador = `<img src="/control_de_pago_remake/public/img/inicio/descongelar.png" class="congelar" title="copito descongelándose :3" onclick="descongelar('${id}')"></img>`;
            }

            let select_0 = "";
            let select_1 = "";
            let select_2 = "";

            let select_3 = "";
            let select_4 = "";

            let select_5 = ""; // para nodos

            if (tipo_formulario == 0) {
                select_0 = "selected";
            } else if (tipo_formulario == 1) {
                select_1 = "selected";
            } else if (tipo_formulario == 2) {
                select_2 = "selected";
            } else if (tipo_formulario == 3) {
                select_5 = "selected";
            }

            if (iptv_formulario == 0) {
                select_3 = "selected";
            } else if (iptv_formulario == 1) {
                select_4 = "selected";
            }

            iptvs = `
        <select name="iptv" id="iptv">
            <option value="0" ${select_3}>NO</option>
            <option value="1" ${select_4}>SI</option>
        </select>`;

            tipo_cliente = `
        <select name="tipo_c" id="tipo_c">
            <option value="0" ${select_0}>Regular</option>
            <option value="1" ${select_1}>PREMIUM</option>
            <option value="2" ${select_2}>DONACIÓN</option>
            <option value="3" ${select_5}>NODO</option>
        </select>`;

            let color = "";
            let title = "";
            let funcion = "";

            if (conducta == 0) {
                if (congelado_formulario == 1 || prorroga_formulario == 1) {
                    color = "#5BC6FF";
                    title = `Imposible dar prorrogas a clientes congelados o con prorroga activa.`;
                    funcion = ``;
                } else {
                    if (prorroga_formulario == 0) {
                        if (fecha_hoy > fecha_formateada_prorroga) {
                            color = "red";
                            title = "No se puede agregar prorroga.";
                            funcion = `error_prorroga(${id_formulario})`;
                        } else {
                            color = "red";
                            title = "Agregar una prorroga.";
                            funcion = `prorroga(0, ${id})`;
                        }
                    } else {
                        color = "#27d719";
                        title = `Prorroga hasta el dia: ${prorroga_hasta_formulario}, motivo: ${prorroga_nota_formulario}. Dar click para eliminar prorroga.`;
                        funcion = `prorroga(1, ${id})`;
                    }
                }
            } else {
                color = "red";
                title = "Cliente mala conducta sin derecho a prorroga.";
            }

            let prorroga = ` <div class="prorrogas" style="background-color: ${color}; display: inline-block;" title="${title}" onclick="${funcion}"></div>`;

            if (tipo_formulario != 0) {
                prorroga = "";
            }

            let prorroga_activa = "";

            if (prorroga_formulario == 1) {
                prorroga_activa = `<br>(PRORROGA ACTIVA)`;
            }

            Swal.fire({
                showConfirmButton: false,
                background: "rgba(0, 0, 0, 0)",
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                    popup: "popup_custom",
                },
                html: `<div action="" method="POST" class="nuevo_cliente">
                <h1 class="titulo titulo_prorroga" style="display: inline;">Modificar a ${nombre_formulario} ${prorroga} ${prorroga_activa}</h1>
                <div class="item_input">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" class="input" name="nombre" id="" placeholder="Nombre del cliente" value="${nombre_formulario}" required>
                </div>
                
                <div class="item_input">
                    <i class="fa-solid fa-phone"></i>
                    <input type="number" class="input" name="telefono" id="" placeholder="Numero telefonico" value="${telefono_formulario}" required>
                </div>
        
                <div class="item_input">
                    <i class="fa-regular fa-id-card"></i>
                    <input type="text" class="input" name="cedula" id="" placeholder="Cedula" value="${cedula_formulario}" required>
                </div>
                
                <div class="item_input">
                    <i class="fa-solid fa-map-location"></i>
                    <input type="text" class="input" name="direccion" id="" placeholder="Dirección" value="${direccion_formulario}" required>
                </div>
                
                <div class="item_input" required>
                    <i class="fa-solid fa-server"></i>
                    <select name="servidor" id=""class="input">${servidores}</select>
                </div>
                
                <div class="item_input">
                    <i class="fa-solid fa-tower-cell"></i>
                    <select name="plan" id="" class="input">${planes}</select>
                </div>
                
                <div class="item_input">
                    <i class="fa-solid fa-network-wired"></i>
                    <input type="text" name="ip" id="" placeholder="Dirección ip" class="input" value="${ip_formulario}" required>
                </div>

                <div class="item_input">
                    <i class="fa-solid fa-network-wired"></i>
                    <input type="text" name="mac" id="" placeholder="Dirección mac" class="input" value="${mac_formulario}" required>
                </div>

                <div class="item_input">
                    <i class="fa-solid fa-scissors"></i>
                    <input type="date" name="corte" id="" class="input_date_cut" value="${corte_formulario}" required>
                </div>

                <div class="item_input">
                    <i class="fa-solid fa-hashtag"></i>
                    <input type="text" name="asignacion" id="" placeholder="Ingrese el serial del equipo asignado" class="input" value="${asignacion}">
                </div>

                <div class="item_input">
                    <i class="fa-regular fa-clipboard"></i>
                    <textarea name="observacion" id="" cols="10" rows="1" class="input">${observacion_formulario}</textarea>
                </div>

                <div class="ops_checks">
                    <div class="act_des">
                        ${iptvs}
                    </div>
                    <div class="act_des">
                        ${tipo_cliente}
                    </div>
                    <div class="act_des">
                        ${checked_active}
                    </div>
                    <div class="act_des">
                        ${ticket_active}
                    </div>
                    <div class="congelar">
                        ${congelador}
                    </div>
                </div>
        
                <input type="submit" class="input_submit" value="Editar cliente" onclick="envio_formulario_editar_cliente('${id}')">
            </div>`,
                focusConfirm: false,
            });
        });
    });
}

async function mostrar_menu(val) {
    let elementos = document.getElementsByClassName("botones");
    let flecha_izq = document.getElementById("flecha_izq");
    let flecha_der = document.getElementById("flecha_der");

    if (val == 1) {
        flecha_izq.style.opacity = "0";
        flecha_izq.style.cursor = "auto";
        flecha_der.style.opacity = "1";
        flecha_der.style.cursor = "pointer";

        for (i = 0; i < elementos.length; i++) {
            elementos[i].style.opacity = "1";
            elementos[i].style.cursor = "pointer";
        }

        setTimeout(() => {
            mostrar_menu_0(1);
        }, 10);
    } else if (val == 0) {
        flecha_izq.style.opacity = "1";
        flecha_izq.style.cursor = "pointer";
        flecha_der.style.opacity = "0";
        flecha_der.style.cursor = "auto";
        for (j = 0; j < elementos.length; j++) {
            elementos[j].style.opacity = "0";
            elementos[j].style.cursor = "auto";
        }

        setTimeout(() => {
            mostrar_menu_0(0);
        }, 10);
    }
}

function mostrar_menu_0(val) {
    let elementos = document.getElementsByClassName("botones");
    if (val == 0) {
        for (j = 0; j < elementos.length; j++) {
            elementos[j].removeAttribute("id", "");
            elementos[j].setAttribute("id", `item_${j + 1}_${j + 1}`);
        }
    } else if (val == 1) {
        for (j = 0; j < elementos.length; j++) {
            elementos[j].removeAttribute("id", "");
            elementos[j].setAttribute("id", `item_${j + 1}`);
        }
    }
}

async function agregar_servicio(tasa, id, nombre, telefono, cedula) {
    let display = "";
    let estilos = "";
    let padding = "";
    var bancos = "";

    if (nombre == undefined) {
        nombre = "";
    }

    if (telefono == undefined) {
        telefono = "";
    }

    if (cedula == undefined) {
        cedula = "";
    }

    if (id > 0) {
        estilos = "grid-template-columns: 1fr;";
        display = "display:none;";
        padding = "padding: 10px;";
    }

    await $.get("/control_de_pago_remake/public/bancos", function (response) {
        response.forEach(function (item) {
            bancos += `<option value="${item["value"]}">${item["banco"]}</option>`;
        });
    });

    const { value: formValues } = await Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
            popup: "popup_custom",
        },
        html: `<form action="/control_de_pago_remake/public/servicio_nuevo" method="POST" class="nuevo_registro_sub">
            <div class="">
                <h1 class="titulo_pre">Registrar pago de servicio<img src="/control_de_pago_remake/public/img/inicio/flecha.png" id="flecha_siguiente_servicio" onclick="facturacion_servicio(1)"></h1>
                <p id="paso_1_servicio">PASO #1: DATOS DEL CLIENTE</p>
                <div  class="nuevo_registro" id="datos_cliente_servicio" style="${estilos}">
                <input type="hidden" name="id" value="${id}" required>
                <input type="hidden" name="cobrador" value="Kennerth Salazar" required>
                <input type="hidden" name="tasa" value="${tasa}" required>
                    <div class="item_input" style="${display}">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" class="input" name="nombre" value="${nombre}" id="" placeholder="Nombre del cliente" required>
                    </div>

                    <div class="item_input" style="${display}">
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" class="input" name="telefono" value="${telefono}" id="" placeholder="Numero telefónico" required>
                    </div>

                    <div class="item_input" style="${display}">
                        <i class="fa-regular fa-id-card"></i>
                        <input type="text" class="input" name="cedula" value="${cedula}" id="" placeholder="Cédula" required>
                    </div>

                    <div class="item_input">
                        <i class="fa-regular fa-clipboard"></i>
                        <input type="text" class="input" name="observacion" id="" placeholder="Información" required>
                    </div>                        
                </div>
            </div>
            <div style="display: none; ${padding}" id="facturacion_servicio">
                <p class="">PASO #2: FACTURACIÓN</p>
                <p id="cambio">Al cambio: 0.0$ / total : 0.0$</p>

                <input type="datetime-local" value="1718915736169" class="input_pre_reg" id="datetimelocal" name="fecha_preg" id="">
    
            <select name="" id="tipo" class="input option" onchange="tipo_de_pago()">
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
                    <input type="number" name="dolar" id="input_1" class="input input_type input_element" placeholder="Dolares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(1,${tasa})">
                    <p>$</p>
                </div>

                <div class="item" id="2">
                    <input type="number" name="bolivar" id="input_2" class="input input_type input_element" placeholder="Bolivares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(2,${tasa})">
                    <p>B</p>
                </div>

                <div class="item" id="3">
                    <input type="number" name="euro" id="input_3" class="input input_type input_element" placeholder="Euros" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(3,${tasa})">
                    <p>€</p>
                </div>
    
                <div class="item" id="4">
                <input type="number" name="zelle_v" id="input_4" class="input input_type input_element" placeholder="Zelle Vladimir" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(4,${tasa})">
                    <p>Z V</p>
                </div>
    
                <div class="item" id="5">
                    <input type="number" name="zelle_j" id="input_5" class="input input_type input_element" placeholder="Zelle Jesus Millan" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(5,${tasa})">
                    <p>Z J</p>
                </div>
            </div>
    
            <div class="item_pm" id="6">
                <input type="number" name="pagomovil" id="input_6" class="input input_type input_element" placeholder="Monto" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                <input type="text" class="input input_type input_element" placeholder="Referencia" pattern="^[0-9]{12}$" value="000000000000" name="referencia" id="input_7" style="display: block;" onkeyup="validar_referencia(${id})" title="debe ingresar un numero de 12 digitos">
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(6,${tasa})">
                <p>PM</p>
            </div>
    
            <div id="fecha_pagomovil"> 
                <input type="date" value="" id="fecha_pagomovil_input" class="input" style="display: none;" name="fecha_pago_movil">
                <select name="banco" id="7" class="input option item_pm_banco input_element" onchange="validar_referencia(${id})" style="display: none; border: solid 1px;" required>
                    ${bancos}
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
    
            <input type="submit" class="input_submit" value="Registrar cliente">
        </div>
                
            </div>
        </form>`,
        focusConfirm: false,
    });

    bancos = "";
}

function editar_tasa(tasa) {
    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
            popup: "popup_custom",
        },
        html: `<form action="/control_de_pago_remake/public/editar_tasa" method="POST" class="nuevo_registro_sub">
                    <h1 class="titulo_tasa">EDITAR TASA</h1>
                        <div  class="editar_tasa" id="datos_cliente_servicio">
                            <div class="item_input">
                                <i class="fa-solid fa-dollar-sign"></i>
                                <input type="number" class="input" name="tasa" step="any" id="tasa_input" value="${tasa}" placeholder="tasa" required>
                            </div>
                            <input type="submit" class="boton_pago" value="Guardar Cambios">             
                        </div>
               </form>`,
        focusConfirm: false,
    });
}

function toLocalISOString(date) {
    const localDate = new Date(date - date.getTimezoneOffset() * 60000);
    localDate.setSeconds(null);
    localDate.setMilliseconds(null);

    return localDate.toISOString().slice(0, -1);
}

function cambios(id) {
    window.open(`/control_de_pago_remake/public/cambios/${id}`, `_blank`);
}

function congelar(id, corte) {
    let hoy = new Date();
    let dia_corte = new Date(corte);

    let hoy_format = new Date(hoy);
    let dia_corte_format = new Date(dia_corte);

    let hoy_formateada = `${hoy_format.getDate() + 1}-${hoy_format.getMonth() + 1}-${hoy_format.getFullYear()}`;
    let corte_formateada = `${dia_corte_format.getDate() + 1}-${dia_corte_format.getMonth() + 1}-${dia_corte_format.getFullYear()}`;

    if (dia_corte > hoy) {
        Swal.fire({
            showConfirmButton: false,
            background: "rgba(0, 0, 0, 0)",
            heightAuto: false,
            customClass: {
                container: "container_modal",
                htmlContainer: "contenedor_add",
                popup: "popup_custom",
            },
            html: `<form action="/control_de_pago_remake/public/congelar" method="GET" class="nuevo_registro_sub">
                        <h1 class="titulo_tasa">Congelar cliente</h1>
                        <div  class="editar_tasa" id="datos_cliente_servicio">
                            <input type="hidden" name="id" value="${id}"> 
                            <input type="hidden" name="hoy" value="${hoy_formateada}"> 
                            <input type="hidden" name="dia" value="${corte_formateada}"> 
                            <input type="submit" class="input_submit" value="Congelar">
                        </div>
                   </form>`,
            focusConfirm: false,
        });
    } else {
        Swal.fire({
            customClass: {
                showConfirmButton: false,
                container: "container_modal",
                htmlContainer: "contenedor_add",
                popup: "popup_custom",
            },
            text: "No puedes congelar el servicio (la fecha de corte tiene que ser mayor al dia de hoy)",
        });
    }
}

function descongelar(id) {
    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
            popup: "popup_custom",
        },
        html: `<form action="/control_de_pago_remake/public/descongelar/${id}" method="GET" class="nuevo_registro_sub">
                    <h1 class="titulo_tasa">Descongelar cliente</h1>
                    <div  class="editar_tasa" id="datos_cliente_servicio">
                        <input type="submit" class="input_submit" value="Descongelar">
                    </div>
               </form>`,
        focusConfirm: false,
    });
}

function prorroga(op, id) {
    let html = "";

    if (op == 0) {
        html = `<form action="/control_de_pago_remake/public/prorroga/${id}" method="GET" class="nuevo_registro_sub">
                    <h1 class="titulo_tasa">Servicio de prorroga</h1>
                    <div  class="servicio_de_prorroga" id="datos_cliente_servicio">
                        <select name="dias" class="select_prorroga">
                            <option class="option_prorroga" value="1">1</option>
                            <option class="option_prorroga" value="2">2</option>
                            <option class="option_prorroga" value="3">3</option>
                            <option class="option_prorroga" value="4">4</option>
                            <option class="option_prorroga" value="5">5</option>
                            <option class="option_prorroga" value="6">6</option>
                            <option class="option_prorroga" value="7">7</option>
                        </select>
                        <textarea name="nota" rows="2" class="textarea_prorroga" id="prorroga_textarea"></textarea>
                    </div>
                    <input type="submit" class="input_submit" value="Prorroga">
                </form>`;
    } else {
        html = `<form action="/control_de_pago_remake/public/prorroga_quitar/${id}" method="GET" class="nuevo_registro_sub">
                    <h1 class="titulo_tasa">Eliminar prorroga</h1>
                    <div  class="editar_tasa" id="datos_cliente_servicio">
                        <input type="submit" class="input_submit" value="Eliminar">
                    </div>
                </form>`;
    }

    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
            popup: "popup_custom",
        },
        html: html,
        focusConfirm: false,
    });

    if (op == 0) {
        document.getElementById("prorroga_textarea").focus();
    }
}

function total_calculado(valor, op, tasa) {
    let parrafo_0 = document.getElementById("resumen_calculado"); // Muestra el resumen al mover ambos inputs
    let parrafo_1 = document.getElementById("total_calculado"); // parafo donde se muestra Total a cancelar (formula: 50 / 30 * ): 0.00$.
    let parrafo_2 = document.getElementById("cambio_calcular"); // parrafo donde se muestra : Al cambio: 0.0$ / total: 0.0$.

    let input_valor = document.getElementById("input_calcular").value; // valor del input de los dias a calcular.
    let resultado = (valor / 30) * input_valor; // formula para calcular el total a pagar.

    if (op == 0) {
        // se movio el input de los dias
        parrafo_0.innerHTML = `Resumen - Dias: ${input_valor} / Pagar: ${resultado.toFixed(2)}$`;
    } else {
        // se movio la cantidad a pagar
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

        let un_dia = (valor / 30) * 1;

        let formula_total = sub_total_global / un_dia;

        parrafo_0.innerHTML = `Resumen - Dias: ${Math.round(formula_total)} / Pagar: ${sub_total_global}$`;
    }

    parrafo_1.innerHTML = `Total a pagar por ${input_valor} dias: ${resultado.toFixed(2)}$`;
    parrafo_2.innerHTML = `total: ${sub_total_global.toFixed(2)}$`;
}

function deuda_funcion(id) {
    fetch(`/control_de_pago_remake/public/retornar_cliente/${id}`)
        .then((response) => response.json())
        .then(function (response) {
            Swal.fire({
                showConfirmButton: false,
                background: "rgba(0, 0, 0, 0)",
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                },
                html: `
                <form action="/control_de_pago_remake/public/menu/add_deuda/${id}" method="POST" id="formulario_de_pago" class="nuevo_pago">
                    <h1 class="titulo">Registrar una deuda a ${response[0]["nombre"]}</h1>
                    <input type="hidden" name="id" value="${id}">
                    <div class="agregar_deuda_sub">
                        <input type="text" name="motivo" placeholder="Motivo de deuda" class="input input_type input_element" required style="width: -webkit-fill-available;">
                        <br>
                        <div class="agregar_deuda_subs">
                            <input type="number" name="cantidad" id="input_1" class="input input_type input_element" placeholder="Deuda en $" value="" step="any" min="1" pattern="^(?!.*[eE].*$)" required style="width: -webkit-fill-available;">
                            <input type="submit" class="boton_pago" style="width: -webkit-fill-available;" value="Agregar deuda">
                        </div>
                    </div>
                <form>`,
                focusConfirm: false,
            });
        });
}

function calcular(id, tasa, valor, almacen) {
    fetch(`/control_de_pago_remake/public/retornar_cliente/${id}`)
        .then((response) => response.json())
        .then(function (response) {
            Swal.fire({
                showConfirmButton: false,
                background: "rgba(0, 0, 0, 0)",
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                },
                html: `
                        <form action="/control_de_pago_remake/public/menu/metodo_prepago/${id}" method="POST" id="formulario_de_pago" onsubmit="validar(event)" class="nuevo_pago">
                            <h1 class="titulo">Registrar cambio de fecha de ${response[0]["nombre"]}</h1>
                            <input type="hidden" name="id" value="${id}">
                            <p id="resumen_calculado">Resumen - Dias: 0 / Pagar: 0$</p>
                            <p id="cambio_calcular">Pagando - tasa: ${tasa}bs / <span>total: 0.00$</span></p>
                            
                            <p id="cambio">Al cambio: ${tasa}bs / total : 0.0$ / total: 0.0$</p>
        
                    <select name="" id="tipo" class="input option" onchange="tipo_de_pago()">
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
                        <input type="number" name="dolar" id="input_1" class="input input_type input_element" placeholder="Dolares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(1,${tasa})">
                        <p>$</p>
                    </div>

                    <div class="item" id="2">
                        <input type="number" name="bolivar" id="input_2" class="input input_type input_element" placeholder="Bolivares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(2,${tasa})">
                        <p>B</p>
                    </div>

                    <div class="item" id="3">
                        <input type="number" name="euro" id="input_3" class="input input_type input_element" placeholder="Euros" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(3,${tasa})">
                        <p>€</p>
                    </div>
        
                    <div class="item" id="4">
                    <input type="number" name="zelle_v" id="input_4" class="input input_type input_element" placeholder="Zelle Vladimir" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(4,${tasa})">
                        <p>Z V</p>
                    </div>
        
                    <div class="item" id="5">
                        <input type="number" name="zelle_j" id="input_5" class="input input_type input_element" placeholder="Zelle Jesus Millan" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(5,${tasa})">
                        <p>Z J</p>
                    </div>
                </div>
        
                <div class="item_pm" id="6">
                    <input type="number" name="pagomovil" id="input_6" class="input input_type input_element" placeholder="Monto" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" required>
                    <input type="text" class="input input_type input_element" placeholder="Referencia" pattern="^[0-9]{12}$" value="000000000000" name="referencia" id="input_7" style="display: block;" onkeyup="validar_referencia(${id})" title="debe ingresar un numero de 12 dígitos">
                    <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(6,${tasa})">
                    <p>PM</p>
                </div>
        
                <div id="fecha_pagomovil"> 
                <input type="date" value="" id="fecha_pagomovil_input" class="input" style="display: none;" name="fecha_pago_movil">
                    <select name="banco" id="7" class="input option item_pm_banco input_element" onchange="validar_referencia(${id})" style="display: none;" required>
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
                    <div class="calculadora_resumen">
                        <input type="number" name="dias" style="border: solid 1px #fff;" min="0" id="input_calcular" class="input input_element" placeholder="Dias a cancelar" value="" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="total_calculado(${response[0]["valor"]}, 0, ${tasa})" onkeyup="total_calculado(${response[0]["valor"]}, 0, ${tasa})">
                        <p id="total_calculado">Total a pagar por 0 dias: 0.00$</p>
                    </div>
                    <button id="submit" class="input_submit" >Registrar pago</button>
                    <small id="mensaje" style="display: none;">debes especificar algún monto*</small>
                </form>`,
                focusConfirm: false,
            });
        });
}

function calculadora(tasa) {
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
            <h1 class="titulo">Busque el cliente</h1>
            <table id="main_ss" class="ui inverted celled table" style="text-align-last: center; width: 99%;">
                <div class="busqueda_cliente">
                    <input type="text" class="input_unir" name="nombre" id="nombre_unir" placeholder="Nombre">
                    <button type="submit" onclick="buscar_cliente_calculadora(${tasa})" class="boton_pago">Buscar</button>
                </div><br>
                <div id="sel_cliente"></div>
                <p style="display: none; color: red;" id="error_unir">Debe ingresar al menos un dato.*</p>
            </table>
        </div>`,
        focusConfirm: false,
    });
}

function buscar_cliente_calculadora(tasa) {
    let contenedor = document.querySelector("#sel_cliente");

    let nombre = document.getElementById("nombre_unir").value;

    if (nombre == "") {
        nombre = "vacio";
    }

    let url = ``;

    url = `/control_de_pago_remake/public/clientes/clientes_ss/vacio/${nombre}`;

    contenedor.innerHTML = "";

    if (nombre != "vacio") {
        fetch(`${url}`)
            .then((response) => response.json())
            .then(function (response) {
                let loop_iteration = 0;

                response.forEach(function (item) {
                    contenedor.style.display = "grid";
                    loop_iteration += 1;
                    contenedor.innerHTML += `<p>${item["id"]}</p>`;
                    contenedor.innerHTML += `<p>${item["nombre"]}</p>`;
                    contenedor.innerHTML += `<p>${item["cedula"]}</p>`;
                    contenedor.innerHTML += `<button class="boton_pago" onclick="calcular(${item["id"]}, ${tasa})">Calcular</button>`;
                    contenedor.innerHTML += `<button class="boton_pago" onclick="deuda_funcion(${item["id"]})">Agregar deuda</button>`;
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

function administrativo() {
    window.open("/control_de_pago_remake/public/administrar", "_blank");
}

function ejecutar_funcion(parametro) {
    Swal.fire({
        title: `Generando factura...`,
        showConfirmButton: false,
    });

    setTimeout(() => {
        window.location.href = `/control_de_pago_remake/public/imprimir_factura_mensualidad/${parametro}`;
    }, 1500);
}

async function envio_formulario_editar_cliente(id) {
    url = `/control_de_pago_remake/public/clientes/menu_editar/${id}`;

    let nombre = document.getElementsByName("nombre")[0].value;
    let telefono = document.getElementsByName("telefono")[0].value;
    let cedula = document.getElementsByName("cedula")[0].value;
    let direccion = document.getElementsByName("direccion")[0].value;
    let servidor = document.getElementsByName("servidor")[0].value;
    let plan = document.getElementsByName("plan")[0].value;
    let ip = document.getElementsByName("ip")[0].value;
    let mac = document.getElementsByName("mac")[0].value;
    let corte = document.getElementsByName("corte")[0].value;
    let observacion = document.getElementsByName("observacion")[0].value;
    let iptv = document.getElementsByName("iptv")[0].value;
    let tipo_c = document.getElementsByName("tipo_c")[0].value;
    let act_des = document.getElementsByName("act_des")[0].value;
    let ticket = document.getElementsByName("ticket")[0].value;
    let asignacion = document.getElementsByName("asignacion")[0].value;

    const datos = {
        id: id,
        nombre: nombre,
        telefono: telefono,
        cedula: cedula,
        direccion: direccion,
        servidor: servidor,
        plan: plan,
        ip: ip,
        mac: mac,
        corte: corte,
        observacion: observacion,
        iptv: iptv,
        tipo_c: tipo_c,
        act_des: act_des,
        ticket: ticket,
        asignacion: asignacion,
    };

    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(datos),
    };

    try {
        // Realizar la petición POST utilizando fetch()
        const response = await fetch(url, options);

        if (!response.ok) {
            // Verificar si la respuesta fue exitosa (200-299)
            throw new Error(`Error al realizar la petición. Código ${response.status}`);
        }

        const resultado = await response.json(); // Obtener y procesar los datos devueltos por el servidor
    } catch (err) {
        console.log(err);
    } finally {
        Swal.fire({
            icon: "success",
            title: `Modificado!`,
            text: `El cliente fue modificado con exito!`,
            showConfirmButton: false,
        });
    }
}
