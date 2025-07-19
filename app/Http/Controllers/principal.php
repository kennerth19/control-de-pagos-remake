<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\clientes;
use App\Models\clientes_aux;
use App\Models\servidores;
use App\Models\planes;
use App\Models\servicios;
use App\Models\sort;
use App\Models\resumen_general;
use App\Models\pago_resumen;
use App\Models\evento;
use App\Models\instalaciones;
use App\Models\historial_tasa;
use App\Models\caja;

use App\api\api;

use Carbon\Carbon;
use DateTime;

date_default_timezone_set('America/Caracas');

class principal extends Controller
{
    public function index() //Función principal para mostrar la vista de la pagina de inicio
    {
        $sort = sort::findOrFail(1)->toArray();
        $tasa = $sort["tasa"];

        $hoy = date('Y-m-d', strtotime(Carbon::now()));
        $ult = clientes::orderBy('id', 'desc')->first()->id;

        for ($i = 1; $ult >= $i; $i++) { // función para actualizar los estados en el modulo de corte, desactivar automáticamente y quitar y agregar prorrogas automáticas.
            $cliente_de_turno = clientes::find($i);
            

            if ($cliente_de_turno != null) {
                //$cliente_de_turno->dias_prorroga = null;
                //eliminar almacén menor a igual a 1$

                if ($cliente_de_turno->almacen <= 1) { // se perdió, todito se perdió :|
                    $cliente_de_turno->almacen = 0;
                }

                if ($cliente_de_turno->corte == $hoy) { // dia de corte
                    $cliente_de_turno->estado = 4;
                    $cliente_de_turno->prorroga = 0;
                    $cliente_de_turno->prorroga_hasta = null;
                    $cliente_de_turno->nota_prorroga = null;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte + 1 day")) == $hoy) { // prorroga dia 1
                    $cliente_de_turno->prorroga = 0;
                    $cliente_de_turno->prorroga_hasta = null;
                    $cliente_de_turno->nota_prorroga = null;
                    $cliente_de_turno->estado = 6;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte + 2 day")) == $hoy) { // prorroga dia 2
                    $cliente_de_turno->prorroga = 0;
                    $cliente_de_turno->prorroga_hasta = null;
                    $cliente_de_turno->nota_prorroga = null;
                    $cliente_de_turno->estado = 7;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte - 1 day")) == $hoy) { // resta 1 dia
                    $cliente_de_turno->prorroga = 0;
                    $cliente_de_turno->prorroga_hasta = null;
                    $cliente_de_turno->nota_prorroga = null;
                    $cliente_de_turno->estado = 3;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte - 2 day")) == $hoy) { // restan 2 días
                    $cliente_de_turno->prorroga = 0;
                    $cliente_de_turno->prorroga_hasta = null;
                    $cliente_de_turno->nota_prorroga = null;
                    $cliente_de_turno->estado = 2;
                } else if ($cliente_de_turno->corte >= date('Y-m-d', strtotime("$hoy + 3 day"))) { // solvente
                    $cliente_de_turno->prorroga = 0;
                    $cliente_de_turno->prorroga_hasta = null;
                    $cliente_de_turno->nota_prorroga = null;
                    $cliente_de_turno->estado = 1;
                } else if ($cliente_de_turno->corte >= date('Y-m-d', strtotime("$hoy - day"))) { // suspendido
                    $cliente_de_turno->estado = 5;
                }

                if ($hoy >= date('Y-m-d', strtotime("$cliente_de_turno->dia + 2 months")) && $cliente_de_turno->active == 1 && $cliente_de_turno->estado == 5 && $cliente_de_turno->tipo_cliente == 0) { //Desactivar automáticamente.

                    if($cliente_de_turno->tipo_cliente > 0){
                        $cliente_de_turno->active = 1;
                    }else{
                        if($cliente_de_turno->estado == 5){
                            $resumen = new resumen_general();
                            $resumen->usuario = 'SYSTEM';
                            $resumen->descripcion = "Se DESACTIVO el cliente: $cliente_de_turno->nombre de forma automática.";
                            $resumen->tipo = 21;
                            $resumen->save();
        
                            $cliente_de_turno->active = 0;
                        } 
                    }
                }

                if($hoy >= $cliente_de_turno->ult_prorroga){
                    $cliente_de_turno->ult_prorroga = null;
                }

                if ($cliente_de_turno->prorroga_hasta < $hoy && $cliente_de_turno->prorroga_hasta != null) {
                    $cliente_de_turno->prorroga = 0;
                    $cliente_de_turno->prorroga_hasta = null;
                    $cliente_de_turno->nota_prorroga = null;

                    /* Resumen General */
                    $resumen = new resumen_general();

                    $resumen->usuario = 'SYSTEM';
                    $resumen->descripcion = "La prorroga del cliente $cliente_de_turno->nombre fue eliminada automáticamente.";
                    $resumen->tipo = 18;

                    /* Resumen General */

                    $resumen->save();
                }

                if ($cliente_de_turno->prorroga == 0 && $cliente_de_turno->estado == 5 && $cliente_de_turno->ult_prorroga == null) { // dar prorroga automática.
                    $plan = planes::findOrFail($cliente_de_turno->plan_id);
                    $medio = ($plan->valor / 2);

                    if ($cliente_de_turno->almacen > $medio) { // valido para prorroga automática
                        $cliente_de_turno->prorroga = 1;
                        $cliente_de_turno->dias_prorroga += 7;
                        $cliente_de_turno->prorroga_hasta = date('Y-m-d', strtotime("$hoy + 7 days"));
                        $cliente_de_turno->nota_prorroga = "Prorroga automática";
                        $cliente_de_turno->ult_prorroga = date("Y-m-d", strtotime("$hoy, + 30 days"));

                        $prorroga_hasta_f = date('d-m-Y', strtotime("$cliente_de_turno->prorroga_hasta"));

                        /* Resumen General */
                        $resumen = new resumen_general();

                        $resumen->usuario = 'SYSTEM';
                        $resumen->descripcion = "Prorroga hasta el dia $prorroga_hasta_f agregada automáticamente al cliente $cliente_de_turno->nombre.";
                        $resumen->tipo = 11;

                        /* Resumen General */

                        $resumen->save();
                    }
                }

                /*
                este bloque se mando a desactivar por marco para no restar 3 días
                if($cliente_de_turno->estado == 5 && $cliente_de_turno->parche_prorroga != 1){
                    $cliente_de_turno->parche_prorroga = 1;
                }

                if($cliente_de_turno->estado == 1 && $cliente_de_turno->parche_prorroga == 1){
                    $cliente_de_turno->parche_prorroga = 0;
                    $cliente_de_turno->corte = date("Y-m-d", strtotime("$cliente_de_turno->corte, - 3 days"));
                }*/

                if($cliente_de_turno->estado == 1){
                    $cliente_de_turno->active = 1;
                }

                $cliente_de_turno->save();
            }
        }

        return view('inicio/principal', compact('tasa'));
    }

