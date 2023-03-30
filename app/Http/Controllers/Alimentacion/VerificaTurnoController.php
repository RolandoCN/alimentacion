<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion\TurnoComida;
use \Log;
use Illuminate\Http\Request;
use DB;
use PDF;
use Storage;
use SplFileInfo;

class VerificaTurnoController extends Controller
{
    //vista para verificar la comida x un funcionario 
    public function vistaVerifica(){
        $alimento=DB::table('alimento')->where('estado','A')->get();
        return view('alimentacion.verifica_comida',[
            "alimento"=>$alimento
        ]);
    }

    public function validarComida (Request $request){

        $cedula=$request->cedula_func;
        if(!is_numeric($cedula)){
            return response()->json([
                'error'=>true,
                'mensaje'=>'Debe ingresar solo números'
            ]);
        }
        
        //primero validamos que se encuentre en el rango de hora de uno de los alimentos
        $hora_actual=date('H:i');
        
        $verificarRangoFecha=DB::table('alimento')->
        where(function($c)use($hora_actual) {
            $c->whereTime('hora_min', '<=', $hora_actual)
            ->whereTime('hora_max', '>=', $hora_actual);
        })
        ->where('estado','A')
        ->first();
        if(is_null($verificarRangoFecha)){
            return response()->json([
                'error'=>true,
                'mensaje'=>'No se encontró alimentos disponibles a esta hora'
            ]);
        }

        //procedemos a buscar si tiene alimentos aprobados de acuerdo al horario del rango encontrado
        $turnos_aprobado=DB::table('al_turno_comida as tc')
        ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
        ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
        ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
        ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
        ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
        ->leftJoin('area as a', 'a.id_area','e.id_area')
        ->where('tc.id_alimento',$verificarRangoFecha->idalimento)
        ->whereDate('tu.start', date('Y-m-d'))
        ->where('tc.estado','=','Aprobado')  
        ->where('e.cedula',$cedula)      
        ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno','tu.start as fecha_turno', 'al.descripcion as comida', 'tc.estado as estado_turno','tc.id_turno_comida as id_turno_comida',
        'tc.hora_retira_comida','tc.estado_retira_comida')
        ->first(); 

        if(is_null($turnos_aprobado)){

            $turnos_dias=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->whereDate('tu.start', date('Y-m-d'))
            ->where('tc.estado','!=','Eliminado')  
            ->where('e.cedula',$cedula)      
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno','tu.start as fecha_turno', 'al.descripcion as comida', 'tc.estado as estado_turno','tc.id_turno_comida as id_turno_comida',
            'tc.hora_retira_comida','tc.estado_retira_comida')
            ->first(); 

            if(!is_null($turnos_dias)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'El alimento está fuera de su horario laboral, que es de '.$turnos_dias->hora_ini.' a '.$turnos_dias->hora_fin
                ]);

            }else{
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La persona no tiene asignado horarios de alimentos el día de hoy'
                ]);
            }


            return response()->json([
                'error'=>true,
                'mensaje'=>'No se encontró alimento aprobado para el número de identificación ingresado'
            ]);
        }


        if($turnos_aprobado->estado_retira_comida=="Si"){
            return response()->json([
                'error'=>true,
                'mensaje'=>'Usted ya realizo la comprobación del alimento el '.$turnos_aprobado->hora_retira_comida
            ]);
        }

        //cambiamos el estado para indicar que la comida a sido verificada desde pantalla comedor
        $turno_cambia_estado=TurnoComida::find($turnos_aprobado->id_turno_comida);
        $turno_cambia_estado->estado_retira_comida="Si";
        $turno_cambia_estado->hora_retira_comida=date('Y-m-d H:i:s');
        if($turno_cambia_estado->save()){
            return response()->json([
                'error'=>false,
                'data'=>$turnos_aprobado
            ]);
        }else{
            return response()->json([
                'error'=>true,
                'mensaje'=>'No se pudo cambiar el estado de recibimiento de alimento'
            ]);
        }
        
        
    }

    //listado de los turnos comidas diferente de eliminado
    public function turnosFecha($fecha, $idalimento){
       
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where('tc.id_alimento',$idalimento)
            ->whereDate('tu.start', $fecha)
            ->where('tc.estado','!=','Eliminado')        
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno')
            ->get();

            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ListadoTurnoController => turnosFecha => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
}