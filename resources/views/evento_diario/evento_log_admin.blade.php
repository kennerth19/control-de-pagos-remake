<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagos.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>

    <title>Eventos Eliminados 2</title>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">

    <div class="main_container_table">
        <h1>Eventos eliminados 2</h1>
        <table id="log" class="table table-bordered display table-hover nowrap hover" style="text-align: center; width:100%;">
            <thead>
                <th>#</th>
                <th>Eliminado por</th>
                <th>Evento</th>
                <th>Fecha</th>
                <th>Dolares</th>
                <th>Pagomovil</th>
                <th>Bolivares</th>
                <th>Euros</th>
                <th>Zelle V / J</th>
            </thead>
        </table>
    </div>

    <script src="{{ asset('js/evento_log_admin.js') }}"></script>

    <script>
        evento_log()
    </script>

    <style>
        #main_filter {
            display: block !important;
        }
    </style>
</body>

</html>