    public function clientes() //Función para recibir todos los clientes de la BD y enviarlos por un json a la ruta.
    {
        $data[] = "";

        $actual = Carbon::now();

        $clientes = DB::select('SELECT clientes.*, planes.plan, planes.valor, estados.estado, estados.color, servidores.nombre_de_servidor FROM clientes INNER JOIN servidores ON clientes.servidor = servidores.id INNER JOIN planes ON clientes.plan_id = planes.id INNER JOIN estados ON clientes.estado = estados.id');

        return response()->json($clientes);
    }

    public function retornar_cliente($id)
    {
        $cliente = DB::select("SELECT clientes.*, planes.plan, planes.valor, estados.estado, estados.color, servidores.nombre_de_servidor FROM clientes INNER JOIN servidores ON clientes.servidor = servidores.id INNER JOIN planes ON clientes.plan_id = planes.id INNER JOIN estados ON clientes.estado = estados.id WHERE clientes.id = $id");

        return response()->json($cliente);
    }

    public function actualizar_estado()
    {
        $ult = clientes::orderBy('id', 'desc')->first()->id;
        $hoy = date('Y-m-d', strtotime(Carbon::now()));

        for ($i = 1; $ult >= $i; $i++) {

            $cliente_de_turno = clientes::find($i);

            if ($cliente_de_turno) {

                if ($cliente_de_turno->corte == $hoy) { // dia de corte
                    $cliente_de_turno->estado = 4;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte + 1 day")) == $hoy) { // prorroga dia 1
                    $cliente_de_turno->estado = 6;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte + 2 day")) == $hoy) { // prorroga dia 2
                    $cliente_de_turno->estado = 7;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte - 1 day")) == $hoy) { // resta 1 dia
                    $cliente_de_turno->estado = 3;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte - 2 day")) == $hoy) { // restan 2 días
                    $cliente_de_turno->estado = 2;
                } else if ($cliente_de_turno->corte >= date('Y-m-d', strtotime("$hoy + 3 day"))) { // solvente
                    $cliente_de_turno->estado = 1;
                } else if ($cliente_de_turno->corte >= date('Y-m-d', strtotime("$hoy - day"))) { // suspendido
                    $cliente_de_turno->estado = 5;
                }

                $cliente_de_turno->save();
            }
        }
    }

