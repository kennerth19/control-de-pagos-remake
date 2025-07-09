<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

use App\Models\inventarios;
use App\Models\existencias;
use App\Models\inventario_log;
use App\Models\resumen_general;

class inventario extends Controller
{
    public function index(){ // Vista del inventario.

        $usuarios = DB::select("SELECT `name` FROM `users` WHERE 1;");
        $cantidadCategoria = DB::select("SELECT COUNT(*) AS cantidad FROM `inventarios`;");
        $cantidadExistencias = DB::select("SELECT COUNT(*) AS existencias FROM `existencias`;");

        $totalCategoria = $cantidadCategoria[0]->cantidad;
        $totalExistencias = $cantidadExistencias[0]->existencias;
        
        return view('inventario.inventario', compact('usuarios','totalCategoria', 'totalExistencias'));
    }

    public function get_inv(){ // Datos estructurados del inventario obtenidos de la BD para obtener por fetch.

        $inventario = DB::select("SELECT inventarios.*, COUNT(existencias.id) AS unidades FROM inventarios LEFT JOIN existencias ON existencias.categoria_id = inventarios.id GROUP BY inventarios.id;");

        return response()->json($inventario);
    }

    public function get_cat($id){ //Obtener categoría por ID.
        $categoria = DB::select("SELECT * FROM `inventarios` WHERE `id` = $id");

        return response()->json($categoria[0]);
    }

