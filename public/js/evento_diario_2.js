async function evento_fecha(int) {
    try {
        let texto = "";

        if (int == 0) {
            texto = "Actualizando lista";
            let actualizar = document.getElementById("actualizar");
            actualizar.style.transform = "rotate(0deg)";
            setTimeout(() => {
                actualizar.style.transform = "rotate(360deg)";
            }, 1000);
        } else if (int == 1) {
            texto = "Buscando los registros del dia seleccionado";
        } else {
            texto = "Buscando los registros del dia de hoy";
        }

        Swal.fire({
            title: texto,
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });

        let fecha = document.getElementById("fecha").value;

        //Total con resto
        await fetch(`/control_de_pago_remake/public/evento_admin/${fecha}`)
            .then((response) => response.json())
            .then(function (response) {
                let total = document.getElementById("total");
                let cantidad_de_eventos = document.getElementById("cantidad_de_eventos");

                if (response[1][0]["D"] == null) {
                    response[1][0]["D"] = 0;
                }

                if (response[1][0]["BS"] == null) {
                    response[1][0]["BS"] = 0;
                }

                if (response[1][0]["PM"] == null) {
                    response[1][0]["PM"] = 0;
                }

                if (response[1][0]["EU"] == null) {
                    response[1][0]["EU"] = 0;
                }

                cantidad_de_eventos.innerHTML = `Cantidad de eventos: ${response[2][0]["cuenta"]}`;

                total.innerHTML = `<p class="sub_title">Total con resto: ${response[1][0]["D"].toFixed(2)}$ / ${response[1][0]["BS"].toFixed(2)}Bs (efectivo) / ${response[1][0]["EU"]}€</p>`;
            });

        //Total sin resto
        await fetch(`/control_de_pago_remake/public/evento_sr_admin/${fecha}`)
            .then((response) => response.json())
            .then(function (response) {
                let total = document.getElementById("total_sin_resto");

                if (response[0][0]["D"] == null) {
                    response[0][0]["D"] = 0;
                }

                if (response[1][0]["BS"] == null) {
                    response[1][0]["BS"] = 0;
                }

                if (response[2][0]["EU"] == null) {
                    response[2][0]["EU"] = 0;
                }

                if (response[3][0]["PM"] == null) {
                    response[3][0]["PM"] = 0;
                }

                total.innerHTML = `<p id="total_sin_resto" class="sub_title">Total ingresado: ${response[0][0]["D"].toFixed(2)}$ / ${response[3][0]["PM"].toFixed(2)}Bs (pagomovil) / ${response[1][0]["BS"].toFixed(2)}Bs (efectivo) / ${response[2][0]["EU"]}€</p>`;
            });

        const response_1 = await fetch("/control_de_pago_remake/public/evento_admin/" + fecha)
            .then((response) => response.json())
            .then((data) => {
                table = $("#main").DataTable({
                    stateSave: false,
                    destroy: true,
                    searching: true,
                    paging: false,
                    order: [[0, "desc"]],
                    lengthMenu: [[-1], ["Todos"]],
                    language: {
                        url: "/control_de_pago_remake/public/js/lenguaje.json",
                    },
                    data: data[0],
                    columns: [
                        { data: "id" },
                        {
                            data: "usuario",
                            render: function (data, type, row) {
                                if (row.usuario == null) {
                                    row.usuario = "Sin especificar.";
                                }

                                return `${row.usuario}`;
                            },
                        },
                        { data: "evento" },
                        {
                            data: "dolares",
                            render: function (data, type, row) {
                                if (row.dolares == null) {
                                    row.dolares = 0;
                                }
                                return `${row.dolares}$.`;
                            },
                        },
                        {
                            data: "bolivares",
                            render: function (data, type, row) {
                                if (row.bolivares == null) {
                                    row.bolivares = 0;
                                }
                                return `${row.bolivares}Bs.`;
                            },
                        },
                        {
                            data: "pagomovil",
                            render: function (data, type, row) {
                                let receptor = "";

                                if (row.receptor == 1) {
                                    receptor = "Pagomovil Provincial";
                                } else if (row.receptor == 2) {
                                    receptor = "Pagomovil Bancesco";
                                } else if (row.receptor == 3) {
                                    receptor = "Pagomovil Venezuela";
                                } else if (row.receptor == 4) {
                                    receptor = "Punto Bicentenario";
                                } else if (row.receptor == 5) {
                                    receptor = "Punto Venezuela";
                                } else if (row.receptor == 6) {
                                    receptor = "Punto caroni";
                                } else if (row.receptor == 7) {
                                    receptor = "Punto biópago";
                                } else if (row.receptor == null) {
                                    receptor = "Sin receptor";
                                } else {
                                    receptor = "Sin receptor";
                                }

                                if (row.pagomovil == null) {
                                    row.pagomovil = 0;
                                }
                                return `${row.pagomovil}Bs.<br>ref # ${row.ref}<br>${receptor}`;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                return `Jesus : ${row.zelle_j}$.<br>vladimir: ${row.zelle_v}$.`;
                            },
                        },
                        {
                            data: "euros",
                            render: function (data, type, row) {
                                if (row.euros == null) {
                                    row.euros = 0;
                                }
                                return `${row.euros}€.`;
                            },
                        },
                        {
                            data: "total",
                            render: function (data, type, row) {
                                if (row.euros == null) {
                                    row.euros = 0;
                                }
                                return `${row.total.toFixed(2)}$.`;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                return `<img class="borrar" src="/control_de_pago_remake/public/img/evento_diario/eliminar.png" alt="" title="Borrar evento" onclick="borrar_evento(${row.id})"></img>`;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                if (row.verificar) {
                                    return `<input type="checkbox" name="" class="verificar" id="check_${row.id}" onchange="check('${row.id}')" checked>`;
                                } else {
                                    return `<input type="checkbox" name="" class="verificar" id="check_${row.id}" onchange="check('${row.id}')">`;
                                }
                            },
                        },
                    ],
                    columnDefs: [{ width: "20%", targets: 1 }],
                });
                let searchba = document.querySelector("#customSearch");

                searchba.addEventListener("input", function () {
                    table.search(this.value).draw();
                });
            });
    } catch (error) {
        console.log(error);
    }
}