    public function data() //Función para recibir los planes en un json
    {
        $data[] = "";

        $data[0] = DB::select('SELECT * FROM `planes`');
        $data[1] = DB::select('SELECT * FROM `servidores` WHERE `active` = 1');
        $data[2] = DB::select("SELECT i.*, COALESCE(COUNT(CASE WHEN e.asignado = 0 THEN e.categoria_id END), 0) AS total_existencias FROM instalaciones AS i LEFT JOIN existencias AS e ON i.inventario_categoria = e.categoria_id GROUP BY i.id, i.router, i.inventario_categoria;");

        return response()->json($data);
    }

    public function bancos() //Función para recibir los bancos en un json
    {
        $data = DB::select('SELECT * FROM `bancos`');

        return response()->json($data);
    }

    public function agregar_cliente(Request $request) //Función para agregar clientes nuevos de manera manual y su servicio principal
    {
        $ult = clientes::orderBy('id', 'desc')->first()->id;
        $actual = Carbon::now();

        $nuevo_cliente = new clientes();
        $nuevo_cliente->nombre = $request->nombre;
        $nuevo_cliente->tlf = $request->telefono;
        $nuevo_cliente->cedula = $request->cedula;
        $nuevo_cliente->direccion = $request->direccion;
        $nuevo_cliente->servidor = $request->servidor;
        $nuevo_cliente->plan_id = $request->plan;
        $nuevo_cliente->ip = $request->ip;
        $nuevo_cliente->mac = $request->mac;
        $nuevo_cliente->observacion = $request->observacion;
        $nuevo_cliente->servicio_id = $ult + 1;
        $nuevo_cliente->mes = Carbon::now()->format('d');

        $nuevo_cliente->estado = 1;
        $nuevo_cliente->active = 1;

        $nuevo_cliente->dia = $actual;
        $nuevo_cliente->corte = $actual;
        $nuevo_cliente->dia_i = $actual;

        $nuevo_cliente->save();

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se AGREGO manualmente el cliente $nuevo_cliente->nombre.";
        $resumen->tipo = 3;

        /* Resumen General */

        $resumen->save();

        return back()->with('enviado', $request->nombre);
    }

