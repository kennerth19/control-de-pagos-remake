<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\clientes;
use App\Models\planes;
use App\Models\resumen_general;
use App\Models\servidores;
use App\Models\instalaciones;

use Carbon\Carbon;

class menu_administrativo extends Controller
{
    public function administrar(){

        $foto_de_perfil = Auth::user()->perfil;
        $perfil = "";
        
        $hoy = Carbon::now()->format('d-m-Y');

        if($foto_de_perfil != null || $foto_de_perfil != ""){
            if(Storage::exists("/public/perfil/$foto_de_perfil")){
                $perfil = "/control_de_pago_remake/public/storage/perfil/$foto_de_perfil";
            }else{
                $perfil = "/control_de_pago_remake/public/storage/perfil/sin_perfil.png";
            }
        }else{
            $perfil = "/control_de_pago_remake/public/storage/perfil/sin_perfil.png";
        }

        return view('configuracion.index', compact('hoy', 'perfil'));
    }

    public function eliminar_perfil($id){
        $usuario = User::findOrFail($id);
        $foto_de_perfil = Auth::user()->perfil;

        $perfil = $usuario->perfil;

        $borrar = Storage::delete("/public/perfil/$perfil");

        $usuario->perfil = "sin_perfil.png";
        $usuario->save();

        return back()->with('eliminado', 'ok');
    }

    public function cambiar_perfil(Request $request){ // agregar o cambiar foto de perfil (elimina totalmente la anterior)

        $usuario = User::findOrFail($request->id);

        $perfil = $usuario->perfil;

        if($perfil != null || $perfil != ""){
            if(Storage::exists("/public/perfil/$perfil")){
                $borrar = Storage::delete("/public/perfil/$perfil");
            }
        }

        $path = $request->file('foto')->store('perfil', 'public');

        $user = User::findOrFail($request->id);

        $user->perfil = substr($path, 7);

        $user->save();

        return back()->with('cambiado', 'ok');
    }

    public function getUsers($op){
        try{
            if ($op == 0) {
                $usuarios = User::where('name', '!=', Auth::user()->name)->get();
            }else{
                $usuarios = User::get();
            }

            return response()->json($usuarios, 200);
        }catch (\Exception $e) {

            return response()->json(['message' => 'Error al obtener usuarios: ' . $e->getMessage()], 500);
        }
    }

