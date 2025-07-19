<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Control de pagos - Inicio</title>

    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/nuevo_cliente.css')); ?>" rel="stylesheet">
    <link rel="shortcut icon" sizes="60x60" href="<?php echo e(asset('img/favicon-16x16.png')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Heebo&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('css/menu.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/pagos.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/sweetalert2.css')); ?>" rel="stylesheet">

    <script src="<?php echo e(asset('js/sweetalert2.js')); ?>"></script>
    <script src="<?php echo e(asset('js/principal.js')); ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">

    

    <div class="header_principal">
        <!-- <img src="<?php echo e(asset('img/logo.png')); ?>"> -->
        <p class="main_title_0">CONTROL DE <span class="main_title_1">PAGOS</span></p>
        <div id="custom-search-bar">
            <input type="search" id="customSearch">
        </div>
        <img src="<?php echo e(asset('img/inicio/tuerquita.png')); ?>" class="tuerquita" id="tuerquita" onclick="side_bar_tuerquita_in()">
    </div>

    <?php
        $left = '-344px;';
    ?>

    <?php if(session('ejecutar_funcion')): ?>
    <script>
        // Obtener el parametro desde session
        let parametro = "<?php echo e(session('parametro')); ?>";
        
        // Ejecutar funcion javascript con parametro
        ejecutar_funcion(parametro);

        <?php echo e(session()->forget('ejecutar_funcion')); ?>

        <?php echo e(session()->forget('parametro')); ?>

    </script>
    <?php elseif((session('pagado') == 'ok')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'PAGO realizado con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('prorroga_dar') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Prorroga REGISTRADA con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('prorroga_quitar') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Prorroga ELIMINADA con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('congelado') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cliente CONGELADO con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('descongelado') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cliente DESCONGELADO con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('agregado') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: "success",
            title: "Agregado!",
            showConfirmButton: false,
            text: "Cliente agregado al PRE-REGISTRO con éxito!",
        });
    </script>
    <?php endif; ?>

    <?php if(session('agregado') == 'no'): ?>
    <script>
        Swal.fire({
            icon: "success",
            showConfirmButton: false,
            text: "Cliente no agregado al PRE-REGISTRO",
        });
    </script>
    <?php endif; ?>

    <?php if(session('borrado') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cliente y servicios ELIMINADOS con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('borrados') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cliente y servicios ELIMINADOS con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('modificado') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cliente MODIFICADO con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('tasa') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'tasa MODIFICADA con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('deuda') == 'pagada'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'La deuda total fue pagada con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('deuda') == 'abonada'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Abono de deuda realizado con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>

    <?php if(session('deuda_agregada') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Deuda agregada con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    <?php endif; ?>
    <style>
        .swal2-popup {
            width: 650px !important;
        }

        .imprimir_datos_b {
            background: black;
            padding: 5px;
            border: solid 1px;
            border-radius: 5px;
            color: #fff;
            text-decoration: none;
        }
    </style>

    <script>
        function pagomovil() {
            window.open("<?php echo e(route('pagomovil_print')); ?>", "_blank");
        }

        function zelle() {
            window.open("<?php echo e(route('zelle_print')); ?>", "_blank");
        }

        function multipago() {
            window.open("<?php echo e(route('multi_pagos')); ?>", "_blank");
        }
    </script>

    <div class="sort">
        <img src="<?php echo e(asset('img/inicio/flecha.png')); ?>" alt="" class="sort_img estado_base" id="flecha_der" onclick="mostrar_menu(0)">

        <img src="<?php echo e(asset('img/inicio/multipago.png')); ?>" id="item_8" alt="" onclick="multipago()" class="sort_img botones estado_base" title="Multi pagos">
        <img src="<?php echo e(asset('img/inicio/calculadora.png')); ?>" id="item_7" alt="" onclick="calculadora(<?php echo e($tasa); ?>)" class="sort_img botones estado_base" title="Servicio prepago">
        <img src="<?php echo e(asset('img/inicio/pm.png')); ?>" id="item_6" alt="" onclick="pagomovil()" class="sort_img botones estado_base" title="Imprimir datos de pagomovil">
        <img src="<?php echo e(asset('img/inicio/zl.webp')); ?>" id="item_5" alt="" onclick="zelle()" class="sort_img botones estado_base" title="Imprimir datos del zelle">
        <img src="<?php echo e(asset('img/inicio/servicio.png')); ?>" id="item_4" alt="" onclick="agregar_servicio(<?php echo e($tasa); ?>)" class="sort_img botones estado_base" title="Agregar pago de un servicio">
        <img src="<?php echo e(asset('img/inicio/pre_registro.png')); ?>" id="item_3" class="sort_img botones estado_base" onclick="agregar_instalacion(<?php echo e($tasa); ?>)" title="Agregar nueva instalación">
        <img src="<?php echo e(asset('img/inicio/new.png')); ?>" id="item_2" class="sort_img new_user_button botones estado_base" onclick="agregar_cliente()" title="Agregar nuevo cliente">
        <img src="<?php echo e(asset('img/inicio/tasa.png')); ?>" id="item_1" class="sort_img botones estado_base" onclick="editar_tasa(<?php echo e($tasa); ?>)" title="Cambiar tasa">

        <img src="<?php echo e(asset('img/inicio/flecha.png')); ?>" alt="" style="transform: rotate(180deg); width: 50px; cursor: pointer; filter: grayscale(100%);" id="flecha_izq" onclick="mostrar_menu(1)">
    </div>
    <div class="main_container_table">
        <table id="main" class="table table-bordered display table-hover nowrap hover" style="text-align: center; width:100%;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Plan</th>
                    <th>Servidor / IP</th>
                    <th>Corte</th>
                    <th>Estado</th>
                    <th>Activo</th>
                    <th>Op</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        function contacto(dir, tlf, nombre) {
            Swal.fire({
                customClass: {
                    container: 'modal_info',
                    popup: 'modal_info',
                    title: 'modal_title',
                },
                showConfirmButton: false,
                title: `Información de contacto de ${nombre}`,
                html: `<p class="modal_m"><strong>DIRECCIÓN: </strong>${dir}.<br><br><strong>TELÉFONO: </strong>${tlf}.</p>`,
            });
        }

        async function copy(id) {

            let texto = document.getElementById('ip_' + id).innerText;
            
            try {
                await navigator.clipboard.writeText(texto);
                Swal.fire({
                    position: "top-end",
                    icon: "success",
                    title: "Ip copiada al portapapeles",
                    showConfirmButton: false,
                    timer: 1500
                });
            } catch (err) {
                Swal.fire({
                    position: "top-end",
                    icon: "error",
                    title: "Error al copiar",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }

        var cadena_menu = '';

        function icon(id) {
            let icon = document.getElementById(id);
            if (icon.innerText == "+") {
                icon.innerText = "-";
                icon.style.backgroundColor = "#d33333";
            } else {
                icon.innerText = "+";
                icon.style.backgroundColor = "#31b131";
            }
        }

        async function showMainTable() {
            try {
                const response = await fetch('/control_de_pago_remake/public/clientes')
                    .then(response => response.json())
                    .then(data => {
                        table = $('#main').DataTable({
                            responsive: true,
                            destroy: true,
                            language: {
                                url: '/control_de_pago_remake/public/js/lenguaje.json',
                            },
                            lengthMenu: [
                                [5, 10, 25, 50, 100, 250, 500, 1000, 1500, -1],
                                ['5', '10', '25', '50', '100', '250', '500', '1000', '1500', 'Todos']
                            ],
                            data: data,
                            columns: [
                                {
                                    data: 'id',
                                    className: 'dt_control nombre_td',
                                    "render": function(data, type, row) {
                                        return `<div class="separador"><p class="nombre_c" id="nombre_c_${row.id}">+</p>${row.id}</div>`
                                    }
                                },
                                {
                                    data: 'id',
                                    className: 'dt_control nombre_td',
                                    "render": function(data, type, row) {

                                        let src = "";
                                        let title = "";

                                        if(row.conducta == 1){
                                            src = "/control_de_pago_remake/public/img/inicio/start.png";
                                            title = row.conducta_nota;
                                        }
                                        
                                        return `<p class="nombre conductas" title="ver servicios"><img src='${src}' style='width: 32px;' title='${title}'>${row.nombre}</p> <p style="display: none;">${row.tlf} ${row.direccion} ${row.cedula} ${row.mac}</p>`
                                    }
                                },
                                {
                                    data: 'plan'
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        return `<p>${row.nombre_de_servidor}</p><hr><p ondblclick="copy('${row.id}')" class="ip" id="ip_${row.id}" title="doble click para copiar al portapapeles">${row.ip}</p>`;
                                    }
                                },
                                {
                                    data: 'corte',
                                    type: 'datetime',
                                    render: function(data, type, row) {
                                        return moment(data).format('L');
                                    }
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {

                                        if(row.prorroga == 1){
                                            let fecha_formateada = moment(row.prorroga_hasta).locale('es').format('DD [de] MMMM [de] YYYY');  
                                            return `<p style="background-color: red;">Prorroga activa hasta: ${fecha_formateada}</p>`
                                        }else{
                                            if (row.tipo_cliente == 1) {
                                                row.estado = "PREMIUM";
                                                row.color = "#5500FF";
                                            } else if (row.tipo_cliente == 2) {
                                                row.estado = "DONACIÓN";
                                                row.color = "#5500FFAB";
                                            } else if (row.tipo_cliente == 3) {
                                                row.estado = "NODO";
                                                row.color = "#5500FFAB";
                                            }
                                            return `<p style="background-color: ${row.color};">${row.estado}</p>`
                                        }
                                    }
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        if (row.active == 0) {
                                            return `<p style="background-color: #ff0000;">No</p>`
                                        } else {
                                            return `<p style="background-color: #27d719;">Si</p>`
                                        }
                                    }
                                },
                                {
                                    data: 'id',
                                    "render": function(data, type, row) {
                                        return `<a href="/control_de_pago_remake/public/clientes/menu/${row.id}" target="_blank" class="pagar">Menu</a>`
                                    }
                                }
                            ]
                        });

                        $('#main tbody').on('click', 'td.dt_control', function() {
                            let tr = $(this).closest('tr');
                            let row = table.row(tr);
                            let datos = row.data();

                            if (row.child.isShown()) {
                                row.child.hide();
                                icon('nombre_c_' + datos.id);
                            } else {
                                $.get(`/control_de_pago_remake/public/servicios_vinculados/${datos.servicio_id}`, function(response) {
                                    row.child(`<div class="sub_menu" style="white-space: break-spaces;">${response}</div>`).show();
                                    icon('nombre_c_' + datos.id);
                                });
                            }
                        });
                    })
            } catch (error) {
                console.log(error)
            }
        }

        async function actualizar_clientes() {
            try {

                Swal.fire({
                    title: 'Actualizando tabla',
                    showConfirmButton: false,
                    timer: 6000,
                    timerProgressBar: true,
                })
                
                await fetch('/control_de_pago_remake/public/actualizar_estado');
            } catch (error) {
                console.log(error)
            } finally {
                showMainTable();
            }
        }

        setTimeout(function(){
            actualizar_clientes();
        },1500);
        
    </script>
    <script src="<?php echo e(asset('js/menu.js')); ?>"></script>
    <script src="<?php echo e(asset('js/pagos.js')); ?>"></script>

    <style>
        @media ((max-width: 360px)) {
            .side_bar {
                font-size: 32px !important;
            }
        }

        .estado_base {
            cursor: auto;
        }
    </style>

    <script>
        setTimeout(function() {
            let titulo = document.getElementsByTagName('title');
            title.innerText = "Control de pagos - Inicio";
        }, 1);
    </script>

    <script src="<?php echo e(asset('js/header.js')); ?>"></script>

</body>

</html>
<?php echo $__env->make('side_bar.side_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\control_de_pago_remake\resources\views/inicio/principal.blade.php ENDPATH**/ ?>