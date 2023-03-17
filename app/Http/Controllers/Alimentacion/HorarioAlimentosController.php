<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion\Horario;
use App\Models\Alimentacion\HorarioAlimento;
use App\Models\Alimentacion\Alimento;
use App\Models\Alimentacion\GestionMenu;
use App\Models\User;
use \Log;
use Illuminate\Http\Request;
use DB;

class HorarioAlimentosController extends Controller
{
    public function index(){
       
        return view('alimentacion.horario_alimento.index');
    }


    public function listar(){
        try{
            $horario=Horario::where('estado','!=','I')->get();
            return response()->json([
                'error'=>false,
                'resultado'=>$horario
            ]);
        }catch (\Throwable $e) {
            Log::error('HorarioAlimentosController => listar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function editar($id){
        try{
            $horario=Horario::where('estado','A')
            ->where('id_horario', $id)
            ->first();
            
            return response()->json([
                'error'=>false,
                'resultado'=>$horario
            ]);
        }catch (\Throwable $e) {
            Log::error('HorarioAlimentosController => editar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
    

    public function guardar(Request $request){
        
        $messages = [
            'codigo.required' => 'Debe ingresar el código', 
            'descripcion.required' => 'Debe ingresar la descripcion', 
            'hora_ini.required' => 'Debe ingresar la hora de inicio', 
            'hora_fin.required' => 'Debe ingresar la hora de fin',             
        ];
           

        $rules = [
            'codigo' =>"required|string|max:100",
            'descripcion' =>"required|string|max:100",
            'hora_ini' =>"required",
            'hora_fin' =>"required",
                 
        ];

        $this->validate($request, $rules, $messages);
        try{

            $guarda_horario= new Horario();
            $guarda_horario->descripcion=$request->descripcion;
            $guarda_horario->codigo=$request->codigo;
            $guarda_horario->hora_ini=$request->hora_ini;
            $guarda_horario->hora_fin=$request->hora_fin;
            $guarda_horario->id_usuario_reg=auth()->user()->id;
            $guarda_horario->fecha_registro=date('Y-m-d H:i:s');
            $guarda_horario->estado="A";

            //validar que el horario no se repita
            $valida_rol=Horario::where('descripcion', $guarda_horario->descripcion)
            ->where('estado','A')
            ->first();

            if(!is_null($valida_rol)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La descripción del horario ya existe'
                ]);
            }

            //validar que el codigo horario no se repita
            $valida_rol=Horario::where('codigo', $guarda_horario->codigo)
            ->where('estado','A')
            ->first();

            if(!is_null($valida_rol)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La descripción del horario ya existe'
                ]);
            }

           
            if($guarda_horario->save()){
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
            Log::error('HorarioAlimentosController => guardar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }


    public function actualizar(Request $request, $id){
       
    
        $messages = [
            'codigo.required' => 'Debe ingresar el código', 
            'descripcion.required' => 'Debe ingresar la descripcion', 
            'hora_ini.required' => 'Debe ingresar la hora de inicio', 
            'hora_fin.required' => 'Debe ingresar la hora de fin',             
        ];
           

        $rules = [
            'codigo' =>"required|string|max:100",
            'descripcion' =>"required|string|max:100",
            'hora_ini' =>"required",
            'hora_fin' =>"required",
                 
        ];

        $this->validate($request, $rules, $messages);
        try{

            $actualiza_horario= Horario::find($id);
            $actualiza_horario->descripcion=$request->descripcion;
            $actualiza_horario->codigo=$request->codigo;
            $actualiza_horario->hora_ini=$request->hora_ini;
            $actualiza_horario->hora_fin=$request->hora_fin;
            $actualiza_horario->id_usuario_act=auth()->user()->id;
            $actualiza_horario->fecha_act=date('Y-m-d H:i:s');
            $actualiza_horario->estado="A";

            //validar que el horario no se repita
            $valida_rol=Horario::where('descripcion', $actualiza_horario->descripcion)
            ->where('estado','A')
            ->where('id_horario','!=', $id)
            ->first();

            if(!is_null($valida_rol)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La descripción del horario ya existe'
                ]);
            }

            //validar que el codigo horario no se repita
            $valida_rol=Horario::where('codigo', $actualiza_horario->codigo)
            ->where('estado','A')
            ->where('id_horario','!=', $id)
            ->first();

            if(!is_null($valida_rol)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La descripción del horario ya existe'
                ]);
            }

           
            if($actualiza_horario->save()){
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
            Log::error('HorarioAlimentosController => actualizar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function alimentosHorario($id){
        try{
            $alimentos=Alimento::where('estado','=','A')->get();
            foreach($alimentos as $key=> $data){
                $verifica=HorarioAlimento::where('id_horario',$id)
                ->where('idalimento',$data->idalimento)->first();
                if(!is_null($verifica)){
                    $alimentos[$key]->accesoPerm="S";
                }else{
                    $alimentos[$key]->accesoPerm="N";
                }
            }
            $horario=Horario::find($id);
            return response()->json([
                'error'=>false,
                'resultado'=>$alimentos,
                'horario'=>$horario
            ]);
               
        }catch (\Throwable $e) {
            Log::error('HorarioAlimentosController => alimentosHorario => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function mantenimientoAlimentoHorario($idali, $tipo, $idhor){
       
        try{
            //agregamos
            if($tipo=="A"){
                //obtenemos el id de la gestion del menu
                $horario_ali= new HorarioAlimento();
                $horario_ali->idalimento=$idali;
                $horario_ali->id_horario=$idhor;
                $horario_ali->save();
                return response()->json([
                    'error'=>false,
                    'mensaje'=>'Información registrada exitosamente'
                ]);
            }else{
                //lo quitamos
                $quitar=HorarioAlimento::where('idalimento',$idali)
                ->where('id_horario',$idhor)->first();
                $quitar->delete();
                return response()->json([
                    'error'=>false,
                    'mensaje'=>'Información registrada exitosamente'
                ]);
            }
           

        }catch (\Throwable $e) {
            Log::error('HorarioAlimentosController => mantenimientoAlimentoHorario => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function eliminar($id){
        try{
            //verificamos que no este relacionda
             
            $veri_Turno=DB::table('al_turno')
            ->where('id_horario',$id)
            ->where('estado','!=','E')
            ->first();
            if(!is_null($veri_Turno)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'El horario está relacionado, no se puede eliminar'
                ]);
            }
            
            $horario=Horario::find($id);
            $horario->id_usuario_act=auth()->user()->id;
            $horario->fecha_act=date('Y-m-d H:i:s');
            $horario->estado="I";
            if($horario->save()){
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
            Log::error('HorarioAlimentosController => eliminar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function datoPerfil(){
        $data=User::with('persona','perfil')->where('id',auth()->user()->id)->first();
       
        return response()->json([
            "error"=>false,
            "data"=>$data
        ]);

      
    }


}