    public function servicios_vinculados($id)
    { // función que busca en la tabla clientes -> servicio_id columna todos los servicios correspondientes a esa columna.
        $servicios_vinculados = DB::select("SELECT clientes.*, planes.valor, servidores.nombre_de_servidor, estados.estado FROM `clientes` INNER JOIN estados ON clientes.estado = estados.id INNER JOIN servidores ON clientes.servidor = servidores.id INNER JOIN planes ON clientes.plan_id = planes.id WHERE `servicio_id` = $id");
        $sort = sort::findOrFail(1)->toArray();
        $tasa = $sort["tasa"];
        $cadena = "<p>ID</p><p>NOMBRE</p><p>DIRECCIÓN</p><p>CORTE</p><p>IP</p><p>SERVIDOR</p><p>ESTADO</p><p>OPCIONES</p><p></p>";
        $hoy = Carbon::now()->format('Y-m-d');

        foreach ($servicios_vinculados as $servicio) {
            $fecha_corte = date('d-m-Y', strtotime($servicio->corte));

            $factura = "";
            $congelado = "";
            $prorroga = "";
            $deuda = "";
            $op = 0;
            $br = "";
            $style = "";
            $dias_p = "";

            if ($servicio->tipo_cliente == 1) {
                $style = "background-color: #5500FF;";
                $servicio->estado = "PREMIUM";
            } else if ($servicio->tipo_cliente == 2) {
                $style = "background-color: #5500FFAB;";
                $servicio->estado = "DONACIÓN";
            } else if ($servicio->tipo_cliente == 3) {
                $style = "background-color: #5500FFAB;";
                $servicio->estado = "NODO";
            }

            if ($servicio->deuda > 5) {
                $op = 1;
            }

            if ($servicio->ticket == 0) {
                $factura = "bolivares";
            } else {
                $factura = "dolares";
            }

            if ($servicio->congelado == 1) {
                $congelado = "<img src='/control_de_pago_remake/public/img/inicio/congelado_icon.png' class='iconos' title='Cliente congelado'>";
            }

            if ($servicio->prorroga == 1) {
                $prorroga = "<img src='/control_de_pago_remake/public/img/inicio/prorroga_icon.png' class='iconos' title='Cliente con prorroga hasta $servicio->prorroga_hasta'>";
            }

            if ($servicio->deuda > 0) {
                $deuda = "<img src='/control_de_pago_remake/public/img/inicio/deuda.png' class='iconos' title='Cliente con deuda pendiente'>";
            }

            if ($servicio->prorroga == 1 && $servicio->congelado == 1 && $servicio->deuda > 0) {
                $br = "<br>";
            } else if ($servicio->prorroga == 1) {
                $br = "<br>";
            } else if ($servicio->congelado == 1) {
                $br = "<br>";
            } else if ($servicio->deuda > 0) {
                $br = "<br>";
            } else {
                $br = "";
            }

            if ($servicio->dias_prorroga != null) {
                $dias_p = "<span class='dias_p'>$servicio->dias_prorroga</span>";
            }

            if ($servicio->deuda <= 0) {
                if ($servicio->principal == 1) {
                    $cadena .= "<p>#$servicio->id</p><p>$prorroga$congelado$deuda$dias_p$br<span class='pago_invididual' target='_blank'>$servicio->nombre (PRINCIPAL)</span></p><p>$servicio->direccion</p><p style='cursor: pointer;' onclick='cambios($servicio->id)' title='Click aquí para ver los cambios nuevos'>$fecha_corte<br>Cambios: $servicio->cambios</p><p>$servicio->ip</p><p>$servicio->nombre_de_servidor</p><p style='$style'>$servicio->estado</p><button id='pago_$servicio->id' class='boton_pago' onclick='pagar(`$servicio->id`, `$tasa`, `$factura`, 1 , `$hoy`)'>Pagar</button><button class='boton_pago' onclick='modificar_cliente($servicio->id)'>Editar</button>";
                } else {
                    $cadena .= "<p>#$servicio->id</p><p>$prorroga$congelado$dias_p$br<span class='pago_invididual' target='_blank'>$servicio->nombre</span></p><p>$servicio->direccion</p><p style='cursor: pointer;' onclick='cambios($servicio->id)' title='Click aquí para ver los cambios nuevos'>$fecha_corte<br>Cambios: $servicio->cambios</p><p>$servicio->ip</p><p>$servicio->nombre_de_servidor</p><p style='$style'>$servicio->estado</p><button id='pago_$servicio->id' class='boton_pago' onclick='pagar(`$servicio->id`, `$tasa`, `$factura`, 1, `$hoy`)'>Pagar</button><button class='boton_pago' onclick='modificar_cliente($servicio->id)'>Editar</button>";
                }
            } else {
                if ($servicio->principal == 1) {
                    $cadena .= "<p>#$servicio->id</p><p>$prorroga$congelado$dias_p$br$servicio->nombre (PRINCIPAL)</p><p>$servicio->direccion</p><p style='cursor: pointer;' onclick='cambios($servicio->id)' title='Click aquí para ver los cambios nuevos'>$fecha_corte<br>Cambios: $servicio->cambios</p><p>$servicio->ip</p><p>$servicio->nombre_de_servidor</p><p style='$style'>$servicio->estado</p><button id='pago_$servicio->id' class='boton_pago' onclick='mensaje_deuda($servicio->id,`$servicio->nombre`,`$servicio->deuda`,`$servicio->motivo_deuda`, `$tasa`, `$factura`, `$op`)'>Pagar</button><button class='boton_pago' onclick='modificar_cliente($servicio->id)'>Editar</button>";
                } else {
                    $cadena .= "<p>#$servicio->id</p><p>$prorroga$congelado$dias_p$br$servicio->nombre</p><p>$servicio->direccion</p><p style='cursor: pointer;' onclick='cambios($servicio->id)' title='Click aquí para ver los cambios nuevos'>$fecha_corte<br>Cambios: $servicio->cambios</p><p>$servicio->ip</p><p>$servicio->nombre_de_servidor</p><p style='$style'>$servicio->estado</p><button id='pago_$servicio->id' class='boton_pago' onclick='mensaje_deuda($servicio->id,`$servicio->nombre`,`$servicio->deuda`,`$servicio->motivo_deuda`, `$tasa`, `$factura`, `$op`)'>Pagar</button><button class='boton_pago' onclick='modificar_cliente($servicio->id)'>Editar</button>";
                }
            }
        }

        return $cadena;
    }

