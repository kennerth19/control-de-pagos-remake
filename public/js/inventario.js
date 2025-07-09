const log = document.getElementById("log");

var html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 30, qrbox: { width: 250, height: 50 }, showTorchButtonIfSupported: true });
var cameraId = -1;
var cameraIdLotesDesde = -1;
var cameraIdLotesHasta = -1;

function onScanSuccess(decodedText, decodedResult) {
    if (cameraId > -1) {
        document.getElementById(`camara_${cameraId}`).value = decodedText;
        document.getElementById(`camara_${cameraId}`).click();

        let identificador = document.getElementById(`camara_${cameraId}`);

        setTimeout(function () {
            identificador.scrollIntoView({ behavior: "smooth", block: "center" });
        }, 500);

        cameraId = -1;
    }

    if (cameraIdLotesDesde > -1) {
        document.getElementById(`desde_${cameraIdLotesDesde}`).value = decodedText;
        document.getElementById(`desde_${cameraIdLotesDesde}`).click();

        let identificador_desde = document.getElementById(`desde_${cameraIdLotesDesde}`);

        setTimeout(function () {
            identificador_desde.scrollIntoView({ behavior: "smooth", block: "center" });
        }, 500);

        cameraIdLotesDesde = -1;
    }

    if (cameraIdLotesHasta > -1) {
        document.getElementById(`hasta_${cameraIdLotesHasta}`).value = decodedText;
        document.getElementById(`hasta_${cameraIdLotesHasta}`).click();

        let identificador_hasta = document.getElementById(`hasta_${cameraIdLotesHasta}`);

        setTimeout(function () {
            identificador_hasta.scrollIntoView({ behavior: "smooth", block: "center" });
        }, 500);
        cameraIdLotesHasta = -1;
    }

    document.getElementById("reader").style.height = "0vh";
    document.getElementById("entrada_menu").style.display = "grid";
    document.getElementById("menu_container").style.display = "block";

    html5QrcodeScanner.clear();
}

function camara(id, op) {
    html5QrcodeScanner.clear();

    document.getElementById("entrada_menu").style.display = "none";
    document.getElementById("menu_container").style.display = "none";
    document.getElementById("reader").style.height = "99vh";

    document.getElementById("reader").scrollIntoView({ behavior: "smooth" });
    document.getElementById("reader").zIndex = "2500";
    document.getElementsByTagName("body")[0].overflowY = "hidden";

    if (op == 0) {
        // lotes desde
        cameraIdLotesDesde = id;
    } else if (op == 1) {
        // lotes hasta
        cameraIdLotesHasta = id;
    } else if (op == 2) {
        // individual
        cameraId = id;
    }

    html5QrcodeScanner.render(onScanSuccess);
}

log.addEventListener("click", () => {
    window.open("/control_de_pago_remake/public/inventario_log", "_blank");
});

var formulario_0 = []; // Variable para guardar seriales, para el uso de objetos en el inventario que son routers.
var formulario_1 = []; // Variable para guardar objetos (id:cantidad), para el uso de objetos en el inventario que no son routers.

var comodin_entrada = false; // Variable para detectar si se pulso el botón de entrada al inventario y quitar el limite de entradas en agregar existencias al inventario.

function add_exi(id, tipo) {
    let mac = `<input type="text" name="mac" id="" placeholder="Mac">`;
    let serial = `<input type="text" name="serial" id="" placeholder="Serial" required>`;
    let cantidad = `<input type="number" name="cantidad" min="1" value="1" required>`;

    let resultado = tipo == 0 ? serial : cantidad;

    Swal.fire({
        title: "¿Agregar productos a esta categoría?",
        showDenyButton: false,
        showConfirmButton: false,
        html: `
                    <form method="POST" action="/control_de_pago_remake/public/add_exi">
                        <input type="hidden" name="id" value="${id}">
                        <input type="hidden" name="tipo" value="${tipo}">
                        ${resultado}
                        <input type="submit" value="Guardar">
                    </form>
                `,
    }).then((result) => {
        if (result.isConfirmed) {
            console.log("Agregando producto al inventario.");
        } else if (result.isDenied) {
            console.log("No se agrego el producto al inventario");
        }
    });
}

