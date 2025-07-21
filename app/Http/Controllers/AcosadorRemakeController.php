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
     * Obtiene los clientes que no estén en el sistema y serán suspendidos en el servidor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataToAcosadorRemakeSerSis()
    {
        try {
            $API = new api();

            $clientesMacs = array_map('strtolower', clientes::pluck('mac')->toArray());
            $servidores = servidores::select('ip', 'nombre_de_servidor')->where('active', 1)->get();
            $cadena = "";

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
                        $API->write("=comment=Regla generada por el acosador (la mac: {$mac} no se encuentra en el sistema)", true);
                        $READ = $API->read(false);

                        $cadena .= "- $mac | IP: $ip | SERVIDO: $servidor->nombre_de_servidor\n";
                     }
                }

                $API->disconnect();
            }

            // Se devuelve el resultado en formato JSON.
            return response()->json($cadena);
        } catch (\Exception $e) {
            Log::error("Error al recibir los datos: {$e->getMessage()}");
            return response()->json(['message' => "Error al recibir los datos: {$e->getMessage()}"], 500);
        }
    }

    /**
     * Obtiene los datos de los clientes que no estén correctamente almacenados y crea regla firewall.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataToAcosadorRemakeSisSer()//: \Illuminate\Http\JsonResponse
    {
        try {
            $API = new api();

            $servidores = servidores::select('ip')->where('active', 1)->get();

            $macAddresses = [];

            foreach ($servidores as $servidor) {
                try {
                    if (!$API->connect($servidor->ip, env('API_USER'), env('API_PASSWORD'))) {
                        Log::warning("No se pudo conectar al servidor {$servidor->ip}");
                        continue;
                    }

                    $API->write('/ip/dhcp-server/lease/print');
                    $READ = $API->read(false);
                    $leases = $API->parseResponse($READ);
                    
                    foreach ($leases as $lease) {
                        if (isset($lease['mac-address'])) {
                            $macAddresses[] = $lease['mac-address'];
                        }
                    }
                    
                } catch (\Exception $e) {
                    Log::error("Error en el servidor {$servidor->ip}: {$e->getMessage()}");
                } finally {
                    $API->disconnect();
                }
            }

            $macAddressesLower = array_map('strtolower', $macAddresses);
            $clientesMacs = array_map('strtolower', clientes::pluck('mac')->toArray());

            foreach ($macAddressesLower as $mac) {
                if (!in_array($mac, $clientesMacs)) {
                    echo "MAC no encontrada: {$mac}<br>";
                }
            }

            //return response()->json(['message' => 'Datos obtenidos correctamente']);
        } catch (\Exception $e) {
            Log::error("Error al recibir los datos: {$e->getMessage()}");
            return response()->json(['message' => "Error al recibir los datos: {$e->getMessage()}"], 500);
        }
    }
}
