<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('js/pago_resumen.js') }}" rel="stylesheet">
    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Heebo&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pago_resumen.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">

    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
    <title>Resumen de pagos</title>
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
        $left = '-342px;';
    @endphp
    <div class="controles">
        <div class="main_filtro">
            <h1>Fecha y filtrado</h1>
            <div class="filtro">
            <label for="desde">
                Desde:<input type="date" class="input" value="{{$hoy}}" name="desde" id="desde">
            </label>
            <label for="hasta">
                Hasta:<input type="date" class="input" value="{{$hoy}}" name="hasta" id="hasta">
            </label>
            <select name="tipo_de_pago" id="tipo_pago" class="input" onchange="tipo_pago()">
                <option value="0">Mostrar todo</option>
                <option value="1">Mensualidades</option>
                <option value="2">Servicios</option>
                <option value="3">Por cobrador</option>
            </select>
            <select name="usuario" id="usuario" class="input" disabled>
                <option value="0">Seleccione un cobrador</option>
                @foreach($usuarios as $usuario)
                    <option value="{{$usuario->name}}">{{$usuario->name}}</option>
                @endforeach
            </select>
            </div>
            <button class="calcular" onclick="generar_endpoint()">Calcular</button>
        </div>

        <div class="total" id="resultados_totales">
            <div class="dia" id="dia">
                <p>Filtrado automático del dia de hoy</p>
            </div>
            <div class="totales" id="totales">
                <p>Total $</p>
                <p>Total Bs</p>
                <p>Total PM</p>
                <p>Total Z J/V</p>
                <p>Total €</p>
                <p>TOTAL</p>
                <p>0$</p>
                <p>0Bs</p>
                <p>0Bs</p>
                <p>0$/0$</p>
                <p>0€</p>
                <p class="resultado">0$</p>
            </div>
            <div class="opciones_resumen">
                <button class="opciones_boton" onclick="comprobar_pagomovil()">Comprobar pagomoviles</button>
                <button class="opciones_boton" onclick="comprobar_zelle()">Comprobar Zelles (en construcción)</button>
            </div>
        </div>
    </div>
    <div class="main_container_table" style="background-color: #212529;">
        <table id="main" class="table table-bordered display table-hover nowrap hover" style="text-align: center; width:100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cobrador<br>Usuario</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Concepto</th>
                    <th>Dolares</th>
                    <th>Bolivares</th>
                    <th>Pagomovil<br>Referencia<br>Receptor</th>
                    <th>Euros</th>
                    <th>Zelle<br>J/V</th>
                    <th>Total $</th>
                    <th>Opciones</th>
                </tr>
            </thead>
        </table>
    </div>
    
    <script src="{{asset('js/pago_resumen.js')}}"></script>
    <script src="{{ asset('js/header.js') }}"></script>

    <script>
        generar_endpoint();
    </script>
</body>

</html>