    public function agregar_servicio(Request $request)
    {
        $servicio_nuevo = new pago_resumen();
        $cuenta = pago_resumen::count();

        $servicio_nuevo->usuario = Auth::user()->name;
        $servicio_nuevo->cobrador = Auth::user()->name;
        $servicio_nuevo->codigo =  "SERV_" . $cuenta;

        //datos del cliente
        $servicio_nuevo->cliente = $request->nombre;
        $servicio_nuevo->cedula = $request->cedula;
        $servicio_nuevo->telefono = $request->telefono;
        $servicio_nuevo->concepto = $request->observacion;
        $servicio_nuevo->id_cliente = $request->id;

        //facturación
        $servicio_nuevo->tasa = $request->tasa;
        $servicio_nuevo->dolares = number_format($request->dolar, 2, '.', '');
        $servicio_nuevo->bolivares = number_format($request->bolivar, 2, '.', '');
        $servicio_nuevo->euros = number_format($request->euro, 2, '.', '');
        $servicio_nuevo->zelle_a = number_format($request->zelle_v, 2, '.', '');
        $servicio_nuevo->zelle_b = number_format($request->zelle_j, 2, '.', '');
        $servicio_nuevo->pagomovil = number_format($request->pagomovil, 2, '.', '');
        $servicio_nuevo->fecha_pago_movil = $request->fecha_pago_movil;
        $servicio_nuevo->banco_receptor = $request->banco_receptor;

        $servicio_nuevo->referencia = $request->referencia;
        $servicio_nuevo->banco = $request->banco;
        $servicio_nuevo->pago = $request->fecha_preg;

        $total_bs = ($servicio_nuevo->bolivares + $servicio_nuevo->pagomovil) / $servicio_nuevo->tasa;

        $servicio_nuevo->total = $servicio_nuevo->dolares + $servicio_nuevo->euros + $servicio_nuevo->zelle_a + $servicio_nuevo->zelle_b + $total_bs;

        //otras columnas
        $servicio_nuevo->direccion = "N/A";
        $servicio_nuevo->corte = "N/A";
        $servicio_nuevo->plan = "N/A";
        $servicio_nuevo->active = 1;
        $servicio_nuevo->servicio = 1;
        $servicio_nuevo->tipo = 1;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se registro un pago de un servicio de $servicio_nuevo->cliente, bajo el concepto de '$servicio_nuevo->concepto'";
        $resumen->tipo = 4;
        $resumen->save();

        /* Resumen General */

        $servicio_nuevo->save();

        /*evento diario*/
        $hoy = Carbon::now()->format('Y-m-d');

        if(Auth::user()->grupo == 1){
            $evento = new evento();
            $evento->usuario = Auth::user()->name;
        }else if(Auth::user()->grupo == 2){
            $evento = new caja();
            $evento->usuario = Auth::user()->name;
        }else{
            $evento = new evento();
        }
        
        $evento->usuario = Auth::user()->name;

        $total_bs = ($request->bolivar + $request->pagomovil) / $request->tasa;
        $total_d = ($request->dolar + $request->euro + $request->zelle_v + $request->zelle_j);

        $total = $total_bs + $total_d;

        $evento->evento = "<b><b class='tipo'>TIPO DE PAGO: SERVICIO</b><br>$request->nombre | $request->cedula | $request->telefono</b><br>$request->observacion";
        $evento->bolivares = number_format($request->bolivar, 2, '.', '');
        $evento->pagomovil = number_format($request->pagomovil, 2, '.', '');
        $evento->ref = $request->referencia;
        $evento->dolares = number_format($request->dolar, 2, '.', '');
        $evento->euros = number_format($request->euro, 2, '.', '');
        $evento->zelle_j = number_format($request->zelle_j, 2, '.', '');
        $evento->zelle_v = number_format($request->zelle_v, 2, '.', '');
        $evento->total = $total;
        $evento->hora = $hoy;

        //grupo oficina o grupo caja fuerte
        if (Auth::user()->grupo == 1 || Auth::user()->grupo == 2) {
            $evento->save();
        }
        /*evento diario */

        return back()->with('pago', 'ok');
    }