async function enviar() {
    try {
        let ev = document.getElementById("evento").value;
        let d = document.getElementById("dolar").value;
        let bs = document.getElementById("bolivar").value;
        let pm = document.getElementById("pagomovil").value;
        let eu = document.getElementById("euro").value;
        let fec = document.getElementById("fecha_side").value;

        alerta = document.getElementById("alerta");

        if (d == "") {
            d = 0;
        }

        if (bs == "") {
            bs = 0;
        }

        if (pm == "") {
            pm = 0;
        }

        if (eu == "") {
            eu = 0;
        }

        if (ev == "" && d + bs + pm + eu == 0) {
            alerta.innerHTML = "Debes especificar el evento y el monto*";
        } else if (ev == "") {
            alerta.innerHTML = "Debes especificar el evento*";
        } else if (d + bs + pm + eu == 0) {
            alerta.innerHTML = "Debes ingresar algún monto*";
        } else if (fec == "") {
            alerta.innerHTML = "Debes ingresar la fecha del evento*";
        } else {
            Swal.fire({
                title: "¿Agregar evento?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/control_de_pago_remake/public/evento_admin/agregar/${ev}/${d}/${bs}/${pm}/${eu}/${fec}`, { method: "POST" })
                        .then(function (response) {
                            Swal.fire({
                                title: "Evento agregado!",
                                icon: "success",
                                showConfirmButton: false,
                            });
                        })
                        .catch(function (error) {
                            console.log(error);
                            Swal.fire({
                                title: "Error de servidor!",
                                icon: "error",
                                showConfirmButton: false,
                            });
                        });
                    setTimeout(() => {
                        evento_fecha(0);
                    }, 4000);
                }
            });
        }
    } catch (error) {
        console.log(error);
    }
}

const borrar_evento = async function borrar_evento(id) {
    try {
        Swal.fire({
            title: "Borrar evento?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si",
            cancelButtonText: "No",
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/control_de_pago_remake/public/evento_admin/eliminar_evento/${id}`)
                    .then(function () {
                        Swal.fire({
                            title: "Eliminado!",
                            icon: "success",
                            showConfirmButton: false,
                        });
                        evento_fecha(0);
                    })
                    .catch(function () {
                        Swal.fire({
                            title: "Error de servidor!",
                            icon: "error",
                        });
                        evento_fecha(0);
                    });
            }
        });
    } catch (error) {
        console.log(error);
    }
};

const check = async function check(id) {
    try {
        let check = document.getElementById(`check_${id}`).checked;

        const response_4 = await fetch(`/control_de_pago_remake/public/evento_admin/check_evento/${id}/${check}`)
            .then(function () {
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Check cambiado!",
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                });
            })
            .catch(function (error) {
                Swal.fire({
                    position: "top-end",
                    title: "Error de servidor!",
                    icon: "error",
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                });
            });
    } catch (error) {
        console.log(error);
    }
};

setTimeout(() => {
    evento_fecha();
}, 1000);

function escapar_texto() {
    const textoTextarea = document.querySelector("#evento").value;
    const regex = /^[^#%_\/\\&]*$/;

    if (!regex.test(textoTextarea)) {
        Swal.fire({
            title: "Error!",
            text: "Caracteres no permitidos: '% _ / # &' ",
            icon: "error",
            showConfirmButton: false,
        });
    } else {
        enviar();
    }
}

function agregar_evento_formulario(fecha) {
    Swal.fire({
        showConfirmButton: false,
        background: "rgba(0, 0, 0, 0)",
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: `  <div class="add_event">
                    <h1>Agregar evento</h1>

                    <textarea id="evento" class="input" cols="30" rows="5" name="evento"></textarea>

                    <div class="monto_container">
                        <label for="dolar">Dolar<br><input type="number" class="input" name="dolar" id="dolar" value="" pattern="^(?!.*[eE].*$)"></label>
                        <label for="bolivar">Bolivar<br><input type="number" class="input" name="bolivar" id="bolivar" value="" pattern="^(?!.*[eE].*$)"></label>
                        <label for="pagomovil">Pagomovil<br><input type="number" class="input" name="pagomovil" id="pagomovil" value="" pattern="^(?!.*[eE].*$)"></label>
                        <label for="euro">Euro<br><input type="number" class="input" name="euro" id="euro" value="" pattern="^(?!.*[eE].*$)"></label>
                    </div>

                    <label for="fecha">Fecha<br><input type="date" class="input" value="${fecha}" name="fec" id="fecha_side"></label><br>

                    <label for="add_evento" class="label_add_evento"><br><button id="add_evento" onclick="escapar_texto()">Agregar evento</button></label>
                    
                    <p id="alerta" style="color: red;">&nbsp;</p>
                </div>`,
    });
}

function evento_log() {
    window.open("/control_de_pago_remake/public/evento_log_data_admin", "_blank");
}
