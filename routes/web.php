<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\principal;
use App\Http\Controllers\menu;
use App\Http\Controllers\pagos;
use App\Http\Controllers\resumen;
use App\Http\Controllers\evento_diario;
use App\Http\Controllers\pre_registro;
use App\Http\Controllers\imprimir_datos;
use App\Http\Controllers\pagos_resumen;
use App\Http\Controllers\prorrogas;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\mikrotik;
use App\Http\Controllers\menu_administrativo;
use App\Http\Controllers\acosador_de_usuarios;
use App\Http\Controllers\comprobar;
use App\Http\Controllers\difusion;
use App\Http\Controllers\caja_fuerte;
use App\Http\Controllers\inventario;
use App\Http\Controllers\CorteRemakeController;
use App\Http\Controllers\AcosadorRemakeController;
use App\Http\Controllers\TelegramController;

use App\Http\Requests\LoginRequest;

use App\Models\cliente;
use App\Models\estados;
use App\Models\pago_resumen;
use App\Models\planes;
use App\Models\resumen_general;
use App\Models\servidores;
use App\Models\evento;
use App\Models\pre_registro_model;
use App\Models\User;
use App\Models\clientes_aux;
use App\Models\acosador;
use App\Models\acosador_log;
use App\Models\instalaciones;
use App\Models\historial_tasa;
use App\Models\evento_log;
use App\Models\zona;
use App\Models\caja;
use App\Models\caja_log;
use App\Models\comprobacion;
use App\Models\inventarios;
use App\Models\existencias;
use App\Models\inventario_log;

use App\fpdf\FPDF;
use App\fpdf\KodePDF;
use App\fpdf\PDF_HTML;

//1: rutas principales
Route::controller(principal::class)->middleware('auth')->group(function () {
    Route::get('/', 'index')->name('inicio'); // esta ruta devuelve la vista "inicio".
    Route::get('/clientes', 'clientes'); // esta ruta trae los clientes de la base de datos.
    Route::get('/actualizar_estado', 'actualizar_estado'); // ruta para traer las fechas de corte de todos los clientes para actualizar sus estados.
    Route::get('/data', 'data'); // esta ruta trae los planes y los servidores e instalaciones de la base de datos.
    Route::get('/bancos', 'bancos'); // esta ruta trae los bancos receptores de la base de datos.
    Route::get('/servicios_vinculados/{id}', 'servicios_vinculados'); // esta ruta trae los planes y los servidores de la base de datos.
    Route::post('/clientes/add', 'agregar_cliente')->name('menu_agregar_cliente'); // Agregar un cliente nuevo de manera manual.
    Route::post('/servicio_nuevo', 'agregar_servicio')->name('agregar_servicio'); // Facturar un nuevo servicio.
    Route::post('/editar_tasa', 'editar_tasa')->name('editar_tasa'); // Editar tasa.
    Route::get('/cambios/{id}', 'lista_de_cambios')->name('lista_de_cambios'); // Listar cambios.
    Route::get('/obtener_cambios/{id}', 'obtener_cambios')->name('obtener_cambios'); // retornar de cambios en json.
    Route::get('/congelar', 'congelar')->name('congelar'); // Congelar servicio.
    Route::get('/descongelar/{id}', 'descongelar')->name('descongelar'); // Descongelar servicio.
    Route::get('/retornar_cliente/{id}', 'retornar_cliente')->name('retornar_cliente'); // Retornar un cliente individual por id.
    Route::get('/ruta_reparar', 'ruta_reparar')->name('ruta_reparar'); // &"%#&//%$"!#$%&/)(=#)
});
//rutas principales

