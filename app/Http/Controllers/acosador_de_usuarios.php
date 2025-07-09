<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\api\api;

use App\Models\acosador;
use App\Models\clientes;
use App\Models\acosador_log;

use Carbon\Carbon;

class acosador_de_usuarios extends Controller
{
    public function acosador()
    { //Sistema - cliente
        //ACOSADOR PARTE 1: RECOPILAR DATOS DE LOS SERVIDORES.

        DB::select('TRUNCATE TABLE `control_de_pagos`.`acosador`');

        $routers = DB::select("SELECT `id`,`nombre_de_servidor`,`ip` FROM `servidores` WHERE `active` = 1");

        $API = new api();
        $API->debug = false;

        foreach ($routers as $router) { // Ciclo repetitivo para guardar todo el DHCP de todos los servidores en la tabla 'acosador'.
            if ($API->connect($router->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {

                $API->write("/ip/dhcp-server/lease/print");
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);

                foreach ($ARRAY as $dhcp) {
                    $acosador = new acosador;

                    $acosador->ip = $dhcp['address'];
                    $acosador->mac = $dhcp['mac-address'];
                    $acosador->servidor_id = $router->id;
                    $acosador->servidor = $router->nombre_de_servidor;
                    $acosador->status = $dhcp['status'];
                    $acosador->disabled = $dhcp['disabled'];

                    if (!empty($dhcp['comment'])) {
                        $acosador->comentario = $dhcp['comment'];
                    } else {
                        $acosador->comentario = 'Sin comentario';
                    }

                    $acosador->save();
                }

                $API->disconnect();
            }
        }

        //ACOSADOR PARTE 2: COMPARACIÓN Y MODIFICACIÓN SISTEMA > SERVIDOR.

        $clientes = DB::select('SELECT clientes.id, clientes.nombre, clientes.ip, clientes.mac, servidores.id AS id_del_servidor, servidores.nombre_de_servidor FROM clientes INNER JOIN servidores ON clientes.servidor = servidores.id;');
        $log = acosador::select('*')->where('disabled', '=', 'false')->get();
        $contador = 1;

        foreach ($clientes as $cliente) {
            foreach ($log as $logs) {
                if (strtolower($cliente->mac) == strtolower($logs->mac)) {

                    $texto = "<br><br>$contador - REGISTRO CON MAC CORRECTA $cliente->nombre ($cliente->mac | $cliente->nombre_de_servidor $logs->status)<br>";
                    $texto_ip = "";
                    $texto_servidor = "";
                    $texto_log_ip = "";
                    $texto_log_servidor = "";

                    if ($cliente->ip != $logs->ip) {
                        $texto_ip = "<br><span style='color: red;'>- (IP'S DIFERENTES $cliente->ip | $logs->ip)<br></span>";
                        $texto_log_ip = "(Ip cambiada ($cliente->ip | $logs->ip))";

                        if ($logs->status == 'bound') {
                            $cambios = clientes::find($cliente->id);

                            if ($cambios) {
                                $cambios->ip = $logs->ip;
                            }

                            //acosador log
                            $reporte = new acosador_log();
                            $reporte->resultado = "Cambios del cliente: $cliente->nombre $texto_log_ip";
                            $reporte->categoria = 1;

                            $reporte->save();
                            $cambios->save();
                        }
                    }

                    if ($cliente->id_del_servidor != $logs->servidor_id) {
                        $texto_servidor = "<br><span style='color: red;'>- (SERVIDORES DIFERENTES $cliente->nombre_de_servidor | $logs->servidor)</span><br>";
                        $texto_log_servidor = "(Servidor cambiado ($cliente->nombre_de_servidor | $logs->servidor))";

                        if ($logs->status == 'bound') {
                            $cambios = clientes::find($cliente->id);

                            if ($cambios) {
                                $cambios->servidor = $logs->servidor_id;
                            }

                            //acosador log
                            $reporte = new acosador_log();
                            $reporte->resultado = "Cambios del cliente: $cliente->nombre $texto_log_servidor";
                            $reporte->categoria = 1;

                            $reporte->save();
                            $cambios->save();
                        }
                    }

                    echo "$texto $texto_ip $texto_servidor";

                    $contador++;
                }
            }
        }
    }

    public function acosador_ser_sis()
    { // Servidor - sistema

        //ACOSADOR PARTE 1: RECOPILAR DATOS DE LOS SERVIDORES.
        DB::select('TRUNCATE TABLE `control_de_pagos`.`acosador`');

        $routers = DB::select("SELECT `id`,`nombre_de_servidor`,`ip` FROM `servidores` WHERE `active` = 1");

        $API = new api();
        $API->debug = false;

        foreach ($routers as $router) { // Ciclo repetitivo para guardar todo el DHCP de todos los servidores en la tabla 'acosador'.
            if ($API->connect($router->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {

                $API->write("/ip/dhcp-server/lease/print");
                $READ = $API->read(false);
                $ARRAY = $API->parseResponse($READ);

                foreach ($ARRAY as $dhcp) {
                    
                    if (!isset($dhcp['disabled']) || $dhcp['disabled'] === 'false') {
                        $acosador = new acosador;
    
                        $acosador->ip = $dhcp['address'];
                        $acosador->mac = $dhcp['mac-address'];
                        $acosador->servidor_id = $router->id;
                        $acosador->servidor = $router->nombre_de_servidor;
                        $acosador->status = $dhcp['status'];
                        $acosador->disabled = $dhcp['disabled'];
    
                        if (!empty($dhcp['comment'])) {
                            $acosador->comentario = $dhcp['comment'];
                        } else {
                            $acosador->comentario = 'Sin comentario';
                        }
    
                        $acosador->save();
                    }
                }

                $API->disconnect();
            }
        }

        //ACOSADOR PARTE 2: COMPARACIÓN SERVIDOR > SISTEMA.

        $clientes = DB::select('SELECT clientes.id, clientes.nombre, clientes.ip, clientes.mac, servidores.id AS id_del_servidor, servidores.nombre_de_servidor FROM clientes INNER JOIN servidores ON clientes.servidor = servidores.id;');
        $log = acosador::select('*')->get();
        $contador = 1;
        $contador_no_encontrado = 0;

        foreach ($log as $logs) {
            foreach ($clientes as $cliente) {
                if (strtolower($logs->mac) != strtolower($cliente->mac)) {
                    $contador_no_encontrado++;
                }
            }

            if ($contador_no_encontrado == clientes::count()) {
                echo "$contador: LA MAC $logs->mac NO FUE ENCONTRADA EN EL SISTEMA.<br>
                - IP: $logs->ip<br>
                - SERVIDOR: $logs->servidor<br>
                - ESTADO: $logs->status<br>
                - DISABLED: $logs->disabled<br>
                - COMENTARIO: $logs->comentario<br><br>";
                $contador++;
            }

            $contador_no_encontrado = 0;
        }
    }

    public function acosador_data()
    {
        $log = acosador_log::select('*')->get();

        return response()->json($log);
    }

    public function acosador_log()
    {
        return view('modulo_de_cortes.acosador');
    }
}