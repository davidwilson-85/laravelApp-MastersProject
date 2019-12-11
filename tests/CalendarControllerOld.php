<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slot;

class CalendarController extends Controller
{
    public function index($week='') {

        // Obtener usuario registrado
        $usuario_activo = auth()->user();

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
    		->where('id_prof', $usuario_activo->id)
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
                $processedSpans[$idx]['left_coord'] = 120 * ($slot->dia_semana - 1);
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
            ->select('days.dia_semana', 'lessons.inicio', 'lessons.final', 'lessons.id_alum', 'lessons.status', 'users.name')
            ->join('days', 'days.id', '=', 'lessons.id_cal')
            ->join('users', 'users.id', '=', 'lessons.id_alum')
            ->where('id_prof', $usuario_activo->id)
            ->where('days.year', '2019')
            ->where('days.semana', $week)
            ->get();

        //return $lecciones;

        // Transformar días y horas en posiciones sobre el calendario
        $leccionesProcesadas = array();
        $idx = 0;

        foreach ($lecciones as $leccion) {
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
            
            $leccionesProcesadas[$idx]['coord_izq'] = 120 * ($leccion->dia_semana - 1);
            $leccionesProcesadas[$idx]['coord_arriba'] = round(($ini_hora-6)*30 + $ini_min/2); 
            $leccionesProcesadas[$idx]['coord_abajo'] = round(($fin_hora-6)*30 + $fin_min/2);;
            $leccionesProcesadas[$idx]['altura'] = $leccionesProcesadas[$idx]['coord_abajo'] - $leccionesProcesadas[$idx]['coord_arriba'];

            $idx++;
        }

        //return $leccionesProcesadas;

    	return view('viewcalendar', ['mesActual' => $mesActual, 'week' => $week, 'weekData' => $weekData, 'slots' => $slots, 'processedSpans' => $processedSpans, 'lecciones' => $leccionesProcesadas]);

    }

    public function update() {
        /*
        request()->validate([
            'title' => ['required', 'min:3', 'max:40'],
            'description' => ['required', 'min:10', 'max:255']
        ]);
        */
        
        $slot = new Slot;
        
        // Transformar semana anual y dia semanal en id calendario. Buscar si ya existe disponibilidad para ese día (para ese profesor) en la db. Si existe, hacer update; si no, hacer insert.
        $cal_id = \DB::table('slots')
            ->select('days.dia_semana', 'slots.disp')
            ->join('days', 'days.id', '=', 'slots.id_cal')
            ->where('id_prof', $usuario_activo->id)
            ->where('days.year', '2019')
            ->where('days.semana', $week)
            ->get();

        //$slot->id_cal = request('dia');
        $slot->id_prof = auth()->user()->id;
        $slot->disp = request('disp');
        $slot->save();

        return redirect('calendario');
    }
}