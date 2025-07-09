<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\caja;
use App\Models\caja_log;
use App\Models\sort;

use Carbon\Carbon;

date_default_timezone_set('America/Caracas');

class caja_fuerte extends Controller
{
    public function index(){
        $actual_local = Carbon::now()->format('Y-m-d');

        if(Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 6){
            return view('evento_diario.caja_fuerte', compact('actual_local'));
        }else{
            return view('privilegios_insuficientes', compact('actual_local'));
        }
    }

    public function evento_admin_fecha($fecha){

        $datos = [];
        
        $datos[0] = DB::select("SELECT * FROM caja_fuerte WHERE DATE(`hora`) = '$fecha'");
        $datos[1] = DB::select("SELECT SUM(`bolivares`) AS BS, SUM(`dolares`) AS D, SUM(`pagomovil`) AS PM, SUM(`euros`) AS EU FROM caja_fuerte WHERE DATE(`hora`) = '$fecha'");
        $datos[2] = DB::select("SELECT COUNT(*) AS cuenta FROM caja_fuerte WHERE DATE(`hora`) = '$fecha'");
        
        return response()->json($datos);
    }

    public function evento_sr_admin($fecha){

        $datos = [];
        
        $datos[0] = DB::select("SELECT SUM(`dolares`) AS D FROM `caja_fuerte` WHERE `dolares` > 0 AND DATE(`hora`) = '$fecha' ORDER BY `hora` ASC;");
        $datos[1] = DB::select("SELECT SUM(`bolivares`) AS BS FROM `caja_fuerte` WHERE `bolivares` > 0 AND DATE(`hora`) = '$fecha' ORDER BY `hora` ASC;");
        $datos[2] = DB::select("SELECT SUM(`euros`) AS EU FROM `caja_fuerte` WHERE `euros` > 0 AND DATE(`hora`) = '$fecha' ORDER BY `hora` ASC;");
        $datos[3] = DB::select("SELECT SUM(`pagomovil`) AS PM FROM `caja_fuerte` WHERE `pagomovil` > 0 AND DATE(`hora`) = '$fecha' ORDER BY `hora` ASC;");
        
        return response()->json($datos);
    }

    public function check_evento_admin($id, $op){
        $check = caja::findOrFail($id);

        if($op == 'false'){
            $check->verificar = 0;
        }else{
            $check->verificar = 1;
        }

        $check->save();
    }

    public function agregar_evento_admin($ev, $d, $bs, $pm, $eu, $fec){// arreglar cagada :3
        $evento = new caja();
        $sort = sort::findOrFail(1)->toArray();

        $tasa = $sort["tasa"];

        $evento->usuario =  Auth::user()->name;
        $evento->evento = $ev;
        $evento->hora = $fec;
        $evento->bolivares = $bs;
        $evento->pagomovil = $pm;
        $evento->dolares = $d;
        $evento->euros = $eu;
        $evento->verificar = 0;
        $evento->total = ($d + $eu) + ($bs + $pm) / $tasa;

        $evento->save();
    }

    public function eliminar_evento_admin($id){
        $evento = caja::findOrFail($id);
        $borrar = new caja_log();

        $borrar->eliminado_por = Auth::user()->name;
        $borrar->evento = $evento->evento;
        $borrar->bolivares = $evento->bolivares;
        $borrar->pagomovil = $evento->pagomovil;
        $borrar->dolares = $evento->dolares;
        $borrar->euro = $evento->euros;
        $borrar->zelle_a = $evento->zelle_j;
        $borrar->zelle_b = $evento->zelle_v;

        $evento->delete();
        $borrar->save();
    }

    public function evento_log_admin(){
        $datos = DB::select("SELECT * FROM `caja_log` ORDER BY `fecha` asc;");
        return $datos;
    }

    public function evento_log_data_admin(){ //we
        return view('evento_diario.evento_log_admin');
    }
}