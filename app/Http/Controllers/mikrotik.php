<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\clientes;
use App\Models\servidores;
use App\Models\resumen_general;

use App\api\api;

use Carbon\Carbon;

date_default_timezone_set('America/Caracas');

class mikrotik extends Controller
{
    public function index()
    {
        return view('modulo_de_cortes.modulo_de_cortes');
    }

    public function datos_corte($op)
    {

        $columnas = "clientes.id, clientes.nombre, clientes.ip, clientes.corte, clientes.estado, clientes.congelado, clientes.active, clientes.almacen, clientes.tipo_cliente, clientes.iptv, clientes.prorroga_hasta, servidores.nombre_de_servidor";

        //Búsqueda separada por categorías.

        if ($op == 0) { // para todos los clientes.
            $where = " (`estado` = 5 OR `congelado` = 1 OR `prorroga` = 1 OR `tipo_cliente` > 0 OR `clientes`.`active` = 0) ";
        } else if ($op == 1) { // para clientes suspendidos.
            $where = " `estado` = 5 ";
        } else if ($op == 2) { // clientes congelados.
            $where = " `congelado` = 1 ";
        } else if ($op == 3) { // clientes desactivados.
            $where = " `active` = 0 ";
        } else if ($op == 4) { // con prorroga activa.
            $where = " `prorroga` = 1  ";
        } else if ($op == 5) { // premium.
            $where = " `tipo_cliente` = 1 ";
        } else if ($op == 6) { // donación.
            $where = " `tipo_cliente` = 2 ";
        } else if ($op == 7) { // IPTV.
            $where = "`iptv` = 1";
        }

        if($op != 8){
            $consulta_armada = DB::select("SELECT $columnas FROM `clientes` INNER JOIN servidores ON clientes.servidor = servidores.id WHERE $where ORDER BY `id` DESC");
        }else{
            $columnas = "clientes.id, clientes.nombre, clientes.ip, clientes.corte, clientes.estado, clientes.congelado, clientes.active, clientes.almacen, clientes.tipo_cliente, clientes.iptv, clientes.prorroga_hasta, planes.plan, planes.valor,estados.estado, estados.color, servidores.nombre_de_servidor";
            $consulta_armada = DB::select("SELECT $columnas FROM `clientes` INNER JOIN servidores ON clientes.servidor = servidores.id INNER JOIN planes ON clientes.plan_id = planes.id INNER JOIN estados ON clientes.estado = estados.id ORDER BY `clientes`.`almacen` DESC");
        }

        return response()->json($consulta_armada);
    }

