<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" sizes="60x60" href="<?php echo e(asset('img/favicon-16x16.png')); ?>">

    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/sweetalert2.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/menu.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/pagos.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/multipago.css')); ?>" rel="stylesheet">
    
    <script src="<?php echo e(asset('js/sweetalert2.js')); ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
    <script src="<?php echo e(asset('js/principal.js')); ?>"></script>
    <p id="fecha_seleccionada" style="display: none;"></p>
    <script src="<?php echo e(asset('js/multi_pagos.js')); ?>"></script>

    <title>Multi-pagos</title>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">
    

    <script>
        sel_dia('<?php echo e($hoy); ?>');
    </script>

    <style>
        #banco_receptor{
            grid-template-columns: 1fr 1fr;
            gap: 3px;
            margin: 5px;
            width: -webkit-fill-available;
        }
    </style>

    <div class="header_principal">
        <p class="main_title_0">CONTROL DE <span class="main_title_1">PAGOS</span></p>
        <div id="custom-search-bar">
            <input type="search" id="customSearch">
        </div>
        <img src="<?php echo e(asset('img/inicio/tuerquita.png')); ?>" class="tuerquita" id="tuerquita" onclick="side_bar_tuerquita_in()">
    </div>

    <?php
    $left = '-344px;';
    ?>

    <div class="contenedor">
        <div class="resumen_multi">
            <h1 id="resumen">Resumen y fecha <?php echo e(date("d/m/Y", strtotime($hoy))); ?></h1>
            <span style="display: none;" id="recurso"><?php echo e($hoy); ?></span>
            <div id="agregados" class="table-bordered display table-hover nowrap hover" style="width:100%;">
                <div class="item_agregado header_agregado">
                    <p>#</p>
                    <p>Cliente</p>
                    <p>Total</p>
                    <p>OP</p>
                </div>
            </div>
            <div id="main_form_multipagos">
                <sup id="sup">Debes agregar al menos dos clientes*</sup>
                <input type="submit" class="desactivado" id="realizar_pago" value="PAGAR" disabled title="Debes agregar al menos dos clientes para pagar">
            </div>
        </div>
        <p id="tasa" style="display: none;"><?php echo e($tasa); ?></p>
        <div class="main_container_table">
            <table id="main" class="table-bordered display table-hover nowrap hover">
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Opción</th>
                </tr>
            </table>
        </div>
    </div>

    <script>
        showMainTable();
    </script>

    <script src="<?php echo e(asset('js/header.js')); ?>"></script>
    <script src="<?php echo e(asset('js/menu.js')); ?>"></script>
    <script src="<?php echo e(asset('js/pagos.js')); ?>"></script>
</body>

</html>
<?php echo $__env->make('side_bar.side_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\control_de_pago_remake\resources\views/pagos/multi_pagos.blade.php ENDPATH**/ ?>