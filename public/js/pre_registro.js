var nombre_plan = "";
var seriales = "";
var identificador = "";

async function asignacion(serial, id) {
    if (serial) {
        try {
            const response = await fetch("/control_de_pago_remake/public/pre_registro/asignacion", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ serial, id }), // enviamos los datos recibidos en el body
            });

            if (!response.ok) {
                throw new Error(`Error en la petición: ${response.status}`);
            }

            const data = await response.json();
            console.log("Respuesta del servidor:", data);
            return data;
        } catch (error) {
            console.error("Error en la petición POST:", error);
        }
    }
}

async function ver_info(id, plan) {
    await $.get(`/control_de_pago_remake/public/pre_registro/get_plan/${plan}`, function (datos_plan) {
        nombre_plan = datos_plan.plan;
        identificador = id;
    });

    await $.get(`/control_de_pago_remake/public/pre_registro/get_serial/${id}`, function (datos_seriales) {
        seriales = "";

        if (datos_seriales[1]) {
            // Agrupar los seriales por producto
            const grupos = {};

            datos_seriales[1].forEach((item) => {
                if (!grupos[item.producto]) {
                    grupos[item.producto] = [];
                }
                
                grupos[item.producto].push(item);
            });

            seriales += `<select name='asignacion' onchange='asignacion(this.value, ${id})'>\n`;

            seriales += `<option value="">Selecciona una opción</option>\n`;

            for (const producto in grupos) {
                seriales += `<optgroup label="${producto}">\n`;

                grupos[producto].forEach((item) => {
                    seriales += `<option value="${item.serial}">${item.serial}</option>\n`;
                });

                seriales += `</optgroup>\n`;
            }

            if(datos_seriales[0][0]['asignacion'] != null && datos_seriales[0][0]['asignacion'] != "Sin equipo asignado."){
                seriales += `<optgroup label="Opciones"><option value="0">Quitar asignación</option></optgroup>\n`;
            }

            seriales += "</select>";
        }
    });

    await $.get(`/control_de_pago_remake/public/pre_registro/get_pre/${id}`, function (datos_pre) {
        let desde_formateada = moment(datos_pre.fecha_de_pago).locale("es").format("DD [de] MMMM [de] YYYY");

        let nombre = datos_pre.nombre;
        let direccion = datos_pre.direccion;
        let cedula = datos_pre.cedula;
        let telefono = datos_pre.telefono;
        let observacion = datos_pre.observacion;

        let cobrador = datos_pre.cobrador;
        let valor = datos_pre.valor;
        let total = datos_pre.total;
        let instalacion = datos_pre.instalacion;
        let tasa = document.getElementById("tasa").innerText;
        let fecha = document.getElementById("fecha").innerText;

        let pagado = "<p class='data'><span style='color: #00e900; font-weight: bolder;'> - PAGADO.</span></p>";

        if (datos_pre.pagado == 0) {
            pagado = `<p class="data"><span style='color: red; font-weight: bolder; cursor: pointer;' onclick="sel_dia('${fecha}', '${id}')"> - CANCELAR RESTANTE.</span></p>`;
        }

        if (observacion == "") {
            observacion = "Sin especificar";
        }

        //información adicional
        let asignacion = "";

        if (datos_pre.asignacion == null || datos_pre.asignacion == "") {
            asignacion = "Router del inventario sin asignar <img src='/control_de_pago_remake/public/img/pre_registro/advertencia.png' title='Este cliente aun no tiene router asignado'>";
        } else {
            asignacion = `${datos_pre.asignacion}`;
        }

        if (seriales == "") {
            seriales = "Privilegios insuficientes";
        }

        Swal.fire({
            showConfirmButton: false,
            heightAuto: false,

            html: ` 
                        <h1>- Datos del cliente:</h1>

                        <p class="data"><span class="data_title"> - Nombre:</span> ${nombre}.</p>
                        <p class="data"><span class="data_title"> - Dirección:</span> ${direccion}.</p>
                        <p class="data"><span class="data_title"> - Cédula:</span> ${cedula}.</p>
                        <p class="data"><span class="data_title"> - Teléfono:</span> ${telefono}.</p>
                        <p class="data"><span class="data_title"> - Plan:</span> ${nombre_plan}.</p>
                        <p class="data"><span class="data_title"> - Observación:</span> ${observacion}.</p>

                        <h1>- Datos del pago:</h1>

                        <p class="data"><span class="data_title"> - Cobrador:</span> ${cobrador}.</p>
                        <p class="data"><span class="data_title"> - Fecha de pago:</span> ${desde_formateada}.</p>
                        <p class="data"><span class="data_title"> - Instalación:</span> ${instalacion}.</p>
                        <p class="data"><span class="data_title"> - Asignación:</span> ${asignacion}.</p>
                        <p class="data"><span class="data_title"> - Asignar otro equipo:</span> ${seriales}.</p>
                        <p class="data"><span class="data_title"> - Total cancelado:</span> ${total}$ / ${valor}$.</p>
                        ${pagado}`,
        });
    });
}

