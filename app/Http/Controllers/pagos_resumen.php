<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\cliente;
use App\Models\pago_resumen;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

date_default_timezone_set('America/Caracas');

class pagos_resumen extends Controller
{
    public function index()
    { // Vista principal
        $usuarios = DB::select("SELECT * FROM `users`");
        $hoy = carbon::now()->format('Y-m-d');
        if(Auth::user()->roles > 0){
            return view('/pagos/pagos_resumen', compact('hoy', 'usuarios'));
        }else{
            return view('privilegios_insuficientes');
        }  
    }

    public function datos($desde, $hasta, $tipo_de_pago, $cobrador) // Devuelve los datos del resumen de pagos
    {
        $datos[] = "";
        $cuenta = 0;

        if ($tipo_de_pago == 0) { //por todo
            $pagos = DB::select("SELECT * FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta'");
            $suma = DB::select("SELECT SUM(`dolares`) AS suma_dolar, SUM(`bolivares`) AS suma_bs, SUM(`pagomovil`) AS suma_pagomovil, SUM(`euros`) AS suma_euro, SUM(`zelle_a`) AS suma_zelle_a, SUM(`zelle_b`) AS suma_zelle_b, SUM(`total`) AS suma_total FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta';");
            $cuenta = DB::select("SELECT count(*) AS pago_cantidad FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta'");
        }

        if ($tipo_de_pago == 1) { //por mensualidad
            $pagos = DB::select("SELECT * FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta' AND `tipo` = 0");
            $suma = DB::select("SELECT SUM(`dolares`) AS suma_dolar, SUM(`bolivares`) AS suma_bs, SUM(`pagomovil`) AS suma_pagomovil, SUM(`euros`) AS suma_euro, SUM(`zelle_a`) AS suma_zelle_a, SUM(`zelle_b`) AS suma_zelle_b, SUM(`total`) AS suma_total FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta' AND `tipo` = 0;");
            $cuenta = DB::select("SELECT count(*) AS pago_cantidad FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta' AND `tipo` = 0;");
        }

        if ($tipo_de_pago == 2) { //por servicio
            $pagos = DB::select("SELECT * FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta' AND `tipo` = 1");
            $suma = DB::select("SELECT SUM(`dolares`) AS suma_dolar, SUM(`bolivares`) AS suma_bs, SUM(`pagomovil`) AS suma_pagomovil, SUM(`euros`) AS suma_euro, SUM(`zelle_a`) AS suma_zelle_a, SUM(`zelle_b`) AS suma_zelle_b, SUM(`total`) AS suma_total FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta' AND `tipo` = 1;");
            $cuenta = DB::select("SELECT count(*) AS pago_cantidad FROM `pago_resumen` WHERE `pago` BETWEEN '$desde' AND '$hasta' AND `tipo` = 1;");
        }

        if ($tipo_de_pago == 3) { //por cobrador
            $pagos = DB::select("SELECT * FROM `pago_resumen` WHERE `cobrador` LIKE '$cobrador' AND `pago` BETWEEN '$desde' AND '$hasta'");
            $suma = DB::select("SELECT SUM(`dolares`) AS suma_dolar, SUM(`bolivares`) AS suma_bs, SUM(`pagomovil`) AS suma_pagomovil, SUM(`euros`) AS suma_euro, SUM(`zelle_a`) AS suma_zelle_a, SUM(`zelle_b`) AS suma_zelle_b, SUM(`total`) AS suma_total FROM `pago_resumen` WHERE `cobrador` LIKE '$cobrador' AND `pago` BETWEEN '$desde' AND '$hasta';");
            $cuenta = DB::select("SELECT count(*) AS pago_cantidad FROM `pago_resumen` WHERE `cobrador` LIKE '$cobrador' AND `pago` BETWEEN '$desde' AND '$hasta'");
        }

        $datos[0] = $suma;
        $datos[1] = $pagos;
        $datos[2] = $cuenta;

        if(Auth::user()->roles > 0){
            return response()->json($datos);
        }else{
            return view('privilegios_insuficientes');
        } 
    }
}
