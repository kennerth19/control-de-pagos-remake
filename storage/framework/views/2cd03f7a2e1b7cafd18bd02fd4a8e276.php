<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de la comprobación</title>
    <link rel="shortcut icon" sizes="60x60" href="<?php echo e(asset('img/favicon-16x16.png')); ?>">
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/sweetalert2.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">

    <script src="<?php echo e(asset('js/sweetalert2.js')); ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
</head>
<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">
    

    <?php
        $left = '-344px;';
    ?>

    <div class="header_principal">
        <!-- <img src="<?php echo e(asset('img/logo.png')); ?>"> -->
        <p class="main_title_0">CONTROL DE <span class="main_title_1">PAGOS</span></p>
        <div id="custom-search-bar">
            <input type="search" id="customSearch">
        </div>
        <img src="<?php echo e(asset('img/inicio/tuerquita.png')); ?>" class="tuerquita" id="tuerquita" onclick="side_bar_tuerquita_in()">
        <script src="<?php echo e(asset('js/header.js')); ?>"></script>
    </div>

    <style>
        #regresar{
            font-family: cursive;
            background-color: #212529;
            color: #fff;
            border: solid 1px;
            border-radius: 4px;
            padding: 5px;
            cursor: pointer;
        }

        .head_comp{
            display: grid;
            grid-template-columns: 75% auto;
            justify-items: center;
            align-items: center;
        }
    </style>

    <div class="contenido">
        <div class="head_comp">
            <h1 style="color: white;">Resultado de la ultima comprobación</h1>
            <button id="regresar">Regresar</button>
        </div>
        

        <div class="main_container_table">
        <table id="main" class="table-bordered display table-hover nowrap hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Documento excel</th>
                    <th>Pago del sistema</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $comprobacion; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->id); ?></td>
                    <td><?php echo e($item->documento); ?></td>
                    <td><?php echo e($item->sistema); ?></td>
                    
                        <?php if($item->alerta == 0): ?>
                            <td>
                                <b style="color: green;">Correcto ✔</b>
                            </td>
                            <td>
                                <b style="color: green;">Marcado</b>
                            </td>
                        <?php elseif($item->alerta == 1): ?>
                            <td>
                                <b style="color: green;">Cantidad dentro del rango permitido (20bs) ✔</b>
                            </td>
                            <td>
                                <b style="color: green;">Marcado</b>
                            </td>
                        <?php elseif($item->alerta == 2): ?>
                            <td>
                                <b style="color: red;">Cantidad fuera del rango permitido (20bs) ❌</b>
                            </td>
                            <td>
                                <b style="color: green;">Sin acciones</b>
                            </td>
                        <?php elseif($item->alerta == 3): ?>
                            <td>
                                <b style="color: red;">No encontrado</b>
                            </td>
                            <td>
                                <b style="color: green;">Sin acciones</b>
                            </td>
                        <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    </div>

    <script>
        const regresar = document.getElementById('regresar');

        table = $('#main').DataTable({
            responsive: true,
            stateSave: false,
            lengthMenu: [
                [25, 50, -1],
                ['25', '50', 'Todos']
            ],
            language: {
                url: '/control_de_pago_remake/public/js/lenguaje.json',
            },
        });

        regresar.addEventListener("click", () => window.location.href = "comprobar");
    </script>
</body>
</html>
<?php echo $__env->make('side_bar.side_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\control_de_pago_remake\resources\views/comprobacion/comprobacion.blade.php ENDPATH**/ ?>