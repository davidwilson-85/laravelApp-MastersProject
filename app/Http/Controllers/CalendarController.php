<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slot;
use App\User;
use Redirect;

class CalendarController extends Controller
{
    public function index($prof='', $week='') {

        // Obtener usuario registrado y su rol
        $usuario_activo = auth()->user();
        $rol = User::find($usuario_activo->id)->role;

        // ***DESARROLLO***
        //$usuario_activo = 5;
        ///$rol = 'prof';
        // ***DESARROLLO*** 

        // El calendario solo puede mostrar slots de un único profesor (= $profesor_activo). Si el usuario registrado es un alumno, determinar el calendario (profesor) a mostrar
        if ($rol == 'prof') {
            $profs = null;
            $prof_activo = $usuario_activo->id;
        } else {
            $profs = \DB::table('users')
                ->select('id', 'name')
                ->where('role', 'prof')
                ->get();

            //return $profs;

            if (empty($prof)) {
                $prof_activo = $profs[0]->id;
            } else {
                $prof_activo = $prof;
            }
        }

        // Si no se ha proporcionado una semana en la ruta, mostramos la semana actual
        if (empty($week)) {
            $week = date('W');
        };

        // Consultar información de una semana en la base de datos.
        $weekData = \DB::table('days')
            ->select('id', 'mes', 'dia', 'dia_semana')
            ->where('semana', $week)
            ->get();

        // Calcular si la semana actual está dentro de un mes o de dos.
        $meses = [];
        foreach ($weekData as $i) {
            $mes = $i->mes;
            array_push($meses, $mes);
        }

        $meses = array_values(array_unique($meses));

        $nombresMeses = [1 => 'Enero', 1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre',11 => 'Noviembre', 12 => 'Diciembre'];

        if (count($meses) == 1) {
            $mesActual = $nombresMeses[$meses[0]];
        } else if (count($meses) == 2) {
            $mesActual = $nombresMeses[$meses[0]] .'/'. $nombresMeses[$meses[1]];
        }

        // Consultar información sobre slots de un profesor para una semana en particular 
    	$slots = \DB::table('slots')
    		->select('days.dia_semana', 'slots.disp')
    		->join('days', 'days.id', '=', 'slots.id_cal')
    		->where('id_prof', $prof_activo)
            ->where('days.year', '2019')
            ->where('days.semana', $week)
            ->get();

        // Transformar días y horas en posiciones sobre el calendario
        // Cómo transformar horas en posiciones en la tabla del calendario: cada hora ocupa 30px, por tanto:
        // posiciónTabla = round ( (hora - 6) x 30 + min / 2 )
        // 6 es la hora a la que empieza el calendario HTML
        

        $processedSpans = array();
        $idx = 0;

        foreach ($slots as $slot) {
            $spans = explode(',', $slot->disp);
            foreach ($spans as $span) {
                $processedSpans[$idx]['ini'] = substr($span, 0, 5);
                $processedSpans[$idx]['fin'] = substr($span, 6, 5);
                $processedSpans[$idx]['left_coord'] = 150 * ($slot->dia_semana - 1);
                $ini_hora = substr($span, 0, 2);
                $ini_min = substr($span, 3, 2);
                $processedSpans[$idx]['top'] = round(($ini_hora-6)*30 + $ini_min/2);
                $fin_hora = substr($span, 6, 2);
                $fin_min = substr($span, 9, 2);
                $processedSpans[$idx]['bottom'] = round(($fin_hora-6)*30 + $fin_min/2);
                $processedSpans[$idx]['height'] = $processedSpans[$idx]['bottom'] - $processedSpans[$idx]['top'];
                $idx++;
            }
        }

        //return $processedSpans;

        // Consultar información sobre las lecciones de un profesor para una semana en particular 
        $lecciones = \DB::table('lessons')
            ->select('days.dia_semana', 'lessons.id', 'lessons.inicio', 'lessons.final', 'lessons.id_alum', 'lessons.status', 'users.name')
            ->join('days', 'days.id', '=', 'lessons.id_cal')
            ->join('users', 'users.id', '=', 'lessons.id_alum')
            ->where('id_prof', $prof_activo)
            ->where('days.year', '2019')
            ->where('days.semana', $week)
            ->get();

        //return $lecciones;

        // Transformar días y horas en posiciones sobre el calendario
        $leccionesProcesadas = array();
        $idx = 0;

        foreach ($lecciones as $leccion) {
            $leccionesProcesadas[$idx]['id'] = $leccion->id;
            $leccionesProcesadas[$idx]['alum_id'] = $leccion->id_alum;
            $leccionesProcesadas[$idx]['alum_nombre'] = $leccion->name;
            $leccionesProcesadas[$idx]['estado'] = $leccion->status;

            $leccionesProcesadas[$idx]['ini'] = substr($leccion->inicio, 0, 5);
            $leccionesProcesadas[$idx]['fin'] = substr($leccion->final, 0, 5);

            $ini_hora = substr($leccion->inicio, 0, 2);
            $ini_min  = substr($leccion->inicio, 3, 2);
            $fin_hora = substr($leccion->final, 0, 2);
            $fin_min  = substr($leccion->final, 3, 2);

            //$leccionesProcesadas[$idx]['ini_h'] = $ini_hora;
            //$leccionesProcesadas[$idx]['ini_m'] = $ini_min;
            //$leccionesProcesadas[$idx]['fin_h'] = $fin_hora;
            //$leccionesProcesadas[$idx]['fin_m'] = $fin_min;
            
            $leccionesProcesadas[$idx]['coord_izq'] = 150 * ($leccion->dia_semana - 1);
            $leccionesProcesadas[$idx]['coord_arriba'] = round(($ini_hora-6)*30 + $ini_min/2); 
            $leccionesProcesadas[$idx]['coord_abajo'] = round(($fin_hora-6)*30 + $fin_min/2);;
            $leccionesProcesadas[$idx]['altura'] = $leccionesProcesadas[$idx]['coord_abajo'] - $leccionesProcesadas[$idx]['coord_arriba'];

            $idx++;
        }

        //return $leccionesProcesadas;

    	return view('calendario', ['mesActual' => $mesActual, 'week' => $week, 'weekData' => $weekData, 'slots' => $slots, 'processedSpans' => $processedSpans, 'lecciones' => $leccionesProcesadas, 'rol' => $rol, 'profs' => $profs, 'prof_activo' => $prof_activo]);

    }

    public function updateSlots() {

        // Validar el formato de la(s) hora(s) de la disponibilidad. Si no es correcto, redireccionar atrás con mensaje. Si es correcto, continuar.
        $disponibilidad = request('disp');

        if (!empty($disponibilidad)) {
            
            $disponibilidad_partes = explode(",", $disponibilidad);
            
            foreach ($disponibilidad_partes as $disponibilidad_parte) {
                if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]-(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $disponibilidad_parte)) {
                    
                    $conflicto = array(True, 'Formato incorrecto. El formato debe ser HH:MM-HH:MM o HH:MM-HH:MM,HH:MM-HH:MM,...');

                    return Redirect::back()->withErrors($conflicto);

                }
            }
        }

