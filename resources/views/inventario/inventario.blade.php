<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/inventario.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">
    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">

    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/principal.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <title>Sistema de inventario</title>
</head>
<body style="background-color: rgb(44 48 53); overflow-x: hidden;">

    @extends('side_bar.side_bar')

    @php
        $left = '-342px;';
    @endphp

<script>var msg = 'Cargando datos';</script>

    @if(session('agregado') == 'ok') 
        <script>
            msg = '¡Categoría agregada!';
        </script>
    @endif

    @if(session('editado') == 'ok') 
        <script>
            msg = '¡Categoría editada!';
        </script>
    @endif

    @if(session('noEditado') == 'ok') 
        <script>
            msg = '¡Sin cambios en la categoría!';
        </script>
    @endif

    @if(session('entrada') == 'ok') 
        <script>
            msg = '¡Productos agregado!';
        </script>
    @endif

    @if(session('salida_otros') == 'ok' || session('salida') == 'ok') 
        <script>
            msg = '¡Salida registrada!';
        </script>
    @endif

    @if(session('adulterado') == 'ok') 
        <script>
            msg = '¡Formulario adulterado, enviando reporte al admin!';
        </script>
    @endif

    @if(session('categoria_c') == 'ok') 
        <script>
            msg = '¡Categoría creada correctamente!';
        </script>
    @endif

    <div id="reader"></div>

    <div class="header">
        <img src="/control_de_pago_remake/public/img/inventario/menu.png" onclick="side_bar_tuerquita_in()" id="tuerquita" style="width: 40px;">
        <img src="/control_de_pago_remake/public/img/inventario/logo.png" class="header_item logo_item" style="width: 100px;">
        <div></div>
    </div>

    <div class="buscador_container">
        <input type="text" id="main_src" value="" placeholder="Buscar...">
    </div>

    <div class="menu"></div>

    <div id="inventario"></div>

    <div class="menu_container" id="menu_container">
        <div class="icon_menu_inv crear" id="crear" onclick="crear_categoria()">
            <h1 class="texto_cerrar">Crear categoría</h1>
            <div class="menu_item">
                <img src="/control_de_pago_remake/public/img/inventario/crear.png" style="width: 35px; height: 35px;">
            </div>
        </div>

        @if($totalCategoria > 0)
            <div class="icon_menu_inv entrar" id="entrar">
        @else
            <div class="icon_menu_inv entrar" id="entrar" onclick="inventario_vacio(1)">
        @endif
        
            <h1 class="texto_cerrar">Entrada de artículos<span id="inventario_vacio_0" style="display: none; font-size: large; color: red;">¡Primero crea una categoría!</span></h1></h1>
            <div class="menu_item">
                <img src="/control_de_pago_remake/public/img/inventario/entrada.png" style="width: 35px; height: 35px;">
            </div>
        </div>

        @if($totalExistencias > 0)
            <div class="icon_menu_inv salir" id="salir" onclick="salida()">
        @else
            <div class="icon_menu_inv salir" id="salir" onclick="inventario_vacio(0)">
        @endif

        <h1 class="texto_cerrar">Salida de artículos<br><span id="inventario_vacio" style="display: none; font-size: large; color: red;">¡Inventario vació!</span></h1></h1>
        <div class="menu_item">
                <img src="/control_de_pago_remake/public/img/inventario/salida.png" style="width: 35px; height: 35px;">
            </div>
        </div>

        <div class="icon_menu_inv log" id="log">
            <h1 class="texto_cerrar">Log</h1>
            <div class="menu_item">
                <img src="/control_de_pago_remake/public/img/inventario/log.png" style="width: 35px; height: 35px;">
            </div>
        </div>
    </div>

    <div id="crear_categoria">
        <form action="{{ route('store') }}" method="POST" enctype="multipart/form-data" id="categoria_form">
            @csrf
            <p id="titulo_crear_categoria">Crear categoría</p>
            <input type="file" name="imagen" id="entrada" accept="image/*" style="display: none;">
            <input type="hidden" name="tipo" id="tipo_categoria" value="0">
            <div class="img_container" id="entrada_img"><img src="/control_de_pago_remake/public/img/inventario/categoria_vacia.png" style="width: 41px;cursor: pointer;" title="Agregar imagen"></div>
            <div class="opciones_categoria">
                <div class="capsula-slash-right" id="right">Router</div>
                <div class="capsula-slash-mid"></div>
                <div class="capsula-slash-left" id="left">Otros</div>
            </div>
            <input type="text" name="nombre" id="nombre_categoria" class="nombre_categoria" placeholder="Nombre">
        </form>
    </div>

    <div id="editar_categoria">
        <form action="/control_de_pago_remake/public/update" method="POST" enctype="multipart/form-data" id="categoria_form_edit">
            <p id="titulo_crear_categoria">Editar categoría</p>
            <input type="file" name="imagen" id="entrada_edit" accept="image/*" style="display: none;">
            <input type="hidden" name="tipo" id="tipo_categoria_edit" value="0">
            <input type="hidden" name="id" id="categoria_id_edit" value="">
            <div class="img_container" id="entrada_img_edit"><img src="/control_de_pago_remake/public/img/inventario/categoria_vacia.png" id="cambiar_img" style="width: 41px;cursor: pointer;" title="Agregar imagen"></div>
                <div class="opciones_categoria">
                <div class="capsula-slash-right_edit" id="right_edit">Router</div>
                <div class="capsula-slash-mid_edit"></div>
                <div class="capsula-slash-left_edit" id="left_edit">Otros</div>
                <p id="categoria_id" style="display: none;"></p>
            </div>
            <input type="text" name="nombre" id="nombre_categoria_edit" value="" class="nombre_categoria_edit" placeholder="Nombre">
        </form>
    </div>
        
    <div id="icon_menu_inv" class="icon_menu_inv cross">
        <h1 class="texto_cerrar">Cerrar</h1>
        <img src="/control_de_pago_remake/public/img/inventario/mas.png" id="cross" onclick="menu_des(this.id)" style="width: 50px; height: 50px;">
    </div>

    <div id="salida_menu">
        <img src="/control_de_pago_remake/public/img/inventario/flecha.png" id="salida_flecha" title="Confirmar salida">
        <div class="menu_div" style="background-color: rgb(246, 63, 67);"><img src="/control_de_pago_remake/public/img/inventario/mas.png" id="salida_salir" class="cross_flecha" title="Salir del modo salida"></div>
    </div>

    <div id="confirmacion_menu">
        <div class="div_camion" id="div_camion" onclick="enviar()"><img src="/control_de_pago_remake/public/img/inventario/camion.png" id="salida_camion" title="Realizar salida"></div>
        <img src="/control_de_pago_remake/public/img/inventario/flecha.png" id="flecha_confirmacion" class="flecha_confirmacion" title="Salir del modo confirmación">
    </div>

    <div id="categoria_menu">
        <img src="/control_de_pago_remake/public/img/inventario/check.png" id="crear_categoria_submit" class="item_menu" title="Crear categoría">
        <div class="menu_div" style="background-color: rgb(246, 63, 67);">
            <img src="/control_de_pago_remake/public/img/inventario/mas.png" id="crear_categoria_cerrar" class="categoria_salir item_menu" title="Salir">
        </div>
    </div>

    <div id="editar_menu">
        <img src="/control_de_pago_remake/public/img/inventario/eliminar.png" id="eliminar_categoria" class="menu_sin_div" title="Eliminar categoría">
        <img src="/control_de_pago_remake/public/img/inventario/check.png" id="editar_categoria_submit" class="menu_sin_div" title="Editar categoría">
        <div class="menu_div"><img src="/control_de_pago_remake/public/img/inventario/mas.png" id="editar_categoria_cerrar" class="item_menu" title="Salir"></div>
    </div>

    <div id="entrada_menu">
        <div class="menu_div" style="background-color: rgb(74 91 224);"><img src="/control_de_pago_remake/public/img/inventario/flecha.png" id="salida_articulos" class="item_menu" title="Ir a formulario de salida de artículos"></div>
        <div class="menu_div"><img src="/control_de_pago_remake/public/img/inventario/mas.png" id="salida_cerrar" class="item_menu" title="Salir"></div>
    </div>

    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    <script src="{{ asset('js/inventario.js') }}"></script>

    <h1 id="sinElementos">Sin elementos en el inventario.</h1>

    <script>
        var usuarios = @json($usuarios);

        if({{$totalCategoria}} > 0){
            setTimeout(() => {
                main_function(msg);
            }, 500);
        }else{
            document.getElementById('sinElementos').style.display = "block";
        }
    </script>

    <input type="hidden" id="customSearch">
    <script src="{{ asset('js/header.js') }}"></script>
</body>
</html>