//2: rutas de clientes
Route::controller(menu::class)->middleware('auth')->group(function () {
    Route::get('/clientes/menu/{id}', 'index')->name('menu'); // Ruta del menu del cliente.
    Route::post('/clientes/add/ser', 'nuevo_ser')->name('nuevo_ser'); // Agregar nuevo servicio a ese cliente.
    Route::post('/clientes/menu_editar/{id}', 'modificar_cliente')->name('menu_modificar_cliente'); // Modificar cliente.
    Route::delete('/clientes/eliminar/{id}', 'eliminar_cliente')->name('menu_eliminar_cliente'); // eliminar cliente.
    Route::get('/sort', 'ip_server')->name('ip_server'); // esta ruta devuelve la "ip".
    Route::get('/clientes/servicios/{ser_id}', 'retornar_servicios')->name('retornar_servicios'); // esta ruta devuelve los servicios vinculados (menos el principal).
    Route::get('/clientes/servicios_todos/{id}', 'servicios_todos')->name('servicios_todos'); // esta ruta devuelve los servicios vinculados.
    Route::get('/clientes/servicios_del', 'borrar_y_asignar')->name('borrar_y_asignar'); // esta ruta elimina un servicio (asigna otro como principal) o todos los servicio.
    Route::get('/clientes/clientes_ss/{cedula}/{nombre}', 'clientes_ss')->name('clientes_ss'); // trae a los clientes sin servicios.
    Route::get('/clientes/clientes_sss/{cedula}/{nombre}', 'clientes_sss')->name('clientes_sss'); // trae a todos los clientes.
    Route::get('/clientes/union/{de}/{para}', 'union')->name('union'); // selecciona un cliente principal y lo hace servicio de otro cliente principal.
    Route::post('/clientes/conducta', 'conducta')->name('conducta'); // Menu para modificar la conducta y el motivo del cliente.
    Route::get('/clientes/separar/{id}', 'separar')->name('separar'); // Separa un cliente secundario y lo hace principal.
    Route::get('/clientes/reiniciar_prorroga/{id}', 'reiniciar_prorroga')->name('reiniciar_prorroga'); // reiniciar prorroga de un cliente.
});
//rutas de clientes

//3: rutas de pagos
Route::controller(pagos::class)->middleware('auth')->group(function () {
    Route::post('/menu/pagar/{id}', 'pagar')->name('pagar'); // pagar
    Route::post('/menu/metodo_prepago/{id}', 'metodo_prepago')->name('metodo_prepago'); // Método pre-pago.
    Route::post('/menu/add_deuda/{id}', 'add_deuda')->name('add_deuda'); // Agregar deudas a clientes.
    Route::get('/eliminar_pago/{id}', 'eliminar_pago')->name('eliminar_pago'); // Eliminar un pago y restar la cantidad de meses correspondiente.
    Route::get('/multi_pagos', 'multi_pagos')->name('multi_pagos'); // Vista del multi-pagos.
    Route::get('/verificar_multipago/{fecha}', 'verificar_multipago')->name('verificar_multipago'); // Verificar si hay tasa registrada esa fecha.
    Route::post('/pagar/multi_pago', 'pagar_multi_pago')->name('pagar_multi_pago'); // Pago de varios clientes a la vez.
    Route::post('/pagar_deuda/{id}', 'pagar_deuda')->name('pagar_deuda'); // Ruta para realizar pagos de deudas.
    Route::get('/referencias/{referencia}', 'verificar_pagomovil')->name('verificar_pagomovil'); // Devuelve las referencias de los pagomoviles.
    Route::get('/pago_individual/{id}', 'pago_individual')->name('pago_individual'); // Pago en ventanas individuales.
    Route::get('/datos_estructurados_pago/{id}', 'datos_estructurados_pago')->name('datos_estructurados_pago'); // Datos estructurados para los pagos.
    Route::get('/datos_estructurados_modificar/{id}', 'datos_estructurados_modificar')->name('datos_estructurados_modificar'); // Datos estructurados para las ediciones.
    Route::get('/consultar_tasa', 'consultar_tasa')->name('consultar_tasa'); //consultar todas las tasa.
    Route::get('/consultar_tasa_fecha/{fecha}', 'consultar_tasa_fecha')->name('consultar_tasa_fecha'); //consultar una tasa por fecha.
});
//rutas de pagos

//4: rutas resumen general
Route::controller(resumen::class)->middleware('auth')->group(function () {
    Route::get('/resumen_general', 'index')->name('resumen_general'); // ruta principal del resumen general.
    Route::get('/resumen_general/get_data', 'data')->name('resumen_general_data'); // obtener datos.
});
//resumen general