$(document).ready(function () {
    $.ajax({
        type: "GET",
        url: "/control_de_pago_remake/public/get_inv",
        dataType: "json",
        success: function (data) {
            let html = `<p id="titulo">Inventario</p>`;

            if (data.length > 0) {
                $.each(data, function (index, item) {
                    let funcion_salida = "display: none;";
                    let funcion_expandir = "display: grid";
                    let existencias = `<div class="existencia" id="existencias_${item.id}" data-selected="0" data-expandido="0"></div>`;
                    let entrada = `
                                    <div class="entrada" id="entrada_${item.id}" data-selected="0" data-expandido="0" style="display: none;">
                                        <div class="opciones_categoria">
                                            <div class="capsula-slash-right unidad" id="right_e_${item.id}">Unidad</div>
                                            <div class="capsula-slash-mid unidad"></div>
                                            <div class="capsula-slash-left lotes" id="left_e_${item.id}" onclick="seleccion_entrada(0, ${item.id})" data-opened="0">Lotes</div>
                                        </div>
                                        <div class="contenedor_seleccion" id="add_item_${item.id}">
                                            <p class="header_seccion" id="header_eliminar_${item.id}">Eliminar</p>
                                            <p class="header_seccion">Serial</p>
                                            <p class="header_seccion">Escanear</p>
                                            <input type="text" class="desde" id="desde_${item.id}" value="" placeholder="Desde" style="display: none;">
                                            <img src="/control_de_pago_remake/public/img/inventario/camara.png" id="camara_desde_${item.id}" onclick="camara(${item.id}, 0)" alt="Capturar serial con la camara." class="camara" style="display: none;">
                                            <input type="text" class="hasta" id="hasta_${item.id}" value="" placeholder="Hasta" style="display: none;">
                                            <img src="/control_de_pago_remake/public/img/inventario/camara.png" id="camara_hasta_${item.id}" onclick="camara(${item.id}, 1)" alt="Capturar serial con la camara." class="camara" style="display: none;">
                                        </div>
                                        <div class="sumar_serial" id="suma_serial_${item.id}" onclick="suma_serial(${item.id})" style="display: none;">+</div>
                                    </div>`;

                    let dataout = 0;
                    let foto = item.pic;
                    let div = "";

                    if (item.unidades <= 0) {
                        funcion_salida = "display: block";
                        funcion_expandir = "display: none;";
                        dataout = 1;
                    }

                    if (item.tipo == 1) {
                        funcion_expandir = "display: none;";
                        existencias = "";
                        entrada = "";
                        div = `<div class="hr_sintetico" id="hr_sintetico_${item.id}" data-selected="0">`;
                    } else {
                        div = `<div class="hr_sintetico" id="hr_sintetico_${item.id}" data-selected="0" onclick="expandir_categoria(${item.id})">`;
                    }

                    if (item.pic == "" || item.pic == null) {
                        foto = `categoria/noFoto.png`;
                    }

                    html += `
                            <div class="item" id="item_${item.id}" data-selected="0">

                                <div class="seleccion_inventario" id="seleccion_${item.id}" onclick="check_salida(${item.id}, ${item.tipo}, ${item.unidades})"></div>

                                <div>
                                    <img src="/control_de_pago_remake/storage/app/public/${foto}" class="ilustracion" title="Ilustración">
                                    <p class="agotado" style="${funcion_salida}" id="agotado_${item.id}" data-id="${item.id}" data-out="${dataout}">Agotado</p>
                                </div>

                                <div style="width: 130px;">
                                    <p class="producto_name">${item.producto}</p>
                                    <br>
                                    <div class="cantidad_container">
                                        cantidad: 
                                        <div class="cantidad" id="cantidad_${item.id}">
                                            ${item.unidades}
                                        </div>

                                        <div class="cantidad_salida" id="cantidad_salida_${item.id}">
                                            <span onclick="span_c(${item.id}, ${item.unidades}, 0)" id="span_r_${item.id}" class="salida_span">-</span><span id="main_span_${item.id}">0</span><span onclick="span_c(${item.id}, ${item.unidades}, 1)" id="span_m_${item.id}" class="salida_span">+</span>
                                        </div>

                                        <div class="cantidad_entrada" id="cantidad_entrada_${item.id}" style="display: none">
                                            <span id="main_span_entrada_${item.id}">0</span>
                                        </div>
                                    </div>
                                </div>

                                <img src="/control_de_pago_remake/public/img/inventario/lapiz.png" data-selected="0" class="lapiz" onclick="editar_categoria(${item.id})" title='Editar la categoría "${item.producto}"'>
                            </div>

                            ${entrada}

                            ${div}
                                <img src="/control_de_pago_remake/public/img/inventario/abajo.png" id="abajo_${item.id}" style="${funcion_expandir}" class="abajo" title="Ver existencias">
                            </div>
                            ${existencias}
                            
                        `;
                });
                $("#inventario").html(html);
            }
        },
    });
});

async function eliminar(id, und) {
    Swal.fire({
        title: "¿Eliminar categoría?",
        text: `Ojo: hay ${und} unidades almacenadas.`,
        showDenyButton: false,
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Eliminar",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/control_de_pago_remake/public/inv_delete/${id}`, { method: "DELETE" })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    } else {
                        Swal.fire({
                            icon: "success",
                            title: "¡Categoría eliminada!",
                            showConfirmButton: false,
                            timer: 3000,
                        });

                        setTimeout(() => window.location.reload(), 3000);
                    }
                })
                .catch((error) => {
                    Swal.fire({
                        icon: "error",
                        title: "¡Error al eliminar la categoría, intente de nuevo!",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                });
        }
    });
}

async function editar_categoria(id) {
    try {
        if (parseInt(document.getElementById(`cantidad_${id}`).innerText) > 0) {
            document.getElementById("eliminar_categoria").style.display = "none";
        }

        await fetch(`/control_de_pago_remake/public/get_cat/${id}`)
            .then((response) => response.json())
            .then((data) => {
                if (data.tipo == 1) {
                    left_edit.click();
                }

                let menu = document.getElementsByClassName("icon_menu_inv");
                document.getElementById("editar_categoria").style.display = "grid";
                document.getElementById("editar_menu").style.display = "flex";
                document.getElementById("inventario").style.display = "none";
                document.getElementById("icon_menu_inv").style.display = "none";
                document.getElementById("categoria_id").innerHTML = id;
                document.getElementById("categoria_id_edit").value = id;
                document.getElementsByClassName("buscador_container")[0].style.display = "none";

                for (let index = 0; index < menu.length; index++) {
                    menu[index].style.display = "none";
                }

                /* Campos del formulario */
                document.getElementById("nombre_categoria_edit").value = data.producto;
                document.getElementById("entrada_img_edit").style.background = `url('/control_de_pago_remake/storage/app/public/${data.pic}')`;
                document.getElementById("entrada_img_edit").style.backgroundSize = "cover";
            });
    } catch (err) {
        console.log(err);
    }
}