    public function actualizar_usuarios(Request $request, $id){

        try{
            $user = User::findOrFail($id);

            $original = $user->name;
            $nuevo = $request->nombre;

            $user->name = $request->nombre;

            if($request->pass != ""){
                $user->password = Hash::make($request->pass);
            }

            $user->save();

            if($original != $nuevo){
                /* Resumen General */
                $resumen = new resumen_general();

                $resumen->usuario = Auth::user()->name;
                $resumen->descripcion = "Se ha editado el nombre del usuario $original para $nuevo";
                $resumen->tipo = 36;

                /* Resumen General */
                $resumen->save();
            }

            return response()->json(['message' => 'ok'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el servidor: ' . $e->getMessage()], 500);
        }
    }

    public function crear_usuarios(Request $request){   
        try {

            $usuario = new User();

            $usuario->name = $request->nombre;
            $usuario->password = Hash::make($request->pass);

            $usuario->roles = 0;
            
            
            $usuario->save();

            return response()->json(['message' => 'ok'], 200);
        } catch (\Exception $e) {
            // Retornar error con mensaje para frontend
            return response()->json(['message' => 'Error al crear el usuario: ' . $e->getMessage()], 500);
        }
    }

    public function usuarios(){
        $usuarios = DB::select("SELECT `id`,`name`,`roles`,`grupo` FROM `users` WHERE 1;");

        return response()->json($usuarios);
    }

    public function planes(){ // devuelve todos los planes.
        $planes = DB::select("SELECT * FROM `planes` WHERE 1;");

        return response()->json($planes);
    }

    public function plan($id){ // devuelve un plan individual por id.
        $planes = DB::select("SELECT * FROM `planes` WHERE `id` = $id");

        return response()->json($planes[0]);
    }

    public function servidores(){
        $servidores = DB::select("SELECT * FROM `servidores` WHERE 1;");

        return response()->json($servidores);
    }

    public function servidor($id){ // devuelve un servidor individual por id.
        $servidores = DB::select("SELECT * FROM `servidores` WHERE `id` = $id");

        return response()->json($servidores[0]);
    }

    public function editar_plan(Request $request){

        $plan = planes::findOrFail($request->id);

        $nombre = $plan->plan;
        $valor = $plan->valor;
        $tipo = $plan->tipo;

        $cambio = "el siguiente dato:";
        $datos_cambiados = "";

        $edicion = "";
        $cambios = 0;

        if($plan->plan != $request->plan){
            $plan->plan = $request->plan;
            $cambios += 1;
            $datos_cambiados .= "- el nombre: $nombre por $request->plan<br>";
        }

        if($plan->valor != $request->valor){
            $plan->valor = $request->valor;
            $cambios += 1;
            $datos_cambiados .= "- el valor: $valor$ por $request->valor$<br>";
        }

        if($plan->tipo != $request->tipo){
            $plan->tipo = $request->tipo;
            $cambios += 1;
            $datos_cambiados .= "- el tipo de plan: $tipo por $request->tipo<br>";
        }

        if($cambios > 1){
            $cambio = "los siguientes datos:";
        }
       
        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se ha editado del plan $nombre $cambio<br><br>$datos_cambiados";
        $resumen->tipo = 27;

        /* Resumen General */

        $resumen->save();
        $plan->save();

        return response()->json(['enviado' => 'ok']);
    }

    public function crear_plan(Request $request){

        $plan = new planes();

        $plan->plan = $request->plan;
        $plan->valor = $request->valor;
        $plan->tipo = $request->tipo;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se ha creado el plan $request->plan (valor: $request->valor)";
        $resumen->tipo = 28;

        /* Resumen General */

        $resumen->save();
        $plan->save();

        return response()->json(['enviado' => 'ok']);
    }

    public function nodos(){

        $nodos = DB::select("SELECT `id`, `nombre`, `ip`, `mac` FROM `clientes` WHERE `tipo_cliente` = 3;");

        return response()->json($nodos);
    }

    public function crear_servidor(Request $request){
        $servidores = new servidores();

        $servidores->nombre_de_servidor = $request->servidor;
        $servidores->ip = $request->ip;

        $servidores->puerto = 8727;
        $servidores->active = 0;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "Se ha creado el servidor $request->servidor (Dirección IP: $request->ip)";
        $resumen->tipo = 29;

        /* Resumen General */

        $resumen->save();
        $servidores->save();

        return response()->json(['enviado' => 'ok']);
    }

    public function crear_instalacion(Request $request){
        try {
            $instalacion = new instalaciones();

            $instalacion->router = $request->instalacion;
            $instalacion->valor = $request->valor;
            $instalacion->categoria = $request->categoria;

            /* Resumen General */
            $resumen = new resumen_general();

            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "Se ha creado una nueva instalación $request->instalacion";
            $resumen->tipo = 33;

            /* Resumen General */

            $resumen->save();
            $instalacion->save();

            return response()->json(['enviado' => 'ok']);
        } catch (\Exception $e) {
            // Retornar error con mensaje para frontend
            return response()->json(['message' => 'Error crear la instalación: ' . $e->getMessage()], 500);
        }
    }

    public function editar_servidor(Request $request) {
        try {
            $servidor = servidores::findOrFail($request->id);

            // Guardar valores originales para comparación y mensajes
            $nombre_original = $servidor->nombre_de_servidor;
            $ip_original = $servidor->ip;
            $puerto_original = $servidor->puerto;
            $active_original = $servidor->active;

            $cambio = "el siguiente dato:";
            $datos_cambiados = "";
            $cambios = 0;

            if ($nombre_original != $request->nombre_de_servidor) {
                $servidor->nombre_de_servidor = $request->nombre_de_servidor;
                $cambios++;
                $datos_cambiados .= "- el nombre: $nombre_original por $request->nombre_de_servidor<br>";
            }

            if ($ip_original != $request->ip) {
                $servidor->ip = $request->ip;
                $cambios++;
                $datos_cambiados .= "- La dirección IP: $ip_original por $request->ip<br>";
            }

            if ($puerto_original != $request->puerto) {
                $servidor->puerto = $request->puerto;
                $cambios++;
                $datos_cambiados .= "- El puerto: $puerto_original por $request->puerto<br>";
            }

            if ($active_original != $request->active) {
                $servidor->active = $request->active;
                $cambios++;
                $datos_cambiados .= "- El estado activo: $active_original por $request->active<br>";
            }

            if ($cambios > 1) {
                $cambio = "los siguientes datos:";
            }

            // Guardar resumen general
            $resumen = new resumen_general();
            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "Se ha editado del servidor $nombre_original $cambio<br><br>$datos_cambiados";
            $resumen->tipo = 30;

            $resumen->save();
            $servidor->save();

            return response()->json(['enviado' => 'ok']);

        } catch (\Exception $e) {
            // Retornar error con mensaje para frontend
            return response()->json(['message' => 'Error al editar servidor: ' . $e->getMessage()], 500);
        }
    }

    public function instalacion($id){
        $instalacion = DB::select("SELECT * FROM `instalaciones` WHERE `id` = $id");

        return response()->json($instalacion[0]);
    }

    public function editar_instalacion(Request $request) {
        try {
            $instalacion = instalaciones::findOrFail($request->id);

            // Guardar valores originales para comparación y mensajes
            $nombre_original = $instalacion->router;
            $valor_original = $instalacion->valor;
            $categoria_original = $instalacion->categoria;
            $active_original = $instalacion->active;

            $cambio = "el siguiente dato:";
            $datos_cambiados = "";
            $cambios = 0;

            if ($nombre_original != $request->instalacion) {
                $instalacion->router = $request->instalacion;
                $cambios++;
                $datos_cambiados .= "- el nombre: $nombre_original por $request->instalacion<br>";
            }

            if ($valor_original != $request->valor) {
                $instalacion->valor = $request->valor;
                $cambios++;
                $datos_cambiados .= "- El valor: $valor_original$ por $request->valor$<br>";
            }

            $categorias = [
                0 => "Sencilla",
                1 => "Intermedia",
                2 => "Avanzada"
            ];          

            if ($categoria_original != $request->categoria) {
                $instalacion->categoria = $request->categoria;
                $cambios++;
                // Obtenemos el texto correspondiente a cada valor, si no existe, mostramos el valor original
                $categoria_original_texto = isset($categorias[$categoria_original]) ? $categorias[$categoria_original] : $categoria_original;
                $categoria_nueva_texto = isset($categorias[$request->categoria]) ? $categorias[$request->categoria] : $request->categoria;

                $datos_cambiados .= "- La Categoría: $categoria_original_texto por $categoria_nueva_texto<br>";
            }

            $estados = [
                0 => 'No',
                1 => 'Si'
            ];

            if ($active_original != $request->active) {
                $instalacion->active = $request->active;
                $cambios++;

                $texto_active = $estados[$active_original];
                $text_request = $estados[$request->active];

                $datos_cambiados .= "- El estado activo: $texto_active por $text_request<br>";
            }

            if ($cambios > 1) {
                $cambio = "los siguientes datos:";
            }

            if ($cambios > 0) {
                // Guardar resumen general
                $resumen = new resumen_general();
                $resumen->usuario = Auth::user()->name;
                $resumen->descripcion = "Se ha editado la instalacion $nombre_original $cambio<br><br>$datos_cambiados";
                $resumen->tipo = 35;

                $resumen->save();
            }
            
            $instalacion->save();

            return response()->json(['enviado' => 'ok']);

        } catch (\Exception $e) {
            // Retornar error con mensaje para frontend
            return response()->json(['message' => 'Error al editar la instalacion: ' . $e->getMessage()], 500);
        }
    }

    public function retornarCantidad($id){
        $datos = [];

        $cantidad = DB::select("SELECT COUNT(*) cantidad FROM `clientes` WHERE `plan_id` = $id;");
        $planes = DB::select("SELECT `id`, `plan` FROM `planes` WHERE `id` NOT IN ($id);");

        $datos[0] = $cantidad[0];
        $datos[1] = $planes;

        return response()->json($datos);
    }

    public function eliminar_plan(Request $request){

        try {

            if($request->op == 0){ // Cuando el plan tiene clientes y se necesita reasignar.

                DB::update("UPDATE clientes SET plan_id = ? WHERE plan_id = ?", [$request->nuevo_plan, $request->id]);
            }

            $plan = planes::findOrFail($request->id);
            
            // Guardar resumen general
            $resumen = new resumen_general();
            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "Se ha eliminado el plan $plan->plan.";
            $resumen->tipo = 30;

            $plan->delete();
            $resumen->save();

            return response()->json(['enviado' => 'ok']);

        } catch (\Exception $e) {
            
            return response()->json(['message' => 'Error al eliminar el plan: ' . $e->getMessage()], 500);
        }
    }

    public function retornarCantidadServidor($id)
    {
        try {
            $id = intval($id);

            $cantidad = clientes::where('servidor', $id)->count();

            $servidores = servidores::where('id', '<>', $id)->select('id', 'nombre_de_servidor')->get();

            return response()->json(['cantidad' => $cantidad, 'servidores' => $servidores,]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recibir los datos: ' . $e->getMessage()], 500);
        }
    }

    public function eliminar_servidor(Request $request){

        try {

            if($request->op == 0){ // Cuando el servidor tiene clientes y se necesita reasignar.

                DB::update("UPDATE clientes SET servidor = ? WHERE servidor = ?", [$request->nuevo_servidor, $request->id]);
            }

            $servidor = servidores::findOrFail($request->id);
            
            // Guardar resumen general
            $resumen = new resumen_general();
            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "Se ha eliminado el servidor $servidor->nombre_de_servidor.";
            $resumen->tipo = 32;

            $servidor->delete();
            $resumen->save();

            return response()->json(['enviado' => 'ok']);

        } catch (\Exception $e) {
            
            return response()->json(['message' => 'Error al eliminar el servidor: ' . $e->getMessage()], 500);
        }
    }

    public function instalaciones(){

        $instalaciones = DB::select("SELECT * FROM `instalaciones`;");

        return response()->json($instalaciones);
    }

    public function eliminar_instalacion($id)
    {
        // Lógica para eliminar la instalación
        $instalacion = instalaciones::find($id);

        if (!$instalacion) {
            return response()->json(['error' => 'Instalación no encontrada'], 404);
        }

        /* Resumen General */
            $resumen = new resumen_general();

            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "Se ha eliminado la instalación $instalacion->router";
            $resumen->tipo = 34;
        /* Resumen General */

        $resumen->save();
        $instalacion->delete();

        return response()->json(['success' => true]);
    }
}