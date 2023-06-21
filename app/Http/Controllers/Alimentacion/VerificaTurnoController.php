<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion\TurnoComida;
use App\Models\Alimentacion\MenuCabecera;
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

    //vista para comprobar que el empleado va a ir a comer (solo los comprobados, podrán ser aprobados desde TH)
    public function vistaComprobar(){
        $alimento=DB::table('alimento')->where('estado','A')->get();
        return view('alimentacion.comprobar_comida_empl',[
            "alimento"=>$alimento
        ]);
    }

    //busca los alimentos disponibles del empleado en el dia actual
    public function consultaComida (Request $request){

        $cedula=$request->cedula_func;
        if(!is_numeric($cedula)){
            return response()->json([
                'error'=>true,
                'mensaje'=>'Debe ingresar solo números'
            ]);
        }
        
        //procedemos a buscar si tiene alimentos registrados (estado=P) en el dia actual
        $turnos_registrados=DB::table('al_turno_comida as tc')
        ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
        ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
        ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
        ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
        ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
        ->leftJoin('area as a', 'a.id_area','e.id_area')
        ->whereDate('tu.start', date('Y-m-d'))
        ->whereIn('tc.estado',['Generado','Confirmado'])  
        ->where('e.cedula',$cedula)      
        ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno','tu.start as fecha_turno', 'al.descripcion as comida', 'tc.estado as estado_turno','tc.id_turno_comida as id_turno_comida','tc.id_alimento',
        'tc.hora_retira_comida','tc.estado_retira_comida')
        ->get(); 

        if(sizeof($turnos_registrados)==0){

            return response()->json([
                'error'=>true,
                'mensaje'=>'No se encontró alimento registrados para el día de hoy con número de identificación ingresado'
            ]);
        }
        $id_alim_lista=[];
        foreach($turnos_registrados as $lista){
           array_push($id_alim_lista, $lista->id_alimento);
        }
       
        //consultamos el menu de cada comida del empleado
        $menuDelDia=MenuCabecera::with('detalle')
        ->where('fecha',date('Y-m-d'))
        ->whereIn('id_alimento', $id_alim_lista)
        ->get();
        
        if(sizeof($menuDelDia)>0){
            foreach($menuDelDia as $key=>$menu){
                foreach($turnos_registrados as $lista_t){
                    if($lista_t->id_alimento === $menu->id_alimento){
                        $menuDelDia[$key]->id_turno_comida=$lista_t->id_turno_comida;
                        $menuDelDia[$key]->comida=$lista_t->comida;
                        $menuDelDia[$key]->estado_comida=$lista_t->estado_turno;
                    }
                }
            }
        }else{
            return response()->json([
                'error'=>true,
                'mensaje'=>'No se encontró el menú del día registrado'
            ]);
        }

        $detalleMenu=["Menu"=>$menuDelDia, "fecha"=>date('d-m-Y')];
       
        return response()->json([
            'error'=>false,
            'data'=>$turnos_registrados[0],
            'detalleMenu'=>$detalleMenu
        ]);
        
        
    }

    //aprobar las comidas chequeadas x el empleado
    public function confirmaComidas (Request $request){
        $transaction=DB::transaction(function() use($request){
            try{
                // dd($request->all());
                $array_errores=[];
                $aprobados=0;
                //actualizamos el estado de los turno (alimento seleccionado)
                foreach($request->alimentos_chequeados as $ali){
                    $confirmarAli=TurnoComida::where('estado','Generado')
                    ->where('id_turno_comida',$ali)->first();
                    //comprobamos la hora maxima de aprobacion x parte de TH o JOB de cada alimento
                    $hora_valida=DB::table('alimento')
                    ->where('estado','A')
                    ->where('idalimento', $confirmarAli->id_alimento)
                    ->first();
                  
                    if(!is_null($hora_valida)){
                        $hora_valida_ax=$hora_valida->hora_max_aprobacion;
                        $solo_hora = explode(":", $hora_valida_ax);
                        if($solo_hora[0]<12){
                            $formato="AM";
                        }else{
                            $formato="PM";
                        }
                        if(strtotime($hora_valida_ax) < strtotime(date('H:i'))){
                            $sms='La hora máxima de aprobación para el tipo de alimento '.$hora_valida->descripcion.' es '.$hora_valida->hora_max_aprobacion. " ".$formato;
                            array_push($array_errores,$sms);
                        }else{
                            $confirmarAli->estado="Confirmado";
                            $confirmarAli->fecha_hora_confirma_emp=date('Y-m-d H:i:s');
                            $confirmarAli->confirma_empleado="Si";
                            $confirmarAli->ip_confirma=$_SERVER['REMOTE_ADDR'];
                            $confirmarAli->save();
                            $aprobados=$aprobados+1;
                        }
                    }
                }
               
                if($aprobados==0){
                    if(sizeof($array_errores)>0){
                        
                        return response()->json([
                            'error'=>true,
                            'mensaje'=>$array_errores,
                        ]);
                    }
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>"No se pudo afirmar el alimento",
                    ]);
                }else{
                    if(sizeof($array_errores)>0){
                        $error_cant=sizeof($array_errores);
                        return response()->json([
                            'error'=>false,
                            'mensaje'=>"Fueron aprobado(s) ".$aprobados. " alimentos y ".$error_cant. " no se pudieron aprobar",
                            "inconsistencia"=>'S'
                        ]);
                    }else{
                        return response()->json([
                            'error'=>false,
                            'mensaje'=>"Fueron aprobados todos los alimentos seleccionados",
                            "inconsistencia"=>'N'
                        ]);
                    }
                }
               
            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error('VerificaTurnoController => confirmaComidas => mensaje => '.$e->getMessage().' linea_error => '.$e->getLine());
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error',
                ]);
                
            }
        });
        return $transaction;
    }

}