//5: rutas del evento diario
Route::controller(evento_diario::class)->middleware('auth')->group(function () {
    Route::get('/evento_diario', 'index')->name('evento_diario'); // Ruta principal del evento diario.
    Route::get('/evento/{fecha}', 'evento')->name('evento'); // Retorna todos los eventos (con resto).
    Route::get('/evento_sr/{fecha}', 'evento_sr')->name('evento_sr'); // Retorna todos los eventos (sin resto).
    Route::post('/evento/agregar/{ev}/{d}/{bs}/{pm}/{eu}/{fec}', 'agregar_evento')->name('agregar_evento'); // Agregar eventos diarios manualmente.
    Route::get('/evento/check_evento/{id}/{op}', 'check_evento')->name('check_evento'); // Agregar check permanente.
    Route::get('/evento/eliminar_evento/{id}', 'eliminar_evento')->name('eliminar_evento'); // Eliminar evento.
    Route::get('/evento_log', 'evento_log')->name('evento_log'); // Eventos eliminados (vista).
    Route::get('/evento_log_data', 'evento_log_data')->name('evento_log_data'); // Eventos eliminados (datos).
});
//rutas del evento diario

//6: rutas del pre-registro
Route::controller(pre_registro::class)->middleware('auth')->group(function () {
    Route::get('/pre_registro', 'index')->name('pre_registro'); // Ruta principal del pre-registro.
    Route::get('/pre_registro/get_pre/{id}', 'get_pre')->name('get_pre'); // Recibir un id para devolver el cliente correspondiente.
    Route::get('/pre_registro/get_plan/{id}', 'get_plan')->name('get_plan'); // Recibir un id para devolver el nombre del plan correspondiente.
    Route::post('/pre_registro/nuevo', 'agregar_pre_registro')->name('agregar_pre_registro'); // Agregar nuevos registros.
    Route::post('/pre_registro/abono', 'pre_registro_abono')->name('pre_registro_abono'); // Abonar o pagar el restante de la instalación.
    Route::post('/pre_registro/editar/{id}', 'editar_pre_reg')->name('editar_pre_reg'); // Editar un registro del pre-registro.
    Route::get('/pre_registro/borrar/{id}/{serial}', 'borrar')->name('pre_registro_borrar'); // Elimina un cliente del pre-registro.
    Route::get('/pre_registro/get_data', 'datos')->name('pre_registro_get_data'); // Retorna todos los eventos.
    Route::get('/pre_registro/agregar/{id}', 'datos_a_registrar')->name('pre_registro_get_data_pre_reg'); // Retorna el pre-reg especifico.
    Route::post('/pre_registro/registrar', 'registrar_pre_reg')->name('registrar_pre_reg'); // Registrar formulario del pre-registro.
    Route::get('/pre_registro/cambiar_estado/{id}', 'cambiar_estado')->name('cambiar_estado'); // Cambiar estado de la instalación (color de la bolita xD).
    Route::get('/pre_registro/get_serial/{id}', 'get_serial')->name('get_serial'); // Obtener los seriales disponibles sin asignar del inventario
    Route::post('/pre_registro/asignacion', 'asignacion')->name('asignacion'); // Obtener los seriales disponibles sin asignar del inventario
});
//rutas del pre-registro

//7: rutas para imprimir datos
Route::controller(imprimir_datos::class)->middleware('auth')->group(function () {
    Route::get('/imprimir_datos_pm', 'imprimir_pm')->name('pagomovil_print'); // imprimir pagomovil.
    Route::get('/imprimir_datos_z', 'imprimir_z')->name('zelle_print'); // imprimir zelle.
    Route::get('/imprimir_factura_mensualidad/{id}', 'imprimir_factura')->name('imprimir_factura_mensualidad'); // Imprimir factura de mensualidad.

});
//rutas para imprimir datos

//8: rutas del resumen de pagos
Route::controller(pagos_resumen::class)->middleware('auth')->group(function () {
    Route::get('/pagos_resumen', 'index')->name('pagos_resumen'); // esta ruta devuelve los "pagos" realizados de todos los usuarios.
    Route::get('/pagos_resumen/data/{desde}/{hasta}/{tipo_de_pago}/{cobrador}', 'datos')->name('pagos_resumen_datos'); // obtener resumen de pagos.
});
//rutas para imprimir datos

