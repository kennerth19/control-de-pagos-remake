<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\api\api;

use App\Models\clientes;
use App\Models\sort;
use App\Models\planes;
use App\Models\servidores;
use App\Models\resumen_general;
use App\Models\pago_resumen;

use Carbon\carbon;

use Illuminate\Support\Facades\DB;

date_default_timezone_set('America/Caracas');

class menu extends Controller
{
    public function index($id)
    {
        $planes = DB::table('planes')->select('*')->get();
        $servidores = DB::table('servidores')->select('*')->get();

        if($id > 1){
            $facturaciones = DB::select("SELECT * FROM `pago_resumen` WHERE `servicio` = $id AND id < (SELECT id FROM `pago_resumen` WHERE servicio = $id ORDER BY id DESC LIMIT 1)");
            $facturaciones_ult = DB::select("SELECT * FROM `pago_resumen` WHERE `servicio` = $id ORDER BY id DESC LIMIT 1;");

        }else{
            $facturaciones = DB::select("SELECT * FROM `pago_resumen` WHERE `id_cliente` = $id ORDER BY `id` DESC;");
            $facturaciones_ult = DB::select("SELECT * FROM `pago_resumen` WHERE `id_cliente` = 1 ORDER BY `id` DESC LIMIT 1");
        }

        $clientes = [];

        $contador_0 = 0;
        $contador_1 = 0;
        $cuenta = 0;

        $sort = sort::findOrFail(1)->toArray();
        $cliente = clientes::findOrFail($id)->toArray();
        $plan = planes::findOrFail($cliente['plan_id'])->toArray();
        $servidor = servidores::findOrFail($cliente['servidor'])->toArray();

        $id_del_servicio = $cliente['servicio_id'];
        $servicios_vinculados = DB::select("SELECT clientes.*, planes.valor FROM `clientes` INNER JOIN planes ON clientes.plan_id = planes.id WHERE `servicio_id` = $id_del_servicio ORDER BY `principal` DESC");
        $cuenta_servicio = DB::select("SELECT COUNT(*) AS cuenta FROM `clientes` WHERE `servicio_id` = $id_del_servicio");

        foreach ($cuenta_servicio as $cuenta_servicios) {
            $cuenta = $cuenta_servicios->cuenta;
        }

        $clientes = $cliente + $plan + $servidor;

        $tasa = $sort["tasa"];

        return view('clientes/menu', compact('clientes', 'planes', 'servidores', 'facturaciones', 'facturaciones_ult', 'contador_0', 'contador_1', 'servicios_vinculados', 'cuenta', 'tasa'));
    }

    public function ip_server()
    {
        $ip = DB::select('SELECT `ip` FROM `sort`');

        return response()->json($ip);
    }

