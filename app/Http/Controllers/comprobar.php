<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\pago_resumen;
use App\Models\comprobacion;

use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

use Carbon\carbon;
use Exception;

use Illuminate\Support\Facades\DB;

date_default_timezone_set('America/Caracas');

class comprobar extends Controller
{
    public function index(){
        if(Auth::user()->name == 'marco' || Auth::user()->name == 'kennerth' || Auth::user()->name == 'antonio' || Auth::user()->name == 'kathielis' || Auth::user()->name == 'igniev'){
            $hoy = carbon::now()->format('Y-m-d');
            return view('pagos.comprobar', compact('hoy'));
        }else{
            return view('privilegios_insuficientes');
        }
    }

    public function resumen_comprobar($fecha){
        $data = [];

        $data[0] = DB::select("SELECT SUM(`pagomovil`) AS provincial FROM pago_resumen WHERE `banco_receptor` = 1 AND `fecha_pago_movil` = '$fecha';");
        $data[1] = DB::select("SELECT SUM(`pagomovil`) AS banesco FROM pago_resumen WHERE `banco_receptor` = 2 AND `fecha_pago_movil` = '$fecha';");
        $data[2] = DB::select("SELECT SUM(`pagomovil`) AS venezuela FROM pago_resumen WHERE `banco_receptor` = 3 AND `fecha_pago_movil` = '$fecha';");
        $data[3] = DB::select("SELECT SUM(`pagomovil`) AS punto FROM pago_resumen WHERE `banco_receptor` = 4 AND `fecha_pago_movil` = '$fecha';");
        $data[4] = DB::select("SELECT SUM(`pagomovil`) AS punto_v FROM pago_resumen WHERE `banco_receptor` = 5 AND `fecha_pago_movil` = '$fecha';");
        $data[5] = DB::select("SELECT SUM(`pagomovil`) AS punto_c FROM pago_resumen WHERE `banco_receptor` = 6 AND `fecha_pago_movil` = '$fecha';");
        $data[6] = DB::select("SELECT SUM(`pagomovil`) AS biopago FROM pago_resumen WHERE `banco_receptor` = 7 AND `fecha_pago_movil` = '$fecha';");

        return $data;
    }

    public function reporte_pagomovil(){
        $comprobacion = DB::select("SELECT * FROM `comprobacion`");

        return view('comprobacion.comprobacion', compact('comprobacion'));
    }

    public function comprobar_datos($fecha){
        $pagos = DB::select("SELECT id, cobrador, cliente, fecha_pago_movil, pagomovil, referencia, banco, pm_comprobar, banco_receptor FROM `pago_resumen` WHERE `pagomovil` > 0 AND `fecha_pago_movil` = '$fecha' ORDER BY `id` DESC");
        
        return response()->json($pagos);
    }

    public function comprobar_datos_automatico(Request $request){

        try {
            DB::select('TRUNCATE TABLE `control_de_pagos`.`comprobacion`');

            // Obtener el archivo subido.
            $file = $request->file('archivo');

            if (!$file) {
                throw new Exception('No se ha seleccionado un archivo.');
            }
            
            // Convertir el archivo a una colección.
            $data = Excel::toCollection(null, $file);
            $no_encontrados = [];
            $interruptor = false;
            $alerta = "";
            
            // Mostrar mensaje de análisis.
            //echo "Se está analizando la fecha entre el día $request->date_0 y $request->date_1<br><br>";
            
            // Consultar resultados de pagos.
            $resultados = pago_resumen::select('id', 'cliente', 'referencia', 'pago', 'pagomovil', 'banco_receptor', 'pm_comprobar')
                ->whereBetween('pago', [$request->date_0, $request->date_1])
                ->whereNotNull('fecha_pago_movil')
                ->where('referencia', '!=', '0000000')
                ->where('banco_receptor', '!=', 4)
                ->get();
            
            // Iterar sobre los datos del archivo.
            foreach ($data as $row) {
                foreach ($row as $value) {
                    // Extraer números del valor.
                    preg_match_all('/[0-9]+/', $value, $matches);
                    $numbersAsStrings = array_map('strval', $matches[0]);

                    // Iterar sobre los resultados de pagos.
                    foreach ($resultados as $resultado) {
                        if (isset($numbersAsStrings[0])) {

                            $bd_store = new comprobacion();

                            if (strlen($numbersAsStrings[0]) >= 7) {
                                $ultimos_7_digitos = substr($numbersAsStrings[0], -7); // Extraer los últimos 7 caracteres si hay suficientes.
                            } else {
                                $ultimos_7_digitos = str_pad($numbersAsStrings[0], 7, '0', STR_PAD_LEFT); // Rellenar con ceros a la izquierda si es necesario.
                            }

                            if($value[1] == $resultado->pagomovil){
                                $bd_store->alerta = 0;
                            }elseif (abs($value[1] - $resultado->pagomovil) <= 20) {
                                $bd_store->alerta = 1;
                            }else{
                                $bd_store->alerta = 2;
                            }
                        
                            if ($ultimos_7_digitos == $resultado->referencia) {
                                //Se encontró la referencia!

                                $interruptor = true;

                                $bd_store->documento = "$ultimos_7_digitos ($value[1] Bs.)";
                                $bd_store->sistema = "$resultado->cliente de $resultado->pagomovil Bs.";

                                $bd_store->save();

                                if($resultado->pm_comprobar == 0 && $bd_store->alerta == 0 || $bd_store->alerta == 1){
                                    $check = pago_resumen::findOrFail($resultado->id);
                                    $check->pm_comprobar = 1;
                                    $check->save();
                                }
                            }

                            $alerta = "";
                        }
                    }

                    if(!$interruptor){
                        $bd_store = new comprobacion();

                        $bd_store->documento = $ultimos_7_digitos;
                        $bd_store->sistema = "Se evaluó (del documento excel) el # de referencia y no se encontró en ningún pago.";
                        $bd_store->alerta = 3;

                        $bd_store->save();
                    }

                    $interruptor = false;
                }
            }

        }catch (Exception $e) {
            echo "Ocurrió un error: " . $e->getMessage() . "<br>";
        }

        return redirect('reporte_pagomovil');
    }

    public function activar_desactivar_check($id, $op){

        $check = pago_resumen::findOrFail($id);
        $check->pm_comprobar = $op;

        if(Auth::user()->name == 'marco' || Auth::user()->name == 'kennerth' || Auth::user()->name == 'kathielis' || Auth::user()->name == 'igniev'){
            $check->save();
        } else{
            return back();
        }
    }

    public function cambiarReceptor(Request $request){
        try {
            
            $request->validate([
                'id' => 'required|integer|exists:pago_resumen,id',
                'receptor' => 'required|string|max:255'
            ]);

            $receptor = pago_resumen::findOrFail($request->id);
            $receptor->banco_receptor = $request->receptor;
            $receptor->save();

            return response()->json([
                'message' => 'Receptor actualizado correctamente',
                'data' => $receptor
            ], 200);

        }catch (Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error: ' . $e->getMessage()
            ], 500);
        }
    }
}