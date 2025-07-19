<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use App\Models\clientes;
use App\Models\sort;
use App\Models\planes;

class TelegramController extends Controller
{

    public function getDataToSendMessages(){
        try{

            $clientes = clientes::select("id")
                ->whereNotNull('telegramId')
                ->where('estado', '!=', 1)
                //->where('tipo_cliente', '=', 0)
                ->get();

            return response()->json($clientes);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al enviar la data'], 500);
        }
    }

    public function ProceedToSendMessages($id){

        try{
            sleep(2);

            $sort = sort::findOrFail(1)->toArray();
            $tasa = $sort["tasa"];

            $token = env('TELEGRAM_BOT_TOKEN');

            $ch = curl_init();

            $url = "https://api.telegram.org/bot$token/sendMessage";

            $clienteDeTurno = clientes::find($id);
            $planDeTurno = planes::find($clienteDeTurno->plan_id);
            $fecha = date('d-m-Y', strtotime($clienteDeTurno->corte));
            $restan = "";

            if($clienteDeTurno->estado == 2){ // restan 2 días
                $restan = "Le informamos que faltan 5 días para la suspensión de su servicio.\n\n";
            }else if($clienteDeTurno->estado == 3){ // resta un día
                $restan = "Le recordamos que queda 4 día para la suspensión de su servicio.\n\n";
            }else if($clienteDeTurno->estado == 4){ // dia de corte (comienzan los 3 Dias de prorroga)
                $restan = "Le recordamos que queda 3 día para la suspensión de su servicio.\n\n";
            }else if($clienteDeTurno->estado == 6){ // prórroga 1
                $restan = "Su servicio será suspendido en 2 días. Por favor, tome las precauciones necesarias.\n\n";
            }else if($clienteDeTurno->estado == 7){ // prórroga 2
                $restan = "Su servicio será suspendido mañana. Le recomendamos cancelar su mensualidad para evitar el corte del mismo.\n\n";
            }

            $totalACancelar = $planDeTurno->valor - $clienteDeTurno->almacen;
            $totalAlCambio = $totalACancelar * $tasa;

            $total = "VALOR TOTAL DEL PLAN: REF $planDeTurno->valor\n\nTOTAL ABONADO: REF $clienteDeTurno->almacen\n\nTOTAL A CANCELAR: REF $totalACancelar ($totalAlCambio Bs.)";

            $mensaje = "Estimado/a $clienteDeTurno->nombre,\n\n"
            . "Esperamos que se encuentre bien. Queremos informarle que la fecha de corte de su servicio 'PLAN $planDeTurno->plan' es el día $fecha.\n\n"
            . "$total\n\n"
            . "$restan"
            . "Si tiene alguna consulta o necesita asistencia, no dude en contactarnos.\n\n"
            . "Numero de cobranza: 04244710322\n\n"
            . "Numero de soporte: 04144404023\n\n"
            . "¡Gracias por confiar en nosotros!\n";

            // Inicializar cURL
            $ch = curl_init();

            $data = [
                'chat_id' => $clienteDeTurno->telegramId,
                'text' => $mensaje,
            ];

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Ejecutar la petición
            $response = curl_exec($ch);

            // Cerrar cURL
            curl_close($ch);

            return response()->json(['message' => 'Mensaje enviado correctamente']);
         } catch (\Exception $e) {
            return response()->json(['message' => 'Error al enviar mensaje al cliente'], 500);
        }
    }

    public function updateId(Request $request){

        try{

            $telegramId = $request->usuario_id;
            $cedula = $request->numero;

            DB::update("UPDATE clientes SET telegramId = ? WHERE cedula = ?", [$telegramId, $cedula]);

            return response()->json(['message' => 'Datos recibidos correctamente',]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recibir los datos del cliente'], 500);
        }
    }
}
