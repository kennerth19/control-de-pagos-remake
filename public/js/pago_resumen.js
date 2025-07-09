function info(cedula, telefono, direccion) {

    Swal.fire({
        title: 'Información:',
        html: `Cédula: ${cedula} <br> Teléfono: ${telefono} <br> Dirección: ${direccion}`,
        showConfirmButton: false,
    })
}

async function showMainTable(desde, hasta, tipo_de_pago, cobrador) {
    try {
        const response = await fetch(`/control_de_pago_remake/public/pagos_resumen/data/${desde}/${hasta}/${tipo_de_pago}/${cobrador}`)
            .then(response => response.json())
            .then(data => {
                console.log(`/control_de_pago_remake/public/pagos_resumen/data/${desde}/${hasta}/${tipo_de_pago}/${cobrador}`)
                table = $('#main').DataTable({
                    responsive: true,
                    stateSave: false,
                    destroy: true,
                    order: [
                        [0, 'desc'],
                    ],
                    language: {
                        url: '/control_de_pago_remake/public/js/lenguaje.json',
                    },
                    lengthMenu: [
                        [100, -1],
                        ['100', 'Todos']
                    ],
                    data: data[1],
                    columns: [
                        { data: 'id' },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                return `${row.usuario}`
                            }
                        },
                        { data: 'codigo' },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                return `<p class="nombre" title="Click para ver información" onclick="info('${row.cedula}','${row.telefono}','${row.direccion}')">${row.cliente}</p>`
                            }
                        },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                let texto = row.concepto;

                                function saltoDeLinea(texto) {
                                    let palabras = texto.split(' ');
                                    let contador = 0;
                                    texto = '';
                                    for (let i = 0; i < palabras.length; i++) {
                                        texto += palabras[i] + ' ';
                                        contador++;
                                        if (contador === 3) {
                                            texto += '<br>';
                                            contador = 0;
                                        }
                                    }
                                    return texto;
                                }
                                return `${saltoDeLinea(texto)}`
                            }
                        },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                return `${row.dolares}$`
                            }
                        },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                return `${row.bolivares}Bs`
                            }
                        },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                let banco_receptor = "";

                                if (row.banco_receptor == 1) {
                                    receptor = "Provincial";
                                } else if (row.banco_receptor == 2) {
                                    receptor = "Bancesco";
                                } else if (row.banco_receptor == 3) {
                                    receptor = "Venezuela";
                                } else if (row.banco_receptor == 4) {
                                    receptor = "Punto bicentenario";
                                } else if (row.banco_receptor == 5) {
                                    receptor = "Punto Venezuela";
                                } else if (row.banco_receptor == 6) {
                                    receptor = "Punto caroni";
                                } else if (row.banco_receptor == 7) {
                                    receptor = "Punto biópago";
                                } else if(row.banco_receptor == null){
                                    receptor = "Sin receptor";
                                } else {
                                    receptor = "Sin receptor";
                                }

                                return `${row.pagomovil}Bs<br><hr>Ref #${row.referencia}<br><hr>${receptor}`
                            }
                        },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                return `${row.euros}€`
                            }
                        },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                return `${row.zelle_a}$<hr>${row.zelle_b}$`
                            }
                        },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                return `${row.total}$`
                            }
                        },
                        {
                            data: 'id',
                            "render": function (data, type, row) {
                                if(row.tipo == 0){
                                    return `<div><a href="/control_de_pago_remake/public/imprimir_factura_mensualidad/${row.id}" class="factura" target="_blank">Factura</a></div>`
                                }else{
                                    return `<div style="display: grid;"><a href="/control_de_pago_remake/public/imprimir_factura_mensualidad/${row.id}" class="factura" target="_blank">Factura</a><p onclick="eliminar_pago(${row.id})" class="factura">Eliminar</p></div>`
                                }

                            }
                        },
                    ]
                });
                let sumas = document.getElementById('totales');

                let dolar = (data[0][0]['suma_dolar'] != null ? data[0][0]['suma_dolar'] : "0");
                let bolivares = (data[0][0]['suma_bs'] != null ? data[0][0]['suma_bs'] : "0");
                let pagomovil = (data[0][0]['suma_pagomovil'] != null ? data[0][0]['suma_pagomovil'] : "0");
                let zelle_a = (data[0][0]['suma_zelle_a'] != null ? data[0][0]['suma_zelle_a'] : "0");
                let zelle_b = (data[0][0]['suma_zelle_b'] != null ? data[0][0]['suma_zelle_b'] : "0");
                let euro = (data[0][0]['suma_euro'] != null ? data[0][0]['suma_euro'] : "0");
                let total = (data[0][0]['suma_total'] != null ? data[0][0]['suma_total'] : "0");

                if(desde == hasta){
                    document.getElementById('pago_cuenta_0').innerHTML = `(${data[2][0]['pago_cantidad']} PAGOS)`;
                }else{
                    document.getElementById('pago_cuenta_1').innerHTML = `(${data[2][0]['pago_cantidad']} PAGOS)`;
                }

                sumas.innerHTML = ``;
                sumas.innerHTML = `
                    <p>Total $</p>
                    <p>Total Bs</p>
                    <p>Total PM</p>
                    <p>Total Z J/V</p>
                    <p>Total €</p>
                    <p>TOTAL</p>
                    <p>${dolar.toFixed(2)}$</p>
                    <p>${bolivares.toFixed(2)}Bs</p>
                    <p>${pagomovil.toFixed(2)}Bs</p>
                    <p>${zelle_a.toFixed(2)}$/${zelle_b.toFixed(2)}$</p>
                    <p>${euro.toFixed(2)}€</p>
                    <p class="resultado">${total.toFixed(2)}$</p>`;

                let searchbar = document.querySelector('#customSearch');

                searchbar.addEventListener('input', function () { table.search(this.value).draw(); })
            })
    } catch (error) { console.log(error) }
}