//9: rutas de las prorrogas
Route::controller(prorrogas::class)->middleware('auth')->group(function () {
    Route::get('/prorroga/{id}', 'prorroga_dar')->name('prorroga'); // ruta para dar prorrogas.
    Route::get('/prorroga_quitar/{id}', 'prorroga_quitar')->name('prorroga_quitar'); // ruta para quitar prorrogas.
});
//rutas de las prorrogas

//10: rutas de autenticación (login con galletas).
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'index')->name('login'); // ruta para mostrar el formulario de inicio de sesión.
    Route::post('/login', 'login')->name('login.submit'); // ruta para procesar los datos para el inicio de sesión.
    Route::get('/logout', 'logout')->name('logout'); // ruta para cerrar sesión.
    Route::post('/login/nuevo_usuario', 'guardar')->name('nuevo_usuario'); // ruta para procesar los datos para crear el usuario nuevo.
    Route::get('/login/data', 'get_data')->name('login_get_data'); // ruta para mostrar el formulario de inicio de sesión.
});
//rutas de autenticación (login con galletas).

//11: rutas de la api de mikrotik.
Route::controller(mikrotik::class)->middleware('auth')->group(function () {
    Route::get('/modulo_de_cortes', 'index')->name('modulo_de_cortes'); // ruta principal para el modulo de cortes.
    Route::get('/datos_corte/{op}', 'datos_corte')->name('datos_corte'); // datos del filtro retornados a la vista principal.
    Route::get('/realizar_cortes', 'realizar_cortes')->name('realizar_cortes'); // realizar los cortes.
});
//rutas de la api de mikrotik.

//12: rutas para comprobar pagos.
Route::controller(comprobar::class)->middleware('auth')->group(function () {
    Route::get('/comprobar', 'index')->name('comprobar'); // vista principal para el modulo de comprobaciones.
    Route::get('/comprobar_datos/{fecha}', 'comprobar_datos')->name('comprobar_datos'); // datos a comprobar.
    Route::get('/comprobar_check/{id}/{op}', 'activar_desactivar_check')->name('activar_desactivar_check'); // Cambiar check.
    Route::post('/comprobar_datos_automatico', 'comprobar_datos_automatico')->name('comprobar_datos_automatico'); // Generar reporte del excel.
    Route::get('/reporte_pagomovil', 'reporte_pagomovil')->name('reporte_pagomovil'); // Vista del reporte.
    Route::get('/resumen_comprobar/{fecha}', 'resumen_comprobar')->name('resumen_comprobar'); // Vista del reporte.
    Route::post('/cambiarReceptor', 'cambiarReceptor')->name('cambiarReceptor'); // Cambiar el banco receptor.
});
//rutas para comprobar pagos.

//13: rutas para el acosador de usuarios.
Route::controller(acosador_de_usuarios::class)->middleware('auth')->group(function () {
    Route::get('/acosador', 'acosador')->name('acosador'); // Empieza el acoso (sistema -> servidores).
    Route::get('/acosador_ser_sis', 'acosador_ser_sis')->name('acosador_ser_sis'); // Empieza el acoso x2 :v (servidores -> sistema).
    Route::get('/acosador_data', 'acosador_data')->name('acosador_data'); // Datos del log.
    Route::get('/acosador_log', 'acosador_log')->name('acosador_log'); // Vista para el log del acosador.
});
//rutas para el acosador de usuarios.