        // Transformar semana anual y dia semanal en id calendario.
        $day = \DB::table('days')
            ->select('days.id')
            ->where('semana', request('semana'))
            ->where('days.dia_semana', request('dia_semana'))
            ->get();

        // Buscar si ya existe disponibilidad para ese profesor ese día.
        $slot_id = \DB::table('slots')
            ->select('slots.id')
            ->where('id_cal', $day[0]->id)
            ->where('id_prof', auth()->user()->id)
            ->get();


        // Función para comprobar y sanitizar (eliminar) lecciones de un día y profesor particulares. Se usa cuando un profesor modifica o elimina los slots de una día. La comprobación se evalúa y se devuelve al usuario para avisarle de la eliminación.
        function ComprobarYsanitizarLecciones($dia_id) {

            $leccionesParaEliminar = \DB::table('lessons')
                ->select('id')
                ->where('id_prof', auth()->user()->id)
                ->where('id_cal', $dia_id)
                ->get();

            if (isset($leccionesParaEliminar[0])) {
                $conflicto = [False, 'Tu disponibilidad ha sido modificada. Por precaución se han eliminado las lecciones de ese día.'];
            } else {
                $conflicto = [False, 'Tu disponibilidad ha sido modificada correctamente.'];
            }

            \DB::table('lessons')
                ->where('id_prof', auth()->user()->id)
                ->where('id_cal', $dia_id)
                ->delete();

            return $conflicto;
            
        }
        
        // Si ya se ha definido la disponibilidad para este día, hacer update; si no, hacer insert. Si la disponibilidad está en blanco, hacer un delete. 
        if (isset($slot_id[0]) and !empty(request('disp'))) {
            
            $conflicto = ComprobarYsanitizarLecciones($day[0]->id);

            $slot = Slot::find($slot_id[0]->id);
            $slot->disp = request('disp');
            $slot->save();

        } else if (isset($slot_id[0]) and empty(request('disp'))) {
            
            $conflicto = ComprobarYsanitizarLecciones($day[0]->id);

            $slot = Slot::find($slot_id[0]->id);
            $slot->delete();
        
        } else if (!isset($slot_id[0]) and !empty(request('disp'))) {
            
            $slot = new Slot;
            $slot->id_cal = $day[0]->id;
            $slot->id_prof = auth()->user()->id;
            $slot->disp = request('disp');
            $slot->save();

            $conflicto = [False, 'Tu disponibilidad ha sido añadida correctamente.'];
        } else if (!isset($slot_id[0]) and empty(request('disp'))) {

            $conflicto = [True, 'Por favor, introduce un rango horario en el campo "Disponibilidad". Si quieres eliminar una disponibilidad, selecciona un día que ya disponga de una.'];

        }

        //return redirect('calendario/'. auth()->user()->id .'/'. request('semana'));


        //$conflicto = [False, 'Tu disponibilidad ha sido modificada correctamente.'];
        //return redirect('calendario/'. request('semana'))->withErrors($conflicto);

        return Redirect::back()->withErrors($conflicto);
    }
}