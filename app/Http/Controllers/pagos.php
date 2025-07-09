<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\clientes;
use App\Models\pago_resumen;
use App\Models\resumen_general;
use App\Models\historial_tasa;
use App\Models\servidores;
use App\Models\planes;
use App\Models\evento;
use App\Models\caja;
use App\Models\sort;

use App\api\api;

use App\fpdf\FPDF;
use App\fpdf\PDF_HTML;

use Illuminate\Support\Facades\DB;

use DateTime;
use Carbon\Carbon;

date_default_timezone_set('America/Caracas');

class pagos extends Controller
{
    public function pagar(Request $request, $id) // Función para realizar los pagos de mensualidad.
    {
        if ($id != 'm') { // apartado para pagos de mensualidades convencionales.
            $cliente = clientes::findOrFail($id);
            $cuenta = pago_resumen::count();
            $plan = planes::findOrFail($cliente->plan_id);
            $servidor = servidores::findOrFail($cliente->servidor);

            $fecha = $request->fecha_pago_movil;

            $hoy = date('Y-m-d', strtotime(Carbon::now()));

            $historial_tasa = DB::table('historial_tasa')->select(DB::raw('count(*) as cuenta'))->where('fecha', '=', "$fecha")->get();

            foreach ($historial_tasa as $historial) {
                $la_cuenta = $historial->cuenta;
            }

            if ($la_cuenta > 0) {
                $historial_tasa = DB::table('historial_tasa')->select(DB::raw('tasa'))->where('fecha', '=', "$fecha")->get();
            } else {
                $historial_tasa = DB::table('historial_tasa')->select(DB::raw('tasa'))->where('fecha', '=', "$hoy")->get();
            }

            foreach ($historial_tasa as $historial) {
                $tasa = $historial->tasa;
            }

            $hoy = Carbon::now();
            $hoy_dia = Carbon::now()->format('d');
            $mes = $hoy->format('m');

            $pago = new pago_resumen();

            if ($cliente->deuda > 0) {
                $cliente->deuda_p = 1;
            }

            $total_d = $request->dolar + $request->euro + $request->zelle_v + $request->zelle_j;
            $total_bs = ($request->bolivar + $request->pagomovil + 1) / $tasa;

            $user_cobrador = Auth::user()->name;
            $pago->usuario = Auth::user()->name;
            $pago->cobrador = Auth::user()->name;
            $pago->servicio = $cliente->id;
            $pago->codigo =  "PW_" . $cuenta;
            $pago->cliente = $cliente->nombre;
            $pago->cedula = $cliente->cedula;
            $pago->direccion = $cliente->direccion;
            $pago->pago = $hoy; //Ya no se podrán pasar pagos que no sean el dia de hoy.

            $pago->plan = $cliente->plan_id;
            $pago->bolivares = $request->bolivar;
            $pago->pagomovil = $request->pagomovil;
            $pago->referencia = $request->referencia;
            $pago->fecha_pago_movil = $request->fecha_pago_movil;
            $pago->banco = $request->banco;
            $pago->dolares = $request->dolar;
            $pago->euros = $request->euro;
            $pago->zelle_a = $request->zelle_v;
            $pago->zelle_b = $request->zelle_j;

            $pago->tasa = $tasa;

            $pago->banco_receptor = $request->banco_receptor;
            $pago->total = $total_d + $total_bs;
            $pago->active = 1;
            $pago->telefono = $cliente->tlf;
            $pago->id_cliente = $cliente->id;
            $pago->tipo = 0;
            $pago->enlace = $cuenta;

            $cliente->almacen = $cliente->almacen + $pago->total;

            /* Ver días de diferencia */

            $reactivacion = "";

            $diferencia_corte = date("Y-m-d", strtotime("$cliente->corte, + 6 days"));

            if ($cliente->prorroga == 0) {
                if ($diferencia_corte < $hoy && $cliente->almacen >= $plan->valor) {
                    $cliente->corte = $hoy;
                    $cliente->mes = $hoy_dia;
                    $reactivacion = "(REACTIVACIÓN)";
                }
            }

            /* Ver días de diferencia */

            /* Aquí se le suben los meses dependiendo de cuanto haya pagado */

            // Formula: total cancelado / precio del plan = numero de meses por subir (sin el decimal)

            $meses_arriba = floor($cliente->almacen / $plan->valor);

            $columna = $cliente->mes;
            $fecha = $cliente->corte;

            if ($columna < 29) {
                $fecha_de_corte = date('Y-m-d', strtotime("$fecha + $meses_arriba month"));
                $cliente->corte = $fecha_de_corte;
            } else {
                $dia_actual_corte = date('d', strtotime($fecha));
                $mes_actual_corte = date('m', strtotime($fecha));
                $ano_actual_corte = date('Y', strtotime($fecha));
                $fecha_de_corte = date('Y-m-d', strtotime($fecha));

                $mes_plus = $mes_actual_corte + $meses_arriba;

                while ($mes_plus > 12) {
                    $mes_plus = $mes_plus - 12;
                    $ano_actual_corte += 1;
                }

                $dia = "";

                if ($mes_plus == 1) {
                    $mes_resultante = "january";
                } else if ($mes_plus == 2) {
                    $mes_resultante = "february";
                } else if ($mes_plus == 3) {
                    $mes_resultante = "march";
                } else if ($mes_plus == 4) {
                    $mes_resultante = "april";
                } else if ($mes_plus == 5) {
                    $mes_resultante = "may";
                } else if ($mes_plus == 6) {
                    $mes_resultante = "june";
                } else if ($mes_plus == 7) {
                    $mes_resultante = "july";
                } else if ($mes_plus == 8) {
                    $mes_resultante = "august";
                } else if ($mes_plus == 9) {
                    $mes_resultante = "september";
                } else if ($mes_plus == 10) {
                    $mes_resultante = "october";
                } else if ($mes_plus == 11) {
                    $mes_resultante = "november";
                } else if ($mes_plus == 12) {
                    $mes_resultante = "december";
                }

                $fecha_resultante = "";

                if ($columna == 31) {
                    $cliente->corte = date('Y-m-d', strtotime("last day of $mes_resultante $ano_actual_corte"));
                } else if ($columna == 30) {

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "31" || date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "30") {
                        $dia = "30";
                    }

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "29") {
                        $dia = "29";
                    }

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "28") {
                        $dia = "28";
                    }

                    $cliente->corte = date('Y-m-d', strtotime("$dia-$mes_resultante-$ano_actual_corte"));
                } else if ($columna == 29) {
                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "31" || date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "30") {
                        $dia = "29";
                    }

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "29") {
                        $dia = "29";
                    }

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "28") {
                        $dia = "28";
                    }

                    $cliente->corte = date('Y-m-d', strtotime("$dia-$mes_resultante-$ano_actual_corte"));
                }
            }

            $pago->corte = $cliente->corte;

            /* Aquí se le suben los meses dependiendo de cuanto haya pagado */

            /* Aquí se reducen los días si el cliente tenia prorroga sin pagar */
            if($cliente->dias_prorroga != null && $reactivacion == "(REACTIVACIÓN)"){
                $cliente->corte = date('Y-m-d', strtotime("$cliente->corte - $cliente->dias_prorroga days"));
                $cliente->dias_prorroga = 0;
                $cliente->prorroga = 0;
                $cliente->prorroga_hasta = null;
                $cliente->nota_prorroga = null;
                $cliente->ult_prorroga = null;
            }
            /* Aquí se reducen los días si el cliente tenia prorroga sin pagar */

            /* Sección para determinar el estado del cliente dependiendo de la fecha de corte */

            $corte_despues_del_pago = date('d-m-Y', strtotime($cliente->corte));

            $hoy = date('d-m-Y', strtotime($hoy)); // hoy
            $hoy_ma_01 = date('d-m-Y', strtotime("$hoy +1 day")); // hoy mas 1 dia
            $hoy_ma_02 = date('d-m-Y', strtotime("$hoy +2 day")); // hoy mas 2 días
            $hoy_ma_03 = date('d-m-Y', strtotime("$hoy +3 day")); // hoy mas 3 días
            $hoy_me_01 = date('d-m-Y', strtotime("$hoy -1 day")); // hoy menos 1 dia
            $hoy_me_02 = date('d-m-Y', strtotime("$hoy -2 day")); // hoy menos 2 días

            if ($corte_despues_del_pago == $hoy) { // dia de corte. funciona 
                $cliente->active = 1;
                $cliente->estado = 4;
            } elseif ($corte_despues_del_pago == $hoy_me_02) { // restan 2 días. funciona
                $cliente->active = 1;
                $cliente->estado = 7;
            } elseif ($corte_despues_del_pago == $hoy_me_01) { // restan 1 días. funciona
                $cliente->active = 1;
                $cliente->estado = 6;
            } elseif ($corte_despues_del_pago == $hoy_ma_01) { // prorroga  dia 1. funciona
                $cliente->active = 1;
                $cliente->estado = 3;
            } elseif ($corte_despues_del_pago == $hoy_ma_02) { // prorroga  dia 2. funciona
                $cliente->estado = 2;
            } elseif ($corte_despues_del_pago > $hoy_ma_03) { // requiere suspension.

                if ($cliente->active == 0) {
                    $cliente->active = 0;
                }

                $cliente->estado = 5;
            } else { //solvente
                $cliente->active = 1;
                $cliente->estado = 1;
            }

            /* Sección para determinar el estado del cliente dependiendo de la fecha de corte */

            /* Aquí se define que tipo de pago se realizo y que procede dependiendo del monto pagado y la deuda actual del cliente */

            $cadena = "";
            $tipo = "";
            $activar = false;

            if (($plan->valor * 2) >= $cliente->almacen) {
                if ($pago->total == $plan->valor) {
                    $tipo = "$reactivacion TIPO DE PAGO: MENSUALIDAD COMPLETA";
                    $pago->concepto = "$reactivacion MENSUALIDAD COMPLETA";
                    $cliente->almacen = $cliente->almacen - $plan->valor;
                    $cliente->dias_prorroga = 0;
                } else if ($pago->total > $plan->valor) {
                    $tipo = "$reactivacion TIPO DE PAGO: MENSUALIDAD COMPLETA";
                    $pago->concepto = "$reactivacion MENSUALIDAD COMPLETA";
                    $cliente->almacen = $cliente->almacen - $plan->valor;
                    $activar = true;
                    $cliente->dias_prorroga = 0;
                } else if ($plan->valor > $cliente->almacen) {
                    $tipo = "TIPO DE PAGO: ABONO";
                    $pago->concepto = 'ABONO';
                    $activar = true;
                } else {
                    if ($cliente->almacen - $plan->valor > 0) {
                        $tipo = "$reactivacion TIPO DE PAGO: COMPLEMENTO DE MENSUALIDAD";
                        $pago->concepto = "$reactivacion COMPLEMENTO DE MENSUALIDAD";
                        $activar = true;
                        $cliente->dias_prorroga = 0;
                    } else {
                        $tipo = "$reactivacion TIPO DE PAGO: COMPLEMENTO DE MENSUALIDAD";
                        $pago->concepto = "$reactivacion COMPLEMENTO DE MENSUALIDAD";
                        $cliente->dias_prorroga = 0;
                    }

                    $cliente->almacen = -1 * ($plan->valor - $cliente->almacen);
                }
            } else {

                if (($cliente->almacen % $plan->valor) == 0) {
                    $tipo = "$reactivacion TIPO DE PAGO: VARIAS MENSUALIDADES (" . $meses_arriba . " MESES)";
                    $pago->concepto = "$reactivacion VARIAS MENSUALIDADES (" . $meses_arriba . " MESES)";
                    $cliente->dias_prorroga = 0;
                    $cliente->almacen = 0;
                } else {
                    $cliente->almacen = ($cliente->almacen % $plan->valor);
                    $tipo = "$reactivacion TIPO DE PAGO: VARIAS MENSUALIDADES (" . $meses_arriba . " MESES)";
                    $pago->concepto = "$reactivacion VARIAS MENSUALIDADES (" . $meses_arriba . " MESES)";
                    $cliente->dias_prorroga = 0;
                    $activar = true;
                }
            }
            /* Aquí se define que tipo de pago se realizo y que procede dependiendo del monto pagado y la deuda actual del cliente */

            /* Evento Diario: pago de mensualidad */

            if(Auth::user()->grupo == 1){
                $evento = new evento();
                $evento->usuario = Auth::user()->name;
            }else if(Auth::user()->grupo == 2){
                $evento = new caja();
                $evento->usuario = Auth::user()->name;
            }else{
                $evento = new evento();
            }

            if ($pago->dolares == null) {
                $pago->dolares = 0;
            }

            if ($pago->bolivares == null) {
                $pago->bolivares = 0;
            }

            if ($pago->pagomovil == null) {
                $pago->pagomovil = 0;
            }

            if ($pago->zelle_a == null) {
                $pago->zelle_a = 0;
            }

            if ($pago->zelle_b == null) {
                $pago->zelle_b = 0;
            }

            //ver si hay resto.

            $resto = $plan->valor - $cliente->almacen;

            if ($activar) {
                $cadena .= "abonado: " . number_format($cliente->almacen, 2, '.', '') . " | " . "restan: " . number_format($resto, 2, '.', '') . "$";
            }

            if ($cliente->almacen == $plan->valor) {
                $cliente->almacen = 0;
            }

            $cliente->dia = Carbon::now();

            $cliente->save();

            $evento->evento = "<b><b class='tipo'>$tipo</b><br>$cliente->nombre | $cliente->cedula | $servidor->nombre_de_servidor</b><br>$cadena";
            $evento->bolivares = number_format($request->bolivar, 2, '.', '');
            $evento->pagomovil = number_format($request->pagomovil, 2, '.', '');
            $evento->ref = $request->referencia;
            $evento->dolares = number_format($request->dolar, 2, '.', '');
            $evento->euros = number_format($request->euro, 2, '.', '');
            $evento->zelle_j = number_format($request->zelle_j, 2, '.', '');
            $evento->zelle_v = number_format($request->zelle_v, 2, '.', '');
            $evento->receptor = $request->banco_receptor;
            $evento->total = $pago->total;
            $evento->enlace = $cuenta;

            $pago->save();

            $nuevo_corte = date('d/m/Y', strtotime("$cliente->corte"));
            /* Evento Diario */

            /* Resumen General */

            if ($meses_arriba > 0) {
                $resumen = new resumen_general();
                $resumen->usuario = Auth::user()->name;
                $resumen->descripcion = "Al cliente $cliente->nombre se le agrego el total de $meses_arriba mes(es), su nueva fecha de corte $nuevo_corte.";
                $resumen->tipo = 0;
                $resumen->save();
            }

            /* Resumen General */

            //grupo oficina o grupo caja fuerte
            if (Auth::user()->grupo == 1 || Auth::user()->grupo == 2) {
                $evento->save();
            }

            /* Sección del ticket */
            $bolivares = round(($total_bs), 2);

            if ($cliente->id > 0) {
                $cliente_datos = clientes::findOrFail($cliente->id);
                $fecha_de_corte = date('d-m-Y', strtotime($cliente->corte));
                if ($cliente_datos->ticket == 0) {

                    $total = "TOTAL CANCELADO: $bolivares Bs.";
                } else {
                    $total = "TOTAL CANCELADO: $total_d$.";
                }
            } else {
                $total = "TOTAL CANCELADO: $bolivares Bs.";
            }

            /*Activar si el cliente esta solvente después del pago*/

            if ($cliente->congelado == 0 || $cliente->active == 1) {
                $API = new api();

                if ($API->connect($servidor->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
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

                $API->disconnect();
            }

            /*Activar si el cliente esta solvente después del pago*/

            $id_a_imprimir = pago_resumen::select('id')->orderBy('id', 'desc')->first();

            if ($request->imprimir_ticket == 1) {
                //return redirect()->action([imprimir_datos::class, 'imprimir_factura'], ['id' => $id_a_imprimir]);
                return back()->with('pagado', 'ok')->with('ejecutar_funcion', true)->with('parametro', $id_a_imprimir['id']);
            } else {
                return back()->with('pagado', 'ok');
            }
        }
    }

    public function eliminar_pago($id) //acomodar nombre
    {
        $pago = pago_resumen::findOrFail($id);

        if ($pago->tipo == 0) {

            $cliente = clientes::findOrFail($pago->id_cliente);
            $plan = planes::findOrFail($cliente->plan_id);

            //Apartado para eliminar meses acorde a los pagos.

            $meses_abajo = 0; // variable para eliminar meses.
            $resto = 0;

            //Restar meses:
            if ($pago->total >= $plan->valor) {
                $meses_abajo = intdiv($pago->total, $plan->valor);
                $resto = number_format(fmod($pago->total, $plan->valor), 2, '.');
                echo "Meses restados: $meses_abajo<br><br>";
            } else {
                $resto = number_format(fmod($pago->total, $plan->valor), 2, '.');
            }

            $resultado = $cliente->almacen - $resto;

            $cliente->almacen = $cliente->almacen - $resto;
            $nueva_fecha = Carbon::parse($cliente->corte);
            $fecha_formato = $nueva_fecha->format('d-m-Y');
            $fecha_vieja = $nueva_fecha->format('d-m-Y');
            $almacen_viejo = $cliente->almacen;
            $cliente->corte = $nueva_fecha->subMonths($meses_abajo);

            $texto = "<br><br>FECHA DE CORTE: $fecha_formato - MESES RESTADOS: $meses_abajo - ALMACÉN ACTUAL: $cliente->almacen$";
        } else if ($pago->tipo == 2) {
            $cliente = clientes::findOrFail($pago->id_cliente);
            $plan = planes::findOrFail($cliente->plan_id);

            $nueva_fecha = Carbon::parse($cliente->corte);
            $fecha_formato = $nueva_fecha->format('d-m-Y');

            $un_dia = ($plan->valor  / 30) * 1;

            $dias_abajo = $pago->total / $un_dia;

            $cliente->corte = $nueva_fecha->subDays($dias_abajo);

            $texto = "<br><br>FECHA DE CORTE: $fecha_formato - DIAS RESTADOS: $dias_abajo";
        }

        /* Resumen General */

        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;

        if ($pago->tipo == 0) {
            $resumen->descripcion = "Se ELIMINO manualmente el pago de $pago->total$ del cliente $pago->cliente ($almacen_viejo$ | $fecha_vieja) $texto.";
        } else {
            $resumen->descripcion = "Se ELIMINO manualmente el pago de $pago->total$ del servicio del cliente $pago->cliente bajo el concepto de: $pago->concepto.";
        }

        $resumen->tipo = 8;
        $resumen->save();

        /* Resumen General */

        //sección para eliminar del evento diario.
        if ($pago->enlace != null) {
            DB::select("DELETE FROM evento_diario WHERE `evento_diario`.`enlace` = $pago->enlace;");
        }
        //sección para eliminar del evento diario.

        $pago->delete();
        $cliente->save();

        return back();
    }

    public function pagar_deuda(Request $request, $id)
    {
        $cliente = clientes::findOrFail($id);
        $servidor = servidores::findOrFail($cliente->servidor);
        $sort = sort::findOrFail(1)->toArray();
        $tasa = $sort["tasa"];
        $cuenta = pago_resumen::count();
        $pago = new pago_resumen();
        $hoy = Carbon::now();

        $total_bs = ($request->bolivar + $request->pagomovil) / $tasa;
        $total_d = ($request->dolar + $request->euro + $request->zelle_v + $request->zelle_j);

        $total = $total_bs + $total_d;

        $cliente->deuda = $cliente->deuda - $total;

        $tipo_pago = "ABONO DE DEUDA";

        if ($cliente->deuda <= 0) {
            $cliente->deuda_p = 0;
            $cliente->motivo_deuda = "Sin deuda";
            $tipo_pago = "DEUDA PAGADA";
        }

        /*evento diario*/
        if(Auth::user()->grupo == 1){
            $evento = new evento();
        }else if(Auth::user()->grupo == 2){
            $evento = new caja();
        }else{
            $evento = new evento();
        }

        $evento->usuario = Auth::user()->name;
        $evento->evento = "<b><b class='tipo'>TIPO DE PAGO: $tipo_pago</b><br>$cliente->nombre | $cliente->cedula | $servidor->nombre_de_servidor</b><br>";
        $evento->bolivares = number_format($request->bolivar, 2, '.', '');
        $evento->pagomovil = number_format($request->pagomovil, 2, '.', '');
        $evento->ref = $request->referencia;
        $evento->dolares = number_format($request->dolar, 2, '.', '');
        $evento->euros = number_format($request->euro, 2, '.', '');
        $evento->zelle_j = number_format($request->zelle_j, 2, '.', '');
        $evento->zelle_v = number_format($request->zelle_v, 2, '.', '');
        $evento->receptor = $request->banco_receptor;
        $evento->total = $total;
        $evento->hora = $hoy;

        /*evento diario */

        /* pago resumen */

        $pago->usuario = Auth::user()->name;
        $pago->cobrador = Auth::user()->name;
        $pago->servicio = $cliente->id;
        $pago->codigo =  "DE_" . $cuenta;
        $pago->cliente = $cliente->nombre;
        $pago->cedula = $cliente->cedula;
        $pago->direccion = $cliente->direccion;
        $pago->concepto = $tipo_pago;
        $pago->pago = $hoy; //Ya no se podrán pasar pagos que no sean el dia de hoy.
        $pago->corte = $cliente->corte;
        $pago->plan = $cliente->plan_id;
        $pago->bolivares = $request->bolivar;
        $pago->pagomovil = $request->pagomovil;
        $pago->referencia = $request->referencia;
        $pago->fecha_pago_movil = $request->fecha_pago_movil;
        $pago->banco = $request->banco;
        $pago->banco_receptor = $request->banco_receptor;
        $pago->dolares = $request->dolar;
        $pago->euros = $request->euro;
        $pago->zelle_a = $request->zelle_v;
        $pago->zelle_b = $request->zelle_j;
        $pago->tasa = $tasa;
        $pago->total = $total_d + $total_bs;
        $pago->active = 1;
        $pago->telefono = $cliente->tlf;
        $pago->id_cliente = $cliente->id;
        $pago->tipo = 0;

        $resumen = new resumen_general();
        $resumen->usuario = Auth::user()->name;

        if ($cliente->deuda <= 0) {
            $resumen->descripcion = "La deuda total del cliente $cliente->nombre fue cancelada";
            $resumen->tipo = 15;
        } else {
            $resumen->descripcion = "Abono de deuda del cliente $cliente->nombre, restando $cliente->deuda$";
            $resumen->tipo = 16;
        }

        /* pago resumen */

        $pago->save();
        $resumen->save();
        $cliente->save();

        //grupo oficina o grupo caja fuerte
        if (Auth::user()->grupo == 1 || Auth::user()->grupo == 2) {
            $evento->save();
        }

        if ($cliente->deuda <= 0) {
            return back()->with('deuda', 'pagada');
        } else {
            return back()->with('deuda', 'abonada');
        }
    }

    public function metodo_prepago(Request $request)
    {
        $cliente = clientes::findOrFail($request->id);
        $plan = planes::findOrFail($cliente->plan_id);
        $servidor = servidores::findOrFail($cliente->servidor);
        $hoy = Carbon::now();
        $sort = sort::findOrFail(1)->toArray();
        $tasa = $sort["tasa"];
        $total = ($request->dolar + $request->euro + $request->zelle_v + $request->zelle_j) + ($request->bolivar + $request->pagomovil) / $tasa;

        //precio / 30 * días
        $plan_valor_dia = ($plan->valor / 30);
        $calculado = floor($total / $plan_valor_dia);

        $cliente->corte = date("Y-m-d", strtotime("$cliente->corte, + $calculado days"));
        $cliente->mes = date("d", strtotime("$cliente->corte"));

        $cliente->save();

        /*reporte de pagos*/
        $cuenta = pago_resumen::count();
        $pago = new pago_resumen();

        $total_d = $request->dolar + $request->euro + $request->zelle_v + $request->zelle_j;
        $total_bs = ($request->bolivar + $request->pagomovil) / $tasa;

        $pago->usuario = Auth::user()->name;
        $pago->cobrador = Auth::user()->name;
        $pago->servicio = $cliente->id;
        $pago->codigo =  "NF_" . $cuenta;
        $pago->cliente = $cliente->nombre;
        $pago->cedula = $cliente->cedula;
        $pago->direccion = $cliente->direccion;
        $pago->pago = $hoy; //Ya no se podrán pasar pagos que no sean el dia de hoy.

        $pago->plan = $cliente->plan_id;
        $pago->bolivares = $request->bolivar;
        $pago->pagomovil = $request->pagomovil;
        $pago->referencia = $request->referencia;
        $pago->fecha_pago_movil = $request->fecha_pago_movil;
        $pago->banco = $request->banco;
        $pago->dolares = $request->dolar;
        $pago->euros = $request->euro;
        $pago->zelle_a = $request->zelle_v;
        $pago->zelle_b = $request->zelle_j;
        $pago->tasa = $tasa;
        $pago->banco_receptor = $request->banco_receptor;
        $pago->total = $total_d + $total_bs;
        $pago->active = 1;
        $pago->telefono = $cliente->tlf;
        $pago->id_cliente = $cliente->id;
        $pago->tipo = 2;
        $pago->corte = $cliente->corte;
        $pago->concepto = "REAJUSTE DE FECHA +$calculado DIAS";
        $pago->save();
        /*Reporte de pagos*/

        /*Activar si el cliente esta solvente despues del pago*/

        if ($cliente->congelado == 0 || $cliente->active == 1) {
            $API = new api();

            if ($API->connect($servidor->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
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

            $API->disconnect();
        }

        /*Activar si el cliente esta solvente despues del pago*/

        /*evento diario*/
        if(Auth::user()->grupo == 1){
            $evento = new evento();
        }else if(Auth::user()->grupo == 2){
            $evento = new caja();
        }else{
            $evento = new evento();
        }

        $evento->usuario = Auth::user()->name;
        $evento->evento = "<b><b class='tipo'>TIPO DE PAGO: REAJUSTE DE FECHA</b><br>$cliente->nombre | $cliente->cedula | $servidor->nombre_de_servidor</b><br>";
        $evento->bolivares = number_format($request->bolivar, 2, '.', '');
        $evento->pagomovil = number_format($request->pagomovil, 2, '.', '');
        $evento->ref = $request->referencia;
        $evento->dolares = number_format($request->dolar, 2, '.', '');
        $evento->euros = number_format($request->euro, 2, '.', '');
        $evento->zelle_j = number_format($request->zelle_j, 2, '.', '');
        $evento->zelle_v = number_format($request->zelle_v, 2, '.', '');
        $evento->receptor = $request->banco_receptor;
        $evento->total = $total;
        $evento->hora = $hoy;

        //grupo oficina o grupo caja fuerte
        if (Auth::user()->grupo == 1 || Auth::user()->grupo == 2) {
            $evento->save();
        }
        /*evento diario */

        /*Resumen general*/
        $resumen = new resumen_general();
        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Al cliente $cliente->nombre se le agrego el total de $calculado dia(s), su nueva fecha de corte $cliente->corte.";
        $resumen->tipo = 20;
        $resumen->save();
        /*Resumen general*/

        return back()->with('reajuste', 'ok');
    }

    public function verificar_pagomovil($referencia)
    {

        if ($referencia == 0000000) { // Para todas las referencias.
            $referencias = DB::select("SELECT cliente, pago, fecha_pago_movil, banco, referencia FROM `pago_resumen` WHERE `referencia` != '0000000' ORDER BY `id` DESC");
        } else { // Referencia individual.
            $referencias = DB::select("SELECT referencia FROM `pago_resumen` WHERE `referencia` = $referencia AND `referencia` != '0000000' ORDER BY `id` DESC");
        }

        return response()->json($referencias);
    }

    public function pago_individual($id)
    {
        $cliente = clientes::findOrFail($id);
        $plan = planes::findOrFail($cliente->plan_id);
        $sort = sort::findOrFail(1)->toArray();

        $ticket = "Bolivares";

        if ($cliente->ticket == 1) {
            $ticket = "Dolares";
        }

        $tasa = $sort["tasa"];

        return view('pagos.pagar', compact('cliente', 'plan', 'tasa', 'ticket'));
    }

    public function datos_estructurados_modificar($id)
    {

        $cliente = clientes::findOrFail($id);

        return response()->json($cliente);

    }

    public function datos_estructurados_pago($id)
    {
        $datos = [];

        $cliente = clientes::findOrFail($id);
        $plan = planes::findOrFail($cliente->plan_id);
        $sort = sort::findOrFail(1)->toArray();

        $ticket = "Bolivares";

        if ($cliente->ticket == 1) {
            $ticket = "Dolares";
        }

        $tasa = $sort["tasa"];

        $datos[0] = $cliente;
        $datos[1] = $plan;
        $datos[2] = $tasa;
        $datos[3] = $ticket;

        return response()->json($datos);
    }

    public function add_deuda(Request $request)
    {
        $cliente = clientes::findOrFail($request->id);

        $cliente->motivo_deuda = $request->motivo;
        $cliente->deuda = $request->cantidad;

        $cliente->save();

        /* Resumen General */

        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se agrego una DEUDA manualmente de $cliente->deuda$ al cliente $cliente->nombre - motivo: $cliente->motivo_deuda.";
        $resumen->tipo = 22;
        $resumen->save();

        /* Resumen General */

        return back()->with('deuda_agregada', 'ok');
    }

    public function consultar_tasa()
    {
        $resultado = DB::select("SELECT tasa, fecha FROM `historial_tasa`;");

        return response()->json($resultado);
    }

    public function consultar_tasa_fecha($fecha)
    {
        $resultado = DB::select("SELECT tasa, fecha FROM `historial_tasa` WHERE `fecha` = '$fecha' ORDER BY `id` DESC;");

        return response()->json($resultado[0]);
    }

    public function multi_pagos() //vista
    {
        $hoy = Carbon::now()->format('Y-m-d');
        $sort = sort::findOrFail(1)->toArray();
        $tasa = $sort["tasa"];

        return view('pagos.multi_pagos', compact('tasa', 'hoy'));
    }

    public function verificar_multipago($fecha){

        //class submit_multi
        
        if(DB::table('historial_tasa')->where('fecha', "$fecha")->exists()){
            return 1;
        }else{
            return 0;
        }

    }

    public function pagar_multi_pago(Request $request)
    {
        $id_s = explode(",", $request->clientes);

        foreach ($id_s as $clientes) {
            $cliente = clientes::findOrFail($clientes);
            $cuenta = pago_resumen::count();
            $plan = planes::findOrFail($cliente->plan_id);
            $servidor = servidores::findOrFail($cliente->servidor);

            $cliente->mes = date("d", strtotime("$cliente->corte"));
            $hoy = Carbon::now();
            $hoy_dia = Carbon::now()->format('d');

            $fecha = $request->fecha_pago_movil;

            $hoy = date('Y-m-d', strtotime(Carbon::now()));

            $historial_tasa = DB::table('historial_tasa')->select(DB::raw('count(*) as cuenta'))->where('fecha', '=', "$fecha")->get();

            foreach ($historial_tasa as $historial) {
                $la_cuenta = $historial->cuenta;
            }

            if ($la_cuenta > 0) {
                $historial_tasa = DB::table('historial_tasa')->select(DB::raw('tasa'))->where('fecha', '=', "$fecha")->get();
            } else {
                $historial_tasa = DB::table('historial_tasa')->select(DB::raw('tasa'))->where('fecha', '=', "$hoy")->get();
            }

            foreach ($historial_tasa as $historial) {
                $tasa = $historial->tasa;
            }

            $pago = new pago_resumen();

            $user_cobrador = Auth::user()->name;
            $pago->usuario = Auth::user()->name;
            $pago->cobrador = Auth::user()->name;
            $pago->servicio = $cliente->id;
            $pago->codigo =  "PW_" . $cuenta;
            $pago->cliente = $cliente->nombre;
            $pago->cedula = $cliente->cedula;
            $pago->direccion = $cliente->direccion;
            $pago->pago = $hoy;

            $pago->plan = $cliente->plan_id;
            $pago->bolivares = 0;
            $pago->pagomovil = $request->pagomovil;
            $pago->referencia = $request->referencia;
            $pago->fecha_pago_movil = $request->fecha_pago_movil;
            $pago->banco = $request->banco;
            $pago->dolares = 0;
            $pago->euros = 0;
            $pago->zelle_a = 0;
            $pago->zelle_b = 0;

            $pago->tasa = $tasa;

            $pago->banco_receptor = $request->banco_receptor;
            $pago->total = $plan->valor;
            $pago->active = 1;
            $pago->telefono = $cliente->tlf;
            $pago->id_cliente = $cliente->id;
            $pago->tipo = 0;
            $pago->enlace = $cuenta;

            /* Ver días de diferencia */

            $reactivacion = "";

            $diferencia_corte = date("Y-m-d", strtotime("$cliente->corte, + 6 days"));

            if ($cliente->prorroga == 0) {
                if ($diferencia_corte < $hoy) {
                    $cliente->corte = $hoy;
                    $cliente->mes = $hoy_dia;
                    $reactivacion = "(REACTIVACIÓN)";
                }
            }

            /* Ver días de diferencia */

            /* Aquí se le suben los meses dependiendo de cuanto haya pagado */

            // Formula: total cancelado / precio del plan = numero de meses por subir (sin el decimal)

            $meses_arriba = 1;

            $columna = $cliente->mes;
            $fecha = $cliente->corte;

            if ($columna < 29) {
                $fecha_de_corte = date('Y-m-d', strtotime("$fecha + $meses_arriba month"));
                $cliente->corte = $fecha_de_corte;
            } else {
                $dia_actual_corte = date('d', strtotime($fecha));
                $mes_actual_corte = date('m', strtotime($fecha));
                $ano_actual_corte = date('Y', strtotime($fecha));
                $fecha_de_corte = date('Y-m-d', strtotime($fecha));

                $mes_plus = $mes_actual_corte + $meses_arriba;

                while ($mes_plus > 12) {
                    $mes_plus = $mes_plus - 12;
                    $ano_actual_corte += 1;
                }

                $dia = "";

                if ($mes_plus == 1) {
                    $mes_resultante = "january";
                } else if ($mes_plus == 2) {
                    $mes_resultante = "february";
                } else if ($mes_plus == 3) {
                    $mes_resultante = "march";
                } else if ($mes_plus == 4) {
                    $mes_resultante = "april";
                } else if ($mes_plus == 5) {
                    $mes_resultante = "may";
                } else if ($mes_plus == 6) {
                    $mes_resultante = "june";
                } else if ($mes_plus == 7) {
                    $mes_resultante = "july";
                } else if ($mes_plus == 8) {
                    $mes_resultante = "august";
                } else if ($mes_plus == 9) {
                    $mes_resultante = "september";
                } else if ($mes_plus == 10) {
                    $mes_resultante = "october";
                } else if ($mes_plus == 11) {
                    $mes_resultante = "november";
                } else if ($mes_plus == 12) {
                    $mes_resultante = "december";
                }

                $fecha_resultante = "";

                if ($columna == 31) {
                    $cliente->corte = date('Y-m-d', strtotime("last day of $mes_resultante $ano_actual_corte"));
                } else if ($columna == 30) {

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "31" || date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "30") {
                        $dia = "30";
                    }

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "29") {
                        $dia = "29";
                    }

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "28") {
                        $dia = "28";
                    }

                    $cliente->corte = date('Y-m-d', strtotime("$dia-$mes_resultante-$ano_actual_corte"));
                } else if ($columna == 29) {
                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "31" || date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "30") {
                        $dia = "29";
                    }

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "29") {
                        $dia = "29";
                    }

                    if (date('d', strtotime("last day of $mes_resultante $ano_actual_corte")) == "28") {
                        $dia = "28";
                    }

                    $cliente->corte = date('Y-m-d', strtotime("$dia-$mes_resultante-$ano_actual_corte"));
                }
            }

            $pago->corte = $cliente->corte;

            /* Aquí se le suben los meses dependiendo de cuanto haya pagado */

            /* Sección para determinar el estado del cliente dependiendo de la fecha de corte */

            $corte_despues_del_pago = date('d-m-Y', strtotime($cliente->corte));

            $hoy = date('d-m-Y', strtotime($hoy)); // hoy
            $hoy_ma_01 = date('d-m-Y', strtotime("$hoy +1 day")); // hoy mas 1 dia.
            $hoy_ma_02 = date('d-m-Y', strtotime("$hoy +2 day")); // hoy mas 2 días.
            $hoy_ma_03 = date('d-m-Y', strtotime("$hoy +3 day")); // hoy mas 3 días.
            $hoy_me_01 = date('d-m-Y', strtotime("$hoy -1 day")); // hoy menos 1 dia.
            $hoy_me_02 = date('d-m-Y', strtotime("$hoy -2 day")); // hoy menos 2 días.

            if ($corte_despues_del_pago == $hoy) { // dia de corte. funciona 
                $cliente->active = 1;
                $cliente->estado = 4;
            } elseif ($corte_despues_del_pago == $hoy_me_02) { // restan 2 días. funciona
                $cliente->active = 1;
                $cliente->estado = 7;
            } elseif ($corte_despues_del_pago == $hoy_me_01) { // restan 1 días. funciona
                $cliente->active = 1;
                $cliente->estado = 6;
            } elseif ($corte_despues_del_pago == $hoy_ma_01) { // prorroga  dia 1. funciona
                $cliente->active = 1;
                $cliente->estado = 3;
            } elseif ($corte_despues_del_pago == $hoy_ma_02) { // prorroga  dia 2. funciona
                $cliente->estado = 2;
            } elseif ($corte_despues_del_pago > $hoy_ma_03) { // requiere suspension.

                if ($cliente->active == 0) {
                    $cliente->active = 0;
                }

                $cliente->estado = 5;
            } else { //solvente
                $cliente->active = 1;
                $cliente->estado = 1;
            }

            /* Sección para determinar el estado del cliente dependiendo de la fecha de corte */

            /* Aquí se define que tipo de pago se realizo y que procede dependiendo del monto pagado y la deuda actual del cliente */

            $tipo = "MENSUALIDAD COMPLETA (MULTI PAGO) $reactivacion";
            $pago->concepto = $tipo;

            /* Aquí se define que tipo de pago se realizo y que procede dependiendo del monto pagado y la deuda actual del cliente */

            /* Evento Diario: pago de mensualidad */

            $plan_cambio = $plan->valor * $tasa;

            if(Auth::user()->grupo == 1){
                $evento = new evento();
            }else if(Auth::user()->grupo == 2){
                $evento = new caja();
            }else{
                $evento = new evento();
            }
            
            $pago->dolares = 0;
            $pago->bolivares = 0;
            $pago->pagomovil = $plan_cambio;
            $pago->zelle_a = 0;
            $pago->zelle_b = 0;

            $cliente->dia = Carbon::now();

            $cliente->save();

            $evento->usuario = Auth::user()->name;
            $evento->evento = "<b><b class='tipo'>$tipo</b><br>$cliente->nombre | $cliente->cedula | $servidor->nombre_de_servidor</b>";
            $evento->bolivares = number_format(0, 2, '.', '');
            $evento->pagomovil = number_format($plan_cambio, 2, '.', '');
            $evento->ref = $request->referencia;
            $evento->dolares = number_format(0, 2, '.', '');
            $evento->euros = number_format(0, 2, '.', '');
            $evento->zelle_j = number_format(0, 2, '.', '');
            $evento->zelle_v = number_format(0, 2, '.', '');
            $evento->receptor = $request->banco_receptor;
            $evento->total = $plan->valor;
            $evento->enlace = $cuenta;

            $pago->save();

            $nuevo_corte = date('d/m/Y', strtotime("$cliente->corte"));
            /* Evento Diario */

            /* Resumen General */

            $resumen = new resumen_general();
            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "$reactivacion Al cliente $cliente->nombre se le agrego el total de $meses_arriba mes, su nueva fecha de corte $nuevo_corte.";
            $resumen->tipo = 0;
            $resumen->save();

            /* Resumen General */

            //grupo oficina o grupo caja fuerte
            if (Auth::user()->grupo == 1 || Auth::user()->grupo == 2) {
                $evento->save();
            }

            /*Activar si el cliente esta solvente despues del pago*/

            if ($cliente->congelado == 0 || $cliente->active == 1) {
                $API = new api();

                if ($API->connect($servidor->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
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

                $API->disconnect();
            }

            /*Activar si el cliente esta solvente despues del pago*/
        }

        return back()->with('pagado','ok');
    }
}