    public function editar_tasa(Request $request)
    {
        $hoy = Carbon::now()->format('Y-m-d');
        $texto = "";
        $sort = sort::findOrFail(1);
        $ult_historial = historial_tasa::orderBy('id', 'desc')->first()->id;

        if ($ult_historial) {

            $historial_tasa = historial_tasa::findOrFail($ult_historial);

            if ($historial_tasa->fecha != $hoy) {

                $texto = " (y se agrego al historial de tasas)";

                $historial_tasa = new historial_tasa();
                $historial_tasa->tasa = $request->tasa;
                $historial_tasa->fecha = $hoy;
                $historial_tasa->save();
            }else{
                $historial_tasa->tasa = $request->tasa;
                $historial_tasa->save();
            }
        }

        $sort->tasa = $request->tasa;
        $sort->save();

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se MODIFICO$texto la tasa a: $request->tasa Bs.";
        $resumen->tipo = 1;
        $resumen->save();
        /* Resumen General */

        return back()->with('tasa', 'ok');
    }

    public function lista_de_cambios($id)
    {
        return view('inicio/cambios', compact('id'));
    }

    public function obtener_cambios($id)
    { //obtener datos de cambios de cliente
        $cambios = DB::select("SELECT * FROM `resumen_general` WHERE `id_cliente` = $id ORDER BY `id` DESC");

        return response()->json($cambios);
    }

