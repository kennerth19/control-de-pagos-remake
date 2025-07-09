<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{$clientes['nombre']}}</title>

    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pagos.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">
    <link href="{{ asset('css/nuevo_cliente.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">

    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
</head>

<body>
    @extends('side_bar.side_bar')

    @php
    $left = '-342px;';
    @endphp

    @if(session('modificado') == 'ok')
    <script>
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Cliente modificado con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('separado') == 'ok')
    <script>
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Cliente SEPARADO con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('union') == 'ok')
    <script>
        Swal.fire({
            position: 'top-end',
            icon: 'success',
            title: 'Clientes unidos con éxito con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('pago') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Pago efectuado con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('servicio') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Servicio agregado con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('conducta') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Conducta modificada con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('reinicio') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Prorroga reiniciada con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    <div class="header">
        <p class="header_item" id="resumen" onclick="switch_menu(1)" style="background-color: #fff; color: #000;">Resumen</p>
        <p class="header_item" id="servicios" onclick="switch_menu(2)">Servicios</p>
        <p class="header_item" id="facturacion_menu" onclick="switch_menu(3)">Facturación</p>
    </div>
    <div class="resumen" id="sec_1">
        <div class="first_sect">
            <div class="head">
                <h1 class="title"><strong>>></strong> Datos del servicio del cliente<span class="span_name"></span></h1>
            </div>

            <div class="servicio_item" id="div_{{$contador_1}}">
                <p style="display: none;">{{$contador_1+=1}}</p>
                <form action="{{route('menu_modificar_cliente',$clientes['id'])}}" method="POST" class="modificar_cliente">
                    @csrf
                    <p class="form_text">Nombre:</p>
                    <input type="text" name="nombre" id="" class="form_item" value="{{$clientes['nombre']}}">

                    <p class="form_text">Dirección:</p>
                    <input type="text" name="direccion" id="" class="form_item" value="{{$clientes['direccion']}}">

                    <p class="form_text">Cedula/RIF:</p>
                    <input type="text" name="cedula" id="" class="form_item" value="{{$clientes['cedula']}}">

                    <p class="form_text">Teléfono:</p>
                    <input type="tel" name="telefono" id="" class="form_item" value="{{$clientes['tlf']}}">

                    <p class="form_text">Dirección IP:</p>
                    <input type="text" name="ip" id="" class="form_item" value="{{$clientes['ip']}}">

                    <p class="form_text">Dirección MAC:</p>
                    <input type="text" name="mac" id="" class="form_item" value="{{$clientes['mac']}}">

                    <p class="form_text">Plan:</p>

                    @if($clientes['almacen'] <= 0) <select name="plan" id="" class="form_item">
                        @else
                        <select name="plan" id="" class="form_item" onclick="msg()">
                            @endif
                            <optgroup label="Inalambrico">
                                @foreach($planes as $plan)
                                @if($plan->tipo == 0)
                                @if($plan->plan == $clientes['plan'])
                                <option value="{{$plan->id}}" selected>{{$plan->plan}}</option>
                                @else
                                <option value="{{$plan->id}}">{{$plan->plan}}</option>
                                @endif
                                @endif
                                @endforeach
                            </optgroup>

                            <optgroup label="Fibra">
                                @foreach($planes as $plan)
                                @if($plan->tipo == 1)
                                @if($plan->plan == $clientes['plan'])
                                <option value="{{$plan->id}}" selected>{{$plan->plan}}</option>
                                @else
                                <option value="{{$plan->id}}">{{$plan->plan}}</option>
                                @endif
                                @endif
                                @endforeach
                            </optgroup>

                            <optgroup label="Fibra Pueblos">
                                @foreach($planes as $plan)
                                @if($plan->tipo == 2)
                                @if($plan->plan == $clientes['plan'])
                                <option value="{{$plan->id}}" selected>{{$plan->plan}}</option>
                                @else
                                <option value="{{$plan->id}}">{{$plan->plan}}</option>
                                @endif
                                @endif
                                @endforeach
                            </optgroup>
                        </select>

                        <p class="form_text">Servidor:</p>
                        <select name="servidor" id="" class="form_item">
                            @foreach($servidores as $servidor)
                            @if($servidor->nombre_de_servidor == $clientes['nombre_de_servidor'])
                            <option value="{{$servidor->id}}" selected>{{$servidor->nombre_de_servidor}}</option>
                            @else
                            <option value="{{$servidor->id}}">{{$servidor->nombre_de_servidor}}</option>
                            @endif
                            @endforeach
                        </select>

                        <p class="form_text">Fecha de corte:</p>
                        <input type="date" name="corte" id="" class="form_item" value="{{$clientes['corte']}}">

                        <p class="form_text">Almacen:</p>
                        <input type="number" name="almacen" id="" class="form_item" value="{{$clientes['almacen']}}">

                        <p class="form_text">Observación:</p>
                        <textarea name="observacion" class="form_item" style="resize: none;">{{$clientes['observacion']}}</textarea>

                        <div class="act_des">
                            @if($clientes['active'] != 0)
                            <p>Activar: <input type="radio" name="act_des" value="1" id="" checked></p>
                            <p>desactivar: <input type="radio" name="act_des" value="0" id=""></p>
                            @else
                            <p>Activar: <input type="radio" name="act_des" value="1" id=""></p>
                            <p>desactivar: <input type="radio" name="act_des" value="0" id="" checked></p>
                            @endif
                        </div>
                </form>
            </div>
        </div>

        <div class="second_sect">
            <h1><strong>>></strong> Opciones y resumen</h1>
            <div class="div_second_sect">

                @if(Auth::user()->name != 'jean')
                <div class="zona_peligrosa">
                    <p class="zona_peligrosa_p">Zona peligrosa:</p>
                    <button class="eliminar_cliente" ondblclick="borrar_cliente(<?php echo $clientes['id'] ?>,<?php echo $cuenta ?>,<?php echo $clientes['servicio_id'] ?>)">Eliminar cliente</button>
                </div>
                @endif

                <div class="zona_union">
                    <p class="zona_peligrosa_p">Zona de union:</p>
                    <button class="unir_clientes" ondblclick="unir_clientes(<?php echo $clientes['servicio_id'] ?>)">Unir clientes</button>
                </div>

                <div class="zona_conducta">
                    <p class="zona_peligrosa_p">Zona conducta:</p>
                    <button class="eliminar_cliente" ondblclick="modificar_conducta(<?php echo $clientes['id'] ?>)">Modificar conducta</button>
                </div>

                @if(Auth::user()->name == 'kennerth' || Auth::user()->name == 'antonio' || Auth::user()->name == 'marco' || Auth::user()->name == 'jean')
                <div class="zona_peligrosa">
                    <p class="zona_peligrosa_p">Zona prorrogas:</p>
                    <button class="eliminar_cliente" ondblclick="reiniciar_prorroga(<?php echo $clientes['id'] ?>)">Reiniciar prorroga</button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div id="sec_2" style="display:none;">
        <div class="head_sec_2">
            <h1>Servicios (con id: #{{$clientes['servicio_id']}})</h1>
            <button class="ser_mas boton_pago" onclick="nuevo_servicio('{{$clientes['servicio_id']}}')"><strong class="mas">+</strong> agregar otro servicio</button>
        </div>

        <div class="servicios_header">
            <p class="item_header">Nombre</p>
            <p class="item_header">Cedula/RIF</p>
            <p class="item_header">Direccion</p>
            <p class="item_header">Ult. pago</p>
            <p class="item_header">Corte</p>
            <p class="item_header">Descripcion</p>
            <p class="item_header">Opciones</p>
            <p></p>
        </div>
        @foreach($servicios_vinculados as $servicios_v)
        <div class="servicios_item">
            @if($servicios_v->principal == 1)
            <p>{{$servicios_v->nombre}} (PRINCIPAL)</p>
            @else
            <p>{{$servicios_v->nombre}}</p>
            @endif
            <p>{{$servicios_v->cedula}}</p>
            <p>{{$servicios_v->direccion}}</p>
            <p>{{$servicios_v->dia}}</p>
            <p>{{$servicios_v->corte}}</p>
            <p>{{$servicios_v->observacion}}</p>
            <button class="boton_pago" onclick="agregar_servicio({{$tasa}}, {{$servicios_v->id}}, '{{$servicios_v->nombre}}', '{{$servicios_v->tlf}}', '{{$servicios_v->cedula}}')">PAGAR SERVICIO</button>
            <button class="boton_pago" onclick="agregar_instalacion({{$tasa}},{{$servicios_v->id}},1)">PAGAR MUDANZA O MIGRACIÓN</button>
            @if($servicios_v->principal == 0)
            <button class="boton_pago" onclick="separar({{$servicios_v->id}})">SEPARAR</button>
            @endif
        </div>
        @endforeach
    </div>

    <div id="sec_3" style="display:none; padding: 12px;">
        <h1>Facturación</h1>
        <div class="main_container_table">
        <table id="main" class="ui inverted celled table" style="text-align-last: center; width: 98%;">
        <thead>
            <tr>
                <th style="text-align: center;">#</th>
                <th style="text-align: center;">Cobrador</th>
                <th style="text-align: center;">Cliente</th>
                <th style="text-align: center;">Fecha de pago</th>
                <th style="text-align: center;">Fecha de corte</th>
                <th style="text-align: center;">Concepto</th>
                <th style="text-align: center;">Total</th>
                <th style="text-align: center;">Eliminar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturaciones as $facturacion)
            <tr>
                <td>{{$facturacion->codigo}}</td>
                <td>{{$facturacion->cobrador}}</td>
                <td onclick="cliente('{{$facturacion->cliente}}', '{{$facturacion->cedula}}', '{{$facturacion->direccion}}', '{{$facturacion->plan}}')">{{$facturacion->cliente}}</td>
                <td>{{$facturacion->pago}}</td>
                <td>@if($facturacion->tipo == 0)
                        {{$facturacion->corte}}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{$facturacion->concepto}}</td>
                <td onclick="factura('{{$facturacion->bolivares}}', '{{$facturacion->pagomovil}}', '{{$facturacion->referencia}}', '{{$facturacion->banco}}','{{$facturacion->euros}}', '{{$facturacion->dolares}}', '{{$facturacion->zelle_a}}' , '{{$facturacion->zelle_b}}', '{{$facturacion->tasa}}', '{{$facturacion->total}}')">{{$facturacion->total}}$</td>
                <td><img src="{{ asset('img/menu/eliminar_pago.png') }}" onclick="ult_pago()" class="eliminar_pago no_ult" alt=""></td>
            </tr>
            @endforeach
            @foreach($facturaciones_ult as $facturacion)
                <td>{{$facturacion->codigo}}</td>
                <td>{{$facturacion->cobrador}}</td>
                <td onclick="cliente('{{$facturacion->cliente}}', '{{$facturacion->cedula}}', '{{$facturacion->direccion}}', '{{$facturacion->plan}}')">{{$facturacion->cliente}}</td>
                <td>{{$facturacion->pago}}</td>
                <td>
                    @if($facturacion->tipo == 0)
                        {{$facturacion->corte}}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{$facturacion->concepto}}</td>
                <td onclick="factura('{{$facturacion->bolivares}}', '{{$facturacion->pagomovil}}', '{{$facturacion->referencia}}', '{{$facturacion->banco}}' ,'{{$facturacion->euros}}', '{{$facturacion->dolares}}', '{{$facturacion->zelle_a}}' , '{{$facturacion->zelle_b}}', '{{$facturacion->tasa}}', '{{$facturacion->total}}')">{{$facturacion->total}}$</td>
                <td><img src="{{ asset('img/menu/eliminar_pago.png') }}" class="eliminar_pago"  onclick="eliminar_pago({{$facturacion->id}})" alt=""></td>
            @endforeach
        </tbody>
    </table>
    </div>
    </div>

    <script src="{{ asset('js/menu.js') }}"></script>
    <script src="{{ asset('js/pagos.js') }}"></script>
    <script src="{{ asset('js/principal.js') }}"></script>
    <script>
        table = $('#main').DataTable({
            responsive: true,
            pageLength: -1,
            ordering: false,
            order: [[3, 'asc']],
            language: {
                url: '/control_de_pago_remake/public/js/lenguaje.json',
            },
            lengthMenu: [
                [100, -1],
                ['100', 'Todos']
            ],
        });      
        
        function factura(bolivares, pagomovil, referencia, banco, euros, dolares, zelle_a, zelle_b, tasa, total){
            if(pagomovil == 0){
                referencia = 'N/A';
                banco = 'N/A'
            }

            Swal.fire({
                icon: 'info',
                title: 'Datos de facturacion',
                html: `- Dolares: ${dolares}$.<br>- Bolivares: ${bolivares}Bs.<br>- Pagomovil: ${pagomovil}Bs.<br>- #${referencia}<br>- Banco: ${banco}<br>- Euros: ${euros}€<br>- Zelle a/b<br>${zelle_a}$/${zelle_b}$<br>- Tasa: ${tasa}Bs.<br><br>TOTAL: ${total}$`,
            })
        }

        async function cliente(nombre, cedula, direccion, plan){
            await fetch(`/control_de_pago_remake/public/pre_registro/get_plan/${plan}`)
            .then((response) => response.json())
            .then(function (response) {
                Swal.fire({
                    icon: 'info',
                    title: 'Datos del cliente',
                    html: `- Nombre: ${nombre}.<br>- Cédula: ${cedula}.<br>- Dirección: ${direccion}.<br>- Plan: ${response.plan}`,
                })
            })
        }
    </script>
</body>

</html>