async function main_function(op) {
    try {
        const requests = [];

        Swal.fire({
            title: op,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        let agotado = document.getElementsByClassName("agotado");

        for (let index = 0; index < agotado.length; index++) {
            requests.push(
                await fetch(`/control_de_pago_remake/public/get_exi/${agotado[index].getAttribute("data-id")}`)
                    .then((response) => response.json())
                    .then((data) => ({ index, data }))
                    .catch((error) => ({ index, error }))
            );
        }

        const results = await Promise.all(requests);

        results.forEach((result) => {
            if (result.error) {
                console.error(`Error en índice ${result.index}:`, result.error);
                return;
            }

            const { index, data } = result;

            if (result.data[0] != undefined) {
                const html = [
                    `<div class="content_serial content_serial_${result.data[0].categoria_id}"><p>Serial</p><p>Asignado</p><p class="seleccionar_head seleccionar_head_${result.data[0].categoria_id}">Seleccionar</p></div>`,
                    ...data.map(
                        (item) => `
                        <div class="content_serial content_serial_${result.data[0].categoria_id}" id="salida_content_serial_${item.id}" data-selected="0">
                            <p>${item.serial}</p>
                            <p>${item.asignado == 1 ? item.observacion : "No"}</p>
                            <div class="seleccionar seleccionar_${result.data[0].categoria_id}" style="display: none;" id="mark_${item.id}" onclick="check(${item.id}, ${result.data[0].categoria_id})"></div>
                        </div>
                    `
                    ),
                ].join("");

                $(`#existencias_${result.data[0].categoria_id}`).html(html);
            }

            Swal.close();
        });
    } catch (error) {
        console.error("Error general:", error);
    }
}

async function salida_router(id) {
    // Para salida de la categoría "router" (especificar responsable y cantidad).
    try {
    } catch (error) {
        console.error(`Error tipo: ${error.name} - ${error.message}`);
    }
}

async function salida_otros(id, cantidad) {
    // Para salida de la categoría "otros" (especificar responsable y cantidad).
    try {
        Swal.fire({
            title: "Generar salida de producto",
            showDenyButton: false,
            showConfirmButton: false,
            html: `
                <form method="POST" action="/control_de_pago_remake/public/salida_otros">
                    <input type="hidden" name="id" value="${id}">
                    <input type="text" name="responsable" placeholder="Nombre del responsable" required>
                    <input type="number" name="cantidad" value="1" min="1" max="${cantidad}" step="1">

                    <input type="submit" value="Generar salida">
                </form>
            `,
        });
    } catch (error) {
        console.error(`Error tipo: ${error.name} - ${error.message}`);
    }
}

// Animación de salida y entrada del menu.

function menu_des(id) {
    // elemento que desencadena el menu
    let cross = document.getElementById(id);
    let cerrar = document.querySelectorAll(".texto_cerrar");
    let menu_container = document.getElementById("menu_container");

    let crear = document.getElementById("crear");
    let entrar = document.getElementById("entrar");
    let salir = document.getElementById("salir");
    let log = document.getElementById("log");

    if (cross.id == "cross") {
        // Propiedades cuando se abre el menu.

        cross.removeAttribute("onclick");

        cross.style.transform = "rotate(315deg)";
        cross.style.backgroundColor = "rgb(246 63 67)";
        cross.id = "mas";

        crear.style.bottom = "65%";
        entrar.style.bottom = "50%";
        salir.style.bottom = "35%";
        log.style.bottom = "20%";

        crear.style.opacity = "1.0";
        entrar.style.opacity = "1.0";
        salir.style.opacity = "1.0";
        log.style.opacity = "1.0";

        setTimeout(() => {
            cross.setAttribute("onclick", "menu_des('mas')");
        }, 1000);

        menu_container.style.backgroundColor = "rgba(0 0 0 / 60%)";
        menu_container.style.zIndex = "15";

        for (let index = 0; index < cerrar.length; index++) {
            cerrar[index].style.position = "static";
            cerrar[index].style.right = "0";
            cerrar[index].style.opacity = "1.0";
        }

        // animaciones por cada menu:
    } else {
        // Propiedades cuando se cierra el menu.

        cross.removeAttribute("onclick");

        cross.style.transform = "rotate(0deg)";
        cross.style.backgroundColor = "rgb(74 91 224)";
        cross.id = "cross";

        crear.style.bottom = "6%";
        entrar.style.bottom = "6%";
        salir.style.bottom = "6%";
        log.style.bottom = "6%";

        crear.style.opacity = "0.0";
        entrar.style.opacity = "0.0";
        salir.style.opacity = "0.0";
        log.style.opacity = "0.0";

        document.getElementById("inventario_vacio").style.display = "none";

        menu_container.style.backgroundColor = "rgba(0 0 0 / 0%)";

        setTimeout(() => {
            menu_container.style.zIndex = "0";
            cross.setAttribute("onclick", "menu_des('cross')");
        }, 1000);

        for (let index = 0; index < cerrar.length; index++) {
            cerrar[index].style.position = "relative";
            cerrar[index].style.right = "-100%";
            cerrar[index].style.opacity = "0";
        }
    }
}

function expandir_categoria(id) {
    let existencia_div = document.getElementById(`existencias_${id}`);
    let abajo = document.getElementById(`abajo_${id}`);

    if (existencia_div.getAttribute("data-expandido") == 0) {
        existencia_div.dataset.expandido = "1";
        existencia_div.style.display = "grid";
        abajo.style.transform = "rotateX(180deg)";
    } else {
        existencia_div.dataset.expandido = "0";
        existencia_div.style.display = "none";
        abajo.style.transform = "rotate(0deg)";
    }
}

function salida() {
    Swal.fire({
        title: "Cargando modo salida",
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
        didOpen: () => {
            menu_des("mas");
            let titulo = document.getElementById("titulo");
            let cantidad = document.getElementsByClassName("cantidad");
            let item = document.getElementsByClassName("item");
            let seleccion_inventario = document.getElementsByClassName("seleccion_inventario");
            let lapiz = document.getElementsByClassName("lapiz");
            let salida_menu = document.getElementById("salida_menu");
            let icon_menu_inv = document.getElementById("icon_menu_inv");

            document.getElementsByClassName("buscador_container")[0].style.display = "none";
            titulo.innerText = "Selección de salida";
            titulo.style.backgroundColor = "rgb(255 60 64)";
            salida_menu.style.display = "grid";
            icon_menu_inv.style.display = "none";

            for (let j = 0; j < item.length; j++) {
                // para mostrar el check para activar la selección por item
                item[j].style.gridTemplateColumns = "5% 30% 1fr 1fr";
                seleccion_inventario[j].style.display = "block";
                let agotado = document.getElementById(`agotado_${j + 1}`);
                let hr_sintetico = document.getElementById(`hr_sintetico_${j + 1}`);
            }

            for (let index = 0; index < lapiz.length; index++) {
                lapiz[index].style.display = "none";
            }

            for (let m = 0; m < cantidad.length; m++) {
                // Para poner las cantidades en 0
                cantidad[m].innerHTML = "0";
            }

            Swal.showLoading();
        },
    });
}

function check(id, cantidad_div) {
    let check = document.getElementById(`mark_${id}`);
    let cantidad = document.getElementById(`cantidad_${cantidad_div}`);

    /* Elementos dataset */
    document.getElementById(`item_${cantidad_div}`).dataset.selected = "1";
    document.getElementById(`hr_sintetico_${cantidad_div}`).dataset.selected = "1";
    document.getElementById(`existencias_${cantidad_div}`).dataset.selected = "1";
    document.getElementById(`salida_content_serial_${id}`).dataset.selected = "1";
    /* Elementos dataset */

    let img = document.createElement("img");
    img.src = "/control_de_pago_remake/public/img/inventario/check.png";
    img.style = "width: 32px";
    img.id = `img_${id}`;

    check.appendChild(img);

    check.removeAttribute("onclick", "");
    check.setAttribute("onclick", `uncheck(${id}, ${cantidad_div})`);

    let cantidad_int = parseInt(cantidad.innerText);

    cantidad_int += 1;

    cantidad.innerText = cantidad_int;

    formulario_0.push({ id: id, categoria: cantidad_div });
}

function uncheck(id, cantidad_div) {
    let check = document.getElementById(`mark_${id}`);
    let cantidad = document.getElementById(`cantidad_${cantidad_div}`);

    let img = document.getElementById(`img_${id}`);

    /* Elementos dataset */
    document.getElementById(`item_${cantidad_div}`).dataset.selected = "0";
    document.getElementById(`hr_sintetico_${cantidad_div}`).dataset.selected = "0";
    document.getElementById(`existencias_${cantidad_div}`).dataset.selected = "0";
    document.getElementById(`salida_content_serial_${id}`).dataset.selected = "0";
    /* Elementos dataset */

    img.remove();

    check.removeAttribute("onclick", "");
    check.setAttribute("onclick", `check(${id}, ${cantidad_div})`);

    let cantidad_int = parseInt(cantidad.innerText);

    cantidad_int -= 1;

    cantidad.innerText = cantidad_int;

    const nuevoArray = formulario_0.filter((obj) => obj.id !== id);

    formulario_0 = nuevoArray;
}

function check_salida(id, op, val) {
    if (val > 0) {
        let img = document.createElement("img");
        let elemento_p = document.getElementById(`seleccion_${id}`);
        let main_span = document.getElementById(`main_span_${id}`);

        main_span.innerText = "0";
        img.src = "/control_de_pago_remake/public/img/inventario/check.png";
        img.style = "width: 32px";
        img.id = `img_salida_${id}`;

        elemento_p.appendChild(img);

        elemento_p.removeAttribute("onclick", "");
        elemento_p.setAttribute("onclick", `uncheck_salida(${id}, ${op}, ${val})`);

        if (op == 0) {
            let contenedor = document.getElementsByClassName(`content_serial_${id}`);
            let elemento_0 = document.getElementsByClassName(`seleccionar_head_${id}`);
            let elemento_1 = document.getElementsByClassName(`seleccionar_${id}`);

            for (let k = 0; k < elemento_0.length; k++) {
                elemento_0[k].style.display = "grid";
            }

            for (let l = 0; l < elemento_1.length; l++) {
                elemento_1[l].style.display = "grid";
            }

            for (let i = 0; i < contenedor.length; i++) {
                contenedor[i].style.gridTemplateColumns = "1fr 1fr 1fr";
            }
        } else {
            let cantidad = document.getElementById(`cantidad_${id}`);
            let cantidad_salida = document.getElementById(`cantidad_salida_${id}`);
            let main_span = document.getElementsByClassName(`main_span_${id}`);

            cantidad_salida.style.display = "grid";
            cantidad.style.display = "none";
        }
    }
}

function uncheck_salida(id, op, val) {
    let elemento_p = document.getElementById(`seleccion_${id}`);
    let img = document.getElementById(`img_salida_${id}`);
    let main_span = document.getElementById(`main_span_${id}`);

    main_span.innerText = "0";
    img.remove();

    elemento_p.removeAttribute("onclick", "");
    elemento_p.setAttribute("onclick", `check_salida(${id}, ${op}, ${val})`);

    if (op == 0) {
        let contenedor = document.getElementsByClassName(`content_serial_${id}`);
        let elemento_0 = document.getElementsByClassName(`seleccionar_head_${id}`);
        let elemento_1 = document.getElementsByClassName(`seleccionar_${id}`);
        let cantidad = document.getElementById(`cantidad_${id}`);
        let arrayFiltrado = formulario_0.filter((item) => item.categoria !== id);

        formulario_0 = arrayFiltrado; // Se filtra (linea anterior) el array con la categoría que se quiere eliminar y se agregar a la variable global.

        for (let k = 0; k < elemento_0.length; k++) {
            elemento_0[k].style.display = "none";
        }

        for (let l = 0; l < elemento_1.length; l++) {
            elemento_1[l].style.display = "none";
            let elemento_2 = document.getElementById(elemento_1[l].id);

            if (elemento_2.hasChildNodes()) {
                elemento_2.click();
            }
        }

        for (let i = 0; i < contenedor.length; i++) {
            contenedor[i].style.gridTemplateColumns = "1fr 1fr";
        }

        cantidad.innerText = "0";
    } else {
        let cantidad = document.getElementById(`cantidad_${id}`);
        let cantidad_salida = document.getElementById(`cantidad_salida_${id}`);

        cantidad_salida.style.display = "none";
        cantidad.style.display = "grid";
        cantidad.innerText = "0";

        let arrayFiltrado = formulario_1.filter((item) => item.id !== id);

        formulario_1 = arrayFiltrado;
    }
}

function span_c(id, val, op) {
    let valor = document.getElementById(`main_span_${id}`);
    let operacion = parseInt(valor.innerText);
    let item = document.getElementById(`item_${id}`);
    let hr_sintetico = document.getElementById(`hr_sintetico_${id}`);

    if (comodin_entrada) {
        val = 1000;
    }

    if (op == 1) {
        if (val != operacion) {
            operacion += 1;
        }
    } else {
        if (operacion > 0) {
            operacion -= 1;
        }
    }

    if (operacion == 0) {
        item.dataset.selected = "0";
        hr_sintetico.dataset.selected = "0";
    } else {
        item.dataset.selected = "1";
        hr_sintetico.dataset.selected = "1";
    }

    valor.innerText = operacion;

    // Buscar el índice del objeto con el id dado
    const index = formulario_1.findIndex((item) => item.id === id);

    if (index !== -1) {
        // Si existe, actualizar la cantidad
        formulario_1[index].cantidad = operacion;

        // Si la cantidad es 0 o menos, eliminar el objeto
        if (formulario_1[index].cantidad <= 0) {
            formulario_1.splice(index, 1);
        }
    } else if (operacion > 0) {
        // Si no existe y la cantidad a agregar es positiva, agregar nuevo objeto
        formulario_1.push({ id: id, cantidad: operacion });
    }
}

const salida_flecha = document.getElementById("salida_flecha");

salida_flecha.addEventListener("click", () => {
    // Muestra los elementos seleccionados para la salida
    if (formulario_0.length != 0 || formulario_1.length != 0) {
        let formulario = `  <form action="/control_de_pago_remake/public/generar_salida" method="post" id="main_form">
                            <input type="hidden" id="nonRouter" name="nonRouter" value="">
                            <input type="hidden" id="router" name="router" value="">

                            <label>
                                Responsable:<br><br>
                                <select name="responsable" id="responsable">
                                    
                                </select>
                            </label>
                        </form>`;

        document.getElementById("inventario").insertAdjacentHTML("beforeend", formulario);
        let titulo = document.getElementById("titulo");
        let salida_menu = document.getElementById("salida_menu");
        let confirmacion_menu = document.getElementById("confirmacion_menu");
        let selectedElements = document.querySelectorAll('[data-selected="0"]');
        let mas = document.getElementsByClassName("salida_span");
        let usuario = "";
        let responsable = document.getElementById("responsable");
        let seleccion_inventario = document.getElementsByClassName("seleccion_inventario");
        let seleccionar = document.getElementsByClassName("seleccionar");

        for (let index = 0; index < mas.length; index++) {
            mas[index].style.opacity = "0";
            mas[index].removeAttribute("onclick");
        }

        for (let index = 0; index < selectedElements.length; index++) {
            selectedElements[index].style.display = "none";
        }

        for (let index = 0; index < usuarios.length; index++) {
            usuario += `<option value="${usuarios[index]["name"]}">${usuarios[index]["name"]}</option>`;
        }

        for (let index = 0; index < seleccion_inventario.length; index++) {
            seleccion_inventario[index].removeAttribute("onclick");
        }

        for (let index = 0; index < seleccionar.length; index++) {
            seleccionar[index].removeAttribute("onclick");
        }

        titulo.innerText = "Confirmación de salida";
        salida_menu.style.display = "none";
        confirmacion_menu.style.display = "grid";

        setTimeout(() => {
            document.getElementById("nonRouter").value = JSON.stringify(formulario_1);
            document.getElementById("router").value = JSON.stringify(formulario_0);
            responsable.innerHTML = usuario;
        }, 500);
    }
});

const salida_articulos = document.getElementById("salida_articulos");

salida_articulos.addEventListener("click", () => {
    // Muestra los elementos seleccionados para la salida
    if (formulario_0.length != 0 || formulario_1.length != 0) {
        let formulario = `  <form action="/control_de_pago_remake/public/generar_entrada" method="post" id="main_form">
                            <input type="hidden" id="nonRouter" name="nonRouter" value="">
                            <input type="hidden" id="router" name="router" value="">

                            <label>
                                Responsable:<br><br>
                                <select name="responsable" id="responsable">
                                    
                                </select>
                            </label>
                        </form>`;

        document.getElementById("inventario").insertAdjacentHTML("beforeend", formulario);
        let titulo = document.getElementById("titulo");
        let salida_menu = document.getElementById("salida_menu");
        let confirmacion_menu = document.getElementById("confirmacion_menu");
        let selectedElements = document.querySelectorAll('[data-selected="0"]');
        let mas = document.getElementsByClassName("salida_span");
        let usuario = "";
        let responsable = document.getElementById("responsable");
        let seleccion_inventario = document.getElementsByClassName("seleccion_inventario");
        let seleccionar = document.getElementsByClassName("seleccionar");
        document.getElementById("entrada_menu").style.display = "none";

        /*
item_10
entrada_10 
*/

        let icon = document.getElementsByClassName("icon");
        let camara = document.getElementsByClassName("camara");
        let opciones_categoria = document.getElementsByClassName("opciones_categoria");
        let header_seccion = document.getElementsByClassName("header_seccion");
        let contenedor_seleccion = document.getElementsByClassName("contenedor_seleccion");

        for (let index = 0; index < icon.length; index++) {
            icon[index].style.display = "none";
        }

        for (let index = 0; index < camara.length; index++) {
            camara[index].style.display = "none";
        }

        for (let index = 0; index < opciones_categoria.length; index++) {
            opciones_categoria[index].style.display = "none";
        }

        for (let index = 0; index < header_seccion.length; index++) {
            header_seccion[index].style.display = "none";
        }

        for (let index = 0; index < contenedor_seleccion.length; index++) {
            contenedor_seleccion[index].style.gridTemplateColumns = "1fr 1fr";
        }

        document.getElementById("salida_camion").removeAttribute("src");
        document.getElementById("salida_camion").setAttribute("src", "/control_de_pago_remake/public/img/inventario/cajacheck.png");

        for (let index = 0; index < mas.length; index++) {
            mas[index].style.opacity = "0";
            mas[index].removeAttribute("onclick");
        }

        for (let index = 0; index < selectedElements.length; index++) {
            selectedElements[index].style.display = "none";
        }

        for (let index = 0; index < usuarios.length; index++) {
            usuario += `<option value="${usuarios[index]["name"]}">${usuarios[index]["name"]}</option>`;
        }

        for (let index = 0; index < seleccion_inventario.length; index++) {
            seleccion_inventario[index].removeAttribute("onclick");
        }

        for (let index = 0; index < seleccionar.length; index++) {
            seleccionar[index].removeAttribute("onclick");
        }

        if (titulo.innerText == "Selección de entrada") {
            titulo.innerText = "Confirmación de entrada";
        } else {
            titulo.innerText = "Confirmación de salida";
        }

        salida_menu.style.display = "none";
        confirmacion_menu.style.display = "grid";

        setTimeout(() => {
            document.getElementById("nonRouter").value = JSON.stringify(formulario_1);
            document.getElementById("router").value = JSON.stringify(formulario_0);
            responsable.innerHTML = usuario;
        }, 500);
    }
});

function enviar() {
    document.getElementById("div_camion").removeAttribute("onclick");
    document.getElementById("main_form").submit();
}

function crear_categoria() {
    Swal.fire({
        title: "Un momento...",
        timer: 500,
        timerProgressBar: true,
        showConfirmButton: false,
        didOpen: () => {
            menu_des("mas");
            let seleccion_inventario = document.getElementsByClassName("seleccion_inventario");
            let inventario = document.getElementById("inventario");
            let crear_categoria = document.getElementById("crear_categoria");
            let categoria_menu = document.getElementById("categoria_menu");
            let icon_menu_inv = document.getElementById("icon_menu_inv");

            document.getElementById("sinElementos").style.display = "none";
            document.getElementById("menu_container").style.display = "none";
            document.getElementsByClassName("buscador_container")[0].style.display = "none";
            inventario.style.display = "none";
            icon_menu_inv.style.display = "none";
            crear_categoria.style.display = "grid";
            categoria_menu.style.display = "flex";

            Swal.showLoading();
        },
    });
}

const crear_categoria_submit = document.getElementById("crear_categoria_submit");

crear_categoria_submit.addEventListener("click", function () {
    let nombre_categoria = document.getElementById("nombre_categoria");
    let entrada = document.getElementById("entrada");

    if (nombre_categoria.value != "" && entrada.files.length > 0) {
        document.getElementById("categoria_form").submit();
    }
});

const editar_categoria_submit = document.getElementById("editar_categoria_submit");

editar_categoria_submit.addEventListener("click", function () {
    let nombre_categoria_edit = document.getElementById("nombre_categoria_edit");
    let entrada_edit = document.getElementById("entrada_edit");

    //if (nombre_categoria_edit.value != "" && entrada_edit.files.length > 0) {
    document.getElementById("categoria_form_edit").submit();
    //}
});

const elementos = ["salida_salir", "flecha_confirmacion", "crear_categoria_cerrar", "editar_categoria_cerrar", "salida_cerrar"];

elementos.forEach((id) => {
    const elemento = document.getElementById(id);
    if (elemento) {
        elemento.addEventListener("click", () => window.location.reload());
    }
});

const right = document.getElementById("right");
const left = document.getElementById("left");
const tipo = document.getElementById("tipo_categoria");

const right_edit = document.getElementById("right_edit");
const left_edit = document.getElementById("left_edit");
const tipo_edit = document.getElementById("tipo_categoria_edit");

const eliminar_categoria = document.getElementById("eliminar_categoria");

eliminar_categoria.addEventListener("click", function () {
    categoria_id = document.getElementById("categoria_id").innerText;

    fetch(`/control_de_pago_remake/public/inv_delete/${categoria_id}`, { method: "DELETE" })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            } else {
                Swal.fire({
                    icon: "success",
                    title: "¡Categoría eliminada!",
                    showConfirmButton: false,
                    timer: 3000,
                });

                setTimeout(() => window.location.reload(), 3000);
            }
        })
        .catch((error) => {
            Swal.fire({
                icon: "error",
                title: "¡Error al eliminar la categoría, intente de nuevo!",
                showConfirmButton: false,
                timer: 3000,
            });
        });
});

