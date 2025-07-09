<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\resumen_general;

date_default_timezone_set('America/Caracas');

class AuthController extends Controller
{
    public function index()
    { // vista principal del sistema de login
        return view('sistema_login/login');
    }

    public function login(Request $request)
    { // Función para iniciar sesión.
        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('incorrecto', 'ok');
        }

        Auth::login($user);

        $token = $user->createToken('Personal Access Token')->plainTextToken;

        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = Auth::user()->name;
        $resumen->descripcion = "El usuario $resumen->usuario inicio sesión";
        $resumen->tipo = 13;
        $resumen->save();
        /* Resumen General */

        return redirect('/');
    }

    public function logout(Request $request)
    { // Función para cerrar sesión.

        if (auth()->check()) {
           /* Resumen General */
            $resumen = new resumen_general();

            $resumen->usuario = Auth::user()->name;
            $resumen->descripcion = "El usuario $resumen->usuario cerro sesión";
            $resumen->tipo = 14;
            $resumen->save();
            /* Resumen General */

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } 
        
        return redirect('/login');
    }

    public function guardar(Request $request)
    { // Función para crear nuevos usuarios.

        $usuario = new User();

        $usuario->name = strtolower("$request->nombre");
        $usuario->password = Hash::make($request->password);

        $usuario->roles = 0;
        /* Resumen General */
        $resumen = new resumen_general();

        $resumen->usuario = "N/A"; // en un futuro cambiar por Auth::user()->name; cuando se mueva esto al modulo administrador
        $resumen->descripcion = "Se creo el usuario: $usuario->name";
        $resumen->tipo = 17;
        $resumen->save();
        /* Resumen General */

        $usuario->save();

        return back()->with('creado', 'ok');
    }

    public function get_data()
    { //Función para obtener datos de los usuarios registrados.
        $user = DB::table('users')->select('name')->get();

        return response()->json($user);
    }
}
