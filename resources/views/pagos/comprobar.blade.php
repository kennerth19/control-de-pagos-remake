<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">

    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <title>Comprobar pagos</title>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">
    @extends('side_bar.side_bar')

    @php
        $left = '-344px;';
    @endphp

    <div class="header_principal">
        <!-- <img src="{{ asset('img/logo.png') }}"> -->
        <p class="main_title_0">CONTROL DE <span class="main_title_1">PAGOS</span></p>
        <div id="custom-search-bar">
            <input type="search" id="customSearch">
        </div>
        <img src="{{ asset('img/inicio/tuerquita.png') }}" class="tuerquita" id="tuerquita" onclick="side_bar_tuerquita_in()">
        <script src="{{ asset('js/header.js') }}"></script>
    </div>
    <style>
        body {
            text-align: center !important;
            font-family: cursive;
        }

        table {
            width: max-content;
        }

        .menu_date {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            justify-items: center;
            align-items: center;
            padding: 5px;
            color: #fff;
            margin-bottom: 15px;
        }

        #filtro_fecha::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        #filtro_fecha, #automatica, .fecha_comprobar, #resumen{
            font-family: cursive;
            background-color: #212529;
            color: #fff;
            border: solid 1px;
            border-radius: 4px;
            padding: 5px;
            cursor: pointer;
        }

        .check {
            width: 30px;
            height: 30px;
        }

        .container_comprobacion{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
            margin-bottom: 5px;
        }

        .recepcion{
            margin-top: 6px;
            color: #fff;
            background-color: #212529;
            border: none;
            font-family: cursive;
            font-weight: bolder;
            font-size: 15px;
        }
    </style>

    <div class="menu_date">
        <h1>Comprobar pagomovil</h1>
        <input type="date" id="filtro_fecha" value="{{$hoy}}" onchange="tabla_comprobar('Comprobando dia seleccionado...')">
        <button id="automatica">Hacer comprobación</button>
        <button id="resumen">Ver resumen</button>
    </div>

    <div class="main_container_table">
        <table id="tabla_comprobar" class="table table-bordered display table-hover nowrap hover" style="text-align: center; width:100%;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cobrador</th>
                    <th>Cliente</th>
                    <th>Fecha del pago</th>
                    <th>Cantidad</th>
                    <th>Referencia</th>
                    <th>Banco emisor</th>
                    <th>Banco receptor</th>
                    <th>Opción</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        function tabla_comprobar(texto) {
            
            Swal.fire({
                title: texto,
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                    let fecha = document.getElementById('filtro_fecha').value;
                    const response = fetch(`/control_de_pago_remake/public/comprobar_datos/${fecha}`)
                        .then(response => response.json())
                        .then(data => {
                            table = $('#tabla_comprobar').DataTable({
                                responsive: true,
                                stateSave: true,
                                destroy: true,
                                language: {
                                    url: '/control_de_pago_remake/public/js/lenguaje.json',
                                },
                                lengthMenu: [
                                    [5, 10, 25, 50, 100, -1],
                                    ['5', '10', '25', '50', '100', 'Todos']
                                ],
                                data: data,
                                columns: [{
                                        data: 'id'
                                    },
                                    {
                                        data: 'cobrador'
                                    },
                                    {
                                        data: 'cliente'
                                    },
                                    {
                                        data: 'fecha_pago_movil'
                                    },
                                    {
                                        data: 'id',
                                        "render": function(data, type, row) {
                                            return `${row.pagomovil}Bs.`;
                                        }
                                    },
                                    {
                                        data: 'id',
                                        "render": function(data, type, row) {
                                            return `# ${row.referencia}`;
                                        }
                                    },
                                    {
                                        data: 'banco'
                                    },
                                    {
                                        data: 'id',
                                        "render": function(data, type, row) {
                                            // Lista de bancos
                                            const bancos = [
                                                { value: 1, label: 'Provincial' },
                                                { value: 2, label: 'Banesco' },
                                                { value: 3, label: 'Venezuela' },
                                                { value: 4, label: 'Punto bicentenario' },
                                                { value: 5, label: 'Punto Venezuela' },
                                                { value: 6, label: 'Punto caroni' },
                                                { value: 7, label: 'Biópago (avícola)' }
                                            ];
                                        
                                            // Genera las opciones con el selected correspondiente
                                            let options = bancos.map(banco => `<option value="${banco.value}"${banco.value == row.banco_receptor ? ' selected' : ''}>${banco.label}</option>`).join('');
                                        
                                            // Renderiza el select (puedes quitar el display:none si quieres que sea visible)
                                            return `
                                                <select class="input recepcion" name="banco_receptor" style="margin-top: 6px;" onchange="cambiarReceptor(${row.id}, this.value)">
                                                    ${options}
                                                </select>
                                            `;
                                        }
                                    },
                                    {
                                        data: 'id',
                                        "render": function(data, type, row) {
                                            if (row.pm_comprobar == 1) {
                                                return `<input type="checkbox" class="check" checked onchange="check(${row.id}, 0)">`;
                                            } else {
                                                return `<input type="checkbox" class="check" onchange="check(${row.id}, 1)">`;
                                            }
                                        }
                                    },
                                ]
                            });
                        })
                },
            });
        }

        async function check(id, check) {
            try {
                await fetch(`/control_de_pago_remake/public/comprobar_check/${id}/${check}`);
            } catch {
                Swal.fire({
                    title: 'Error de servidor...',
                    showConfirmButton: false,
                })
            } finally {
                /*let texto = "";

                if (check == 0) {
                    texto = "Eliminando confirmación...";
                }else{
                    texto = "Confirmando pagomovil...";
                }

                tabla_comprobar(texto);*/
            }
        }

        const boton = document.getElementById('automatica');

        boton.addEventListener("click", function() {
            Swal.fire({
                background: '#34393d',
                showConfirmButton: false,
                showCancelButton: false,
                html: `
                <form method="post" action="comprobar_datos_automatico" enctype="multipart/form-data">
                <h1 style="color: white;">Comprobación automática</h1><br>
                <div class="container_comprobacion">
                    <input type="date" id="date_0" name="date_0" class="fecha_comprobar" value="{{$hoy}}">
                    <input type="date" id="date_1" name="date_1" class="fecha_comprobar" value="{{$hoy}}">
                </div>
                <div style="display: grid; gap: 5px;">
                    <input type="file" id="file" name="archivo" class="fecha_comprobar" accept="pdf, .xls, .xlsx, .xlsm, .xlsb, .xla, .xlam, .xlt, .xltm, .xltx, .xltm, .ods, .odt" required>
                    <input type="submit" class="fecha_comprobar" value="Hacer comprobación">
                </div>
                </form>
                `,
                focusConfirm: true,
            })
        });

        tabla_comprobar("Cargando registros del dia...");

        const resumen = document.getElementById('resumen');

       resumen.addEventListener('click', async () =>{
            let  fecha = document.getElementById('filtro_fecha').value;
            let  url = `/control_de_pago_remake/public/resumen_comprobar/${fecha}`;

            try {
                let response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                let data = await response.json();

                let provincial = data[0][0]['provincial'] != null ? data[0][0]['provincial'] : 0;
                let banesco = data[1][0]['banesco'] != null ? data[1][0]['banesco'] : 0;
                let venezuela = data[2][0]['venezuela'] != null ? data[2][0]['venezuela'] : 0;
                let punto = data[3][0]['punto'] != null ? data[3][0]['punto'] : 0;
                let punto_v = data[4][0]['punto_v'] != null ? data[4][0]['punto_v'] : 0;
                let punto_c = data[5][0]['punto_c'] != null ? data[5][0]['punto_c'] : 0;
                let biopago = data[6][0]['biopago'] != null ? data[6][0]['biopago'] : 0;  

                Swal.fire({
                background: '#34393d',
                showConfirmButton: false,
                showCancelButton: false,
                html: `
                <div>
                    <h1 style="color: white;">Resumen del dia</h1><br>
                    <p style="color: white;">- Pagomovil Provincial: ${provincial.toFixed(2)}Bs.</p><br>
                    <p style="color: white;">- Pagomovil Banesco: ${banesco.toFixed(2)}Bs.</p><br>
                    <p style="color: white;">- Pagomovil Venezuela: ${venezuela.toFixed(2)}Bs.</p><br>
                    <p style="color: white;">- Punto Bicentenario: ${punto.toFixed(2)}Bs.</p><br>
                    <p style="color: white;">- Punto Venezuela: ${punto_v.toFixed(2)}Bs.</p><br>
                    <p style="color: white;">- Punto Caroni: ${punto_c.toFixed(2)}Bs.</p><br>
                    <p style="color: white;">- Biopago (avicola): ${biopago.toFixed(2)}Bs.</p><br>
                </div>
                `
            })
            } catch (error) {
                console.error('Fetch error:', error);
            }
        });

        async function cambiarReceptor(id, valor){
            try{
                const response = await fetch("/control_de_pago_remake/public/cambiarReceptor", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        id: id,
                        receptor: valor,
                    }),
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response}`);
                }

                const result = await response.json();

                tabla_comprobar('Cambiando banco receptor...');
            } catch (error) {
                tabla_comprobar('Error al cambiar el banco receptor...');
            }
        }
    </script>
</body>

</html>