right.addEventListener("click", function () {
    right.style.backgroundColor = "rgb(74 91 224)";
    left.style.backgroundColor = "rgba(54 57 63 / 90%)";

    right.style.height = "28px";
    left.style.height = "25px";

    tipo.value = 0;
});

left.addEventListener("click", function () {
    right.style.backgroundColor = "rgba(54 57 63 / 90%)";
    left.style.backgroundColor = "rgb(74 91 224)";

    right.style.height = "25px";
    left.style.height = "28px";

    tipo.value = 1;
});

right_edit.addEventListener("click", function () {
    right_edit.style.backgroundColor = "rgb(74 91 224)";
    left_edit.style.backgroundColor = "rgba(54 57 63 / 90%)";

    right_edit.style.height = "28px";
    left_edit.style.height = "25px";

    tipo_edit.value = 0;
});

left_edit.addEventListener("click", function () {
    right_edit.style.backgroundColor = "rgba(54 57 63 / 90%)";
    left_edit.style.backgroundColor = "rgb(74 91 224)";

    right_edit.style.height = "25px";
    left_edit.style.height = "28px";

    tipo_edit.value = 1;
});

function seleccion_entrada(op, id) {
    let right_e = document.getElementById(`right_e_${id}`);
    let left_e = document.getElementById(`left_e_${id}`);

    let desde = document.getElementById(`desde_${id}`);
    let hasta = document.getElementById(`hasta_${id}`);

    let camara_desde = document.getElementById(`camara_desde_${id}`);
    let camara_hasta = document.getElementById(`camara_hasta_${id}`);

    let contenedor = document.getElementById(`add_item_${id}`);
    let contenedor_seleccion = contenedor.querySelectorAll("contenedor_seleccion");
    let quitar = document.getElementsByClassName("quitar");
    let header_eliminar = document.getElementById(`header_eliminar_${id}`);
    let suma_serial = document.getElementById(`suma_serial_${id}`);
    suma_serial.removeAttribute("title");
    suma_serial.removeAttribute("onclick");
    document.getElementById(`item_${id}`).dataset.selected = "0";

    document.getElementById(`cantidad_${id}`).innerText = 0;

    formulario_0 = formulario_0.filter((formulario_0) => formulario_0.id !== id);

    if (op) {
        // individual

        for (let index = 0; index < contenedor_seleccion.length; index++) {
            contenedor_seleccion[index].style.gridTemplateColumns = "1fr 1fr 1fr";
        }

        for (let index = 0; index < quitar.length; index++) {
            quitar[index].style.display = "grid";
        }

        document.getElementById(`cantidad_entrada_${id}`).innerText = 0;

        contenedor.style.gridTemplateColumns = "1fr 1fr 1fr";
        header_eliminar.style.display = "block";
        suma_serial.innerText = "+";
        suma_serial.style.width = "30px";
        suma_serial.style.height = "30px";
        suma_serial.setAttribute("title", "Agregar otro serial.");
        suma_serial.setAttribute("onclick", `suma_serial(${id})`);

        right_e.style.backgroundColor = "rgb(35, 165, 90)";
        left_e.style.backgroundColor = "rgba(54 57 63 / 90%)";

        right_e.style.height = "28px";
        left_e.style.height = "25px";

        right_e.removeAttribute("onclick");
        left_e.setAttribute("onclick", `seleccion_entrada(0, ${id})`);

        desde.style.display = "none";
        hasta.style.display = "none";

        camara_desde.style.display = "none";
        camara_hasta.style.display = "none";
    } else {
        // lotes

        for (let index = 0; index < contenedor_seleccion.length; index++) {
            contenedor_seleccion[index].style.gridTemplateColumns = "1fr 1fr";
        }

        if (contenedor) {
            const elementosQuitar = contenedor.querySelectorAll(".quitar");

            for (let index = 0; index < elementosQuitar.length; index++) {
                elementosQuitar[index].click();
            }
        }

        contenedor.style.gridTemplateColumns = "1fr 1fr";
        header_eliminar.style.display = "none";
        suma_serial.innerText = "✔";
        suma_serial.style.width = "35px";
        suma_serial.style.height = "35px";
        suma_serial.setAttribute("title", "Generar cantidad por lote.");
        suma_serial.setAttribute("onclick", `generar_lote(${id})`);

        right_e.style.backgroundColor = "rgba(54 57 63 / 90%)";
        left_e.style.backgroundColor = "rgb(35, 165, 90)";

        right_e.style.height = "25px";
        left_e.style.height = "28px";

        desde.style.display = "block";
        hasta.style.display = "block";

        desde.value = "";
        hasta.value = "";

        camara_desde.style.display = "block";
        camara_hasta.style.display = "block";

        left_e.removeAttribute("onclick");
        right_e.setAttribute("onclick", `seleccion_entrada(1, ${id})`);
    }
}

