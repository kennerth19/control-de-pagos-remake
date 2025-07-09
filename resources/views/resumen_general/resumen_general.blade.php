<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="stylesheet" href="{{ asset('css/resumen_general.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.2/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.0/css/dataTables.semanticui.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">

    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.0/js/dataTables.semanticui.js"></script>

    <title>Resumen General</title>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">
    @extends('side_bar.side_bar')

    <div class="header_principal">
        <p class="main_title_0">CONTROL DE <span class="main_title_1">PAGOS</span></p>
        <div id="custom-search-bar">
            <input type="search" id="customSearch">
        </div>
        <img src="{{ asset('img/inicio/tuerquita.png') }}" class="tuerquita" id="tuerquita" onclick="side_bar_tuerquita_in()">
    </div>

    @php
    $left = '-313px;';
    @endphp
    
    <table id="main" class="ui inverted celled table" style="text-align-last: center; width: 99%;">
        <thead>
            <tr>
                <th style="text-align: center;">#</th>
                <th style="text-align: center;">Usuario</th>
                <th style="text-align: center;">Descripción</th>
                <th style="text-align: center;">Fecha</th>
                <th style="text-align: center;">Tipo de reporte</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th>#</th>
                <th>Usuario</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Tipo de reporte</th>
            </tr>
        </tfoot>
    </table>

    <script src="{{ asset('js/resumen_general.js') }}"></script>
    <style>.dt-search{display: none;}</style>
    <script src="{{ asset('js/header.js') }}"></script>
</body>

</html>