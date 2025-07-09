<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.semanticui.js"></script>
    <script src="<?php echo e(asset('js/evento_diario.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
    <script src="<?php echo e(asset('js/sweetalert2.js')); ?>"></script>

    <link href="<?php echo e(asset('css/sweetalert2.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
    <link rel="shortcut icon" sizes="60x60" href="<?php echo e(asset('img/favicon-16x16.png')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.semanticui.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/evento_diario.css')); ?>">

    <title>Evento diario</title>
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
        $left = '-314px;';
    ?>

    <div class="header">
        <h1 class="main_title">Evento diario</h1>
        <div class="fecha_actualizar">
            <input type="date" id="fecha" value="<?php echo e($actual_local); ?>" onchange="evento_fecha(1)">
            <img src="/control_de_pago_remake/public/img/evento_diario/actualizar.png" alt="" id="actualizar" onclick="evento_fecha(0)">
        </div>
        <div>
            <button class="boton_agregar_evento" onclick="agregar_evento_formulario('<?php echo e($actual_local); ?>')">Agregar evento</button>
            <button class="boton_agregar_evento" onclick="evento_log('<?php echo e($actual_local); ?>')">Log</button>
            <p id="cantidad_de_eventos">Cantidad de eventos: 0</p>
        </div>
        <div>
            <p id="total_sin_resto" class="sub_title">Total ingresado: 0$ / 0Bs (pagomovil) / 0Bs (efectivo) / 0€</p>
            <br>
            <p id="total" class="sub_title">Total con resto: 0$ / 0Bs (efectivo) / 0€</p>
        </div>
    </div>

    <table id="main" class="ui inverted celled table" style="text-align-last: center; width: 99%;">
        <thead>
            <tr>
                <th>#</th>
                <th>Usuario</th>
                <th>Evento</th>
                <th>Dólares</th>
                <th>Bolivares</th>
                <th>Pagomovil<br>Referencia</th>
                <th>Zelle</th>
                <th>Euros</th>
                <th>Total</th>
                <th>Opción</th>
                <th>Verificar</th>
            </tr>
        </thead>
    </table>

    <script src="<?php echo e(asset('js/header.js')); ?>"></script>
    <style>
        .dt-search {
            display: none !important;
        }
    </style>
</body>

</html>
<?php echo $__env->make('side_bar.side_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\control_de_pago_remake\resources\views/evento_diario/evento_diario.blade.php ENDPATH**/ ?>