// Función para escapar caracteres especiales en HTML
function escapeHtml(text) {
    return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function registrar_cliente_pre_registro(id) {
    //agregar cliente desde el pre-registro
    $.get(`/control_de_pago_remake/public/pre_registro/get_pre/${id}`, function (datos_pre) {
        planes = "";
        servidores = "";
        $.get("/control_de_pago_remake/public/data", function (response) {
            //llenar planes

            planes += '<optgroup label="Inalambrico">';
            let plan = datos_pre.plan;

            response[0].forEach(function (item) {
                if (item["tipo"] == 0) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });

            planes += "</optgroup>";

            planes += '<optgroup label="Fibra">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 1) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Empresariales">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 2) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Planes especiales">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 3) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Noria">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 4) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            //llenar planes fin

            response[1].forEach(function (item) {
                servidores += `<option value="${item["id"]}">${item["nombre_de_servidor"]}</option> \n`;
            });

            $.get(`/control_de_pago_remake/public/pre_registro/agregar/${id}`, function (response) {
                const { value: formValues } = Swal.fire({
                    showConfirmButton: false,
                    background: "rgba(0, 0, 0, 0)",
                    heightAuto: false,
                    customClass: {
                        container: "container_modal",
                        htmlContainer: "contenedor_add",
                        popup: "popup_custom",
                    },
                    html: `<form action="/control_de_pago_remake/public/pre_registro/registrar" method="POST" class="nuevo_cliente nuevo_pre_reg"> 
                <h1 class="titulo">Registrar cliente ${response[0]["nombre"]}</h1>
                
                <input type="hidden" name="id" value="${id}" required>
                <input type="hidden" name="nombre" value="${response[0]["nombre"]}" required>
                <input type="hidden" name="telefono" value="${response[0]["telefono"]}" required>
                <input type="hidden" name="cedula" value="${response[0]["cedula"]}" required>
                <input type="hidden" name="direccion" value="${response[0]["direccion"]}" required>
                
                <div class="item_input item_input_servicio" required>
                    <i class="fa-solid fa-server"></i>
                    <select name="servidor" id=""class="input">${servidores}</select>
                </div>
                
                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-tower-cell"></i>
                    <select name="plan" id="" class="input">${planes}</select>
                </div>
                
                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-network-wired"></i>
                    <input type="text" name="ip" id="" placeholder="Dirección ip" pattern="(([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\\.){3}([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" title="Ingrese una dirección ipv4" class="input" required>
                </div>

                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-network-wired"></i>
                    <input type="text" name="mac" id="" placeholder="Mac" pattern="^([0-9A-Fa-f]{2}[:\-]){5}([0-9A-Fa-f]{2})$" title="Ingrese una dirección mac" class="input" required>
                </div>

                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-calendar-check"></i>
                    <input type="date" name="fecha_i" id="" class="input_date date_picker" required>
                </div>

                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-dollar-sign"></i>
                    <input type="text" name="deuda" id="" placeholder="Motivo de la deuda" class="input">
                </div>

                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-dollar-sign"></i>
                    <input type="number" name="motivo" id="" step="any" placeholder="Ingrese la deuda" class="input">
                </div>
                
                <div class="item_input item_input_servicio">
                    <i class="fa-regular fa-clipboard"></i>
                    <input type="text" name="observacion" id="" placeholder="Observación" class="input">
                </div>
        
                <input type="submit" class="input_submit" value="Registrar cliente">
            </form>`,
                    focusConfirm: false,
                });
            });
        });
    });
}

function borrar(id, nombre, serial) {
    Swal.fire({
        title: `¿Estas seguro que vas a eliminar a ${nombre}?`,
        text: "No seras capaz de revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Eliminar",
    }).then((result) => {
        if (result.isConfirmed) {
            const borrar = fetch(`/control_de_pago_remake/public/pre_registro/borrar/${id}/${serial}`);

            Swal.fire({
                icon: "success",
                title: `${nombre} fue eliminad@ del PRE-REGISTRO con éxito!`,
                showConfirmButton: false,
                timer: 3000,
            });

            setTimeout(function () {
                window.location.reload();
            }, 3000);
        }
    });
}

async function pre_registro() {
    try {
        const pre_registro_datos = await fetch("/control_de_pago_remake/public/pre_registro/get_data")
            .then((response) => response.json())
            .then((data) => {
                $("#main").DataTable({
                    responsive: true,
                    destroy: true,
                    info: false,
                    paging: false,
                    language: {
                        url: "/control_de_pago_remake/public/js/lenguaje.json",
                    },
                    data: data,
                    columns: [
                        {
                            data: "id",
                            render: function (data, type, row) {
                                let titulo = "";
                                
                                if (row.estado == 0) {
                                    titulo = "En espera.";
                                } else if (row.estado == 1) {
                                    titulo = "Instalado sin registrar.";
                                } else {
                                    titulo = "Error al instalar.";
                                }

                                return `<div class="estado estado_color_${row.estado}" title="${titulo}" onclick="estado(${row.estado}, ${row.id})">${row.id}</div>`;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                let hr = "";
                                let tipo = "";
                                let advertencia = "";
                                let finanza = "";

                                if (row.comentario != "") {
                                    hr = "<hr>";
                                }

                                if (row.pagado == 0) {
                                    advertencia = `<img src='/control_de_pago_remake/public/img/pre_registro/advertencia.png' title='Este cliente aun no paga su instalación'>`;
                                }

                                if(row.total == 0){
                                    finanza = "<img src='/control_de_pago_remake/public/img/pre_registro/m.png' class='finanzaImg' title='Instalación autorizada por Marco Escala'>";
                                }

                                if (row.tipo_de_servicio == 0) {
                                    tipo = `<span style="text-decoration: underline;">FIBRA</span>${advertencia}<br>`;
                                } else {
                                    tipo = `<span style="text-decoration: underline;">INALÁMBRICO</span>${advertencia}<br>`;
                                }

                                return `<div onclick="ver_info('${row.id}','${row.plan}')"><p>${tipo}<span class="nombreFinanza">${row.nombre}${finanza}</span>${hr}${row.comentario}<span style="display: none;">'${row.nombre}','${row.direccion}','${row.cedula}','${row.telefono}','${row.tipo_de_servicio}','${row.plan}', '${row.fecha_de_pago}'</span></p></div>`;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                return `<img src="/control_de_pago_remake/public/img/pre_registro/registrar.png" class="boton filtro" onclick="registrar_cliente_pre_registro('${row.id}')">`;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                return `<img src="/control_de_pago_remake/public/img/pre_registro/resumen.png" class="boton filtro" onclick="editar_cliente_pre_registro('${row.id}')">`;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                return `<img src="/control_de_pago_remake/public/img/pre_registro/eliminar.png" class="boton" onclick="borrar('${row.id}', '${row.nombre}', '${row.asignacion}')">`;
                            },
                        },
                    ],
                });
            });
    } catch (error) {
        console.log(error);
    }
}

function editar_cliente_pre_registro(id) {
    //editar cliente desde el pre-registro
    $.get(`/control_de_pago_remake/public/pre_registro/get_pre/${id}`, function (datos_pre) {
        planes = "";
        servidores = "";

        let nombre = datos_pre.nombre;
        let direccion = datos_pre.direccion;
        let cedula = datos_pre.cedula;
        let telefono = datos_pre.telefono;
        let tipo_de_servicio = datos_pre.tipo_de_servicio;
        let plan = datos_pre.plan;
        let observacion = datos_pre.observacion;
        let id = datos_pre.id;

        $.get("/control_de_pago_remake/public/data", function (response) {
            //llenar planes

            planes += '<optgroup label="Inalambrico">';

            response[0].forEach(function (item) {
                if (item["tipo"] == 0) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });

            planes += "</optgroup>";

            planes += '<optgroup label="Fibra">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 1) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });

            planes += "</optgroup>";

            planes += '<optgroup label="Empresariales">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 2) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Planes especiales">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 3) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            planes += '<optgroup label="Noria">';
            response[0].forEach(function (item) {
                if (item["tipo"] == 4) {
                    if (plan == item["id"]) {
                        planes += `<option value="${item["id"]}" selected>${item["plan"]} ${item["valor"]}$</option> \n`;
                    } else {
                        planes += `<option value="${item["id"]}">${item["plan"]} ${item["valor"]}$</option> \n`;
                    }
                }
            });
            planes += "</optgroup>";

            //llenar planes fin

            let antena = "";
            let fibra = "";

            if (datos_pre.tipo_de_servicio == 0) {
                fibra = "checked";
            } else {
                antena = "checked";
            }

            planes += "</optgroup>";

            Swal.fire({
                showConfirmButton: false,
                background: "rgba(0, 0, 0, 0)",
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                    popup: "popup_custom",
                },
                html: `<form action="/control_de_pago_remake/public/pre_registro/editar/${id}" method="POST" class="nuevo_cliente nuevo_pre_reg"> 
                <h1 class="titulo">Editar cliente ${nombre}</h1>

                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" class="input" name="nombre" id="" value="${nombre}" required>
                    <input type="hidden" class="input" name="id" id="" value="${id}" required>
                </div>
                <div class="item_input item_input_servicio">
                    <i class="fa-regular fa-id-card"></i>
                    <input type="text" class="input" name="cedula" id="" value="${cedula}" required>
                </div>
                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-phone"></i>
                    <input type="text" class="input" name="telefono" id="" value="${telefono}" required>
                </div>
                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-map-location"></i>
                    <input type="text" class="input" name="direccion" id="" value="${direccion}" required>
                </div>

                <div class="item_input item_input_servicio">
                    <i class="fa-solid fa-tower-cell"></i>
                    <select name="plan" id="" class="input">${planes}</select>
                </div>
                
                <div class="item_input item_input_servicio">
                    <i class="fa-regular fa-clipboard"></i>
                    <input type="text" name="observacion" id="" placeholder="Observación" value="${observacion}" class="input">
                </div>

                <div id="registrado">
                    Tipo de instalación<br>
                    <label for="no">Antena</label>
                    <input type="radio" name="tipo_de_servicio" value="1" id="no" ${antena} required>
                    <label for="si">Fibra</label>
                    <input type="radio" name="tipo_de_servicio" value="0" id="si" ${fibra} required>
                </div>
        
                <input type="submit" class="input_submit" value="Editar cliente">
            </form>`,
                focusConfirm: false,
            });
        });
    });
}

