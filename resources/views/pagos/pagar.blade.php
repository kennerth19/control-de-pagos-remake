<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pagos.css') }}">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">

    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <title>Pago de: {{$cliente->nombre}}</title>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">

    <form action="/control_de_pago_remake/public/menu/pagar/{{$cliente->id}}" method="POST" id="formulario_de_pago" onsubmit="validar(event)" class="nuevo_pago">
        <h1 class="titulo">Registrar pago</h1>
        <div class="servicio_small">
            <p>Servicio de {{$cliente->nombre}}</p>
            <small>(La factura sera emitida en {{$ticket}})
                ¿imprimir factura?
                <select name="imprimir_ticket"  class="input option" style="text-align: center;">
                    <option value="1">Si</option>
                    <option value="0">No</option>
                </select>
            </small>
        </div>
        <br>
        <p>El costo del plan es: {{$plan->valor}}$ / total abonado: {{$cliente->almacen}}$ <br> <span style="font-size: 15px"> monto a pagar: {{$plan->valor - $cliente->almacen}}$ (Al cambio: {{$plan->valor - $cliente->almacen * $tasa}}Bs.)<span></p>
        <select name="" id="tipo" class="input option" onchange="tipo_de_pago({{$tasa}})">
            <option value="">Seleccione tipo de pago</option>
            <option value="1">Dolares</option>
            <option value="2">Bolivares</option>
            <option value="3">Euros</option>
            <option value="4">Zelle Vladimir</option>
            <option value="5">Zelle Jesus Millan</option>
            <option value="6">Pagomovil</option>
        </select>

        <div class="tipo_de_pago">
            <div class="item" id="1">
                <input type="number" name="dolar" id="input_1" class="input input_type input_element" placeholder="Dolares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio({{$tasa}})" onkeyup="cambio({{$tasa}})" ondblclick="agg_cantidad('input_1', {{$plan->valor - $cliente->almacen}},{{$tasa}})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(1,{{$tasa}})">
                <p>$</p>
            </div>

            <div class="item" id="2">
                <input type="number" name="bolivar" id="input_2" class="input input_type input_element" placeholder="Bolivares" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio({{$tasa}})" onkeyup="cambio({{$tasa}})" ondblclick="agg_cantidad('input_2', {{($plan->valor - $cliente->almacen) * $tasa}}, {{$tasa}})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(2,{{$tasa}})">
                <p>B</p>
            </div>

            <div class="item" id="3">
                <input type="number" name="euro" id="input_3" class="input input_type input_element" placeholder="Euros" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio({{$tasa}})" onkeyup="cambio({{$tasa}})" ondblclick="agg_cantidad('input_3', {{$plan->valor - $cliente->almacen}},{{$tasa}})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(3,{{$tasa}})">
                <p>€</p>
            </div>

            <div class="item" id="4">
                <input type="number" name="zelle_v" id="input_4" class="input input_type input_element" placeholder="Zelle Vladimir" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio({{$tasa}})" onkeyup="cambio({{$tasa}})" ondblclick="agg_cantidad('input_4', {{$plan->valor - $cliente->almacen}},{{$tasa}})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(4,{{$tasa}})">
                <p>Z V</p>
            </div>

            <div class="item" id="5">
                <input type="number" name="zelle_j" id="input_5" class="input input_type input_element" placeholder="Zelle Jesus Millan" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio({{$tasa}})" onkeyup="cambio({{$tasa}})" ondblclick="agg_cantidad('input_5', {{$plan->valor - $cliente->almacen}},{{$tasa}})" required>
                <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(5,{{$tasa}})">
                <p>Z J</p>
            </div>
        </div>

        <div class="item_pm" id="6">
            <input type="number" name="pagomovil" id="input_6" class="input input_type input_element" placeholder="Monto" value="0" step="any" min="0" pattern="^(?!.*[eE].*$)" onchange="cambio({{$tasa}})" onkeyup="cambio({{$tasa}})" ondblclick="agg_cantidad('input_6',{{($plan->valor - $cliente->almacen) * $tasa}},{{$tasa}})" required>
            <input type="text" class="input input_type input_element" placeholder="Referencia" pattern="^[0-9]{12}$" value="000000000000" name="referencia" id="input_7" style="display: block;" title="debe ingresar un numero de 12 dígitos" onkeyup="validar_referencia({{$cliente->id}})">
            <img src="/control_de_pago_remake/public/img/menu/eliminar.png" class="menos" onclick="menos(6,{{$tasa}})">
            <p>PM</p>
        </div>
        <div id="fecha_pagomovil">
            <input type="date" value="" id="fecha_pagomovil_input" class="input" style="display: none;" name="fecha_pago_movil">
            <select name="banco" id="7" class="input option item_pm_banco input_element" style="display: none;" onchange="validar_referencia({{$cliente->id}})" required>
                <option value="provincial">Provincial</option>
                <option value="venezuela">Banco de Venezuela</option>
                <option value="banca amiga">Banca amiga</option>
                <option value="mercantil">Mercantil</option>
                <option value="bancaribe">Bancaribe</option>
                <option value="banesco">Banesco</option>
                <option value="bnc">BNC</option>
                <option value="banfanb">BANFANB</option>
                <option value="bangente">BANGENTE</option>
                <option value="banplus">BANPLUS</option>
                <option value="bfc">BFC</option>
                <option value="sofitasa">SOFITASA</option>
                <option value="BDC">Venezolana de Credito</option>
                <option value="bicentenario">BICENTENARIO</option>
                <option value="mi_banco">Mi Banco</option>
                <option value="plaza">Plaza</option>
                <option value="crecer">bancrecer</option>
                <option value="plaza">Plaza</option>
                <option value="100x100">100 X 100 Banco</option>
                <option value="activo">Banco Activo</option>
                <option value="agricola">Banco Agricola</option>
                <option value="caroni">Caroni</option>
                <option value="sur">Banco del Sur</option>
                <option value="tesoro">Tesoro</option>
                <option value="exterior">Exterior</option>
                <option value="otros">Otros</option>
            </select>
        </div>
        <div id="banco_receptor" style="display: none;">
            <label>Provincial<br><input type="radio" value="1" class="" checked name="banco_receptor"></label>
            <label>Banesco<br><input type="radio" value="2" class="" name="banco_receptor"></label>
            <label>Venezuela<br><input type="radio" value="3" class="" name="banco_receptor"></label>
        </div>
        </div>
        <p id="cambio">tasa: {{$tasa}}bs / al cambio: 0.0$ / total : 0.0$</p>
        <button id="submit" class="input_submit">Registrar pago</button>
        <small id="mensaje" style="display: none;">debes especificar algún monto*</small>
    </form>

    <script src="{{ asset('js/pagos.js') }}"></script>
    <script src="{{ asset('js/menu.js') }}"></script>

    <script>
        setTimeout(function() {
            let titulo = document.getElementsByTagName('title');
            title.innerText = "Pagar: {{$cliente->nombre}}";
        }, 1);
    </script>
</body>

</html>