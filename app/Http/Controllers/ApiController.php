<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use \Log;
use DB;
use App\Models\Alimentacion\TurnoComida;
use App\Models\Alimentacion\MenuCabecera;
use App\Models\Alimentacion\Empleado;
use Illuminate\Http\Request;
use PDF;
use Storage;
use SplFileInfo;

class ApiController extends Controller
{
      
    public function index(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
        dd("ssas");
    }

    public function consultaComidaApi (Request $request){
       
        $transaction=DB::transaction(function() use($request){

            try{
               
                $cedula=$request->cedula_func;
                $telef_pin=$request->telef_pin;
                if(!is_numeric($cedula)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'Debe ingresar solo números, para el campo cedula'
                    ]);
                }
                //comrpobamos si no tiene pin si no lo tiene le agregamos
                $tiene_pin=Empleado::where('cedula',$cedula)->first();
            
                if(is_null($tiene_pin)){
                    //si no existe en la bd
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'No se encontro empleado con el numero de cedula ingresado'
                    ]);
                }else{
                    
                    if($tiene_pin->estado!="A"){
                        //si fue eliminado
                        return response()->json([
                            'error'=>true,
                            'mensaje'=>'El empleado a sido dado de baja'
                        ]);
                    }
                
                    //si no ha sido notificado validamos el telefono
                    if($tiene_pin->notificado!="S"){
                    
                        if(!is_numeric($telef_pin)){
                            return response()->json([
                                'error'=>true,
                                'mensaje'=>'Debe ingresar solo números, para el campo telefono'
                            ]);
                        }
                        //validamosel telefono
                        $cantidaddigitos=strlen($telef_pin);
                        if($cantidaddigitos!=10){
                            return response()->json([
                                'error'=>true,
                                'mensaje'=>'El numero de telefono debe tener 10 digitos'
                            ]);
                        }
                        $tel=substr($telef_pin, 1, 10);
                        $telefono='593'.$tel;
                        $existeTel=Empleado::where('telefono',$telefono)
                        ->where('cedula','!=',$cedula)
                        ->first();
                        
                        if(!is_null($existeTel)){
                            return response()->json([
                                'error'=>true,
                                'mensaje'=>'Ya existe el telefono ingresado en otro empleado'
                            ]);
                        }
                        //ingresamos el telefono
                        $tele=$telef_pin;
                        $tel=substr($tele, 1, 10);
                        $tiene_pin->telefono='593'.$tel;
                        $tiene_pin->save();
                    
                        //si no tiene pin
                        if(is_null($tiene_pin->pin)){
                            //creamos
                            $crearPin=$this->creaPin(4);
                            

                            $tiene_pin->pin=$crearPin;
                            $tiene_pin->save();
                        }
                    }else{
                        //ya tiene pin y fue notificado

                        //comprobamos q el pin sea correcto para el usuario
                        $valida_pin=Empleado::where('cedula',$cedula)
                        ->where('pin',$telef_pin)
                        ->first();
                    
                        if(is_null($valida_pin)){
                            return response()->json([
                                'error'=>true,
                                'mensaje'=>'El pin ingresado es incorrecto'
                            ]);
                        }
                
                    }

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
                ->where('tu.estado','!=','E')
                
                // ->whereIn('tc.estado',['Generado','Confirmado'])  
                ->where('e.cedula',$cedula)      
                ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno','tu.start as fecha_turno', 'al.descripcion as comida', 'tc.estado as estado_turno','tc.id_turno_comida as id_turno_comida','tc.id_alimento',
                'tc.hora_retira_comida','tc.estado_retira_comida','tc.confirma_empleado','tc.motivo_eliminacion')
                ->get(); 
                // dd($turnos_registrados);

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
                                $menuDelDia[$key]->confirma_empleado=$lista_t->confirma_empleado;
                                $menuDelDia[$key]->motivo_eliminacion=$lista_t->motivo_eliminacion;

                                if($lista_t->confirma_empleado=="Si" && $lista_t->estado_comida!="Eliminado"){
                                    $menuDelDia[$key]->chequear=true;
                                }else{
                                    $menuDelDia[$key]->chequear=false;
                                }
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
                    'detalleMenu'=>$detalleMenu,
                
                ]);
            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error('VerificaTurnoController => consultaComida => mensaje => '.$e->getMessage().' linea_error => '.$e->getLine());
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error, intentelo mas tarde',
                ]);
                
            }
        });
        return $transaction;
            
        
    }
}