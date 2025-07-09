<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\api\api;

use App\Models\pre_registro_model;
use App\Models\evento;
use App\Models\resumen_general;
use App\Models\clientes;
use App\Models\servidores;
use App\Models\planes;
use App\Models\pago_resumen;
use App\Models\sort;
use App\Models\instalaciones;
use App\Models\caja;
use App\Models\inventarios;
use App\Models\existencias;
use App\Models\inventario_log;

use App\Fpdf\FPDF;
use App\Fpdf\KodePDF;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

date_default_timezone_set('America/Caracas');

class pre_registro extends Controller
{
    public function index()
    {
        $sort = sort::findOrFail(1)->toArray();
        $tasa = $sort["tasa"];
        $hoy = Carbon::now();

        $fecha = date('Y-m-d', strtotime($hoy));

        return view('pre_registro.pre_registro', compact('tasa', 'fecha'));
    }

    public function get_pre($id){
        $datos = DB::table('pre-registro')->select('*')->where('id', $id)->get();

        return $datos[0];
    }

    public function get_plan($id){
        $datos = DB::table('planes')->select('plan')->where('id', $id)->get();

        return $datos[0];
    }

    public function get_serial($id){

        $datos = [];

        if(Auth::user()->name != 'merulo'){
            $datos[0] = DB::select("SELECT `asignacion` FROM `pre-registro` WHERE `id` = $id;");

            $datos[1] = DB::select("SELECT `existencias`.`id`, `existencias`.`serial`, `existencias`.`categoria_id`, `inventarios`.`producto` FROM `existencias` INNER JOIN `inventarios` ON `existencias`.`categoria_id` = `inventarios`.`id` WHERE `serial` != 'N/A' AND `asignado` = 0 ORDER BY `existencias`.`id` DESC;");
        }else{
            $datos[1] = "";
        }

        return response()->json($datos);
    }

    public function datos()
    {
        $datos = DB::select("SELECT * FROM `pre-registro` ORDER BY `pre-registro`.`fecha_de_pago` ASC");
        return response()->json($datos);
    }

    public function datos_a_registrar($id)
    {
        $datos = DB::table('pre-registro')->select('*')->where('id', $id)->get();
        return response()->json($datos);
    }

