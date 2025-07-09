<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\servidores;
use App\Models\estados;
use App\Models\sectores;

use Illuminate\Support\Facades\DB;

class difusion extends Controller
{
    public function difusion(){

        return view('cheems/difusion');
    }

    public function get_data_difusion(){

        $data = [];

        $data[0] = DB::select('SELECT `id`, `estado` FROM estados');
        $data[1] = DB::select('SELECT `id`, `sector` FROM sectores');
        $data[2] = DB::select('SELECT `s`.`id`, `nombre_de_servidor`, COUNT(`servidor`) AS cantidad FROM clientes c JOIN servidores s ON servidor = s.id GROUP BY s.nombre_de_servidor ORDER BY `c`.`servidor` ASC');

        return response()->json($data);
    }

    public function verificar_filtro(Request $request){

        $where = "1";
        $deuda = "";
        $activo = "";
        $iptv = "";

        if($request->todos == 0 ){
            $where = " `servidor` IN ($request->servidores) AND `estado` IN ($request->estados) ";

            if($request->deudor != 2){
                $deuda = $request->deudor == 0 ? "AND `deuda` = 0" : "AND `deuda` > 0";
            }
    
            if($request->activo != 2){
                $activo = $request->activo == 0 ? "AND `active` = 0" : "AND `active` = 1";
            }
    
            if($request->iptv != 2){
                $iptv = $request->iptv == 0 ? "AND `iptv` = 0" : "AND `iptv` = 1";
            }
            
            //$response = DB::select("SELECT `id`, `nombre`, `tlf`, `servidor`, `corte`, `estado`, `deuda`, `iptv` FROM `clientes` WHERE $where $deuda $activo $iptv ORDER BY `id` ASC;");
            $response = DB::select("SELECT `id`, `nombre`, `tlf`, `servidor`, `corte`, `estado`, `deuda`, `iptv` FROM `clientes` WHERE id IN(2385, 2569, 2675, 2565, 2493, 2527, 2474, 2549, 2495, 2500, 2564, 2472, 2546, 2552, 2471, 2503, 2498) ORDER BY `id` ASC;");
        }else{
            //$response = DB::select("SELECT `id`, `nombre`, `tlf`, `servidor`, `corte`, `estado`, `deuda`, `iptv` FROM `clientes` ORDER BY `id` ASC;");

            $response = DB::select("SELECT `id`, `nombre`, `tlf`, `servidor`, `corte`, `estado`, `deuda`, `iptv` FROM `clientes` WHERE id IN(2385, 2569, 2675, 2565, 2493, 2527, 2474, 2549, 2495, 2500, 2564, 2472, 2546, 2552, 2471, 2503, 2498) ORDER BY `id` ASC;");
        }

        $data = [
            "mensaje" => $request->mensaje,
            "usuarios" => $response,
        ];

        // La URL a la que deseas enviar el POST
        $url = "http://localhost:3000/difusion";

        // Convertir los datos a JSON
        $jsonData = json_encode($data);

        // Inicializar cURL
        $ch = curl_init($url);

        // Configurar opciones de cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Para recibir la respuesta como string
        curl_setopt($ch, CURLOPT_POST, true); // Indicar que es una solicitud POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Establecer el tipo de contenido
            'Content-Length: ' . strlen($jsonData) // Longitud del contenido
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Agregar los datos JSON

        // Ejecutar la solicitud y almacenar la respuesta
        $response = curl_exec($ch);

        return $response;
    }
}