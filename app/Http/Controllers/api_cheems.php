<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\clientes;

use Illuminate\Support\Facades\DB;

class api_cheems extends Controller
{
    public function cheems_clients(){ // Recibir todos los cientes que no esten solventes.
        $clientes = DB::select("SELECT nombre, tlf, corte, estados.estado, estados.id AS estado_id, planes.plan FROM `clientes` INNER JOIN planes ON clientes.plan_id = planes.id INNER JOIN estados ON clientes.estado = estados.id WHERE clientes.`estado` IN (2,3,4,6,7) AND `tipo_cliente` = 0 AND `congelado` = 0 AND `active` = 1 ORDER BY clientes.`id` DESC;");

        foreach($clientes as $cliente){
            $cliente->corte = date("d/m/Y", strtotime($cliente->corte));
        }

        return response()->json($clientes);
    }

    public function cheems_client($tlf){ // Recibir cliente individual por telefono.
        $cliente = DB::select("SELECT nombre, corte, estados.estado, planes.plan, deuda, motivo_deuda FROM `clientes` INNER JOIN planes ON clientes.plan_id = planes.id INNER JOIN estados ON clientes.estado = estados.id WHERE clientes.tlf = '$tlf' ORDER BY clientes.`id` DESC");
        
        foreach($cliente as $cliente){
            $cliente->corte = date("d/m/Y", strtotime($cliente->corte));
        }

        return response()->json($cliente);
    }
}