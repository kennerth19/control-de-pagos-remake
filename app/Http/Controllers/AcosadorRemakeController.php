<?php

namespace App\Http\Controllers;

use App\api\api;
use App\Models\clientes;
use App\Models\servidores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcosadorRemakeController extends Controller
{
    /**
     * Obtiene los clientes que serán suspendidos en el servidor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataToAcosadorRemake(): \Illuminate\Http\JsonResponse
    {
        try {
            $API = new api();

            $clientesMacs = array_map('strtolower', clientes::pluck('mac')->toArray());
            $servidores = servidores::select('ip')->get();

            $result = [];

            foreach ($servidores as $servidor) {
                if (!$API->connect($servidor->ip, env('API_USER'), env('API_PASSWORD'))) {
                    Log::warning("No se pudo conectar al servidor {$servidor->ip}");
                    continue;
                }

                $API->write('/ip/dhcp-server/lease/print');
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);

                foreach ($ARRAY as $dhcp) {
                    $mac = strtolower($dhcp['mac-address']);
                    $ip = $dhcp['address'];

                    if (!in_array($mac, $clientesMacs)) {

                        $API->write('/ip/firewall/address-list/add', false);
                        $API->write("=address={$ip}", false);
                        $API->write('=list=BLOCK', false);
                        $API->write("=comment=Regla generada por el acosador (la mac {$mac} no se encuentra en el sistema)", true);
                        $READ = $API->read(false);
                        
                        $result[] = [
                            'mac' => $mac,
                            'ip' => $ip,
                        ];
                    }
                }

                $API->disconnect();
            }

            // Se devuelve el resultado en formato JSON.
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error("Error al recibir los datos: {$e->getMessage()}");
            return response()->json(['message' => "Error al recibir los datos: {$e->getMessage()}"], 500);
        }
    }
}