function generar_lote(id) {
    let desde = document.getElementById(`desde_${id}`).value;
    let hasta = document.getElementById(`hasta_${id}`).value;
    let item = document.getElementById(`item_${id}`);
    let main_span_entrada = document.getElementById(`cantidad_entrada_${id}`);

    // Función para encontrar el prefijo común entre dos strings
    function prefijoComun(str1, str2) {
        let i = 0;
        while (i < str1.length && i < str2.length && str1[i] === str2[i]) {
            i++;
        }
        return str1.slice(0, i);
    }

    const prefijo = prefijoComun(desde, hasta);

    // Extraemos la parte numérica al final de cada string (lo que queda después del prefijo)
    const numDesdeStr = desde.slice(prefijo.length);
    const numHastaStr = hasta.slice(prefijo.length);

    // Convertimos a números enteros
    const numDesde = parseInt(numDesdeStr, 10);
    const numHasta = parseInt(numHastaStr, 10);

    // Validamos que numDesde y numHasta sean números válidos
    if (isNaN(numDesde) || isNaN(numHasta)) {
        main_span_entrada.innerText = "0";
        item.dataset.selected = "0";
        throw new Error("Los seriales no tienen un formato numérico válido después del prefijo común.");
    }

    // Longitud de la parte numérica para mantener ceros a la izquierda
    const longitudNumerica = numDesdeStr.length;

    contadora = [];

    let arrayFiltrado = formulario_0.filter((item) => item.id !== id);

    formulario_0 = arrayFiltrado;

    for (let i = numDesde; i <= numHasta; i++) {
        const numeroFormateado = i.toString().padStart(longitudNumerica, "0");
        contadora.push({ conteo: i });
        formulario_0.push({ id: id, serial: prefijo + numeroFormateado, contador: `${i}` });
    }

    main_span_entrada.innerText = contadora.length;

    if (main_span_entrada != "0") {
        item.dataset.selected = "1";
    } else {
        item.dataset.selected = "0";
    }
}