    public function store(Request $request){ // crear categoría
    
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,gif|max:20480', // Máximo 20 MB
        ]);

        $imagen = $request->file('imagen');

        // Comprimir imagen si supera 20 MB
        if ($imagen->getSize() > 20000000) {
            $img = Image::make($imagen->getRealPath());
            $img->save(null, 80); // Reducir calidad a 80%
            // Si aún supera 20 MB, puedes implementar un bucle para reducir más la calidad
        }

        // Generar nombre único para el archivo
        $nombreArchivo = time() . '.' . $imagen->getClientOriginalExtension();

        // Guardar la imagen en el sistema de archivos
        $ruta = Storage::putFileAs('public/categoria', $imagen, $nombreArchivo);

        // Guardar la ruta en la base de datos (sin 'public/')
        $rutaEnBD = str_replace('public/', '', $ruta); // Ejemplo: 'img/1234567890.jpg'

        $nueva_categoria = new inventarios();

        $nueva_categoria->producto = $request->nombre;
        $nueva_categoria->tipo = $request->tipo;
        $nueva_categoria->pic = $rutaEnBD;

        $nueva_categoria->save();

        /* Area log */
        $inv_log = new inventario_log();

        $inv_log->usuario = Auth::user()->name;
        $inv_log->evento = "Se agrego la categoría $request->nombre";
        $inv_log->tipo = 1;

        $inv_log->save();
        /* Area log */

        return redirect()->back()->with('categoria_c', 'ok');
    }

    public function update(Request $request){ // Editar categoría existente.
        $categoria = inventarios::findOrFail($request->id);

        $nombre = "";
        $tipo = "";
        $producto = $categoria->producto;
        $tipo_aux = $categoria->tipo;

        if($categoria->producto != $request->nombre){
            $nombre = "<br>■ Nombre de la categoría: $categoria->producto ➡ $request->nombre<br>";
        }

        if($categoria->tipo != $request->tipo){

            $tipo_0 = $categoria->tipo == 0 ? "Router" : "Otros";
            $tipo_1 = $request->tipo == 0 ? "Router" : "Otros";

            $tipo = "<br>■ tipo de categoría: $tipo_0 ➡ $tipo_1<br>";
        }

        /* Area log */
        $inv_log = new inventario_log();

        $inv_log->usuario = Auth::user()->name;
        $inv_log->evento = "Se ha editado de la categoría $request->producto: <br><br>$nombre$tipo";
        $inv_log->tipo = 7;
 
        if($producto != $request->nombre || strval($tipo_aux) != $request->tipo){
            $inv_log->save();
        }
        /* Area log */

        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,gif|max:20480', // Máximo 20 MB
        ]);

        $imagen = $request->file('imagen');

        // Comprimir imagen si supera 20 MB
        if ($imagen->getSize() > 20000000) {
            $img = Image::make($imagen->getRealPath());
            $img->save(null, 80); // Reducir calidad a 80%
            // Si aún supera 20 MB, puedes implementar un bucle para reducir más la calidad
        }

        // Generar nombre único para el archivo
        $nombreArchivo = time() . '.' . $imagen->getClientOriginalExtension();

        // Guardar la imagen en el sistema de archivos
        $ruta = Storage::putFileAs('public/categoria', $imagen, $nombreArchivo);

        // Guardar la ruta en la base de datos (sin 'public/')
        $rutaEnBD = str_replace('public/', '', $ruta); // Ejemplo: 'img/1234567890.jpg'

        $categoria->producto = $request->nombre;
        $categoria->tipo = $request->tipo;
        echo $categoria->pic . " " . $rutaEnBD;
        $categoria->pic = $rutaEnBD;

        $categoria->save();

        if($producto == $request->nombre && strval($tipo_aux) == $request->tipo){
            return back()->with('noEditado', 'ok');
        }else{
            return back()->with('editado', 'ok');
        }
    }

    public function inv_delete($id){ // Borrar categoría.

        $categoria = inventarios::findOrFail($id);

        /* Area log */
        $inv_log = new inventario_log();

        $inv_log->usuario = Auth::user()->name;
        $inv_log->evento = "Se Elimino la categoría $categoria->producto";
        $inv_log->tipo = 3;

        $inv_log->save();
        /* Area log */

        $categoria->delete();
    }

    public function get_exi($id) {
        try {
            // Aseguramos que $id sea un entero
            $categoria_id = intval($id);
    
            $existencias = DB::select("SELECT * FROM `existencias` WHERE `categoria_id` = ?", [$categoria_id]);
    
            return response()->json($existencias);
        } catch (\Exception $e) {
            // Loguear el error para diagnóstico
            \Log::error("Error en get_exi con id $id: " . $e->getMessage());
    
            // Retornar respuesta JSON con error y código 500
            return response()->json([
                'error' => 'Error al obtener existencias',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function get_count($id){ // esta función debería estar en el controlador de pre-registro pero por motivos de orden de inventario esta aquí

        $cantidad = DB::select("SELECT COUNT(*) AS cantidad FROM existencias WHERE `categoria_id` = $id");

        return response()->json($cantidad[0]->cantidad);
    }

    public function add_exi(Request $request){ // Agregar existencias (ya sea router u otros).

        $categoria = inventarios::findOrFail($request->id);

        if($request->tipo == 0) { 
            DB::insert("INSERT INTO `existencias` (`id`, `categoria_id`, `serial`, `mac`, `asignado`, `observacion`) VALUES (NULL, $request->id, '$request->serial', '$request->mac', '0', NULL);");
            /* Area log */
            $inv_log = new inventario_log();

            $inv_log->usuario = Auth::user()->name;
            $inv_log->evento = "Se ha agregado un producto a la categoría $categoria->producto (serial: $request->serial)";
            $inv_log->tipo = 2;

            $inv_log->save();
            /* Area log */
        }else{
            for($i = 0; $i <= $request->cantidad - 1; $i++){
                DB::insert("INSERT INTO `existencias` (`id`, `categoria_id`, `serial`, `mac`, `asignado`, `observacion`) VALUES (NULL, $request->id, 'N/A', 'N/A', '0', NULL);");
            }

            /* Area log */
            $inv_log = new inventario_log();

            $inv_log->usuario = Auth::user()->name;
            $inv_log->evento = "Se han agregado $request->cantidad producto(s) a la categoría $categoria->producto";
            $inv_log->tipo = 2;

            $inv_log->save();
            /* Area log */
        }

        return back()->with("add_exi", "ok");
    }

    public function inventario_log(){ //Vista del log.
        return view('inventario.log');
    }

    public function inventario_log_data(){ // Datos estructurados del log obtenidos de la BD para obtener por fetch.
        $log = DB::select("SELECT * FROM `inventario_log`;");

        return response()->json($log);
    }

    public function salida_otros(Request $request){ //eliminar ultima existencias "que no este asignada" por ID de categoria

        DB::delete("DELETE FROM existencias WHERE `categoria_id` = $request->id LIMIT $request->cantidad");
        $categoria = inventarios::findOrFail($request->id);

        /* Area log */
        $inv_log = new inventario_log();

        $inv_log->usuario = Auth::user()->name;
        $inv_log->evento = "Se han reportado una salida de $request->cantidad existencias de la categoria $categoria->producto, responsable: $request->responsable";
        $inv_log->tipo = 8;

        $inv_log->save();
        /* Area log */

        return back()->with("salida_otros", "ok");
    }

    public function generar_salida(Request $request){

        $responsable = $request->input('responsable');
        $usuarios = DB::select("SELECT `name` FROM `users` WHERE 1;");
        $cantidad = DB::select("SELECT COUNT(*) as cantidad FROM `users` WHERE 1;");
        $comodin = 0;

        foreach($cantidad as $numero){
            $adulterado = $numero->cantidad;
        }

        foreach($usuarios as $usuario){
            echo "$usuario->name != $responsable<br>";
            if($usuario->name != $responsable){
                $comodin++;
            }
        }

        if($adulterado == $comodin){
            /* Resumen General */

            $resumen = new resumen_general();
            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "El usuario adultero el formulario de salida del inventario.";
            $resumen->tipo = 26;
            $resumen->save();

            /* Resumen General */

            return back()->with("adulterado", "ok");
        }else {
            $formulario0 = json_decode($request->input('nonRouter'), true);
            $formulario1 = json_decode($request->input('router'), true);

            $router = "";
            $no_router = "";
            $asignacion = "";

            $cantidad_router = 0;
            $cantidad_nonRouter = 0;

            $agrupacion = "";
            $categoria_agrupacion = "";
            $br = "";
        
            if(!empty($formulario0)){ // Mostrar otros con categoria y cantidad.

                foreach($formulario0 as $noRouter){
                    $nombre = inventarios::findOrFail($noRouter['id']);
                    $cantidad = $noRouter['cantidad'];
                    $no_router .= "■ $nombre->producto - cantidad: $cantidad<br><br>";
                    $cantidad_nonRouter += $cantidad;
                }

                DB::delete("DELETE FROM existencias WHERE `categoria_id` = $nombre->id LIMIT $cantidad_nonRouter");
            }else{
                $no_router = "Ninguno.<br><br>";
            }

            if(!empty($formulario1)){ // Para agrupar categorías con sus existencias en el reporte.
                foreach($formulario1 as $routers){
                    $resultado_1 = existencias::findOrFail($routers['id']);

                    if($resultado_1->observacion != null || $resultado_1->observacion != ""){
                        $asignacion = "Asignación: $resultado_1->observacion";
                    }else{
                        $asignacion = "Asignación: Sin asignar";
                    }

                    $categoria = inventarios::findOrFail($resultado_1->categoria_id);
                    if($agrupacion == $categoria->producto){
                        $categoria_agrupacion = "";
                        $br = "";
                    }else{
                        $categoria_agrupacion = "■ Categoría: $categoria->producto";
                        $br = "<br>";

                    }
                    $router .= "$categoria_agrupacion$br$br - Serial: $resultado_1->serial<br> - $asignacion<br><br>";

                    $cantidad_router++;

                    $agrupacion = $categoria->producto;

                    $resultado_1->delete();
                }
            }else{
                $router = "Ninguno.<br><br>";
            }

            /* Area log */
            $inv_log = new inventario_log();

            $inv_log->usuario = Auth::user()->name;
            $inv_log->evento = "Se han reportado una salida bajo la responsabilidad de $responsable<br><br>- Router ($cantidad_router):<br><br>$router<br><hr><br>- Otros ($cantidad_nonRouter):<br><br>$no_router";
            $inv_log->tipo = 10;

            $inv_log->save();
            /* Area log */

            return back()->with("salida", "ok");
        }
    }

    public function generar_entrada(Request $request){
        $responsable = $request->input('responsable');
        $formulario0 = json_decode($request->input('nonRouter'), true);
        $formulario1 = json_decode($request->input('router'), true);

        $cantidad_router = 0;
        $cantidad_nonRouter = 0;

        $router = "";
        $no_router = "";

        foreach($formulario0 as $noRouter){ 
            $id_categora = $noRouter['id'];
            $id_cantidad = $noRouter['cantidad'];

            for ($i = 0; $i < $id_cantidad; $i++) {
                DB::select("INSERT INTO `existencias` (`id`, `categoria_id`, `serial`, `mac`, `asignado`, `observacion`) VALUES (NULL, $id_categora, 'N/A', 'N/A', '0', NULL);");
                $cantidad_nonRouter++;
            }

            $categoria = inventarios::findOrFail($id_categora);

            $no_router .= "■ $categoria->producto - cantidad ingresada: $id_cantidad<br>";
        } 

        $agrupacion = "";

        foreach($formulario1 as $routers){ 
            $id_categora = $routers['id'];
            $id_serial = $routers['serial'];

            if($id_serial != ""){

                DB::select("INSERT INTO `existencias` (`id`, `categoria_id`, `serial`, `mac`, `asignado`, `observacion`) VALUES (NULL, $id_categora, '$id_serial', 'N/A', '0', NULL);");
                $cantidad_router++;

                $categoria = inventarios::findOrFail($id_categora);

                if($categoria->producto != $agrupacion){
                    $router .= "■ Categoría: $categoria->producto<br><br> - $id_serial<br><br>";
                }else{
                    $router .= "- $id_serial<br><br>";
                }

                $agrupacion = $categoria->producto;
            }
        }

        /* Area log */
        $inv_log = new inventario_log();

        $inv_log->usuario = Auth::user()->name;
        $inv_log->evento = "Se han reportado una entrada bajo la responsabilidad de $responsable<br><br>- Router (total ingresado: $cantidad_router):<br><br>$router<br><hr><br>- Otros (total ingresado: $cantidad_nonRouter):<br><br>$no_router";
        $inv_log->tipo = 2;

        $inv_log->save();
        /* Area log */

        return back()->with("entrada", "ok");
    }
}