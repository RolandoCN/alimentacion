<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;

use \Log;
use DB;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    
  
    public function index(){
        
        return view('alimentacion.auditoria');
    }


    public function buscarInfoTurnos($ini, $fin){
        try{
            $audi_turnos=DB::table('al_turno as t')
            ->leftJoin('empleado as e', 'e.id_empleado','t.id_persona')
            ->leftJoin('horario as h', 'h.id_horario','t.id_horario')  
            ->leftJoin('users as ui', 'ui.id','t.id_usuario_reg') 
            ->leftJoin('persona as pui', 'pui.idpersona','ui.id_persona') 
            ->leftJoin('users as ua', 'ua.id','t.id_usuario_act') 
            ->leftJoin('persona as pua', 'pua.idpersona','ua.id_persona')    
            ->select('t.start as fecha_comida', 't.estado as estado_turno',
             't.motivo_elimina', 't.fecha_reg','pui.nombres as nombre_user_ingresa','pui.apellidos as apellidos_user_ingresa','pua.nombres as nombre_user_actualiza','pua.apellidos as apellidos_user_actualiza','t.fecha_act', 'h.descripcion as desc_horario','h.hora_ini','h.hora_fin','e.nombres as nombre_empleado')
            ->where(function($query)use($ini, $fin){
                $query->whereDate('t.fecha_reg', '>=', $ini)
                ->whereDate('t.fecha_reg', '<=',  $fin);
            })
            ->get();
            return response()->json([
                'error'=>false,
                'resultado'=>$audi_turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('AuditoriaController => listar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function editar($id){
        try{
            $empleado=Empleado::where('estado','A')
            ->where('id_empleado', $id)
            ->first();
            
            return response()->json([
                'error'=>false,
                'resultado'=>$empleado
            ]);
        }catch (\Throwable $e) {
            Log::error('AuditoriaController => editar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
    

    public function guardar(Request $request){
        
        $messages = [
            'cedula.required' => 'Debe ingresar la cédula',  
            'nombres.required' => 'Debe ingresar los nombres',           
            'idpuesto.required' => 'Debe seleccionar el puesto',  
            'idarea.required' => 'Debe seleccionar el área',  

        ];
           

        $rules = [
            'cedula' =>"required|string|max:10",
            'nombres' =>"required|string|max:100",
            'idpuesto' =>"required",
            'idarea' =>"required",
                     
        ];

        $this->validate($request, $rules, $messages);
        try{

            $validaCedula=validarCedula($request->cedula);
            if($validaCedula==false){
                return response()->json([
                    "error"=>true,
                    "mensaje"=>"El numero de identificacion ingresado no es valido"
                ]);
            }          

            $guarda_empleado=new Empleado();
            $guarda_empleado->cedula=$request->cedula;
            $guarda_empleado->nombres=$request->nombres;
            $guarda_empleado->id_puesto=$request->idpuesto;
            $guarda_empleado->id_area=$request->idarea;
            $guarda_empleado->id_usuario_reg=auth()->user()->id;
            $guarda_empleado->fecha_reg=date('Y-m-d H:i:s');
            $guarda_empleado->estado="A";

            //validar que la cedula no se repita
            $valida_cedula=Empleado::where('cedula', $guarda_empleado->cedula)
            ->where('estado','A')
            ->first();

            if(!is_null($valida_cedula)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'El número de cédula ya existe, en otro empleado'
                ]);
            }

           
            if($guarda_empleado->save()){
                return response()->json([
                    'error'=>false,
                    'mensaje'=>'Información registrada exitosamente'
                ]);
            }else{
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'No se pudo registrar la información'
                ]);
            }


        }catch (\Throwable $e) {
            Log::error('AuditoriaController => guardar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }


    public function actualizar(Request $request, $id){
           
        $messages = [
            'cedula.required' => 'Debe ingresar la cédula',  
            'nombres.required' => 'Debe ingresar los nombres',           
            'idpuesto.required' => 'Debe seleccionar el puesto',  
            'idarea.required' => 'Debe seleccionar el área',  

        ];
           

        $rules = [
            'cedula' =>"required|string|max:10",
            'nombres' =>"required|string|max:100",
            'idpuesto' =>"required",
            'idarea' =>"required",
                     
        ];

        $this->validate($request, $rules, $messages);
        try{

            $validaCedula=validarCedula($request->cedula);
            if($validaCedula==false){
                return response()->json([
                    "error"=>true,
                    "mensaje"=>"El numero de identificacion ingresado no es valido"
                ]);
            }   
            
            $actualiza_empleado= Empleado::find($id);
            $actualiza_empleado->cedula=$request->cedula;
            $actualiza_empleado->nombres=$request->nombres;
            $actualiza_empleado->id_puesto=$request->idpuesto;
            $actualiza_empleado->id_area=$request->idarea;
            $actualiza_empleado->id_usuario_act=auth()->user()->id;
            $actualiza_empleado->fecha_act=date('Y-m-d H:i:s');
            $actualiza_empleado->estado="A";

            //validar que la cedula no se repita
            $valida_cedula=Empleado::where('cedula', $actualiza_empleado->cedula)
            ->where('estado','A')
            ->where('id_empleado','!=', $id)
            ->first();

            if(!is_null($valida_cedula)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'El número de cédula ya existe, en otra persona'
                ]);
            }

            
            if($actualiza_empleado->save()){
                return response()->json([
                    'error'=>false,
                    'mensaje'=>'Información actualizada exitosamente'
                ]);
            }else{
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'No se pudo actualizar la información'
                ]);
            }

        }catch (\Throwable $e) {
            Log::error('AuditoriaController => actualizar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function eliminar($id){
        try{
            //verificamos que no este asociado a un turno
            $veri_Turno=DB::table('al_turno')
            ->where('id_persona',$id)
            ->where('estado','!=','E')
            ->first();
            if(!is_null($veri_Turno)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La persona está relacionada, no se puede eliminar'
                ]);
            }

            $empleado=Empleado::find($id);
            $empleado->id_usuario_act=auth()->user()->id;
            $empleado->fecha_actualiza=date('Y-m-d H:i:s');
            $empleado->estado="I";
            if($empleado->save()){
                return response()->json([
                    'error'=>false,
                    'mensaje'=>'Información eliminada exitosamente'
                ]);
            }else{
                return response()->json([
                    'error'=>false,
                    'mensaje'=>'No se pudo eliminar la información'
                ]);
            }
               
        }catch (\Throwable $e) {
            Log::error('AuditoriaController => eliminar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }
    
}