// Para editar la categoria
const cambiar_img = document.getElementById("cambiar_img"); // boton para activar evento.
const entrada_edit = document.getElementById("entrada_edit"); // Input type="file".

cambiar_img.addEventListener("click", function () {
    entrada_edit.click();
});

entrada_edit.addEventListener("change", (e) => {
    // mostrar imagen en el contenedor
    const fondoImagen = document.getElementById("entrada_img_edit");
    const file = e.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            console.log(`${e.target.result}`);
            fondoImagen.style.backgroundImage = `url('${e.target.result}')`;
            fondoImagen.style.backgroundSize = "cover";
        };
        reader.readAsDataURL(file);
    }
});

// Para nueva categoria
const entrada_img = document.getElementById("entrada_img");
const entrada = document.getElementById("entrada");

entrada_img.addEventListener("click", function () {
    // activar evento
    entrada.click();
});

entrada.addEventListener("change", (e) => {
    // mostrar imagen en el contenedor
    const fondoImagen = document.getElementById("entrada_img");
    const file = e.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            fondoImagen.style.backgroundImage = `url('${e.target.result}')`;
            fondoImagen.style.backgroundSize = "cover";
        };
        reader.readAsDataURL(file);
    }
});

function inventario_vacio(op) {
    if (op) {
        document.getElementById("inventario_vacio_0").style.display = "block";
    } else {
        document.getElementById("inventario_vacio").style.display = "block";
    }
}

