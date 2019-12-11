<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Slot;
use App\Lesson;
use Redirect;

class LessonsController extends Controller
{
    public function update() {

        // Validar el formato de la(s) hora(s) de la lección. Si no es correcto, redireccionar atrás con mensaje. Si es correcto, continuar.        
        if (!empty(request('hora'))) {

            $hora_intervalo = request('hora');
            
            if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]-(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$/', $hora_intervalo)) {
                
                $conflicto = array(True, 'Formato incorrecto. El formato debe ser HH:MM-HH:MM.');
                return Redirect::back()->withErrors($conflicto);

            }
        }

    	// Transformar semana anual y dia semanal en id calendario.
        $dia = \DB::table('days')
            ->select('days.id')
            ->where('semana', request('semana'))
            ->where('dia_semana', request('dia_semana'))
            ->get();

        // Procesar y simplificar información de la solicitud
        $dia_id = $dia[0]->id;
        if (!empty(request('hora'))) {
            $hora_ini = trim(explode("-", request('hora'))[0]);
            $hora_fin = trim(explode("-", request('hora'))[1]);
        }

        // Buscar si ya hay lecciones del alumno activo con el profesor activo ese día para saber si hacer INSERT vs UPDATE, o DELETE vs ignorar

        // Ver si ya hay lecciones ese día
        $leccion_guardada = \DB::table('lessons')
            ->select('lessons.id')
            ->where('id_cal', $dia[0]->id)
            ->where('id_prof', request('prof'))
            ->where('id_alum', auth()->user()->id)
            ->get();

        //return array($dia[0]->id, request('prof'), auth()->user()->id, $leccion_guardada);

        // Determinar si hay o no conflicto
        // * con slots del profesor
        // * con lecciones del profesor con otros alumnos
        // * con lecciones propias con otro profesor
        function comprobarConflictos($dia_id, $hora_ini, $hora_fin) {

            // Comprobar si la lección solicitada cae dentro de un slot del profesor
            $slot = \DB::table('slots')
                ->select('slots.disp')
                ->where('id_cal', $dia_id)
                ->where('id_prof', request('prof'))
                ->get();

            if (!isset($slot[0])) {
                
                return array(True, 'No hay slots para este profesor en el día que has solicitado.');
            
            } else {

                // Determinar slot y subslots
                $subslots = explode(',', $slot[0]->disp);
                $slot_test = 'fail';
                
                foreach ($subslots as $subslot) {
                    $subslot_ini = trim(explode('-', $subslot)[0]);
                    $subslot_fin = trim(explode('-', $subslot)[1]);
                    if ($subslot_ini <= $hora_ini AND $subslot_fin >= $hora_fin) {
                        $slot_test = 'pass';
                    }
                }

                if ($slot_test == 'fail') {

                    return array(True, 'La lección que has solicitado está fuera del slot del profesor.');

                } else {
                    
                    // Comprobar si hay conflicto con lecciones de otros alumnos
                    $lecciones_preexistentes = \DB::table('lessons')
                        ->select('inicio', 'final')
                        ->where('id_cal', $dia_id)
                        ->where('id_prof', request('prof'))
                        ->get();

                    if (isset($lecciones_preexistentes[0])) {

                        foreach ($lecciones_preexistentes as $lec_pre) {

                            if (($hora_ini >= $lec_pre->inicio && $hora_ini <= $lec_pre->final) || ($lec_pre->inicio >= $hora_ini && $lec_pre->inicio <= $hora_fin))
                            {
                                return array(True, 'La lección que has solicitado se solapa con otras lecciones.');
                            }

                        }

                    }

                }
            }

            // Comprobar si la lección solicitada se solapa con lecciones propias con otro profesor
            $leccionesConOtrosProfs = \DB::table('lessons')
                ->select('inicio', 'final')
                ->where('id_alum', auth()->user()->id)
                ->where('id_cal', $dia_id)
                ->get();

            if (isset($leccionesConOtrosProfs[0])) {

                foreach ($leccionesConOtrosProfs as $lec_otros) {

                    if (($hora_ini >= $lec_otros->inicio && $hora_ini <= $lec_otros->final) || ($lec_otros->inicio >= $hora_ini && $lec_otros->inicio <= $hora_fin))
                    {
                        return array(True, 'La lección solicitada se solapa con otra lección que ya tienes con otro profesor.');
                    }

                }

            }

            return array(False, 'Tu lección ha sido solicitada correctamente. Ahora debes esperar a que el profesor la confirme.');
        }

        //return comprobarConflictos($dia_id, $hora_ini, $hora_fin);

        // ------------------------------------------------------------------------------

        // Si no hay info de hora en la solicitud (el ususario quiere cancelar su clase(s) de ese día), y para esa fecha había info en base de datos, eliminar el registro en la base de datos. Si no había nada para ese día en la base de datos, simplemente creara la variable $conflicto.
        if (empty(request('hora'))) {

            if (isset($leccion_guardada[0])) {
            
                $leccion_cancelar = Lesson::find($leccion_guardada[0]->id);
                $leccion_cancelar->delete();

                $conflicto = array(False, 'Tu lección ha sido cancelada correctamente.');

            } else {

                $conflicto = array(True, 'No tenías ninguna lección ese día. Si quieres crear una, introduce una hora.');

            }
        
        }

        // Si hay info de hora en la solicitud, determinar si hay conflicto con slots y con lecciones con otros alumnos
        if (!empty(request('hora'))) {

            // Comprobar conflictos de tipo horario
            $conflicto = comprobarConflictos($dia_id, $hora_ini, $hora_fin);

            if ($conflicto[0] == True) {
                
                //return Redirect::back()->withErrors($conflicto[1]);
            }

            // Si no hay conflicto hacer INSERT o UPDATE
            if ($conflicto[0] == False) {

                if (isset($leccion_guardada[0])) {

                    // Actualizar lección            
                    $leccion_actualizada = Lesson::find($leccion_guardada[0]->id);
                    $leccion_actualizada->inicio = $hora_ini;
                    $leccion_actualizada->final = $hora_fin;
                    $leccion_actualizada->status = 'solicitada';
                    $leccion_actualizada->save();
                
                } else {

                    // Crear una nueva lección
                    $nueva_leccion = new Lesson;
                    $nueva_leccion->id_cal = $dia[0]->id;
                    $nueva_leccion->inicio = $hora_ini;
                    $nueva_leccion->final = $hora_fin;
                    $nueva_leccion->id_prof = request('prof');
                    $nueva_leccion->id_alum = auth()->user()->id;
                    $nueva_leccion->status = 'solicitada';
                    $nueva_leccion->save();

                }

            }
        }

    	return Redirect::back()->withErrors($conflicto);

    }

    public function confirmar($id) {

        $lesson = Lesson::find($id);
        $lesson->status = 'confirmada';
        $lesson->save();

        return back();

    }
}