    public function agregar_pre_registro(Request $request)
    {
        //datos de facturación para el evento diario y la pagina de pagos

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
        $cuenta = pago_resumen::count();
        $sort = sort::findOrFail(1)->toArray();
        $tasa = $sort["tasa"];
        $texto = "(No agregado al Pre-Registro) ";
        $usuario_online = Auth::user()->name;

        if($request->instalado == 1){
            $texto = "";
        }

        $cliente_nuevo = new pago_resumen();

        /* pago resumen */
        $cliente_nuevo->usuario = $usuario_online;
        $cliente_nuevo->cobrador = $usuario_online;
        $cliente_nuevo->codigo =  "NEW_" . $cuenta;

        //datos del cliente
        $cliente_nuevo->cliente = $request->nombre;
        $cliente_nuevo->cedula = $request->cedula;
        $cliente_nuevo->telefono = $request->telefono;
        $cliente_nuevo->direccion = $request->direccion;

        if($request->observacion == ""){
            $cliente_nuevo->concepto = "Sin observación";
        }else{
            $cliente_nuevo->concepto = $request->observacion;
        }

        //facturación
        $cliente_nuevo->tasa = $request->tasa;
        $cliente_nuevo->dolares = number_format($request->dolar, 2, '.', '');
        $cliente_nuevo->bolivares = number_format($request->bolivar, 2, '.', '');
        $cliente_nuevo->euros = number_format($request->euro, 2, '.', '');
        $cliente_nuevo->zelle_a = number_format($request->zelle_v, 2, '.', '');
        $cliente_nuevo->zelle_b = number_format($request->zelle_j, 2, '.', '');
        $cliente_nuevo->pagomovil = number_format($request->pagomovil, 2, '.', '');
        $cliente_nuevo->fecha_pago_movil = $request->fecha_pago_movil;
        $cliente_nuevo->banco_receptor = $request->banco_receptor;

        $cliente_nuevo->referencia = $request->referencia;
        $cliente_nuevo->banco = $request->banco;
        $cliente_nuevo->pago = $request->fecha_preg;

        $total_bs = ($cliente_nuevo->bolivares + $cliente_nuevo->pagomovil) / $cliente_nuevo->tasa;

        $cliente_nuevo->total = $cliente_nuevo->dolares + $cliente_nuevo->euros + $cliente_nuevo->zelle_a + $cliente_nuevo->zelle_b + $total_bs;
        $pagado_aux = $cliente_nuevo->total;

        //otras columnas
        $cliente_nuevo->corte = "N/A";
        $cliente_nuevo->id_cliente = "N/A";
        $cliente_nuevo->plan = $request->plan;
        $cliente_nuevo->active = 1;
        $cliente_nuevo->servicio = 1;
        $cliente_nuevo->tipo = 1;

        /* pago resumen */

        $total_d = $request->dolar + $request->euro + $request->zelle_v + $request->zelle_j;
        $total_bs = ($request->bolivar + $request->pagomovil) / $tasa;

        $evento->evento = "<b><b class='tipo'>TIPO DE PAGO: INSTALACIÓN$texto</b><br>CLIENTE: " . $request->nombre . "<br>" . $request->observacion . "</b>";
        $evento->hora = $request->fecha_preg;
        $evento->bolivares = number_format($request->bolivar, 2, '.', '');
        $evento->pagomovil = number_format($request->pagomovil, 2, '.', '');
        $evento->ref = $request->referencia;
        $evento->dolares = number_format($request->dolar, 2, '.', '');
        $evento->euros = number_format($request->euro, 2, '.', '');
        $evento->zelle_j = number_format($request->zelle_j, 2, '.', '');
        $evento->zelle_v = number_format($request->zelle_v, 2, '.', '');
        $evento->total = $total_d + $total_bs;
        $evento->verificar = 0;

        //grupo oficina o grupo caja fuerte
        if (Auth::user()->grupo == 1 || Auth::user()->grupo == 2) {
            $evento->save();
        }

        if ($request->instalado == 1) {

            $pre_registro = new pre_registro_model();
            $equipo = instalaciones::findOrFail($request->router);
            $aux_asignado = 0;

            /* Area para asignar el router del inventario (solo si hay disponibles) */

            $resultado = existencias::where('categoria_id', $equipo->inventario_categoria)
            ->where('asignado', 0)
            ->first();
                
            if ($resultado) {
                $inventario = existencias::findOrFail($resultado->id);

                $pre_registro->asignacion = $inventario->serial;

                $inventario->asignado = 1;
                $inventario->observacion = $request->nombre;

                $aux_asignado = 1;

                $inventario->save();
            } else {
                $pre_registro->asignacion = "Sin equipo asignado.";
            }

            /* Area para asignar el router del inventario (solo si hay disponibles) */

            //datos del cliente
            $total_cancelado = number_format($evento->total, 2, '.', '');

            $pre_registro->cliente_id = $request->cliente_id;
            $pre_registro->tipo_de_servicio = $request->tipo_de_instalacion;
            $pre_registro->nombre = $request->nombre;
            $pre_registro->telefono = $request->telefono;
            $pre_registro->cedula = $request->cedula;
            $pre_registro->direccion = $request->direccion;
            $pre_registro->plan = $request->plan;
            $pre_registro->observacion = "$request->observacion";
            $pre_registro->estado = 0;

            $pre_registro->cobrador = $usuario_online;
            $pre_registro->total = $total_cancelado;
            $pre_registro->valor = $equipo->valor;
            $pre_registro->instalacion = $equipo->router;

            if($pagado_aux +1 >= $equipo->valor){
                $pre_registro->pagado = 1;
            }else{
                $pre_registro->pagado = 0;
            }

            /* Resumen General */
            $resumen = new resumen_general();

            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "Se AGREGO el cliente $request->nombre al PRE-REGISTRO";
            $resumen->tipo = 3;

            /* Resumen General */

            $pre_registro->save();
            $resumen->save();
            $cliente_nuevo->save();

            /* Area log inventario */
            $inv_log = new inventario_log();

            $inv_log->usuario = Auth::user()->name;

            if($aux_asignado == 1){
                $inv_log->evento = "Se ha asignado un equipo (SERIAL: $inventario->serial) al cliente $request->nombre.";
                $inv_log->tipo = 5;
            }else{
                $inv_log->evento = "No se le pudo asignar un equipo al cliente $request->nombre (Sin equipos disponibles).";
                $inv_log->tipo = 9;
            }

            $inv_log->save();
            /* Area log inventario */

            return back()->with('agregado', 'ok');
        } else {
            /* Resumen General */
            $resumen = new resumen_general();

            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "Se Realizo el pago de la instalación del cliente $request->nombre $texto";
            $resumen->tipo = 19;

            /* Resumen General */

            $resumen->save();
            $cliente_nuevo->save();
            return back()->with('agregado', 'no');
        }
    }