function estado(estado, id) {
    if (estado != 1) {
        Swal.fire({
            title: "Cambiando estado...",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: async function () {
                Swal.showLoading();
                await fetch(`/control_de_pago_remake/public/pre_registro/cambiar_estado/${id}`);
                pre_registro();
            },
        });
    } else {
        Swal.fire({
            showConfirmButton: false,
            heightAuto: false,
            html: `
            <form action="/control_de_pago_remake/public/pre_registro/cambiar_estado/${id}" method="GET" class="nuevo_cliente nuevo_pre_reg">
                <input type="text" name="comentario" placeholder="Ingrese el comentario" value="" class="input" required>
                <input type="submit" class="input_submit" value="Enviar">
            </form>`,
        });
    }
}

async function devolver_tasa(fecha, id) {
    try {
        await fetch(`/control_de_pago_remake/public/consultar_tasa_fecha/${fecha}`)
            .then((response) => response.json())
            .then((data) => {
                tasa = data.tasa;

                deuda_instalacion(`${id}`, `${fecha}`, `${tasa}`);
            });
    } catch (err) {
        console.log(err);
    }
}

function fecha_seleccionada(id) {
    let dia_sel = document.getElementById("fecha_pagomovil_input_dia").value;

    devolver_tasa(dia_sel, id);

    swal.close();
}