//14: rutas para la configuración de la pagina.
Route::controller(menu_administrativo::class)->middleware('auth')->group(function () {
    Route::get('/administrar', 'administrar')->name('administrar'); // Ruta principal.
    Route::get('/administrar/eliminar_perfil/{id}', 'eliminar_perfil')->name('eliminar_perfil'); // Eliminar foto de perfil.
    Route::post('/administrar/cambiar_perfil', 'cambiar_perfil')->name('cambiar_perfil'); // Cambiar foto de perfil.
    Route::get('/administrar/users', 'usuarios')->name('usuarios'); // Devuelve los usuarios registrados.
    Route::get('/administrar/planes', 'planes')->name('planes'); // Devuelve los planes registrados.
    Route::get('/administrar/plan/{id}', 'plan')->name('plan'); // Devuelve un plan individual por id.
    Route::get('/administrar/servidores', 'servidores')->name('servidores'); // Devuelve los servidores registrados.
    Route::get('/administrar/servidor/{id}', 'servidor')->name('servidor'); // Devuelve un servidor individual por id.
    Route::get('/administrar/instalacion/{id}', 'instalacion')->name('instalacion'); // Devuelve una instalación individual por id.
    Route::get('/administrar/getUsers/{op}', 'getUsers')->name('getUsers'); // Devuelve todos los nombres de usuarios registrados en la BD.
    Route::post('/administrar/crear_plan', 'crear_plan')->name('crear_plan'); // Creación de planes nuevos.
    Route::post('/administrar/crear_servidor', 'crear_servidor')->name('crear_servidor'); // Creación de servidores nuevos.
    Route::post('/administrar/crear_instalacion', 'crear_instalacion')->name('crear_instalacion'); // Creación de instalaciones nuevas.
    Route::post('/administrar/crear_usuarios', 'crear_usuarios')->name('crear_usuarios'); // Para crear usuarios.
    Route::put('/administrar/actualizar_usuarios/{id}', 'actualizar_usuarios')->name('actualizar_usuarios'); // Para actualizar datos de usuarios.
    Route::post('/administrar/editar_plan', 'editar_plan')->name('editar_plan'); // Para editar planes.
    Route::post('/administrar/editar_servidor', 'editar_servidor')->name('editar_servidor'); // Para editar servidores.
    Route::post('/administrar/editar_instalacion', 'editar_instalacion')->name('editar_instalacion'); // Para editar instalaciones.
    Route::get('/administrar/nodos', 'nodos')->name('nodos'); // Para devolver todos los nodos.
    Route::get('/administrar/instalaciones', 'instalaciones')->name('instalaciones'); // Para devolver todas las instalaciones.
    Route::post('/administrar/eliminar_plan', 'eliminar_plan')->name('eliminar_plan'); // Para eliminar un plan individual.
    Route::post('/administrar/eliminar_servidor', 'eliminar_servidor')->name('eliminar_servidor'); // Para eliminar un servidor individual.
    Route::delete('/administrar/eliminar_instalacion/{id}', 'eliminar_instalacion')->name('eliminar_instalacion'); // Para eliminar una instalación individual.
    Route::get('/administrar/retornarCantidad/{id}', 'retornarCantidad')->name('retornarCantidad'); // Para devolver la cantidad de clientes pertenecientes a un plan por ID.
    Route::get('/administrar/retornarCantidadServidor/{id}', 'retornarCantidadServidor')->name('retornarCantidadServidor'); // Para devolver la cantidad de clientes pertenecientes a un servidor por ID.
});
//rutas para la configuración de la pagina.

//15: rutas para el bot cheems.
Route::controller(difusion::class)->middleware('auth')->group(function () {
    Route::get('/difusion', 'difusion')->name('difusion'); // Ruta principal.
    Route::get('/get_data_difusion', 'get_data_difusion')->name('get_data_difusion'); // Ruta para recibir los servidores, sectores y estados en un array.
    Route::post('/verificar_filtro', 'verificar_filtro')->name('verificar_filtro'); //verificar filtro.
});
//rutas para el bot cheems.

//16: rutas de la caja fuerte.
Route::controller(caja_fuerte::class)->middleware('auth')->group(function () {
    Route::get('/evento_admin', 'index')->name('evento_admin'); // Ruta principal.
    Route::get('/evento_admin/{fecha}', 'evento_admin_fecha')->name('evento_admin_fecha'); // Retorna todos los eventos (con resto).
    Route::get('/evento_sr_admin/{fecha}', 'evento_sr_admin')->name('evento_sr_admin'); // Retorna todos los eventos (sin resto).
    Route::post('/evento_admin/agregar/{ev}/{d}/{bs}/{pm}/{eu}/{fec}', 'agregar_evento_admin')->name('agregar_evento_admin'); // Agregar eventos diarios manualmente.
    Route::get('/evento_admin/check_evento/{id}/{op}', 'check_evento_admin')->name('check_evento_admin'); // Agregar check permanente.
    Route::get('/evento_admin/eliminar_evento/{id}', 'eliminar_evento_admin')->name('eliminar_evento_admin'); // Eliminar evento.
    Route::get('/evento_log_admin', 'evento_log_admin')->name('evento_log_admin'); // Evento del log (vista).
    Route::get('/evento_log_data_admin', 'evento_log_data_admin')->name('evento_log_data_admin'); // Eventos del log (datos).
});
//rutas evento diario 2.