    public function modificar_cliente(Request $request)
    {
        $cliente = clientes::findOrFail($request->input('id'));

        $plan_cliente = planes::findOrFail($cliente->plan_id);
        $plan_seleccionado = planes::findOrFail($request->input('plan'));

        $servidor_cliente = servidores::findOrFail($cliente->servidor);
        $servidor_seleccionado = servidores::findOrFail($request->input('servidor'));

        $cambios = "";
        $detector_de_cambios = 0;

        $usuarioDeTurno = Auth::user()->name;
        $mensaje = "El usuario $usuarioDeTurno modifico al cliente $cliente->nombre:\n\n";

        if ($cliente->nombre != $request->input('nombre')) {
            $cambios .= "NOMBRE - $cliente->nombre a nombre de ".$request->input('nombre')." <br>";
            $cliente->nombre = $request->input('nombre');
            $detector_de_cambios += 1;
        }

        if ($cliente->direccion != $request->input('direccion')) {
            $cambios .= "DIRECCIÓN - $cliente->direccion para ".$request->input('direccion')." <br>";
            $cliente->direccion = $request->input('direccion');
            $detector_de_cambios += 1;
        }

        if ($cliente->cedula != $request->input('cedula')) {
            $cambios .= "CÉDULA - $cliente->cedula al numero ".$request->input('cedula')." <br>";
            $cliente->cedula = $request->input('cedula');
            $detector_de_cambios += 1;
        }

        if ($cliente->tlf != $request->input('telefono')) {
            $cambios .= "TELÉFONO - $cliente->tlf al numero ".$request->input('telefono')." <br>";
            $cliente->tlf = $request->input('telefono');
            $detector_de_cambios += 1;
        }

        if ($cliente->ip != $request->input('ip')) {
            $cambios .= "IP - $cliente->ip a ".$request->input('ip')." <br>";
            $cliente->ip = $request->input('ip');
            $detector_de_cambios += 1;
        }

        if ($cliente->mac != $request->input('mac')) {
            $cambios .= "MAC - $cliente->mac a ".$request->input('mac')." <br>";
            $cliente->mac = $request->input('mac');
            $detector_de_cambios += 1;
        }

        if ($cliente->plan_id != $request->input('plan')) {
            $cambios .= "PLAN - $plan_cliente->plan a $plan_seleccionado->plan <br>";
            $cliente->plan_id = $request->input('plan');
            $detector_de_cambios += 1;
        }

        if ($cliente->servidor != $request->input('servidor')) {
            $cambios .= "SERVIDOR - $servidor_cliente->nombre_de_servidor a $servidor_seleccionado->nombre_de_servidor <br>";
            $cliente->servidor = $request->input('servidor');
            $detector_de_cambios += 1;
        }

        if($cliente->corte != $request->input('corte') && Auth::user()->name == 'antonio' || Auth::user()->name == 'kennerth' || Auth::user()->name == 'marco'){
            $nuevoCorte = $request->input('corte');
            $corte = $cliente->corte;
            $mensaje .= "- DIA DE CORTE: $corte al dia $nuevoCorte\n\n";

            $cambios .= "DIA DE CORTE - $cliente->corte para el dia ".$request->input('corte')." <br>";
            $cliente->corte = $request->input('corte');
            $cliente->mes = date('d', strtotime($request->input('corte')));
            $detector_de_cambios += 1;
        }

        if($cliente->asignacion != $request->input('asignacion')){
            $cambios .= "EQUIPO ASIGNADO - $cliente->asignacion para el numero ".$request->input('asignacion')." <br>";
            $cliente->asignacion = $request->input('asignacion');
            $detector_de_cambios += 1;
        }

        if ($cliente->observacion != $request->input('observacion')) {
            $cambios .= "OBSERVACIÓN - $cliente->observacion a ".$request->input('observacion')." <br>";
            $cliente->observacion = $request->input('observacion');
            $detector_de_cambios += 1;
        }

        if ($cliente->tipo_cliente != $request->input('tipo_c')) {
            $tipo_0 = "";
            $tipo_1 = "";

            if($cliente->tipo_cliente == 0){
                $tipo_0 = "Regular";
            }else if ($cliente->tipo_cliente == 1){
                $tipo_0 = "Premium";
            }else if ($cliente->tipo_cliente == 2){
                $tipo_0 = "Donación";
            }else{
                $tipo_0 = "Nodo";
            }

            if($request->input('tipo_c') == 0){
                $tipo_1 = "Regular";
                $mensaje .= "- ESTADO: CLIENTE REGULAR";
            }else if ($request->input('tipo_c') == 1){
                $tipo_1 = "Premium";
                $mensaje .= "- ESTADO: PREMIUM";
            }else if ($request->input('tipo_c') == 2){
                $tipo_1 = "Donación";
                $mensaje .= "- ESTADO: DONACIÓN";
            }else{
                $tipo_1 = "Nodo";
                $mensaje .= "- ESTADO: NODO";
            }

            if($mensaje != ""){
                $token = env('TELEGRAM_BOT_TOKEN');
    
                $url = "https://api.telegram.org/bot$token/sendMessage";
        
                // Inicializar cURL
                $ch = curl_init();
        
                $data = [
                    'chat_id' => '5809644916',
                    'text' => $mensaje,
                ];
        
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
                // Ejecutar la petición
                $response = curl_exec($ch);
        
                // Cerrar cURL
                curl_close($ch);
            }

            $cambios .= "TIPO DE CLIENTE - $tipo_0 a ".$tipo_1." <br>";
            $cliente->tipo_cliente = $request->input('tipo_c');
            $detector_de_cambios += 1;
        }

        if ($cliente->iptv != $request->input('iptv')) {
            $cambios .= "IPTV - $cliente->iptv a ".$request->input('iptv')." <br>";
            $cliente->iptv = $request->input('iptv');
            $detector_de_cambios += 1;
        }

        if ($cliente->ticket != $request->input('ticket')) {
            $facturita_ANTES = "";
            $facturita_DESPUES = "";

            if ($request->input('ticket') == 0) {
                $facturita_ANTES = "DOLARES";
                $facturita_DESPUES = "BOLIVARES";
            } else {
                $facturita_ANTES = "BOLIVARES";
                $facturita_DESPUES = "DOLARES";
            }

            $cambios .= "EMITIR FACTURA EN - $facturita_ANTES a $facturita_DESPUES <br>";
            $cliente->ticket = $request->input('ticket');
            $detector_de_cambios += 1;
        }

        if ($detector_de_cambios > 0) {
            $cliente->cambios = $cliente->cambios += 1;
        }

        if ($cliente->active != $request->input('act_des')) {
            $activo = [];

            if ($request->input('act_des') == 0) {
                $activo[0] = "NO";
            } else {
                $activo[0] = "SI";
            }

            if ($request->input('act_des') == 0) {
                $activo[1] = "NO";
            } else {
                $activo[1] = "SI";
            }

            $cambios .= "CLIENTE ACTIVO - $activo[0] a $activo[1] <br>";
            $cliente->active = $request->input('act_des');
            $detector_de_cambios += 1;
        }

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->tipo = 1;

        /* Resumen General */

        //variable para buscar los cambios del cliente

        $resumen->id_cliente = $cliente->id;

        if ($detector_de_cambios == 1) {
            $resumen->descripcion = "Se EDITO del cliente $cliente->nombre el siguiente dato: <br><br> $cambios";
            $resumen->save();
        } elseif ($detector_de_cambios > 1) {
            $resumen->descripcion = "Se EDITO del cliente $cliente->nombre los siguientes datos: <br><br> $cambios";
            $resumen->save();
        }

        /* Sección para determinar el estado del cliente dependiendo de la fecha de corte */
        /*
            1 solvente
            2 restan 2 días
            3 resta 1 dia
            4 dia de corte
            5 requiere suspension
            6 prorroga dia 1
            7 prorroga dia 2
        */

        $hoy = Carbon::now();

        $corte_despues_del_pago = date('d-m-Y', strtotime($cliente->corte));

        $hoy = date('d-m-Y', strtotime($hoy)); // hoy
        $hoy_ma_01 = date('d-m-Y', strtotime("$hoy +1 day")); // hoy mas 1 dia
        $hoy_ma_02 = date('d-m-Y', strtotime("$hoy +2 day")); // hoy mas 2 días
        $hoy_ma_03 = date('d-m-Y', strtotime("$hoy +3 day")); // hoy mas 3 días
        $hoy_me_01 = date('d-m-Y', strtotime("$hoy -1 day")); // hoy menos 1 dia
        $hoy_me_02 = date('d-m-Y', strtotime("$hoy -2 day")); // hoy menos 2 días

        if ($corte_despues_del_pago == $hoy) { // dia de corte. funciona 
            $cliente->estado = 4;
        } elseif ($corte_despues_del_pago == $hoy_me_02) { // restan 2 días. funciona
            $cliente->estado = 7;
        } elseif ($corte_despues_del_pago == $hoy_me_01) { // restan 1 días. funciona
            $cliente->estado = 6;
        } elseif ($corte_despues_del_pago == $hoy_ma_01) { // prorroga  dia 1. funciona
            $cliente->estado = 3;
        } elseif ($corte_despues_del_pago == $hoy_ma_02) { // prorroga  dia 2. funciona
            $cliente->estado = 2;
        } elseif ($corte_despues_del_pago > $hoy_ma_03) { // requiere suspension.
            $cliente->estado = 5;
        } else { //solvente
            $cliente->estado = 1;
        }

        /* Sección para determinar el estado del cliente dependiendo de la fecha de corte */

        $cliente->save();

        $servidor_cliente = servidores::findOrFail($cliente->servidor);

        if ($cliente->active == 0) {
            $API = new api();
            if ($API->connect($servidor_cliente->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
                $API->write("/ip/firewall/address-list/add", false);
                $API->write('=address=' . $cliente->ip, false);   // IP
                $API->write('=list=BLOCK', false);       // lista
                $API->write('=comment=' . strtoupper("El cliente $cliente->nombre fue desactivado manualmente"), true);  // comentario
                $READ = $API->read(false);
            }
        } else{
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
        }

        return response()->json(['Enviado' => 'ok']);
    }

    public function eliminar_cliente($id)
    {
        $cliente = clientes::findOrFail($id);
        
        $clientes = DB::select("SELECT `id`, `mac`, `estado` FROM `clientes` WHERE `mac` = '$cliente->mac' AND `id` != $id;");
        $cuenta_clientes = count($clientes);
        $cuenta_estados = 0;
        $corte = false;

        if($cuenta_clientes > 1){
            echo "Hay $cuenta_clientes clientes con la misma mac ($cliente->mac)<br>";

            foreach($clientes as $busqueda){
                if($busqueda->estado != 5){
                    echo "Sumando 1 a la cuenta con el estado $busqueda->estado<br>";
                    $cuenta_estados ++;
                }
            }
            
            echo "Variable contadora de estados solventes: $cuenta_estados<br>";

            if($cuenta_estados > 0){
                $corte = true;
            }

        }else{
            echo "Solo hay un cliente con la mac ($cliente->mac)<br>";
        }

        if(!$corte){
            echo "Regla generada en el servidor!<br>";

            $servidor_cliente = servidores::findOrFail($cliente->servidor);

            $API = new api();

            if ($API->connect($servidor_cliente->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
                $API->write("/ip/firewall/address-list/add", false);
                $API->write('=address=' . $cliente->ip, false);
                $API->write('=list=BLOCK', false);
                $API->write('=comment=' . strtoupper("El cliente $cliente->nombre fue eliminado manualmente desde el MENU"), true);
                $READ = $API->read(false);
            }

            $API->disconnect();
        }

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se ELIMINO el cliente $cliente->nombre.";
        $resumen->tipo = 2;
        $resumen->save();
        /* Resumen General */

        $cliente->delete();

        return redirect('inicio');
    }

    public function nuevo_ser(Request $request)
    {
        $servicio = new clientes();

        $servicio->nombre = $request->nombre;
        $servicio->tlf = $request->telefono;
        $servicio->cedula = $request->cedula;
        $servicio->servidor = $request->servidor;
        $servicio->direccion = $request->direccion;
        $servicio->plan_id = $request->plan;
        $servicio->ip = $request->ip;
        $servicio->mac = $request->mac;
        $servicio->observacion = $request->observacion;
        $servicio->corte = Carbon::now();
        $servicio->dia = Carbon::now();
        $servicio->dia_i = Carbon::now();
        $servicio->mes = Carbon::now()->format('d');

        $servicio->estado = 1;
        $servicio->active = 1;
        $servicio->servicio_id = $request->servicio_id;
        $servicio->principal = 0;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se AGREGO manualmente el servicio $servicio->nombre.";
        $resumen->tipo = 3;

        /* Resumen General */

        $resumen->save();
        $servicio->save();

        return back()->with('servicio', 'ok');
    }

    public function retornar_servicios($ser_id) // retorna los servicios (no el principal)
    {
        $servicios = DB::select("SELECT `id`, `nombre`, `principal` FROM `clientes` WHERE `servicio_id` = $ser_id");
        $data = "";

        foreach ($servicios as $servicio) {

            if ($servicio->principal == 0) {
                $data .= "<option value='$servicio->id'>$servicio->nombre</option>";
            }
        }

        return $data;
    }

    public function servicios_todos($id) // retorna los servicios (no el principal)
    {
        $servicios = DB::select("SELECT `id`, `nombre`, `direccion`, `tlf`, `cedula`, `mac`, `ip` FROM `clientes` WHERE `servicio_id` = $id AND `principal` = 0");
        $data = "";

        foreach ($servicios as $servicio) {

            $data .= "$servicio->id";
            $data .= " ";
            $data .= "$servicio->nombre";
            $data .= " ";
            $data .= "$servicio->direccion";
            $data .= " ";
            $data .= "$servicio->tlf";
            $data .= " ";
            $data .= "$servicio->cedula";
            $data .= " ";
            $data .= "$servicio->mac";
            $data .= " ";
            $data .= "$servicio->ip";
            $data .= " ";
        }

        return $data;
    }

    public function borrar_y_asignar(Request $request)
    {

        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;

        $borrar = clientes::findOrFail($request->identificacion);
        $servicios = DB::select("SELECT nombre FROM `clientes` WHERE `servicio_id` = $request->ser_id");

        $texto = "";

        foreach ($servicios as $servicio) {
            $texto .= "- $servicio->nombre. <br>";
        }

        if ($request->servicio == 'todo') {
            $servicios = DB::select("DELETE FROM `clientes` WHERE `servicio_id` = $request->ser_id");
            $resumen->descripcion = "Se ELIMINO manualmente el cliente: $borrar->nombre y sus servicios:<br><br> $texto";
            $resumen->tipo = 5;
            $resumen->save();

            return redirect("/")->with('borrados', 'ok');
        } else {
            $cambiar = clientes::findOrFail($request->servicio);
            $cambiar->principal = 1;

            $resumen->descripcion = "Se ELIMINO <br>manualmente el servicio: $borrar->nombre y se asigno como principal: $cambiar->nombre.";
            $resumen->tipo = 6;

            $borrar->delete();
            $cambiar->save();
            $resumen->save();

            return redirect("/clientes/menu/$cambiar->id")->with('asignado', 'ok');
        }
    }

    public function clientes_ss($cedula, $nombre)
    {

        if ($cedula == 'vacio' && $nombre != 'vacio') {
            $clientes = DB::select("SELECT id, nombre, cedula, servicio_id FROM `clientes` WHERE `nombre` LIKE '%$nombre%' AND `principal` = 1  ORDER BY `id` ASC");
        } else if ($cedula != 'vacio' && $nombre == 'vacio') {
            $clientes = DB::select("SELECT id, nombre, cedula, servicio_id FROM `clientes` WHERE `cedula` = $cedula AND `principal` = 1 ORDER BY `id` ASC");
        } else {
            $clientes = DB::select("SELECT id, nombre, cedula, servicio_id FROM `clientes` WHERE (`nombre` LIKE '%$nombre%' OR `cedula` = $cedula) AND `principal` = 1 ORDER BY `id` ASC");
        }

        return response()->json($clientes);
    }

    public function clientes_sss($cedula, $nombre)
    {

        if ($cedula == 'vacio' && $nombre != 'vacio') {
            $clientes = DB::select("SELECT clientes.id, nombre, cedula, servicio_id, planes.valor  FROM `clientes` INNER JOIN planes ON clientes.plan_id = planes.id WHERE `nombre` LIKE '%$nombre%'ORDER BY `id` ASC");
        } else if ($cedula != 'vacio' && $nombre == 'vacio') {
            $clientes = DB::select("SELECT clientes.id, nombre, cedula, servicio_id, planes.valor  FROM `clientes` INNER JOIN planes ON clientes.plan_id = planes.id WHERE `cedula` = $cedula ORDER BY `id` ASC");
        } else {
            $clientes = DB::select("SELECT clientes.id, nombre, cedula, servicio_id, planes.valor  FROM `clientes` INNER JOIN planes ON clientes.plan_id = planes.id WHERE (`nombre` LIKE '%$nombre%' OR `cedula` = $cedula) ORDER BY `id` ASC");
        }

        return response()->json($clientes);
    }

    public function union($de, $para)
    {

        $de = clientes::findOrFail($de);
        $para = clientes::findOrFail($para);

        $de->servicio_id = $para->servicio_id;
        $de->principal = 0;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se UNIÓ manualmente el servicio $de->nombre al cliente principal $para->nombre.";
        $resumen->tipo = 7;

        /* Resumen General */

        $resumen->save();
        $de->save();

        return back()->with('union', 'ok');
    }

    public function conducta(Request $request){

        $cliente = clientes::findOrFail($request->id);

        $cliente->conducta = $request->conducta;
        $cliente->conducta_nota = $request->motivo;

        $cliente->save();

        return back()->with('conducta', 'ok');
    }

    public function separar($id){

        $ult = clientes::orderBy('id', 'desc')->first()->id;

        $servicio = clientes::findOrFail($id);

        $servicio->principal = 1;
        $servicio->servicio_id = $ult + 1;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se SEPARO el servicio $servicio->nombre del cliente principal.";
        $resumen->tipo = 23;

        $resumen->save();
        /* Resumen General */

        $servicio->save();

        return back()->with('separado', 'ok');
    }

    public function reiniciar_prorroga($id){

        $cliente = clientes::findOrFail($id);

        $cliente->ult_prorroga = null;
        $cliente->prorroga = 0;
        $cliente->prorroga_hasta = null;
        $cliente->nota_prorroga = null;

        if(Auth::user()->name == "antonio" || Auth::user()->name == "kennerth" || Auth::user()->name == "marco" || Auth::user()->name == "jean"){
            $cliente->save();
        }
    }
}