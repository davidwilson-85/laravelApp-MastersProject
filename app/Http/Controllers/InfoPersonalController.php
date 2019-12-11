<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Description;

class InfoPersonalController extends Controller
{
    public function show() {

    	// Obtener el nombre de la imagen de perfil de la tabla 'descriptions'. Si el usuario todavía no ha seleccionado ninguna, no hay nada en la base de datos
    	$imagen_perfil = \DB::table('descriptions')
            ->select('imagen_perfil')
            ->where('id_usuario', auth()->user()->id)
            ->get();

	    if (count($imagen_perfil) != 0) {
        	$nombre_imagen_perfil = $imagen_perfil[0]->imagen_perfil;
        }

        if (empty($nombre_imagen_perfil)) {
        	$nombre_imagen_perfil = 'imagen_perfil_generica.png';
        }

        
        // Dependiendo de si el usuario activo es profesor o alumno, algunas consultas a la base de datos deben utilizar un campo un otro. Lo defino aquí.
        if (auth()->user()->role == 'prof') {
        	$campo_consulta = 'id_prof'; 
        } else {
        	$campo_consulta = 'id_alum';
        }

        // Obtener, usando el método count() de Eloquent, cuántas clases solicitadas y confirmadas tiene el usuario.        
        $clases_solicitadas = \DB::table('lessons')
        	->where($campo_consulta, auth()->user()->id)
        	->where('status', 'solicitada')
        	->count();

        $clases_confirmadas = \DB::table('lessons')
        	->where($campo_consulta, auth()->user()->id)
        	->where('status', 'confirmada')
        	->count();



        // Calcular la longitud media (en minutos) de las lecciones del usuario
        $clases_intervalos = \DB::table('lessons')
            ->select('inicio', 'final')
            ->where($campo_consulta, auth()->user()->id)
            ->get();

        $clases_longitud = [];

        foreach ($clases_intervalos as $intval) {
            $clases_longitud[] = ( strtotime($intval->final) - strtotime($intval->inicio) ) / 60;
        }

        if (count($clases_longitud)) {
            $clases_longitud_promedio = round(array_sum($clases_longitud) / count($clases_longitud));
        } else {
            $clases_longitud_promedio = 0;
        }



        // Obtener todos los nombres (histórico) de:
        //		a) todos los alumnos de un profesor
        //		b) todos los profesores de un alumno
        $interactores = \DB::table('users')
        	->join('lessons', 'lessons.id_alum', '=', 'users.id')
        	->select('users.name')
        	->where('lessons.'. $campo_consulta, auth()->user()->id)
        	->where('lessons.status', 'confirmada')
        	->distinct()->get();

        if (count($interactores) == 0) {

        	$cadena_lista_interactores = 'Todavía no tienes ninguno.';

        } else {

        	// Convierto colección en array normal para poder usar funciones de array
        	foreach ($interactores as $interactor) {
        		$array_nombres_interactores[] = $interactor->name;
        	}

        	// Detecto cual el la última clave del array
			$claves_array = array_keys($array_nombres_interactores);
			$ultima_clave = end($claves_array);
			reset($array_nombres_interactores);

			// Creo una cadena de texto separando cada nombre por una coma, y añadiendo un punto al final
        	$cadena_lista_interactores = '';
        	foreach ($array_nombres_interactores as $clave => $interactor) {
        		if ($clave != $ultima_clave) {
        			$cadena_lista_interactores .= $interactor .', ';
        		} else {
        			$cadena_lista_interactores .= $interactor .'. ';
        		}
        	}

        }

        // Obtener de la base de datos el texto de presentación
    	$info = \DB::table('descriptions')
            ->select('textopresentacion')
            ->where('id_usuario', auth()->user()->id)
            ->get();

        // Comprobar si hay texto de presentación en la base de datos, y si no lo hay enviar a la vista un mensaje genérico.
        if (count($info) != 0) {
        	$textoPresentacion = $info[0]->textopresentacion;
        }

        if (empty($textoPresentacion)) {
        	$textoPresentacion = 'Tu descripción personal está vacía. Pincha aquí para introducir una. Todos los alumnos pueden verla.';
        }
		
        return view('areapersonal', [
        	'textopresentacion' => $textoPresentacion,
        	'clases_solicitadas' => $clases_solicitadas,
        	'clases_confirmadas' => $clases_confirmadas,
        	'lista_interactores' => $cadena_lista_interactores,
        	'imagen_perfil' => $nombre_imagen_perfil,
            'clases_longitud_promedio' => $clases_longitud_promedio
        ]);
        
    }

    public function update() {

    	// Con la función updateOrCreate(), modificar la tabla descriptions:
		$descripcion = Description::updateOrCreate([
			'id_usuario' => auth()->user()->id], [ 
		    'textopresentacion' => request('textopresentacion')
		]);
		$descripcion->save();
		return back();

    }

    public function subirImagen(Request $request) {
	    
	    $this->validate($request, [
	        'imagen' => 'required|image|mimes:jpeg,png,jpg|max:2048',
	    ]);

	    if ($request->hasFile('imagen')) {

	    	// Procesar imagen, cambiarle el nombre y guardarla en carpeta /imagenes
	        $imagen = $request->file('imagen');
	        $imagen_nombre = 'imagen_perfil_id_'. auth()->user()->id .'.'. $imagen->getClientOriginalExtension();
	        $dir_destino = public_path('/images');
	        $imagen->move($dir_destino, $imagen_nombre);

	        // El nombre de las imagenes siempre va a ser 'imagen_perfil_id_{{id}}', pero la extensión es variable, y eso crea la necesidad de guardala en la base de datos para consultarla cada vez que se solicite la vista'
	        $descripcion = Description::updateOrCreate([
				'id_usuario' => auth()->user()->id], [ 
			    'imagen_perfil' => $imagen_nombre
			]);
			$descripcion->save();

			// Por hacer: en caso de que el archivo de imagen no sobreescriba al preexistente, podría eliminarlo aquí.

	        return back();
	    }

	}

}
