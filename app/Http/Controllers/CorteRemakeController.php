<?php

namespace App\Http\Controllers;

use App\api\api;
use App\Models\clientes;
use App\Models\estados;
use App\Models\resumen_general;
use App\Models\servidores;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CorteRemakeController extends Controller
{
    /*
    *
    * Obtiene los clientes que serán suspendidos en el servidor.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function getDataToCutOff(): \Illuminate\Http\JsonResponse
    {
        try {
            $clientes = clientes::select('clientes.nombre', 'clientes.corte', 'clientes.servidor', 'clientes.ip', 'servidores.nombre_de_servidor')
                ->join('servidores', 'clientes.servidor', '=', 'servidores.id')
                ->where('servidores.active', 1)
                ->where('clientes.estado', clientes::ESTADO_SUSPENDIDO)
                ->where('clientes.tipo_cliente', clientes::TIPO_NO_PREMIUM)
                ->where('clientes.prorroga', clientes::PRORROGA_NO)
                ->whereNotNull('clientes.ip')
                ->where('clientes.ip', '!=', '0.0.0.0')
                ->where('clientes.ip', 'REGEXP', '^([0-9]{1,3}\.){3}[0-9]{1,3}$')
                ->orderBy('clientes.servidor', 'asc')
                ->get();

            if ($clientes->isEmpty()) {
                return response()->json(['message' => 'No hay clientes para suspender'], 204);
            }

            return response()->json($clientes);
        } catch (\Exception $e) {
            Log::error("Error al recibir los datos: {$e->getMessage()}");
            return response()->json(['message' => "Error al recibir los datos: {$e->getMessage()}"], 500);
        }
    }

    /*
    * Realiza el corte bloqueando las IPs de los clientes.
    *
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function makeCut(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $ip = servidores::where('id', $request->servidorId)->value('ip');

            $API = new api();

            if ($API->connect($ip, env('API_USER'), env('API_PASSWORD'))) {
                foreach ($request->clientes as $cliente) {
                    $nombre = strtoupper($cliente['nombre']);
                    $dia = $cliente['corte'];
                    
                    $API->write('/ip/firewall/address-list/add', false);
                    $API->write("=address={$cliente['ip']}", false);
                    $API->write('=list=BLOCK', false);
                    $API->write("=comment={$nombre} dia de corte {$dia}", true);
                    $READ = $API->read(false);
                }
            } else {
                return response()->json(['message' => 'No se pudo conectar al servidor'], 500);
            }

            return response()->json(['message' => 'Proceso finalizado']);
        } catch (\Exception $e) {
            Log::error("Error al recibir los datos: {$e->getMessage()}");
            return response()->json(['message' => "Error al recibir los datos: {$e->getMessage()}"], 500);
        } finally {
            $API->disconnect();
        }
    }
}
