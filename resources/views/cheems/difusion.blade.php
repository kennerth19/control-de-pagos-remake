<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/difusion.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
    <script src="{{ asset('js/principal.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    
    <title>Difusión Cheems Bot</title>
</head>
<body>

    @extends('side_bar.side_bar')

    <div class="header_principal">
        <p class="main_title_0">CONTROL DE <span class="main_title_1">PAGOS</span></p>
        <div id="custom-search-bar">
            <div id="customSearch"></div>
        </div>
        <img src="{{ asset('img/inicio/tuerquita.png') }}" class="tuerquita" id="tuerquita" onclick="side_bar_tuerquita_in()">
    </div>

    @php
        $left = '-344px;';
    @endphp

    <h1 style="margin-left: 25%;">Difusión de mensajes</h1>
    <form class="main" action="{{route('verificar_filtro')}}" method="POST">
        <div class="filtro">  
            @csrf
            <h1>Filtro</h1>

            <input type="hidden" name="servidores" id="data_hidden_servidores" value="">
            <input type="hidden" name="estados" id="data_hidden_estados" value="">

            <div class="filtro_item" style="text-align: center;">
                Seleccionar todos <br>
                <label for="todo_0">
                    Si
                    <input type="radio" name="todos" value="1" id="todo_0" required>
                </label>

                <label for="todo_1">
                    No
                    <input type="radio" name="todos" value="0" id="todo_1" checked required>
                </label>
            </div>

            <div id="boton_filtro" class="button">Filtro <i class="fa-solid fa-filter"></i></div>

            <div class="filtros_varios">

                <div class="filtro_item" style="text-align: center;">
                    Con deuda <br>
                    <label for="deudor_0">
                        Si
                        <input type="radio" name="deudor" value="1" id="deudor_0" required>
                    </label>

                    <label for="deudor_1">
                        No
                        <input type="radio" name="deudor" value="0" id="deudor_1" required>
                    </label>

                    <label for="deudor_2">
                        Ambos
                        <input type="radio" name="deudor" value="2" id="deudor_2" required>
                    </label>
                </div>
                <br>
                <div class="filtro_item" style="text-align: center;">
                    Activo <br>
                    <label for="activo_0">
                        Si
                        <input type="radio" name="activo" value="1" id="activo_0" required>
                    </label>

                    <label for="activo_1">
                        No
                        <input type="radio" name="activo" value="0" id="activo_1" required>
                    </label>

                    <label for="activo_2">
                        Ambos
                        <input type="radio" name="activo" value="2" id="activo_2" required>
                    </label>
                </div>
                <br>
                <div class="filtro_item" style="text-align: center;">
                    IPTV <br>
                    <label for="iptv_0">
                        Si
                        <input type="radio" name="iptv" value="1" id="iptv_0" required>
                    </label>

                    <label for="iptv_1">
                        No
                        <input type="radio" name="iptv" value="0" id="iptv_1" required>
                    </label>

                    <label for="iptv_2">
                        Ambos
                        <input type="radio" name="iptv" value="2" id="iptv_2" required>
                    </label>
                </div>

            </div>

            <div style="text-align: center;">
                <input type="submit" id="confirmar" title="Debe escribir un mensaje" value="Confirmar envío">
            </div>
            
        </div>
        <div class="mensaje">
            <h2>Escribe un mensaje</h2>
            <textarea id="mensaje" rows="15" name="mensaje" required>Debes escribir un texto de al menos diez (10) caracteres.</textarea>
        </div>
    </form>

    <script src="{{ asset('js/header.js') }}"></script>
    <script src="{{ asset('js/difusion.js') }}"></script>

</body>
</html>