    public function realizar_cortes()
    {
        $API = new api();
        $hoy = date('Y-m-d', strtotime(Carbon::now()));
        $ult = clientes::orderBy('id', 'desc')->first()->id;
        $contador = 1;
        $contador_bd = 1;

        echo "$hoy <br><br>";

        for ($i = 1; $ult >= $i; $i++) { // función para actualizar los estados en el modulo de corte
            $cliente_de_turno = clientes::find($i);

            if ($cliente_de_turno) {

                if ($cliente_de_turno->corte == $hoy) { // dia de corte
                    $cliente_de_turno->estado = 4;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte + 1 day")) == $hoy) { // prorroga dia 1
                    $cliente_de_turno->estado = 6;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte + 2 day")) == $hoy) { // prorroga dia 2
                    $cliente_de_turno->estado = 7;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte - 1 day")) == $hoy) { // resta 1 dia
                    $cliente_de_turno->estado = 3;
                } else if (date('Y-m-d', strtotime("$cliente_de_turno->corte - 2 day")) == $hoy) { // restan 2 días
                    $cliente_de_turno->estado = 2;
                } else if ($cliente_de_turno->corte >= date('Y-m-d', strtotime("$hoy + 3 day"))) { // solvente
                    $cliente_de_turno->estado = 1;
                } else if ($cliente_de_turno->corte >= date('Y-m-d', strtotime("$hoy - day"))) { // suspendido
                    $cliente_de_turno->estado = 5;
                }

                if ($cliente_de_turno->prorroga_hasta < $hoy && $cliente_de_turno->prorroga_hasta != null) {
                    $cliente_de_turno->prorroga = 0;
                    $cliente_de_turno->prorroga_hasta = null;
                    $cliente_de_turno->nota_prorroga = null;

                    /* Resumen General */
                    $resumen = new resumen_general();

                    $resumen->usuario = Auth::user()->name;
                    $resumen->descripcion = "La prorroga del cliente $cliente_de_turno->nombre fue eliminada automáticamente.";
                    $resumen->tipo = 18;

                    /* Resumen General */

                    $resumen->save();
                }

                $cliente_de_turno->save();
            }
        }

        $servidores = DB::select("SELECT `id`,`nombre_de_servidor`,`ip` FROM `servidores` WHERE `active` = 1");
        $clientes_por_cortar = DB::select("SELECT id, nombre, corte, estado, ip, servidor, congelado, active, prorroga, tipo_cliente, conducta FROM `clientes` ORDER BY `servidor` ASC;");

        echo "verificar quien cortar:<br><br>";

        $contador_de_corte = 0;
        $texto = "";

        foreach ($servidores as $servidor) { // ciclo de servidores.

            foreach ($clientes_por_cortar as $cortar) { // ciclo de corte.

                //OJO IMPORTANTE: DESACTIVAR ESTE FILTRO CUANDO SE AGREGUEN LOS EDIFICIOS MILITARES!.
                if ($cortar->ip != "" && $cortar->ip != "0.0.0.0" && $cortar->ip != null && $cortar->servidor != 8) { // filtro de comodines de ip.

                    if ($servidor->id == $cortar->servidor) { // verificar a que servidor pertenece el cliente.

                        if ($API->connect($servidor->ip, 'api-pwt-admin', '@p1pwt@dm1n2024')) {

                            if ($cortar->prorroga != 1 || $cortar->tipo_cliente != 0) {

                                if ($cortar->estado == 5) {
                                    $contador_de_corte++;
                                    $texto .= "Cortado por fecha ";
                                }

                                if ($cortar->congelado == 1) {
                                    $contador_de_corte++;
                                    $texto .= "Cortado por estar congelado ";
                                }

                                if($cortar->conducta == 1){
                                    if ($cortar->estado == 4 || $cortar->estado == 6 || $cortar->estado == 7) {
                                        $contador_de_corte++;
                                        $texto .= "Cortado por fecha (y por mala conducta) ";
                                    }  
                                }

                                if ($cortar->congelado == 1) {
                                    $contador_de_corte++;
                                    $texto .= "Cortado por estar congelado ";
                                }

                                if ($cortar->active == 0) {
                                    $contador_de_corte++;
                                    $texto .= "Cortado por inactivo";
                                }

                                if ($cortar->tipo_cliente != 0) {
                                    $contador_de_corte = $contador_de_corte - 20;
                                }

                                if ($contador_de_corte > 0) {
                                    echo "$contador - Cortado: $cortar->nombre: $contador_de_corte ($texto) - regla generada en el servidor: $servidor->nombre_de_servidor.<br>";
                                    $contador +=1;
                                    $API->write("/ip/firewall/address-list/add", false);
                                    $API->write('=address=' . $cortar->ip, false);   // IP
                                    $API->write('=list=BLOCK', false);       // lista
                                    $API->write('=comment=' . strtoupper($cortar->nombre . ' dia de corte ' . $cortar->corte), true);  // comentario
                                    $READ = $API->read(false);
                                    $cortar->server_active = 0;
                                } else {
                                    $API->write("/ip/firewall/address-list/getall", false);
                                    $API->write('?address=' . $cortar->ip, false);
                                    $API->write('?list=BLOCK', true);
                                    $READ = $API->read(false);
                                    $ARRAY = $API->parseResponse($READ);

                                    if (count($ARRAY) > 0) {

                                        $ID = $ARRAY[0]['.id'];
                                        $API->write('/ip/firewall/address-list/remove', false);
                                        $API->write('=.id=' . $ID, true);
                                        $READ = $API->read(false);
                                    }

                                    echo "$contador - Sin cortar: $cortar->nombre ($servidor->nombre_de_servidor)<br>";
                                    $contador +=1;
                                }

                                $contador_de_corte = 0;
                                $texto = "";
                            } else {
                                echo "$contador - Sin cortar: $cortar->nombre (PREMIUM, DONACIÓN, NODO O PRORROGA ACTIVA) ($servidor->nombre_de_servidor)<br>";
                                $contador +=1;
                                $API->write("/ip/firewall/address-list/getall", false);
                                $API->write('?address=' . $cortar->ip, false);
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
                        }
                    }
                }
            }
        }

        $API->disconnect();
    }
}