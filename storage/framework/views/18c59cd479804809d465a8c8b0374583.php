<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acosados</title>

    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/mikrotik.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/style.css')); ?>" rel="stylesheet">
    <link rel="shortcut icon" sizes="60x60" href="<?php echo e(asset('img/favicon-16x16.png')); ?>">

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
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

    <h1 style="color: #fff;">Clientes acosados</h1>

    <table id="main" class="table table-bordered display table-hover nowrap hover" style="text-align: center; width:100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>Resultado</th>
                <th>Fecha</th>
            </tr>
        </thead>
    </table>

    <script>
        fetch('/control_de_pago_remake/public/acosador_data')
            .then(response => response.json())
            .then(data => {
                table = $('#main').DataTable({
                    responsive: true,
                    destroy: true,
                    order: [
                        [0, 'desc'],
                    ],
                    language: {
                        url: '/control_de_pago_remake/public/js/lenguaje.json',
                    },
                    lengthMenu: [
                        [100, 250, 500, 1000, 1500, -1],
                        ['100', '250', '500', '1000', '1500', 'Todos']
                    ],
                    data: data,
                    columns: [{
                            data: 'id'
                        },
                        {
                            data: 'resultado'
                        },
                        {
                            data: 'fecha',
                            "render": function(data, type, row) {
                                return moment(row.fecha).locale('es').format('DD [de] MMMM [de] YYYY [a las] hh:mm:ss A');
                            },
                        },
                    ]
                });
            })

        function side_bar_in() {
            let side_bar = document.getElementById('side_bar');
            let tuerquita = document.getElementById('tuerquita');

            side_bar.classList.add('side_bar_hovered');
            side_bar.removeAttribute('onmouseover');
            side_bar.setAttribute('onmouseleave', 'side_bar_out()');

            side_bar_tuerquita_in();
        }

        function side_bar_out() {
            let side_bar = document.getElementById('side_bar');
            let tuerquita = document.getElementById('tuerquita');

            side_bar.classList.remove('side_bar_hovered');
            side_bar.removeAttribute('onmouseleave');
            side_bar.setAttribute('onmouseover', 'side_bar_in()');

            side_bar_tuerquita_out();
        }

        function efecto(id_side, id_img, dec, op) {
            let side = document.getElementById(id_side);
            let img = document.getElementById(id_img);
            let decoracion = document.getElementById(dec);

            if (op == 0) {
                side.style.transform = 'scale(1.2)';
                img.style.transform = 'scale(1.2)';
                decoracion.style.width = '100%';
            } else {
                side.style.transform = 'scale(1.0)';
                img.style.transform = 'scale(1.0)';
                decoracion.style.width = '0%';
            }
        }
    </script>

    <script src="<?php echo e(asset('js/header.js')); ?>"></script>

</body>

</html>
<?php echo $__env->make('side_bar.side_bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\control_de_pago_remake\resources\views/modulo_de_cortes/acosador.blade.php ENDPATH**/ ?>