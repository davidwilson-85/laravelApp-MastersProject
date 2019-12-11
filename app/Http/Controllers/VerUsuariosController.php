<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerUsuariosController extends Controller
{
    public function index() {

    	if (auth()->user()->role == 'prof') {
    		$campo_consulta_role = 'alum';
    	} else {
    		$campo_consulta_role = 'prof';
    	}

    	$usuarios = \DB::table('users')
    		->select('name', 'whatsapp', 'email', 'users.id', 'users.created_at', 'imagen_perfil', 'textopresentacion')
    		->leftJoin('descriptions', 'descriptions.id_usuario', '=', 'users.id')
    		->where('users.role', $campo_consulta_role)
    		->get();


    	//return $usuarios;

    	return view('verusuarios', ['usuarios' => $usuarios]);
    
    }
}
