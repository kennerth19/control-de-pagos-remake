<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Menu administrativo</title>

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="shortcut icon" sizes="60x60" href="{{ asset('img/favicon-16x16.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Heebo&display=swap" rel="stylesheet">
    <link href="{{ asset('css/menu.css') }}" rel="stylesheet">
    <link href="{{ asset('css/menu_administrativo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.css') }}" rel="stylesheet">

    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <script src="{{ asset('js/principal.js') }}"></script>
    <script src="{{ asset('js/menu_administrativo.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.6/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/es.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">
    @if(session('cambiado') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Imagen de perfil cambiada!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('eliminado') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Imagen de perfil cambiada!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    @if(session('usuario') == 'ok')
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Usuario modificado con éxito!',
            showConfirmButton: false,
            timer: 3000
        })
    </script>
    @endif

    <div class="menu_administrador">
        <div class="perfil" style="background-image: url('{{$perfil}}');" title="perfil de {{Auth::user()->name}}"><img src="/control_de_pago_remake/public/img/perfil/camara.png" title="cambiar foto de perfil" class="camara" onclick="cambiar_eliminar({{Auth::user()->id}})"></div>
        <p class="fecha_administrador"><i class="fa-regular fa-calendar-days"></i>{{$hoy}}</p>
        <div class="div_aux" id="div_aux_0">
            <p class="menu_item" onmouseover="selected_in(0)" onmouseleave="selected_out(0)" onclick="cambiar('inicio')"><i class="fa-solid fa-house"></i>Inicio</p>
            <div class="adorno" id="adorno_0"></div>
        </div>
        <div class="div_aux" id="div_aux_1">
            <p class="menu_item" onmouseover="selected_in(1)" onmouseleave="selected_out(1)" onclick="cambiar('cuentas')"><i class="fa-regular fa-user"></i>Cuentas</p>
            <div class="adorno" id="adorno_1"></div>
        </div>
        <div class="div_aux" id="div_aux_2">
            <p class="menu_item" onmouseover="selected_in(2)" onmouseleave="selected_out(2)" onclick="cambiar('planes')"><i class="fa-solid fa-house-signal"></i>Planes</p>
            <div class="adorno" id="adorno_2"></div>
        </div>
        <div class="div_aux" id="div_aux_3">
            <p class="menu_item" onmouseover="selected_in(3)" onmouseleave="selected_out(3)" onclick="cambiar('servidores')"><i class="fa-solid fa-server"></i>Servidores</p>
            <div class="adorno" id="adorno_3"></div>
        </div>
        <div class="div_aux" id="div_aux_4">
            <p class="menu_item" onmouseover="selected_in(4)" onmouseleave="selected_out(4)" onclick="cambiar('nodos')"><i class="fa-solid fa-satellite-dish"></i>Nodos</p>
            <div class="adorno" id="adorno_4"></div>
        </div>
        <div class="div_aux" id="div_aux_5">
            <p class="menu_item" onmouseover="selected_in(5)" onmouseleave="selected_out(5)" onclick="cambiar('api')"><i class="fa-solid fa-gear"></i>API</p>
            <div class="adorno" id="adorno_5"></div>
        </div>
        <div class="div_aux" id="div_aux_6">
            <p class="menu_item cerrar_sesion" onmouseover="selected_in(6)" onmouseleave="selected_out(6)"><i class="fa-solid fa-right-from-bracket"></i>Cerrar sesión</p>
            <div class="adorno" id="adorno_6"></div>
        </div>
    </div>

    <div id="inicio" class="seccion">
        <h1 style="text-align: center;">Bienvenido <span id="username">{{Auth::user()->name}}</span></h1>
        
        <div class="sub_title">
            <h1 style="text-align: center;">Inicio</h1>
            <button onclick="editar_usuario({{Auth::user()->id}})" class="button edit">Configurar usuario</button>
            <button onclick="crear_usuario()" class="button edit">Crear usuario</button>
            <button class="button edit" onclick="crear_instalacion()">Crear instalación</button>
        </div>
        
        <table id="tabla_instalaciones" class="table-bordered display table-hover nowrap hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Instalación</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div id="cuentas" style="display: none;" class="seccion">
        <h1 style="text-align: center;">Cuentas</h1>
        <table id="tabla_usuarios" class="table-bordered display table-hover nowrap hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Grupo</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div id="planes" style="display: none;" class="seccion">

        <div class="sub_title">
            <h1 style="text-align: center;">Planes</h1>
            <button class="button edit" onclick="crear_plan()">Crear plan</button>
        </div>

        <table id="tabla_planes" class="table-bordered display table-hover nowrap hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>valor</th>
                    <th>tipo</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div id="servidores" style="display: none;" class="seccion">
        <div class="sub_title">
            <h1 style="text-align: center;">Servidores</h1>
            <button class="button edit" onclick="crear_servidor()">Crear Servidor</button>
        </div>
        
        <table id="tabla_servidores" class="table-bordered display table-hover nowrap hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>IP</th>
                    <th>Puerto</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div id="nodos" style="display: none;" class="seccion">
        <h1 style="text-align: center;">Nodos</h1>
        <table id="tabla_nodos" class="table-bordered display table-hover nowrap hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>IP</th>
                    <th>MAC</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div id="api" style="display: none;" class="seccion">
        <h1 style="text-align: center;">Api</h1>
        <button class="button edit" onclick="realizarCortes()">Realizar cortes</button>
        <button class="button edit" onclick="enviarRecordatorio()">Enviar recordatorio</button>
    </div>

    <script>
        const sesion = document.getElementById("div_aux_6");

        sesion.addEventListener('click', () => {
            window.location.href = "/control_de_pago_remake/public/logout";
        })
    </script>
</body>

</html>