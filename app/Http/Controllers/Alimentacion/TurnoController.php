<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion\Menu;
use App\Models\Alimentacion\Turno;
use App\Models\Alimentacion\TurnoComida;
use App\Models\Alimentacion\Horario;
use App\Models\Persona;
use App\Models\Alimentacion\Empleado;
use \Log;
use DB;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
      
    public function index(){
        $TipoTurno=Horario::where('estado','A')->get();
        return view('alimentacion.turno.busqueda1',[
            "TipoTurno"=>$TipoTurno
        ]);
    }

    public function buscarPersona(Request $request){

        $data = [];
        if($request->has('q')){
            $search = $request->q;
            $text=mb_strtoupper($search);
            $data=Empleado::where(function($query)use($text){
                $query->where('nombres', 'like', '%'.$text.'%')
                ->orWhere('cedula', 'like', '%'.$text.'%');
            })
            ->take(10)->get();
        }
        
        return response()->json($data);

    }

    public function infoPersona($id_empleado){
        $empleado=Empleado::where('id_empleado', $id_empleado)->get();
       
        return response()->json([
            'error'=>false,
            'resultado'=>$empleado
        ]);
    }


    public function mostrarAux($id, $interno=null){
      
        $data=DB::table('al_turno as turnoev')
        ->leftJoin('horario as tipo', 'tipo.id_horario','turnoev.id_horario')
        ->where('turnoev.id_persona',$id)
        ->where('turnoev.estado','!=','E')
        ->select('turnoev.estado','turnoev.id as id', 'turnoev.start','turnoev.end', DB::raw("CONCAT(tipo.codigo, ' -- [', tipo.hora_ini, ' - ', tipo.hora_fin,']') AS title"))
        ->get();
     
        // $empleado=Empleado::where('id_empleado', $id)->first();

        $empleado=DB::table('empleado as e')
        ->leftJoin('puesto as p', 'p.id_puesto','e.id_puesto')
        ->leftJoin('area as a', 'a.id_area','e.id_area')
        ->where('p.estado','A')
        ->where('a.estado','A')
        ->where('e.estado','A')
        ->where('e.id_empleado',$id)
        ->select('e.id_empleado', 'e.cedula', 'e.nombres','p.nombre as puesto','a.nombre as area')
        ->first();

        if($interno==null){
            return response()->json([
                "data"=>$data,
                "empleado"=>$empleado,
            
            ]);
        }else{
            return["data"=>$data];
        }
    }

    public function asignar(Request $request){

        $transaction=DB::transaction(function() use($request){
            try{ 
                if(is_null($request->Turno)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'Debe seleccionar un turno'
                    ]);
                }

                if(is_null($request->idpers)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'No se pudo obtener la información del empleado'
                    ]);
                }
                
                $event=new Turno();
                $event->start=$request->fecha_inicio;
                $event->end= date('Y-m-d', strtotime("{$event->start} + 1 day"));
                $event->id_horario=$request->Turno;
                $event->id_persona=$request->idpers;
                $event->estado="P";
                $event->id_usuario_reg=auth()->user()->id;
                $event->fecha_reg=date('Y-m-d H:i:s');

                //turno
                $horarios=DB::table('horario')
                ->where('id_horario',$event->id_horario)
                ->where('estado','=','A')
                ->first();
                if(is_null($horarios)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'El turno seleccionado no se encuentra activo'
                    ]);
                }

                
                //comprobamos si existen alimentos asociados al horario
                $horario_ali=DB::table('horario_alimento')
                ->where('id_horario',$event->id_horario)
                ->get();

                if(sizeof($horario_ali)==0){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'No se encontró alimentos asociadas al turno seleccionado'
                    ]);
                }
                
                //validamos que no se repita
                $valida=Turno::where('id_horario',$event->id_horario)
                ->whereDate('start',$event->start)
                ->where('id_persona',$event->id_persona)
                ->where('estado','!=','E')
                ->first();
                if(!is_null($valida)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'La información ya existe'
                    ]);
                }

                //no dejamos ingresar mas de un turno en el mismo dia
                $ya_tiene=Turno::whereDate('start',$event->start)
                ->where('id_persona',$event->id_persona)
                ->where('estado','!=','E')
                ->first();
                if(!is_null($ya_tiene)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'Ya existe un turno para el día seleccionado'
                    ]);
                }
            
                if($event->save()){

                    //recorremos los alimentos del horario seleccionado
                    foreach($horario_ali as $dato){
                        //guardamos cada una de las comidas asociadas al horario del turno
                        $guarda_turno_comida=new TurnoComida();
                        $guarda_turno_comida->id_alimento=$dato->idalimento;
                        $guarda_turno_comida->id_turno=$event->id;
                        $guarda_turno_comida->estado="Generado"; //cuando se registra
                        $guarda_turno_comida->fecha_registro=date('Y-m-d H:i:s');
                        $guarda_turno_comida->id_usuario_reg=auth()->user()->id;
                        $guarda_turno_comida->save();
                    }

                    $data=DB::table('al_turno as turnoev')
                    ->leftJoin('horario as tipo', 'tipo.id_horario','turnoev.id_horario')
                    ->where('turnoev.id',$event->id)
                    ->select('turnoev.id as id', 'turnoev.start','turnoev.end', DB::raw("CONCAT(tipo.codigo, ' -- [', tipo.hora_ini, ' - ', tipo.hora_fin,']') AS title"))
                    ->get()->last();
                    return response()->json([
                        'error'=>false,
                        'mensaje'=>'Información registrada exitosamente',
                        'dato'=>$data
                    ]);
                }else{
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'No se pudo registrar la información'
                    ]);
                }
            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error('TurnoController => asignar => mensaje => '.$e->getMessage().' linea_error => '.$e->getLine());
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error',
                    'dataArray'=>[]
                ]);
                
            }
        });
        return $transaction;

    }
 
    public function eliminarTurnoComida(Request $request){

        $transaction=DB::transaction(function() use($request){ 
            try{
                $Turno = Turno::find($request->id);
                if($Turno->estado=="A"){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'El turno ya fué aprobado y no se puede eliminar',
                        'dataArray'=>[]
                    ]);
                }
               
                $Turno->estado="E";
                $Turno->id_usuario_act=auth()->user()->id;
                $Turno->fecha_act=date('Y-m-d H:i:s');
                $Turno->save();

                //eliminamos las comidas asociadas al horario del turno
                $elimina_turno_comida=TurnoComida::where('id_turno',$request->id)
                ->update(['estado'=>'Eliminado', 'id_usuario_elim'=>auth()->user()->id,
                'fecha_elimina'=>date('Y-m-d H:i:s')]);

                return response()->json([
                    'error'=>false,
                    'mensaje'=>'Turno eliminado sastifactoriamente'
                ]);
                
            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error('TurnoController => eliminarTurnoComida => mensaje => '.$e->getMessage().' linea_error => '.$e->getLine());
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error',
                    'dataArray'=>[]
                ]);
                
            }
        });
        return $transaction;
    }

    public function consultaTurno($id){
       
        $data=DB::table('al_turno as turnoev')
        ->leftJoin('horario as tipo', 'tipo.id_horario','turnoev.id_horario')
        ->where('turnoev.id_persona',$id)
        ->where('turnoev.estado','!=','E')
        ->select('turnoev.estado','turnoev.id as id', 'turnoev.start','turnoev.end', DB::raw("CONCAT(tipo.codigo, ' -- [', tipo.hora_ini, ' - ', tipo.hora_fin,']') AS title"))
        ->get();

        return[
            "data"=>$data,
        ];
    } 

    public function actualizarTurnoComida(Request $request)
    {
        $transaction=DB::transaction(function() use($request){   
            try{
              
                $event=Turno::find($request->id);
               
                if($event->estado=="A"){
                    $turno=$this->consultaTurno($event->id_persona);
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'El turno ya fué aprobado y no se puede eliminar',
                        'dataArray'=>$turno
                    ]);
                }
               
                $event->start=$request->start;
                $event->end= date('Y-m-d', strtotime("{$event->start} + 1 day"));
                $event->estado="P";
                $event->id_usuario_act=auth()->user()->id;
                $event->fecha_act=date('Y-m-d H:i:s');

                //turno
                $horarios=DB::table('horario')
                ->where('id_horario',$event->id_horario)
                ->where('estado','=','A')
                ->first();
                if(is_null($horarios)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'El turno seleccionado no se encuentra activo',
                        'dataArray'=>$turno
                    ]);
                }

                
                //comprobamos si existen alimentos asociados al horario
                $horario_ali=DB::table('horario_alimento')
                ->where('id_horario',$event->id_horario)
                ->get();

                if(sizeof($horario_ali)==0){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'No se encontró alimentos asociadas al turno seleccionado',
                        'dataArray'=>$turno
                    ]);
                }

                //validamos que no se repita
                $valida=Turno::where('start',$event->start)
                ->where('id_persona',$event->id_persona)
                ->where('id_horario',$event->id_horario)
                ->where('estado','!=','E')
                ->first();
                if(!is_null($valida)){
                    $turno=$this->consultaTurno($event->id_persona);
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'La información ya existe',
                        'dataArray'=>$turno
                    ]);
                }

                //no dejamos ingresar mas de un turno en el mismo dia
                $ya_tiene=Turno::whereDate('start',$event->start)
                ->where('id_persona',$event->id_persona)
                ->where('estado','!=','E')
                ->first();
                if(!is_null($ya_tiene)){
                    $turno=$this->consultaTurno($event->id_persona);
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'Ya existe un turno para el día seleccionado',
                        'dataArray'=>$turno
                    ]);
                }
                        
                if($event->save()){

                    //primero eliminamos las comidas asociadas al horario del turno
                    $elimina_turno_comida=TurnoComida::where('id_turno',$event->id)
                    ->update(['estado'=>'Eliminado', 'id_usuario_elim'=>auth()->user()->id,
                    'fecha_elimina'=>date('Y-m-d H:i:s')]);

                    //recorremos los alimentos del horario seleccionado
                    foreach($horario_ali as $dato){
                        //guardamos cada una de las comidas asociadas al horario del turno
                        $guarda_turno_comida=new TurnoComida();
                        $guarda_turno_comida->id_alimento=$dato->idalimento;
                        $guarda_turno_comida->id_turno=$event->id;
                        $guarda_turno_comida->estado="Generado"; //cuando se registra
                        $guarda_turno_comida->fecha_registro=date('Y-m-d H:i:s');
                        $guarda_turno_comida->id_usuario_reg=auth()->user()->id;
                        $guarda_turno_comida->save();
                    }


                    return response()->json([
                        'error'=>false,
                        'mensaje'=>'Turno actualizado sastifactoriamente'
                    ]);
                }else{
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'No se pudo actualizada la información',
                        'dataArray'=>$turno
                    ]);
                }
                
            
            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error('TurnoController => actualizarTurnoComida => mensaje => '.$e->getMessage().' linea_error => '.$e->getLine());
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error',
                    'dataArray'=>[]
                ]);
                
            }
        });
        return $transaction;
    }
}