async function verificar_pre() {
    const fecha = document.getElementById("fecha_pagomovil_input_dia").value;

    const response = await fetch(`/control_de_pago_remake/public/verificar_multipago/${fecha}`)
        .then((response) => response.json())
        .then((data) => {
            let alerta_tasa = document.getElementById("alerta_tasa");
            let correcta_tasa = document.getElementById("correcta_tasa");
            let desactivado = document.getElementById("desactivado");

            if (data) {
                alerta_tasa.style.display = "none";
                correcta_tasa.style.display = "block";
                desactivado.removeAttribute("disabled");
                desactivado.setAttribute("class", "submit_multi");
            } else {
                alerta_tasa.style.display = "block";
                correcta_tasa.style.display = "none";
                desactivado.setAttribute("disabled", "");
                desactivado.setAttribute("class", "desactivado");
            }
        });
}

async function sel_dia(hoy, id) {
    let max = new Date().toISOString().split("T")[0];
    console.log(hoy);

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
                <h1>Seleccione la fecha del pago<h1>
                <div class="pay_date">
                    <input type="date" id="fecha_pagomovil_input_dia" value="${hoy}" max="${max}" onchange="verificar_pre()" class="input fecha_pagomovil_aux" style="font-size: large; display: grid;">
                    <button id="desactivado" class="desactivado" title="seleccionar fecha del pagomovil" onclick="fecha_seleccionada(${id})" disabled>Seleccionar</button>
                </div>
                <p id="alerta_tasa">La fecha seleccionada no tiene tasa registrada*</p>
                <p id="correcta_tasa">La fecha seleccionada es valida!*</p>
            </div>`,
        focusConfirm: false,
    });

    setTimeout(() => {
        verificar_pre();
    }, 1500);
}

function deuda_instalacion(id, dia, tasa) {
    // Pagar deuda de instalación
    $.get(`/control_de_pago_remake/public/pre_registro/get_pre/${id}`, function (response) {
        // variables para el formulario

        let nombre_formulario = response.nombre;
        let id_formulario = response.id;
        let valor_formulario = response.valor;
        let total_formulario = response.total;

        let fecha = document.getElementById("fecha").value;

        let sub_total = valor_formulario - total_formulario;

        const { value: formValues } = Swal.fire({
            showConfirmButton: false,
            background: "rgba(0, 0, 0, 0)",
            heightAuto: false,
            customClass: {
                container: "container_modal",
                htmlContainer: "contenedor_add",
            },
            html: `<form action="/control_de_pago_remake/public/pre_registro/abono" method="POST" id="formulario_de_pago" onsubmit="validar(event)" class="nuevo_pago">
                    <h1 class="titulo">Pago de instalación</h1>
                    <br>
                    <p>El restante de la instalación es: ${parseFloat(sub_total).toFixed(2)}$<br>(Al cambio: ${(parseFloat(sub_total) * parseFloat(tasa)).toFixed(2)}Bs.)<span></p>
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="tasa" value="${tasa}">
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
                sub_total
            ).toFixed(2)},${tasa})" required>
                            <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(1,${tasa})">
                            <p>$</p>
                        </div>
                        
                        <div class="item" id="2">
                            <input type="number" name="bolivar" id="input_2" class="input input_type input_element" placeholder="Bolivares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_2',${(
                parseFloat(sub_total) * parseFloat(tasa)
            ).toFixed(2)},${tasa})" required>
                            <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(2,${tasa})">
                            <p>B</p>
                        </div>
            
                        <div class="item" id="3">
                            <input type="number" name="euro" id="input_3" class="input input_type input_element" placeholder="Euros" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_3', ${parseFloat(
                sub_total
            ).toFixed(2)},${tasa})" required>
                            <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(3,${tasa})">
                            <p>€</p>
                        </div>
            
                        <div class="item" id="4">
                        <input type="number" name="zelle_v" id="input_4" class="input input_type input_element" placeholder="Zelle Vladimir" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_4', ${parseFloat(
                sub_total
            ).toFixed(2)},${tasa})" required>
                            <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(4,${tasa})">
                            <p>Z V</p>
                        </div>
            
                        <div class="item" id="5">
                            <input type="number" name="zelle_j" id="input_5" class="input input_type input_element" placeholder="Zelle Jesus Millan" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_5', ${parseFloat(
                sub_total
            ).toFixed(2)},${tasa})" required>
                            <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(5,${tasa})">
                            <p>Z J</p>
                        </div>
                    </div>
            
                    <div class="item_pm" id="6">
                        <input type="number" name="pagomovil" id="input_6" class="input input_type input_element" placeholder="Monto" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio(${tasa})" onkeyup="cambio(${tasa})" ondblclick="agg_cantidad('input_6',${(
                parseFloat(sub_total) * parseFloat(tasa)
            ).toFixed(2)},${tasa})" required>
                        <input type="text" class="input input_type input_element" placeholder="Referencia" pattern="^[0-9]{12}$" value="000000000000" name="referencia" id="input_7" style="display: block;" title="debe ingresar un numero de 12 dígitos" onkeyup="validar_referencia(${id})">
                        <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(6,${tasa})">
                        <p>PM</p>
                    </div>
                    <div id="fecha_pagomovil"> 
                    <input type="hidden" id="fecha_pagomovil_input" value="${dia}" class="input" style="display: none;" name="fecha_pago_movil">
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
                        <option value="BDC">Venezolana de Crédito</option>
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
                    <div id="banco_receptor" style="display: none;">
                        <label>Provincial<br><input type="radio" value="1" class="" checked name="banco_receptor"></label>
                        <label>Banesco<br><input type="radio" value="2" class="" name="banco_receptor"></label>
                        <label>Venezuela<br><input type="radio" value="3" class="" name="banco_receptor"></label>
                        <label>Punto bicentenario<br><input type="radio" value="4" class="" name="banco_receptor"></label>
                        <label>Punto Venezuela<br><input type="radio" value="5" class="" name="banco_receptor"></label>
                        <label>Punto caroni<br><input type="radio" value="6" class="" name="banco_receptor"></label>
                        <label>Biópago (avícola)<br><input type="radio" value="7" class="" name="banco_receptor"></label>
                    </div>
                        </div>
                        <p id="cambio">tasa: ${tasa}bs / al cambio: 0.0$ / total : 0.0$</p>
                    <button id="submit" class="input_submit" >Registrar pago</button>
                    <small id="mensaje" style="display: none;">debes especificar algún monto*</small>
                </form>`,
            focusConfirm: false,
        });
    });
}

pre_registro();
