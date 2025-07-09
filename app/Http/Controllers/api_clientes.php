<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\clientes;
use App\Models\planes;
use App\Models\servicios;
use App\Models\pago_resumen;

class api_clientes extends Controller
{
    public function get_c($id){ // función para devolver un cliente individual por ID. 
        $clientes = DB::select("SELECT clientes.id, clientes.nombre, clientes.cedula, clientes.correo, clientes.direccion, clientes.estado, clientes.tlf, clientes.ip, clientes.dia, clientes.corte, clientes.active, clientes.almacen, clientes.deuda, clientes.motivo_deuda, clientes.mac, clientes.prorroga_hasta, clientes.tipo_cliente, clientes.iptv, clientes.congelado, clientes.congelar, plan, planes.valor, estados.estado FROM clientes INNER JOIN planes ON clientes.plan_id = planes.id INNER JOIN estados ON clientes.estado = estados.id WHERE clientes.id = $id");
        
        return response()->json($clientes[0] ?? (object)[]);
    }

    public function get_f($id){ // función para devolver los pagos realizados por el cliente.
        $facturacion = DB::select("SELECT * FROM `pago_resumen` WHERE `id_cliente` = $id ORDER BY `servicio` DESC");

        return response()->json($facturacion);
    }

    public function servicio_get($id){ // función para devolver los servicios pertenecientes a ese cliente (incluye al cliente).
        $servicios = DB::select("SELECT clientes.id, clientes.nombre, clientes.deuda, planes.valor FROM `clientes` INNER JOIN planes ON clientes.plan_id = planes.id WHERE `servicio_id` = $id ORDER BY `id` ASC");

        return response()->json($servicios);
    }

    public function servicio_get_i($id){ // función para devolver los servicios pertenecientes a ese cliente (incluye al cliente).
        $servicios = DB::select("SELECT clientes.id, clientes.nombre, clientes.deuda, planes.valor FROM `clientes` INNER JOIN planes ON clientes.plan_id = planes.id WHERE clientes.id = $id");

        return response()->json($servicios);
    }

    public function acosados_ping(){
        $acosados_ping = DB::select("SELECT `id`,`nombre`,`servidor`,`ip`FROM `clientes` WHERE `estado` = 5 AND `prorroga` = 0 AND `tipo_cliente` = 0 ORDER BY `id` ASC;");

        return response()->json($acosados_ping);
    }   
}