function tipo_pago() {
    let select = document.getElementById('usuario');
    let tipo = document.getElementById('tipo_pago');

    if (tipo.selectedIndex == 3) {
        select.disabled = false;
    } else {
        select.disabled = true;
    }
}

function generar_endpoint() {
    let desde = document.getElementById('desde').value;
    let hasta = document.getElementById('hasta').value;

    if (desde <= hasta) {
        Swal.fire({
            title: "Cargando tabla...",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();

                let tipo_de_pago = document.getElementById('tipo_pago').value;
                let cobrador = document.getElementById('usuario').value;

                let resultados = document.getElementById('dia');
                let fecha = "";

                let desde_formateada = moment(desde).locale('es').format('DD [de] MMMM [de] YYYY');
                let hasta_formateada = moment(hasta).locale('es').format('DD [de] MMMM [de] YYYY');

                if (desde == hasta) {
                    fecha = `<p>Total del día ${desde_formateada}. <span id="pago_cuenta_0">(0 PAGOS)</span></p>`;
                } else {
                    fecha = `<p>Total del ${desde_formateada} al<br> ${hasta_formateada}. <span id="pago_cuenta_1">(0 PAGOS)</span></p>`;
                }

                resultados.innerHTML = `${fecha}`;

                showMainTable(desde, hasta, tipo_de_pago, cobrador);
            }
        })
    } else {
        Swal.fire({
            title: 'La primera fecha no debe ser mayor que la segunda.',
            showConfirmButton: false,
        })
    }
}

function eliminar_pago(id) {
    Swal.fire({
        title: "¿Estas seguro?",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "No",
        confirmButtonText: "Si",
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('listo')
            fetch(`/control_de_pago_remake/public/eliminar_pago/${id}`)
                .then(() => {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Pago ELIMINADO con éxito! ACTUALIZANDO TABLA...',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    setTimeout(() => {
                        let desde = document.getElementById('desde').value;
                        let hasta = document.getElementById('hasta').value;
                        let tipo_de_pago = document.getElementById('tipo_pago').value;
                        let cobrador = document.getElementById('usuario').value;
                        showMainTable(desde, hasta, tipo_de_pago, cobrador);
                    }, 1000);
                })
                .catch(err => {
                    console.error("ERROR: ", err.message)
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'error!',
                        showConfirmButton: false,
                        timer: 3000
                    })
                });
        }
    });
}

function comprobar_pagomovil() {
    window.open("/control_de_pago_remake/public/comprobar", "Comprobar");
}

function comprobar_zelles() {
    //window.open("/control_de_pago_remake/public/comprobar", "Comprobar");
}