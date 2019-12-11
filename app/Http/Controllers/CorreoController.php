<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Mensaje;

class CorreoController extends Controller
{
    public function index($destinatario='') {

    	// Obtener usuario registrado y su rol
        $usuario_activo = auth()->user()->id;
        $rol = User::find($usuario_activo)->role;
        
        if ($rol == 'prof') {
        	$prof = $usuario_activo;
    		$alum = $destinatario;

    		$posibles_destinatarios = \DB::table('users')
                ->select('id', 'name')
                ->where('role', 'alum')
                ->get();
        } else {
           	$prof = $destinatario;
    		$alum = $usuario_activo;

    		$posibles_destinatarios = \DB::table('users')
                ->select('id', 'name')
                ->where('role', 'prof')
                ->get();
        }

        
        $mensajes = \DB::table('mensajes')
    		->select('users.name', 'mensajes.id', 'mensajes.id_prof', 'mensajes.id_alum', 'mensajes.id_remitente', 'mensajes.contenido', 'mensajes.created_at')
    		->join('users', 'users.id', '=', 'mensajes.id_remitente')
    		->where('id_prof', $prof)
    		->where('id_alum', $alum)
    		->orderBy('mensajes.id', 'desc')
            ->get();

        //return $mensajes;
    	return view('correo', ['mensajes' => $mensajes, 'prof' => $prof, 'alum' => $alum, 'posibles_destinatarios' => $posibles_destinatarios]);
    }

    public function store() {
    	
    	$mensaje = new Mensaje;
    	$mensaje->id_prof = request('prof');
        $mensaje->id_alum = request('alum');
        $mensaje->id_remitente = auth()->user()->id;
        $mensaje->contenido = request('contenido');
        $mensaje->save();

        return back();

    }

    public function destroy($id) {

    	$mensaje = Mensaje::find($id);
        $mensaje->delete();

        return back();
    }
}