    public function congelar(Request $request)
    {
        $cliente = clientes::findOrFail($request->id);
        $servidor_cliente = servidores::findOrFail($cliente->servidor);

        $corte = new DateTime("$request->dia");
        $hoy = new DateTime("$request->hoy");

        $interval = $hoy->diff($corte);
        $cliente->congelado = 1;
        $interval_string = "+ " . strval(intval($interval->format('%R%a days')) + 1) . " dias";
        $cliente->congelar = "+ " . strval(intval($interval->format('%R%a days')) + 1) . " days";

        //eliminar prorroga
        $cliente->prorroga_hasta = null;
        $cliente->nota_prorroga = null;
        $cliente->prorroga = 0;


        $API = new api();
        if ($API->connect($servidor_cliente->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
            $API->write("/ip/firewall/address-list/add", false);
            $API->write('=address=' . $cliente->ip, false);   // IP
            $API->write('=list=BLOCK', false);       // lista
            $API->write('=comment=' . strtoupper("El cliente $cliente->nombre fue congelado (dias a favor: $interval_string)"), true);  // comentario
            $READ = $API->read(false);
        }

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se CONGELO el cliente: $cliente->nombre.";
        $resumen->tipo = 9;
        $resumen->save();

        /* Resumen General */

        $cliente->save();

        return back()->with('congelado', 'ok');
    }

    public function descongelar($id)
    {
        $cliente = clientes::findOrFail($id);

        $hoy = Carbon::now()->format('Y-m-d');

        $cliente->corte = date('Y-m-d', (strtotime("$hoy $cliente->congelar")));
        $cliente->congelado = 0;
        $cliente->congelar = "+0 days";

        $servidor_cliente = servidores::findOrFail($cliente->servidor);

        $API = new api();
        if ($API->connect($servidor_cliente->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
            $API->write("/ip/firewall/address-list/getall", false);
            $API->write('?address=' . $cliente->ip, false);
            $API->write('?list=BLOCK', true);
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);

            if (count($ARRAY) > 0) {

                $ID = $ARRAY[0]['.id'];
                $API->write('/ip/firewall/address-list/remove', false);
                $API->write('=.id=' . $ID, true);
                $READ = $API->read(false);
            }
        }

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se DESCONGELO el cliente: $cliente->nombre.";
        $resumen->tipo = 10;
        $resumen->save();

        /* Resumen General */

        $cliente->save();

        return back()->with('descongelado', 'ok');
    }

    public function ruta_reparar()
    { // ruta por si hay cosas raras

        $cuenta_aux = clientes::orderBy('id', 'desc')->first()->id;

        for ($i = 0; $i <= $cuenta_aux; $i++) {

            $cliente_uwu = clientes::find($i);

            if ($cliente_uwu && $cliente_uwu->tipo_cliente == 0) {

                //Mantener la fecha de corte igual a al dia de corte del cliente.
                /*$corte = date('d', strtotime($cliente_uwu->corte));
                
                if($corte != $cliente_uwu->mes){
                    echo "$cliente_uwu->mes y $corte son diferentes <br>";
                    $cliente_uwu->mes = $corte;
                }

                //Aqui se reinicia la columna de dias de prorroga
                $cliente_uwu->dias_prorroga = null;

                $cliente_uwu->save();*/

                $corte = pago_resumen::where('id_cliente', $cliente_uwu->id)
                    ->orderBy('pago', 'desc')
                    ->limit(1)
                    ->value('corte');
                
                if($cliente_uwu->corte != $corte && $corte != "" && $corte != "0000-00-00"){
                    echo "{$i} - {$cliente_uwu->nombre} - {$cliente_uwu->corte} | $corte<br><br>";

                    $cliente_uwu->corte = $corte;

                    //$cliente_uwu->save();
                }
            }

        }
    }
}