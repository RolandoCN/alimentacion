<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Alimentacion\Empleado;
use App\Models\Alimentacion\Area;
use App\Models\Alimentacion\Puesto;

use App\Models\Alimentacion\Turno;
use App\Models\Alimentacion\TurnoComida;

use \Log;
use DB;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
      
    public function index(){

        // $empleado=DB::table('empleado as e')
        // ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
        // ->whereIn('e.id_puesto',[3,4,10])   
        // ->select('e.id_empleado','e.cedula', 'e.nombres', 'pu.nombre as puesto')
        // ->get();
        
        // foreach($empleado as $data){
        //     log::info("emplea ".$data->id_empleado);
        //     $turno=Turno::where('id_persona', $data->id_empleado)
        //     ->whereDate('start','>','2024-01-12')
        //     // ->where('id_horario','!=',4)
        //     ->where('estado','P')->get();
        //     if(sizeof($turno)>0){

        //         foreach($turno as $cab){
                   
        //             // $actualizacabecera=Turno::where('id',$cab->id)->first();
                    
        //             $actualizacabecera=Turno::where('id',$cab->id)->first();
        //             // $actualizacabecera->id_horario=4;
        //             // 
                    
                  
        //             $eliminaDetalleTurno=TurnoComida::where('id_turno',$actualizacabecera->id)->delete();

        //             $actualizacabecera->delete();

        //             // $id_iali=2;
        //             // for($i=0; $i<=2; $i++){
                        
        //             //     log::info($id_iali);

        //             //     log::info(" id_iali ".$actualizacabecera->id);

        //             //     $guarda_turno_comida=new TurnoComida();
        //             //     $guarda_turno_comida->id_alimento=$id_iali;
        //             //     $guarda_turno_comida->id_turno=$actualizacabecera->id;
        //             //     $guarda_turno_comida->estado="Generado"; //cuando se registra
        //             //     $guarda_turno_comida->fecha_registro=date('Y-m-d H:i:s');
        //             //     $guarda_turno_comida->id_usuario_reg=10;
        //             //     $guarda_turno_comida->save();
        //             //     $id_iali=$id_iali+1;

                        
        //             // }
        //         }

              
              
        //     }
           
        // }

        $area=Area::where('estado','A')->get();
        $puesto=Puesto::where('estado','A')->get();
        return view('alimentacion.empleado',[
            "area"=>$area,
            "puesto"=>$puesto
        ]);
    }


    public function listar(){
        try{
            $empleado=DB::table('empleado as e')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where('e.estado','=','A')        
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','e.id_empleado','e.notificado','e.telefono','e.pin','e.id_empleado')
            ->get();

            foreach($empleado as $key =>$data){
                if($data->pin){
                    $empleado[$key]->generad="Generado";
                }else{
                    $empleado[$key]->generad="Pendiente";
                }

                if($data->notificado){
                    $empleado[$key]->notifi="Enviado";
                }else{
                    $empleado[$key]->notifi="Pendiente";
                }
            }
            $superAdmin=auth()->user()->perfil->nombre_perfil->descripcion;
           
            return response()->json([
                'error'=>false,
                'resultado'=>$empleado,
                'superAdmin'=>$superAdmin
            ]);
        }catch (\Throwable $e) {
            Log::error('EmpleadoController => listar => mensaje => '.$e->getMessage());
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
            Log::error('EmpleadoController => editar => mensaje => '.$e->getMessage());
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
            
            //validar que la cedula no se repita
            $validar_cedula=Empleado::where('cedula', $request->cedula)
            ->whereIn('estado',['A','I'])
            ->first();
         
            if(!is_null($validar_cedula)){
                if($validar_cedula->estado=="A"){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'El número de identificación ya existe'
                    ]);
                }else{
                    //ha sido eliminado lo actualizamos

                    $actualiza_empleado= Empleado::find($validar_cedula->id_empleado);
                    $actualiza_empleado->cedula=$request->cedula;
                    $actualiza_empleado->nombres=$request->nombres;
                    $actualiza_empleado->id_puesto=$request->idpuesto;
                    $actualiza_empleado->id_area=$request->idarea;
                    $actualiza_empleado->id_usuario_act=auth()->user()->id;
                    $actualiza_empleado->fecha_act=date('Y-m-d H:i:s');
                    $actualiza_empleado->estado="A";

                    if($actualiza_empleado->save()){
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
                }
            }

            $guarda_empleado=new Empleado();
            $guarda_empleado->cedula=$request->cedula;
            $guarda_empleado->nombres=$request->nombres;
            $guarda_empleado->id_puesto=$request->idpuesto;
            $guarda_empleado->id_area=$request->idarea;
            $guarda_empleado->id_usuario_reg=auth()->user()->id;
            $guarda_empleado->fecha_reg=date('Y-m-d H:i:s');
            $guarda_empleado->estado="A";

           
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
            Log::error('EmpleadoController => guardar => mensaje => '.$e->getMessage());
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
            Log::error('EmpleadoController => actualizar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function eliminar($id){
        try{
            //verificamos que no este asociado a un turno
            /*$veri_Turno=DB::table('al_turno')
            ->where('id_persona',$id)
            ->where('estado','!=','E')
            ->first();
            if(!is_null($veri_Turno)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La persona está relacionada, no se puede eliminar'
                ]);
            }*/

            $empleado=Empleado::find($id);
          
            $empleado->id_usuario_act=auth()->user()->id;
            $empleado->fecha_act=date('Y-m-d H:i:s');
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
            Log::error('EmpleadoController => eliminar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function notifica($id){
        try{
            $empleado=Empleado::find($id);
          
            $empleado->id_usuario_act=auth()->user()->id;
            $empleado->fecha_act=date('Y-m-d H:i:s');
            $empleado->notificado="S";
            if($empleado->save()){
                return response()->json([
                    'error'=>false,
                    'mensaje'=>'Información actualizada exitosamente'
                ]);
            }else{
                return response()->json([
                    'error'=>false,
                    'mensaje'=>'No se pudo actualizar la información'
                ]);
            }
               
        }catch (\Throwable $e) {
            Log::error('EmpleadoController => notifica => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function vistaPin(){
        
        $pin=rand(1000,9999);
        // dd($pin);
        return view('alimentacion.pin_empleado');
    }


    public function listarPin(){
        try{
            $empleado=DB::table('empleado as e')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where('e.estado','=','A')        
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','e.id_empleado','e.notificado','e.telefono','e.pin','e.id_empleado')
            ->get();

            foreach($empleado as $key =>$data){
                if($data->pin){
                    $empleado[$key]->generad="Generado";
                }else{
                    $empleado[$key]->generad="Pendiente";
                }

                if($data->notificado){
                    $empleado[$key]->notifi="Enviado";
                }else{
                    $empleado[$key]->notifi="Pendiente";
                }
            }
            $superAdmin=auth()->user()->perfil->nombre_perfil->descripcion;
           
            return response()->json([
                'error'=>false,
                'resultado'=>$empleado,
                'superAdmin'=>$superAdmin
            ]);
        }catch (\Throwable $e) {
            Log::error('EmpleadoController => listarPin => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
    
}