    public function borrar($id, $serial)
    {
        $pre_registro = pre_registro_model::findOrFail($id);
        $exists = DB::table('existencias')->where('serial', $serial)->exists();
        $evento = "";
        $tipo = 0;

        if ($exists) {
            DB::select("UPDATE `existencias` SET `asignado` = '0', `observacion` = null WHERE `serial` = '$serial';");
            $evento = "Se elimino el cliente $pre_registro->nombre y se elimino la asignación del router $serial";  
            $tipo = 6;         
        }

        /* Area log */
        $inv_log = new inventario_log();

        $inv_log->usuario = Auth::user()->name;
        $inv_log->evento = $evento;
        $inv_log->tipo = $tipo;

        $inv_log->save();
        /* Area log */

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se ELIMINO el cliente $pre_registro->nombre del PRE-REGISTRO";
        $resumen->tipo = 2;

        /* Resumen General */

        $resumen->save();

        $pre_registro->delete();
    }

    public function editar_pre_reg(Request $request)
    {
        $pre_registro = pre_registro_model::findOrFail($request->id);
        $plan_cliente = planes::findOrFail($pre_registro->plan);
        $plan_seleccionado = planes::findOrFail($request->plan);

        $cambios = "";
        $detector_de_cambios = 0;

        if ($pre_registro->nombre != $request->nombre) {
            $cambios .= "NOMBRE - $pre_registro->nombre a nombre de $request->nombre <br>";
            $pre_registro->nombre = $request->nombre;
            $detector_de_cambios += 1;
        }

        if ($pre_registro->cedula != $request->cedula) {
            $cambios .= "CÉDULA - $pre_registro->cedula al numero $request->cedula <br>";
            $pre_registro->cedula = $request->cedula;
            $detector_de_cambios += 1;
        }

        if ($pre_registro->plan != $request->plan) {
            $cambios .= "PLAN - $plan_cliente->plan a $plan_seleccionado->plan <br>";
            $pre_registro->plan = $request->plan;
            $detector_de_cambios += 1;
        }

        if ($pre_registro->telefono != $request->telefono) {
            $cambios .= "TELÉFONO - $pre_registro->telefono al numero $request->telefono <br>";
            $pre_registro->telefono = $request->telefono;
            $detector_de_cambios += 1;
        }

        if ($pre_registro->direccion != $request->direccion) {
            $cambios .= "DIRECCIÓN - $pre_registro->direccion para $request->direccion <br>";
            $pre_registro->direccion = $request->direccion;
            $detector_de_cambios += 1;
        }

        if ($pre_registro->observacion != $request->observacion) {
            $cambios .= "OBSERVACION - $pre_registro->observacion para $request->observacion <br>";
            $pre_registro->observacion = $request->observacion;
            $detector_de_cambios += 1;
        }

        $tipo_0 = "";
        $tipo_1 = "";

        if ($request->tipo_de_servicio == 0) {
            $tipo_0 = "ANTENA";
            $tipo_1 = "FIBRA";
        } else {
            $tipo_0 = "FIBRA";
            $tipo_1 = "ANTENA";
        }

        if ($pre_registro->tipo_de_servicio != $request->tipo_de_servicio) {
            $cambios .= "TIPO DE SERVICIO - $tipo_1 a $tipo_0 <br>";
            $pre_registro->tipo_de_servicio = $request->tipo_de_servicio;
            $detector_de_cambios += 1;
        }

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;

        if ($detector_de_cambios == 1) {
            $resumen->descripcion = "Se EDITO del cliente $pre_registro->nombre (PRE-REGISTRO) el siguiente dato: <br><br> $cambios";
        } else if ($detector_de_cambios > 1) {
            $resumen->descripcion = "Se EDITO del cliente $pre_registro->nombre (PRE-REGISTRO) los siguientes datos: <br><br> $cambios";
        }

        $resumen->tipo = 1;

        /* Resumen General */

        if(Auth::user()->name == "kennerth" || Auth::user()->name == "antonio" || Auth::user()->name == "marco" || Auth::user()->name == "kathielis"){
            $pre_registro->save();
            $resumen->save();
        }
        
        return back()->with('pre_reg_editado', 'ok');
    }

