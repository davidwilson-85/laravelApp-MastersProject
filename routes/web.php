<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Auth::routes();

//Route::get('/home', 'HomeController@index')->middleware('auth');


/*------------------------------
	PÁGINA BIENVENIDA
------------------------------*/

Route::get('/', function() {
    return view('bienvenida');
})->middleware('guest');

//Route::get('/', function() {
//    return 'test2';
//})->middleware('auth');


/*------------------------------
	ÁREA PERSONAL
------------------------------*/

Route::get('/areapersonal', 'InfoPersonalController@show')->middleware('auth');
Route::post('/areapersonal', 'InfoPersonalController@update')->middleware('auth');
Route::post('/areapersonal/imagen', 'InfoPersonalController@subirImagen')->middleware('auth');


/*------------------------------
	CALENDARIO
------------------------------*/

Route::get('/calendario/{prof}/{week?}', 'CalendarController@index')->middleware('auth');
Route::patch('/calendario/slots', 'CalendarController@updateSlots')->middleware('auth');
Route::post('/calendario/lecciones', 'LessonsController@update')->middleware('auth');
// Al no poder poner variables en el valor de action en un formulario, redirijo usando una ruta. Para poder usar request aquí, necesito importar la clase.
Route::post("calendario/cambiarprofesor",function(\Illuminate\Http\Request $request){
    $url="calendario/{$request->profesor}/{$request->semana}";
    return redirect($url);
})->middleware('auth');
Route::get('/calendario/lecciones/confirmar/{id}', 'LessonsController@confirmar')->middleware('auth');


/*------------------------------
	CORREO
------------------------------*/

Route::get('/correo/{destinatario?}', 'CorreoController@index')->middleware('auth');
Route::post('/correo/{destinatario}', 'CorreoController@store')->middleware('auth');
Route::get('/correo/delete/{id}', 'CorreoController@destroy')->middleware('auth');
// Al no poder poner variables en el valor de action en un formulario, redirijo usando una ruta. Para poder usar request aquí, necesito importar la clase.
Route::post("correo/cambiar/dest",function(\Illuminate\Http\Request $request){
    $url="correo/{$request->destinatario}";
    return redirect($url);
})->middleware('auth');


/*------------------------------
	VER USUARIOS
------------------------------*/

Route::get('/ver-usuarios', 'VerUsuariosController@index')->middleware('auth');


/*------------------------------
	TEST
------------------------------*/

Route::get('/test', function() {
	return view('gridtest');
})->middleware('auth');