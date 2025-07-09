<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\resumen_general;
use Carbon\Carbon;

date_default_timezone_set('America/Caracas');

class resumen extends Controller
{
    public function index(){

        $resumen = DB::select('SELECT * FROM `resumen_general`');

        if(Auth::user()->roles > 0){
            return view('resumen_general/resumen_general', compact('resumen'));
        }else{
            return view('privilegios_insuficientes');
        }
    }

    public function data(){

        if(Auth::user()->roles > 0){
            $resumen = DB::select('SELECT * FROM `resumen_general`');
            return response()->json($resumen);
        }else{
            return view('privilegios_insuficientes');
        }
    }
}