    public function registrar_pre_reg(Request $request)
    {
        $pre_registro = pre_registro_model::findOrFail($request->id);
        $ult = clientes::orderBy('id', 'desc')->first()->id;

        if($pre_registro->cliente_id > 0){

            $cliente = clientes::findOrFail($pre_registro->cliente_id);
            $servidores = DB::select("SELECT * FROM `servidores` WHERE `active` = 1");

            $API = new api();

            foreach ($servidores as $servidor) { // ciclo de servidores.

                if ($servidor->id == $cliente->servidor) {

                    if ($API->connect($servidor->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
                        $API->write("/ip/firewall/address-list/add", false);
                        $API->write('=address=' . $cliente->ip, false);
                        $API->write('=list=BLOCK', false);
                        $API->write('=comment=' . strtoupper("Al cliente $cliente->nombre se le realizo un cambio del equipo (MIGRACION), esta IP se desactivo AUTOMATICAMENTE"), true);
                        $READ = $API->read(false);
                    }

                    $API->disconnect();
                }
            }

        }else{
            $cliente = new clientes();
            $cliente->servicio_id = $ult + 1;
        }
        
        $cliente->nombre = $request->nombre;
        $cliente->cedula = $request->cedula;
        $cliente->direccion = $request->direccion;
        $cliente->estado = 1;
        $cliente->plan_id = $request->plan;
        $cliente->tlf = $request->telefono;
        
        if($request->observacion == ""){
            $cliente->observacion = "Sin observación";
        }else{
            $cliente->observacion = $request->observacion;
        }
        
        $cliente->servidor = $request->servidor;
        $cliente->ip = $request->ip;
        $cliente->mac = $request->mac;
        $cliente->dia = Carbon::now();
        $cliente->mes = Carbon::now()->format('d');

        $nueva_fecha = Carbon::parse($request->fecha_i); // variable auxiliar de la librería carbon para agregar un mes

        $cliente->corte = $nueva_fecha->addMonth();
        $cliente->dia_i = $request->fecha_i;
        $cliente->active = 1;
        $cliente->almacen = 0;
        $cliente->deuda = $request->motivo;
        $cliente->motivo_deuda = $request->deuda;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se AGREGO el cliente $request->nombre al sistema desde el PRE-REGISTRO";
        $resumen->tipo = 3;

        /* Resumen General */

        $resumen->save();
        $cliente->save();
        $pre_registro->delete();

        return back()->with('registrado', 'ok');
    }

    public function cambiar_estado(Request $request, $id){
        $cliente = pre_registro_model::findOrFail($id);

        if($cliente->estado == 0){
            $cliente->estado = 1;
            $marca = "Instalado sin registrar";
        }else if($cliente->estado == 1){
            $cliente->comentario = $request->comentario;
            $cliente->estado = 2;
            $marca = "Error al instalar ($request->comentario)";
        }else{
            $cliente->comentario = null;
            $cliente->estado = 0;
            $marca = "En espera";
        }

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se MARCO el cliente $cliente->nombre a '$marca' del sistema desde el PRE-REGISTRO.";
        $resumen->tipo = 24;

        /* Resumen General */

        $resumen->save();
        $cliente->save();

        return back()->with('comentario', 'ok');
    }

    public function pre_registro_abono(Request $request){

        $pre_registro = pre_registro_model::findOrFail($request->id);

        $total_d = ($request->dolar + $request->euro + $request->zelle_v + $request->zelle_j) + ($request->bolivar + $request->pagomovil + 1) / $request->tasa;

        $factor = pow(10, 2);
        $descuento_final = intval($total_d * $factor) / $factor;

        $pre_registro->total = $pre_registro->total + $descuento_final;

        $resultado = "ABONO a";

        if($pre_registro->total >= $pre_registro->valor){
            $pre_registro->pagado = 1;
            $resultado = "CANCELO LA TOTALIDAD de";
        }

        if(Auth::user()->grupo == 1){
            $evento = new evento();
            $evento->usuario = Auth::user()->name;
        }else if(Auth::user()->grupo == 2){
            $evento = new caja();
            $evento->usuario = Auth::user()->name;
        }else{
            $evento = new evento();
        }

        $usuario_online = Auth::user()->name;

        $abono = new pago_resumen();
        $cuenta = pago_resumen::count();

        /* pago resumen */
        $abono->usuario = $usuario_online;
        $abono->cobrador = $usuario_online;
        $abono->codigo =  "NEW_" . $cuenta;

        //datos del cliente
        $abono->cliente = $pre_registro->nombre;
        $abono->cedula = $pre_registro->cedula;
        $abono->telefono = $pre_registro->telefono;
        $abono->direccion = $pre_registro->direccion;
        $abono->concepto = "Se $resultado la deuda de la instalación de $pre_registro->nombre.";

        //facturación
        $abono->tasa = $request->tasa;
        $abono->dolares = number_format($request->dolar, 2, '.', '');
        $abono->bolivares = number_format($request->bolivar, 2, '.', '');
        $abono->euros = number_format($request->euro, 2, '.', '');
        $abono->zelle_a = number_format($request->zelle_v, 2, '.', '');
        $abono->zelle_b = number_format($request->zelle_j, 2, '.', '');
        $abono->pagomovil = number_format($request->pagomovil, 2, '.', '');
        $abono->fecha_pago_movil = $request->fecha_pago_movil;
        $abono->banco_receptor = $request->banco_receptor;

        $abono->referencia = $request->referencia;
        $abono->banco = $request->banco;
        $abono->pago = $request->fecha_pago_movil;

        $total_bs = ($abono->bolivares + $abono->pagomovil) / $request->tasa;

        $abono->total = $abono->dolares + $abono->euros + $abono->zelle_a + $abono->zelle_b + $total_bs;

        //otras columnas
        $abono->corte = "N/A";
        $abono->id_cliente = "N/A";
        $abono->plan = $pre_registro->plan;
        $abono->active = 1;
        $abono->servicio = 1;
        $abono->tipo = 1;

        /* pago resumen */

        /* Evento diario (o caja fuerte) */
        
        $evento->evento = "<b><b class='tipo'>TIPO DE PAGO: ABONO DE INSTALACION</b><br>CLIENTE: $pre_registro->nombre <br> Observacion: $pre_registro->observacion </b>";
        $evento->hora = $request->fecha_pago_movil;
        $evento->bolivares = number_format($request->bolivar, 2, '.', '');
        $evento->pagomovil = number_format($request->pagomovil, 2, '.', '');
        $evento->ref = $request->referencia;
        $evento->dolares = number_format($request->dolar, 2, '.', '');
        $evento->euros = number_format($request->euro, 2, '.', '');
        $evento->zelle_j = number_format($request->zelle_j, 2, '.', '');
        $evento->zelle_v = number_format($request->zelle_v, 2, '.', '');
        $evento->total = $total_d + $total_bs;
        $evento->verificar = 0;

        //grupo oficina o grupo caja fuerte
        if (Auth::user()->grupo == 1 || Auth::user()->grupo == 2) {
            $evento->save();
        }

        /* Evento diario (o caja fuerte) */

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se $resultado la deuda de la instalación de $pre_registro->nombre.";
        $resumen->tipo = 25;

        /* Resumen General */

        $abono->save();
        $resumen->save();
        $pre_registro->save();

        return back()->with('abono', 'ok');
    }

    public function asignacion(Request $request){
        $cliente = pre_registro_model::findOrFail($request->id);

        $inv_log = new inventario_log();

        $inv_log->usuario = Auth::user()->name;

        $inventario = DB::select("SELECT `id` FROM `pre-registro` WHERE `asignacion` = '$cliente->asignacion' ORDER BY `asignacion` DESC;");

        if($request->serial == 0){ // Condición para desasignar.
            
            $inv_log->evento = "Se ha desasignado un equipo (SERIAL: $cliente->asignacion) del cliente $cliente->nombre.";
            $inv_log->tipo = 6;

            DB::select("UPDATE `existencias` SET `observacion` = null, `asignado` = 0 WHERE `existencias`.`serial` = '$cliente->asignacion';");
            $cliente->asignacion = null;

        } else { // Condición para asignar un equipo.
            
            DB::select("UPDATE `existencias` SET `observacion` = '$cliente->nombre', `asignado` = 1 WHERE `existencias`.`serial` = '$request->serial';");
            $cliente->asignacion = $request->serial;

            $inv_log->evento = "Se le asigno un nuevo equipo ($request->serial) al cliente $cliente->nombre";
            $inv_log->tipo = 5;
        }

        $cliente->save();
        $inv_log->save();

        return response()->json(['enviado' => 'ok']);
    }
}