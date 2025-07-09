//Lista de variables globales para el filtro

var servidores = "";
var sectores = "";
var estado = "";

//Lista de elementos del filtro.

//Con deuda: Radio button.
let deuda_0 = document.querySelector("deudor_0");
let deuda_1 = document.querySelector("deudor_1");

//Fin de lista de elementos del filtro.

//botón filtro
let boton_filtro = document.getElementById("boton_filtro");

//Textarea.
let mensaje = document.getElementById("mensaje");

//Botón de confirmar envío.
let confirmar = document.getElementById("confirmar");

//variables para construir la instrucción sql

var bd_servidores = [];
var bd_estados = [];

/*Funciones para botón TODOS.*/
document.getElementById("todo_0").addEventListener("click", () => {
    //Todos Si.
    boton_filtro.setAttribute("disabled", "true");
});

document.getElementById("todo_1").addEventListener("click", () => {
    //Todos No.
    boton_filtro.removeAttribute("disabled", "false");
});
/*Fin de funciones para botón TODOS.*/

//Función para verificar si el textarea tiene texto.
mensaje.addEventListener("keyup", () => {
    if (mensaje.value == "" || mensaje.value.length < 10) {
        confirmar.setAttribute("disabled", "true");
        confirmar.title = "Debe escribir un mensaje";
    } else {
        confirmar.removeAttribute("disabled", "false");
        confirmar.title = "Confirmar envío";
    }
});

mensaje.addEventListener("click", () => {
    mensaje.innerText ==
    "Debes escribir un texto de al menos diez (10) caracteres."
        ? (mensaje.innerText = "")
        : (mensaje.innerText = mensaje.innerText);
});

confirmar.addEventListener("click", (e) => {
    let todo_0 = document.getElementById("todo_0");

    if (!todo_0.checked) {
        if (bd_servidores.toString() == "" || bd_estados.toString() == "") {
            e.preventDefault();
            Swal.fire(
                "Debes abrir el filtro y seleccionar los servidores y los estados"
            );
        }
    }
});

function todo(val) {
    if (val == 0) {
        let servidor = document.getElementsByClassName("servidor");
        let todo_servidor = document.getElementById("todo_servidor");

        for (let index = 0; index <= servidor.length - 1; index++) {
            if (todo_servidor.checked == 1) {
                servidor[index].checked = 1;
            } else {
                servidor[index].checked = 0;
            }
        }
    } else if (val == 1) {
        let sector = document.getElementsByClassName("sector");
        let todo_sector = document.getElementById("todo_sector");

        for (let index = 0; index <= sector.length - 1; index++) {
            if (todo_sector.checked == 1) {
                sector[index].checked = 1;
            } else {
                sector[index].checked = 0;
            }
        }
    } else if (val == 2) {
        let estado = document.getElementsByClassName("estado");
        let todo_estado = document.getElementById("todo_estado");

        for (let index = 0; index <= estado.length - 1; index++) {
            if (todo_estado.checked == 1) {
                estado[index].checked = 1;
            } else {
                estado[index].checked = 0;
            }
        }
    }
}

boton_filtro.addEventListener("click", async () => {
    try {
        Swal.fire({
            background: "#34393d",
            width: "100%",
            allowOutsideClick: false,
            showConfirmButton: true,
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonText: "Seleccionar",
            html: `
            <div class="filtro_maestro">
        
        <hr>
        
        <h2>Servidores</h2>

        <div id="servidores_item" class="main_item">
        
        </div>

        <hr>

        <h2>Sectores (en disputa)</h2>
        <div id="sectores_item" class="main_item">
            
        </div>

        <hr>

        <h2>Estados</h2>
        <div id="estados_item" class="main_item">
            
        </div>

        <hr>

    </div>
                `,
            focusConfirm: true,
        }).then((result) => {
            if (result.isConfirmed) {
                generar_formulario();
            }
        });

        await fetch(`/control_de_pago_remake/public/get_data_difusion`)
            .then((response) => response.json())
            .then(function (response) {
                //Por servidor
                let servidores_item =
                    document.getElementById("servidores_item");
                let cantidad = 0;

                response[2].forEach(function (item) {
                    servidores_item.innerHTML += `<div class="item"><p>${item["nombre_de_servidor"]} (${item["cantidad"]})</p><input type="checkbox" class="servidor" value="${item["id"]}" id="id_servidor_${item["id"]}"></div>`;
                    cantidad += item["cantidad"];
                });

                servidores_item.innerHTML += `<div class="item"><p>Seleccionar todos (${cantidad})</p><input type="checkbox" onchange="todo(0)" class="" value="0" id="todo_servidor"></div>`;

                //Por sector (PON O NO SE v:)
                let sectores_item = document.getElementById("sectores_item");

                response[1].forEach(function (item) {
                    sectores_item.innerHTML += `<div class="item"><p>${item["sector"]}</p><input type="checkbox" class="sector" id="id_sector_${item["id"]}" value="${item["id"]}" disabled></div>`;
                });

                sectores_item.innerHTML += `<div class="item"><p>Seleccionar todos</p><input type="checkbox" onchange="todo(1)" class="" id="todo_sector" value="0" disabled></div>`;

                //Por estado
                let estados_item = document.getElementById("estados_item");

                response[0].forEach(function (item) {
                    estados_item.innerHTML += `<div class="item"><p>${item["estado"]}</p><input type="checkbox" class="estado" value="${item["id"]}" id="id_estado_${item["id"]}"></div>`;
                });

                estados_item.innerHTML += `<div class="item"><p>Seleccionar todos</p><input type="checkbox" onchange="todo(2)" class="" value="0" id="todo_estado"></div>`;
            });

            bd_servidores.forEach((id) => {
                let servidor_turno = document.getElementById(`id_servidor_${id}`)
                servidor_turno.checked = 1;
            });
    
            bd_estados.forEach((id) => {
                let estado_turno = document.getElementById(`id_estado_${id}`)
                estado_turno.checked = 1;
            });
    } catch (err) {
        console.error("function failed:", err);
    }
});

function generar_formulario() {
    bd_servidores = [];
    bd_estados = [];

    let servidor = document.getElementsByClassName("servidor");
    let estado = document.getElementsByClassName("estado");

    for (let index = 0; index <= servidor.length - 1; index++) {
        if (servidor[index].checked == 1) {
            bd_servidores.push(servidor[index].value);
        }
    }

    for (let index = 0; index <= estado.length - 1; index++) {
        if (estado[index].checked == 1) {
            bd_estados.push(estado[index].value);
        }
    }

    let data_hidden_servidores = document.getElementById(
        "data_hidden_servidores"
    );
    let data_hidden_estados = document.getElementById("data_hidden_estados");

    data_hidden_servidores.value = bd_servidores.toString();
    data_hidden_estados.value = bd_estados.toString();
}