function expandir_entrada(id) {
    let entrada = document.getElementById(`entrada_${id}`);
    let abajo = document.getElementById(`abajo_${id}`);

    if (entrada.getAttribute("data-expandido") == 0) {
        entrada.dataset.expandido = "1";
        entrada.style.display = "grid";
        abajo.style.transform = "rotateX(180deg)";
    } else {
        entrada.dataset.expandido = "0";
        entrada.style.display = "none";
        abajo.style.transform = "rotateX(0deg)";
    }
}

const entrar = document.getElementById("entrar");

entrar.addEventListener("click", () => {
    let titulo = document.getElementById("titulo");
    let icon_menu_inv = document.getElementById("icon_menu_inv");
    let abajo = document.getElementsByClassName("abajo");
    let hr_sintetico = document.getElementsByClassName("hr_sintetico");
    let lapiz = document.getElementsByClassName("lapiz");
    let seleccion_inventario = document.getElementsByClassName("seleccion_inventario");
    let item = document.getElementsByClassName("item");
    let existencia = document.getElementsByClassName("existencia");
    let entrada_menu = document.getElementById("entrada_menu");

    comodin_entrada = true;

    titulo.innerText = "Selección de entrada";
    titulo.style.backgroundColor = "rgb(35 165 90)";

    icon_menu_inv.style.display = "none";
    entrada_menu.style.display = "flex";

    document.querySelectorAll("div.hr_sintetico").forEach((div) => {
        const onclickValue = div.getAttribute("onclick");
        if (onclickValue && onclickValue.startsWith("expandir_categoria(")) {
            const param = onclickValue.match(/\((\d+)\)/)[1];
            div.setAttribute("onclick", `expandir_entrada(${param})`);
        }
    });

    for (let index = 0; index < abajo.length; index++) {
        abajo[index].style.display = "grid";
    }

    /*for (let index = 0; index < hr_sintetico.length; index++) {
        hr_sintetico[index].style.display = "grid"; 
    }*/

    for (let index = 0; index < lapiz.length; index++) {
        lapiz[index].style.display = "none";
    }

    for (let index = 0; index < seleccion_inventario.length; index++) {
        seleccion_inventario[index].style.display = "block";

        let onclickValue = seleccion_inventario[index].getAttribute("onclick");
        let newOnclickValue = onclickValue.replace("check_salida", "check_entrada");

        seleccion_inventario[index].setAttribute("onclick", newOnclickValue);
    }

    for (let index = 0; index < item.length; index++) {
        item[index].style.gridTemplateColumns = "13% 35% 1fr";
        item[index].style.width = "100%";
        item[index].style.gap = "0";
    }

    for (let index = 0; index < existencia.length; index++) {
        existencia[index].style.display = "none";
    }

    menu_des("mas");
});

//0 cat router, 1 cat otros.
function check_entrada(id, op, cantidad = 1000) {
    let seleccion = document.getElementById(`seleccion_${id}`);
    let cantidad_text = document.getElementById(`cantidad_${id}`);

    seleccion.setAttribute("onclick", `uncheck_entrada(${id}, ${op}, ${cantidad})`);

    let img = document.createElement("img");
    img.src = "/control_de_pago_remake/public/img/inventario/check.png";
    img.style = "width: 32px";
    img.id = `img_${id}`;

    seleccion.appendChild(img);

    if (op) {
        // para otros
        document.getElementById(`cantidad_${id}`).style.display = "none";
        document.getElementById(`cantidad_salida_${id}`).style.display = "grid";
        cantidad_text.innerText = 0;
    } else {
        // para routers
        document.getElementById(`cantidad_${id}`).style.display = "none";
        document.getElementById(`suma_serial_${id}`).style.display = "flex";
        document.getElementById(`cantidad_entrada_${id}`).style.display = "grid";
    }
}

