<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api_clientes;
use App\Http\Controllers\api_cheems;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(api_clientes::class)->group(function () {
    Route::get('/get_c/{id}', 'get_c')->name('get_c');
    Route::get('/get_f/{id}', 'get_f')->name('get_f');
    Route::get('/servicio_get/{id}', 'servicio_get')->name('servicio_get');
    Route::get('/servicio_get_i/{id}', 'servicio_get_i')->name('servicio_get_i');
    //esta ruta es para el acosador de ping: devuelve clientes suspendidos sin prorroga.
    Route::get('/acosados_ping', 'acosados_ping')->name('acosados_ping');
});

Route::controller(api_cheems::class)->group(function () {
    Route::get('/cheems_clients', 'cheems_clients')->name('cheems_clients');
    Route::get('/cheems_client/{tlf}', 'cheems_client')->name('cheems_client');
});

