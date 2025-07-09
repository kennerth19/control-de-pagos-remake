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
    <script src="{{ asset('js/pre_registro.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>

    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/pre_registro.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagos.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nuevo_cliente.css') }}">
    <link rel="stylesheet" href="{{ asset('css/multipago.css') }}">
    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.semanticui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
    
    <title>Pre-registro</title>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">

    @if(session('registrado') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cliente REGISTRADO con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif
    
    @if(session('pre_reg_editado') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Cliente MODIFICADO con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('cambiado') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Estado MODIFICADO con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('abono') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Abono REALIZADO con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    <table id="main" class="ui inverted stripe cell-border">
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th colspan="3">Opciones</th>
            </tr>
        </thead>
    </table>

    <span id="tasa" style="display: none;">{{$tasa}}</span>
    <span id="fecha" style="display: none;">{{$fecha}}</span>

    <script>
        setTimeout(function() {
            document.querySelector("#main_wrapper > div:nth-child(1) > div.right.floated.right.aligned.eight.wide.column > div > label").style.display = "none"
            document.querySelector("#dt-search-0").setAttribute('placeholder','Búsqueda general');
        }, 1000);
    </script>
    <script src="{{ asset('js/pagos.js') }}"></script>
    <script src="{{ asset('js/menu.js') }}"></script>
</body>

</html>