//17: rutas para el inventario.
Route::controller(inventario::class)->middleware('auth')->group(function () {
    Route::get('/inventario', 'index')->name('inventario'); // Ruta principal..
    Route::get('/get_inv', 'get_inv')->name('get_inv'); // Devuelve el inventario..
    Route::post('/store', 'store')->name('store'); // Almacena una nueva categoría.
    Route::post('/update', 'update')->name('update'); // Editar una categoría (Nombre, imagen y tipo de categoría).
    Route::get('/get_cat/{id}', 'get_cat')->name('get_cat'); // Devuelve una categoría por id.                               no aplicada aun
    Route::get('/get_exi/{id}', 'get_exi')->name('get_exi'); // Devuelve las existencias de una categoría por id.
    Route::delete('/inv_delete/{id}', 'inv_delete')->name('inv_delete'); // Elimina una categoría.
    Route::post('/inv_add', 'inv_add')->name('inv_add'); // Agrega un producto o mas a una categoría.
    Route::get('/get_count/{id}', 'get_count')->name('get_count'); // Devuelve la cantidad del inventario por categoría.
    Route::post('/add_exi', 'add_exi')->name('add_exi'); // Agrega un producto o mas a una categoría.
    Route::get('/inventario_log', 'inventario_log')->name('inventario_log'); // Log del inventario (vista).
    Route::get('/inventario_log_data', 'inventario_log_data')->name('inventario_log_data'); // Log del inventario (datos)..
    Route::post('/salida_otros', 'salida_otros')->name('salida_otros'); // Genera una salida de productos de una categoría con responsable y cantidad.
    Route::post('/generar_salida', 'generar_salida')->name('generar_salida'); // Generar salida 2.0
    Route::post('/generar_entrada', 'generar_entrada')->name('generar_entrada'); // Generar entrada.
});
//rutas para el inventario.

//18: rutas para los nuevos cortes.
Route::controller(CorteRemakeController::class)->middleware('auth')->group(function () {
    Route::get('/getDataToCutOff', 'getDataToCutOff')->name('getDataToCutOff'); // Obtener datos para los cortes.
    Route::post('/makeCut', 'makeCut')->name('makeCut'); // Realizar los cortes con los datos obtenidos.
});
//rutas para los nuevos cortes.

//19: rutas para el acosador remake.
Route::controller(AcosadorRemakeController::class)->group(function () {
    Route::get('/getDataToAcosadorRemakeSerSis', 'getDataToAcosadorRemakeSerSis')->name('getDataToAcosadorRemakeSerSis'); // Listar y cortar clientes que no estén en el sistema.
    Route::get('/getDataToAcosadorRemakeSisSer', 'getDataToAcosadorRemakeSisSer')->name('getDataToAcosadorRemakeSisSer'); // Listar, comparar y actualizar (IP, SERVIDOR) clientes dependiendo de la MAC.
});
//rutas para el acosador remake.

//20: rutas para la api de telegram.
Route::controller(TelegramController::class)->group(function () {
    Route::get('/getDataToSendMessages', 'getDataToSendMessages')->name('getDataToSendMessages'); // Listar clientes para envió de mensaje automático.
    Route::get('/ProceedToSendMessages/{id}', 'ProceedToSendMessages')->name('ProceedToSendMessages'); // Proceder al envió de mensajes.
    Route::post('/updateId', 'updateId')->name('updateId'); // Actualiza el id del cliente por el comando registrar + cédula.
});
//rutas para la api de telegram.