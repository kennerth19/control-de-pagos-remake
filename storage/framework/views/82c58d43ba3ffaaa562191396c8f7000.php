<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modulo de estados de clientes</title>

    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link rel="shortcut icon" sizes="60x60" href="<?php echo e(asset('img/favicon-16x16.png')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/mikrotik.css')); ?>">
    <link href="<?php echo e(asset('css/sweetalert2.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">

    <script src="<?php echo e(asset('js/sweetalert2.js')); ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">
    

    <div class="header_principal">
        <p class="main_title_0">CONTROL DE <span class="main_title_1">PAGOS</span></p>
        <div id="custom-search-bar">
            <input type="search" id="customSearch">
        </div>
        <img src="<?php echo e(asset('img/inicio/tuerquita.png')); ?>" class="tuerquita" id="tuerquita" onclick="side_bar_tuerquita_in()">
    </div>

    <?php
    $left = '-340px;';
    ?>

    <div class="titulo">
        <div class="resumen">
            <p>RESUMEN <a href="/control_de_pago_remake/public/cortes_log" target="_blank" class="boton_log">(Ir a resumen de cortes)</a></p>
            <div id="numeros">
                <p>Suspendidos: 0</p>
                <p>Prorroga: 0</p>
                <p>Desactivados: 0</p>
                <p>Congelados: 0</p>
                <p>Premium: 0</p>
                <p>Donaciones: 0</p>
            </div>
            <div class="acciones">
                <button class="boton_corte" id="cortes" onclick="realizar_cortes()">Cortes</button>
                <button class="boton_acosador" onclick="ver_cambios()">Cambios</button>
                <button class="boton_acosador" id="boton_acosador" onclick="realizar_acoso()">Acoso (sistema → servidor)</button>
                <button class="boton_acosador"onclick="realizar_acoso_ser_sis()">Acoso (servidor → sistema)</button>
                <button class="boton_acosador"onclick="difusion()">Difusion Whatsapp</button>
            </div>
        </div>
        <h1>MODULO DE ESTADOS DE CLIENTES</h1>
        <div class="filtro">
            <p>FILTRO DE BUSQUEDA</p>
            <select onchange="tabla_principal()" id="filtro_select">
                <option>Todos</option>
                <option>Clientes suspendidos</option>
                <option>Clientes congelados</option>
                <option>Clientes desactivados</option>
                <option>Prorroga activa</option>
                <option>Clientes PREMIUM</option>
                <option>Donaciones</option>
                <option>IPTV</option>
                <option>Abonos</option>
            </select>
        </div>
    </div>
    <table id="main">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Fecha de corte</th>
                <th>Suspendido</th>
                <th>IP / Servidor</th>
                <th>Congelado / Activo</th>
                <th>Tipo de cliente / IPTV</th>
                <th>Prorroga hasta</th>
            </tr>
        </thead>
    </table>

    <script>
        var suspendido = 0;
        var prorroga = 0;
        var desactivados = 0;
        var congelados = 0;
        var premium = 0;
        var donaciones = 0;
        var iptvs = 0;

        async function tabla_principal(dato) {
            let indice = "";
            if (dato == 'main') {
                indice = 0;
            } else {
                indice = document.getElementById('filtro_select').selectedIndex;
            }
            try {
                Swal.fire({
            title: "Actualizando tabla...",
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            },
        });
                let response = await fetch(`/control_de_pago_remake/public/datos_corte/${indice}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach((element) => {
                            element.estado == 5 ? suspendido++ : 0;
                            element.congelado == 1 ? congelados++ : 0;
                            element.active == 0 ? desactivados++ : 0;
                            element.tipo_cliente == 1 ? premium++ : 0;
                            element.tipo_cliente == 2 ? donaciones++ : 0;
                            element.prorroga_hasta != null ? prorroga++ : 0;

                            if (element.iptv == 1) {
                                iptvs++
                            }

                        });

                        table = $('#main').DataTable({
                            responsive: true,
                            stateSave: false,
                            destroy: true,
                            language: {
                                url: '/control_de_pago_remake/public/js/lenguaje.json',
                            },
                            lengthMenu: [
                                [25, 50, 100, 250, 500, 1000, 1500, -1],
                                ['25', '50', '100', '250', '500', '1000', '1500', 'Todos']
                            ],
                            order: [],
                            data: data,
                            columns: [{
                                    data: 'id'
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        if(indice == 8){
                                            return `${row.nombre}<br>(Plan: ${row.valor}$ | abono: ${row.almacen}$)`;
                                        }else{
                                            return `${row.nombre}`;
                                        }
                                    }
                                },
                                {
                                    data: 'corte'
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        if (row.estado == 5) {
                                            return `SI`;
                                        } else {
                                            return `NO`;
                                        }

                                    }
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        return `${row.ip}<hr>${row.nombre_de_servidor}`
                                    }
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        let congelado = "NO";
                                        let activo = "NO";

                                        if (row.congelado == 1) {
                                            congelado = "SI";
                                        }

                                        if (row.active == 1) {
                                            activo = "SI";
                                        }
                                        return `${congelado} / ${activo}`
                                    }
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        let cadena = "";
                                        let iptv = "NO";

                                        if (row.iptv == 1) {
                                            iptv = "SI"
                                        }

                                        if (row.tipo_cliente == 2) {
                                            cadena = "DONACIÓN";
                                        } else if (row.tipo_cliente == 1) {
                                            cadena = "PREMIUM";
                                        } else {
                                            cadena = "REGULAR";
                                        }

                                        return `${cadena}<hr>${iptv}`
                                    }
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        if (row.prorroga_hasta == null || row.prorroga == 0) {
                                            return `Sin prorroga registrada`;
                                        } else {
                                            return `Prorroga hasta el dia<br>${row.prorroga_hasta}`;
                                        }
                                    }
                                },
                            ]
                        });
                        document.querySelector('#customSearch').addEventListener('input', function() {
                            table.search(this.value).draw();
                        })
                        let resumen_datos = document.getElementById('numeros');
                        resumen_datos.innerHTML = `<p>Suspendidos: ${suspendido}</p><p>Prorroga: ${prorroga}</p><p>Desactivados: ${desactivados}</p><p>Congelados: ${congelados}</p><p>Premium: ${premium}</p><p>Donaciones: ${donaciones}</p><p>IPTV: ${iptvs}</p>`;
                        suspendido = 0;
                        prorroga = 0;
                        desactivados = 0;
                        congelados = 0;
                        premium = 0;
                        donaciones = 0;
                        iptvs = 0;
                    })
            } catch (error) {
                console.log(error)
            }
        }

        function realizar_cortes() {
            window.open("/control_de_pago_remake/public/realizar_cortes", "Realizando cortes, espere...", "popup");
        }

        function ver_cambios() {
            window.open("/control_de_pago_remake/public/acosador_log", "Realizando cortes, espere...");
        }

        function realizar_acoso() {
            window.open("/control_de_pago_remake/public/acosador", "Realizando acoso, espere...", "popup");
        }

        function realizar_acoso_ser_sis() {
            window.open("/control_de_pago_remake/public/acosador_ser_sis", "Realizando acoso, espere...", "popup");
        }

        function difusion() {
            window.open("/control_de_pago_remake/public/difusion", "Difusión CheemsBot");
        }

        tabla_principal('main')
    </script>
    <script src="<?php echo e(asset('js/modulo_de_estados.js')); ?>"></script>

</body>

</html>
<?php echo $__env->make('side_bar.side_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\control_de_pago_remake\resources\views/modulo_de_cortes/modulo_de_cortes.blade.php ENDPATH**/ ?>