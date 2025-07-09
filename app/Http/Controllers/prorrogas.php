<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\clientes;
use App\Models\resumen_general;
use App\Models\servidores;

use App\api\api;

use Carbon\Carbon;

date_default_timezone_set('America/Caracas');

class prorrogas extends Controller
{
    public function prorroga_dar(Request $request, $id)
    {
        $cliente = clientes::findOrFail($id);
        $hoy = Carbon::now()->format("Y-m-d");
        $servidor = servidores::findOrFail($cliente->servidor);

        $total = ($request->dias - 1);
        
        $cliente->ult_prorroga = date("Y-m-d", strtotime("$hoy, + 30 days"));
        $cliente->prorroga_hasta = date("Y-m-d", strtotime("$hoy, + $total days"));
        $cliente->dias_prorroga = $cliente->dias_prorroga + $total;
        $prorroga_formateada = date("d-m-Y", strtotime("$hoy, + $total days"));

        if ($request->nota != "" || $request->nota != null) {
            $cliente->nota_prorroga = $request->nota;
        } else {
            $cliente->nota_prorroga = "Sin especificar";
        }

        $cliente->prorroga = 1;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se registro una prorroga a cliente: $cliente->nombre hasta el $prorroga_formateada, Nota: $cliente->nota_prorroga.";
        $resumen->tipo = 11;
        $resumen->save();
        /* Resumen General */

        $cliente->save();

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

        return back()->with('prorroga_dar', 'ok');
    }

    public function prorroga_quitar($id)
    {
        $cliente = clientes::findOrFail($id);
        $servidor = servidores::findOrFail($cliente->servidor);

        $cliente->prorroga_hasta = null;
        $cliente->nota_prorroga = null;
        $cliente->dias_prorroga = null;
        $cliente->prorroga = 0;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se elimino la prorroga del cliente: $cliente->nombre.";
        $resumen->tipo = 12;
        $resumen->save();
        /* Resumen General */

        $cliente->save();

        if ($cliente->congelado == 0 || $cliente->active == 1 || $cliente->estado != 5) { //agregar aquí si esta cortado no activar
            $API = new api();

            if ($API->connect($servidor->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {
                $API->write("/ip/firewall/address-list/add", false);
                $API->write('=address=' . $cliente->ip, false);   // IP
                $API->write('=list=BLOCK', false);       // lista
                $API->write('=comment=' . strtoupper($cliente->nombre . ' dia de corte ' . $cliente->corte), true);  // comentario
                $READ = $API->read(false);

                $API->disconnect();
            }
        }

        return back()->with('prorroga_quitar', 'ok');
    }
}