function uncheck_entrada(id, op, cantidad = 1000) {
    let seleccion = document.getElementById(`seleccion_${id}`);
    seleccion.setAttribute("onclick", `check_entrada(${id}, ${op}, ${cantidad})`);

    document.getElementById(`cantidad_${id}`).style.display = "grid";
    document.getElementById(`cantidad_salida_${id}`).style.display = "none";
    document.getElementById(`main_span_${id}`).innerText = "0";

    document.getElementById(`cantidad_${id}`).style.display = "grid";
    document.getElementById(`cantidad_entrada_${id}`).style.display = "none";

    let img = document.getElementById(`img_${id}`);
    img.remove();

    if (op) {
        // para NonRouters

        let arrayFiltrado = formulario_1.filter((item) => item.id !== id);

        formulario_1 = arrayFiltrado;
    } else {
        // para routers

        document.getElementById(`suma_serial_${id}`).style.display = "none";

        let arrayFiltrado = formulario_0.filter((item) => item.id !== id);

        formulario_0 = arrayFiltrado;
    }
}

function suma_serial(id) {
    let input_serial = document.getElementsByClassName(`input_serial_${id}`);
    let interruptor = true;

    for (const input of input_serial) {
        if (input.value == "") {
            interruptor = false;
            input.style.border = "1px solid rgb(246, 63, 67)";
        } else {
            input.style.border = "none";
        }
    }

    if (interruptor) {
        const padre = document.getElementById(`add_item_${id}`);
        let main_span_entrada = document.getElementById(`cantidad_entrada_${id}`);
        let item = document.getElementById(`item_${id}`);
        let elementos = document.getElementsByClassName(`texto_serial`).length;

        main_span_entrada.innerText = parseInt(main_span_entrada.innerText) + 1;

        if (parseInt(main_span_entrada.innerText) > 0) {
            item.dataset.selected = "1";
        } else {
            item.dataset.selected = "0";
        }

        const divHijo = document.createElement("div");

        divHijo.onclick = function () {
            descartar_elemento(this, id);
        };

        divHijo.className = "quitar";
        padre.appendChild(divHijo);

        const imagen_hijo = document.createElement("img");
        imagen_hijo.src = "/control_de_pago_remake/public/img/inventario/mas.png";
        imagen_hijo.alt = "Descripción de la imagen";
        imagen_hijo.className = "icon";

        divHijo.appendChild(imagen_hijo);

        const inputTexto = document.createElement("input");
        inputTexto.type = "text";
        inputTexto.placeholder = "Escribe el serial";
        inputTexto.className = `texto_serial input_serial_${id}`;
        inputTexto.dataset.id_texto = id;
        inputTexto.id = `camara_${elementos}`;
        inputTexto.dataset.contador = elementos;
        inputTexto.onchange = function () {
            actualizarFormulario(this.dataset.id_texto, this.value, this.dataset.contador);
        };
        inputTexto.onclick = function () {
            actualizarFormulario(this.dataset.id_texto, this.value, this.dataset.contador);
        };
        padre.appendChild(inputTexto);

        const imagen = document.createElement("img");
        imagen.src = "/control_de_pago_remake/public/img/inventario/camara.png";
        imagen.alt = "Capturar serial con la camara.";
        imagen.className = "camara";
        imagen.onclick = function () {
            camara(elementos, 2);
        };

        padre.appendChild(imagen);

        document.getElementById(`camara_${elementos}`).focus();
    }
}

function descartar_elemento(elementoQuitar, id) {
    // elementoQuitar es el div.quitar que se pasó al hacer clic

    // Obtener el siguiente input y la imagen que sigue al input
    const inputSiguiente = elementoQuitar.nextElementSibling;
    const imagenSiguiente = inputSiguiente ? inputSiguiente.nextElementSibling : null;

    // Validar que existan y sean los elementos esperados
    if (inputSiguiente && inputSiguiente.tagName === "INPUT" && imagenSiguiente && imagenSiguiente.tagName === "IMG") {
        elementoQuitar.remove();
        inputSiguiente.remove();
        imagenSiguiente.remove();
        let item = document.getElementById(`item_${id}`);
        let cantidad = document.getElementById(`cantidad_entrada_${id}`);
        cantidad.innerHTML = cantidad.innerHTML - 1;

        if (parseInt(cantidad.innerText) > 0) {
            item.dataset.selected = "1";
        } else {
            item.dataset.selected = "0";
        }

        formulario_0 = formulario_0.filter((formulario_0) => formulario_0.serial !== inputSiguiente.value);
    } else {
        console.warn("No se encontraron los elementos esperados para eliminar.");
    }
}

function comprobar_duplicado(id) {
    const serialesVistos = new Set();
    let valoresVistos = new Set();
    let suma_serial = document.getElementById(`suma_serial_${id}`);

    const objetosSinDuplicados = formulario_0.filter((obj) => {
        if (serialesVistos.has(obj.serial)) {
            return false; // Ya vimos este serial, lo descartamos
        } else {
            serialesVistos.add(obj.serial);
            return true; // Es la primera vez que vemos este serial, lo mantenemos
        }
    });

    let inputs = document.getElementsByClassName(`input_serial_${id}`);

    formulario_0 = objetosSinDuplicados;

    for (const input of inputs) {
        let valor = input.value;

        if (valoresVistos.has(valor)) {
            input.style.border = "solid 1px rgb(246, 63, 67)";
            input.style.color = "rgb(246, 63, 67)";
            input.style.fontWeight = "bolder";
            let cantidad = document.getElementById(`cantidad_entrada_${id}`);

            //cantidad.innerHTML = parseInt(cantidad.innerHTML) - 1;
            suma_serial.removeAttribute("onclick");
        } else {
            valoresVistos.add(valor);
            suma_serial.setAttribute("onclick", `suma_serial(${id})`);
        }
    }
}

function actualizarFormulario(id, serial, contador) {
    const numericId = Number(id);

    const index = formulario_0.findIndex((obj) => obj.contador === contador && obj.id === numericId);

    if (index !== -1) {
        formulario_0[index].serial = serial;
    } else {
        formulario_0.push({ id: numericId, serial: serial, contador: contador });
    }

    comprobar_duplicado(id);
}

const main_src = document.getElementById("main_src");

main_src.addEventListener('keyup', (event) => {
    
    let producto = document.getElementsByClassName('producto_name');

    for (let index = 0; index < producto.length; index++) {
        
        let input = (producto[index].innerHTML).toLowerCase(); //valor del input

        let respuesta = input.toLowerCase().includes(event.target.value) ? producto[index].parentNode.style.display = "grid" : producto.parentNode.style.display = "none";

        console.log(respuesta);
    }
});