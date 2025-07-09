<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\evento;
use App\Models\evento_log;
use App\Models\sort;

use Carbon\Carbon;

date_default_timezone_set('America/Caracas');

class evento_diario extends Controller
{
    
    public function index(){

        $actual_local = Carbon::now()->format('Y-m-d');

        if(Auth::user()->id != 16){ //boom solution no puede entrar al evento diario
            return view('evento_diario.evento_diario', compact('actual_local'));
        }else{
            return view('privilegios_insuficientes', compact('actual_local'));
        }
    }

    public function evento($fecha){

        $datos = [];
        
        $datos[0] = DB::select("SELECT * FROM evento_diario WHERE DATE(`hora`) = '$fecha'");
        $datos[1] = DB::select("SELECT SUM(`bolivares`) AS BS, SUM(`dolares`) AS D, SUM(`pagomovil`) AS PM, SUM(`euros`) AS EU FROM evento_diario WHERE DATE(`hora`) = '$fecha'");
        $datos[2] = DB::select("SELECT COUNT(*) AS cuenta FROM evento_diario WHERE DATE(`hora`) = '$fecha'");
        
        return response()->json($datos);
    }

    public function evento_sr($fecha){

        $datos = [];
        
        $datos[0] = DB::select("SELECT SUM(`dolares`) AS D FROM `evento_diario` WHERE `dolares` > 0 AND DATE(`hora`) = '$fecha' ORDER BY `hora` ASC;");
        $datos[1] = DB::select("SELECT SUM(`bolivares`) AS BS FROM `evento_diario` WHERE `bolivares` > 0 AND DATE(`hora`) = '$fecha' ORDER BY `hora` ASC;");
        $datos[2] = DB::select("SELECT SUM(`euros`) AS EU FROM `evento_diario` WHERE `euros` > 0 AND DATE(`hora`) = '$fecha' ORDER BY `hora` ASC;");
        $datos[3] = DB::select("SELECT SUM(`pagomovil`) AS PM FROM `evento_diario` WHERE `pagomovil` > 0 AND DATE(`hora`) = '$fecha' ORDER BY `hora` ASC;");
        
        return response()->json($datos);
    }

    public function check_evento($id, $op){
        $check = evento::findOrFail($id);

        if($op == 'false'){
            $check->verificar = 0;
        }else{
            $check->verificar = 1;
        }

        $check->save();
    }

    public function agregar_evento($ev, $d, $bs, $pm, $eu, $fec){// arreglar cagada :3
        $evento = new evento();
        $sort = sort::findOrFail(1)->toArray();

        $tasa = $sort["tasa"];

        $evento->usuario = Auth::user()->name;
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

    public function eliminar_evento($id){
        $evento = evento::findOrFail($id);
        $borrar = new evento_log();

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

    public function evento_log_data(){
        $datos = DB::select("SELECT * FROM `evento_log` ORDER BY `fecha` asc;");
        return $datos;
    }

    public function evento_log(){
        return view('evento_diario.evento_log');
    }
}