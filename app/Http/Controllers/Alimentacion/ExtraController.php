<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Alimentacion\Alimento;
use App\Models\Alimentacion\ExtraAlimento;
use \Log;
use DB;
use PDF;
use Storage;
use Illuminate\Http\Request;

class ExtraController extends Controller
{
    
  
    public function index(){
        $alimento=Alimento::where('estado','A')->get();
        return view('alimentacion.extra.extra_alim',[
            "alimento"=>$alimento
        ]);
    }


    public function listar(){
        try{
            $extra_ali=DB::table('al_alimentos_extra as ae')
            ->leftJoin('empleado as e', 'e.id_empleado','ae.id_empleado')
            ->leftJoin('alimento as a', 'a.idalimento','ae.id_alimento')
            ->where('ae.estado', 'A')
            ->select('ae.idalimentos_extra', 'ae.fecha', 'ae.motivo', 'a.descripcion as alimento', 
            'e.cedula','e.nombres')
            ->get();
            return response()->json([
                'error'=>false,
                'resultado'=>$extra_ali
            ]);
        }catch (\Throwable $e) {
            Log::error('ExtraController => listar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function guardar(Request $request){
        
        $messages = [
            'id_empleado.required' => 'Debe seleccionar un empleado',  
            'fecha.required' => 'Debe seleccionar la fecha',           
            'motivo.required' => 'Debe ingresar el motivo',  
            'id_alimento.required' => 'Debe seleccionar el alimento',  

        ];
           

        $rules = [
            'id_empleado' =>"required|string|max:10",
            'fecha' =>"required",
            'motivo' =>"required|string|max:100",
            'id_alimento' =>"required",
                     
        ];

        $this->validate($request, $rules, $messages);
        try{     

            $guarda_alimento=new ExtraAlimento();
            $guarda_alimento->id_empleado=$request->id_empleado;
            $guarda_alimento->fecha=$request->fecha;
            $guarda_alimento->motivo=$request->motivo;
            $guarda_alimento->id_alimento=$request->id_alimento;
            $guarda_alimento->id_usuario_reg=auth()->user()->id;
            $guarda_alimento->fecha_reg=date('Y-m-d H:i:s');
            $guarda_alimento->estado="A";

            //validar que no se repita
            $valida_cedula=ExtraAlimento::where('id_empleado', $guarda_alimento->id_empleado)
            ->where('fecha',$guarda_alimento->fecha)
            ->where('id_alimento', $guarda_alimento->id_alimento)
            ->where('estado','A')
            ->first();

            if(!is_null($valida_cedula)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La solicitud ya existe'
                ]);
            }

           
            if($guarda_alimento->save()){
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
            Log::error('ExtraController => guardar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function eliminar($id){
        try{
            $extra=ExtraAlimento::find($id);
            $extra->id_usuario_act=auth()->user()->id;
            $extra->fecha_actualiza=date('Y-m-d H:i:s');
            $extra->estado="I";
            if($extra->save()){
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
            Log::error('ExtraController => eliminar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function vistaReporte(){
        return view('alimentacion.extra.reporte_extra_alim');
    }

    public function alimentoExtraFechas($fecha_in, $fecha_fin){
        try{
            $extra_ali=DB::table('al_alimentos_extra as ae')
            ->leftJoin('empleado as e', 'e.id_empleado','ae.id_empleado')
            ->leftJoin('alimento as a', 'a.idalimento','ae.id_alimento')
            ->where('ae.estado', 'A')
            ->where(function($query)use($fecha_in, $fecha_fin){
                $query->whereDate('ae.fecha', '>=', $fecha_in)
                ->whereDate('ae.fecha', '<=',  $fecha_fin);
            })
            ->select('ae.idalimentos_extra', 'ae.fecha', 'ae.motivo', 'a.descripcion as alimento', 
            'e.cedula','e.nombres')
            ->get();
            return response()->json([
                'error'=>false,
                'resultado'=>$extra_ali
            ]);
        }catch (\Throwable $e) {
            Log::error('ExtraController => alimentoExtraFechas => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function testreporteExtraFechas($fecha_ini, $fecha_fin){
        try{
            $extra_ali=DB::table('al_alimentos_extra as ae')
            ->leftJoin('empleado as e', 'e.id_empleado','ae.id_empleado')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('alimento as a', 'a.idalimento','ae.id_alimento')
            ->where('ae.estado', 'A')
            ->where(function($query)use($fecha_ini, $fecha_fin){
                $query->whereDate('ae.fecha', '>=', $fecha_ini)
                ->whereDate('ae.fecha', '<=',  $fecha_fin);
            })
            ->select('ae.idalimentos_extra', 'ae.fecha', 'ae.motivo', 'a.descripcion as alimento', 
            'e.cedula','e.nombres','pu.nombre as puestos')
            ->get();

            $crearpdf=PDF::loadView('alimentacion.extra.pdf_entre_fecha_extra',['lista_datos'=>$extra_ali,'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $crearpdf->setPaper("A4", "landscape");

           
            $nombre="reporte_entre_fecha_".date('d-m-Y').".pdf";
            //creamos el objeto            

            return $crearpdf->stream($nombre."_".date('YmdHis').'.pdf');



            return response()->json([
                'error'=>false,
                'resultado'=>$extra_ali
            ]);
        }catch (\Throwable $e) {
            Log::error('ExtraController => testreporteExtraFechas => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function reporteExtraFechas(Request $request){
        try{
            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;

            $extra_ali=DB::table('al_alimentos_extra as ae')
            ->leftJoin('empleado as e', 'e.id_empleado','ae.id_empleado')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('alimento as a', 'a.idalimento','ae.id_alimento')
            ->where('ae.estado', 'A')
            ->where(function($query)use($fecha_ini, $fecha_fin){
                $query->whereDate('ae.fecha', '>=', $fecha_ini)
                ->whereDate('ae.fecha', '<=',  $fecha_fin);
            })
            ->select('ae.idalimentos_extra', 'ae.fecha', 'ae.motivo', 'a.descripcion as alimento', 
            'e.cedula','e.nombres','pu.nombre as puestos')
            ->get();

            $nombrePDF="reporte_extra_".date('d-m-Y').".pdf";

            $crearpdf=PDF::loadView('alimentacion.extra.pdf_entre_fecha_extra',['lista_datos'=>$extra_ali,'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $crearpdf->setPaper("A4", "landscape");

            $estadoarch = $crearpdf->stream();

            //lo guardamos en el disco temporal
            Storage::disk('public')->put(str_replace("", "",$nombrePDF), $estadoarch);
            $exists_destino = Storage::disk('public')->exists($nombrePDF); 
            if($exists_destino){ 
                return response()->json([
                    'error'=>false,
                    'pdf'=>$nombrePDF
                ]);
            }else{
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'No se pudo crear el documento'
                ]);
            }



            return response()->json([
                'error'=>false,
                'resultado'=>$extra_ali
            ]);
        }catch (\Throwable $e) {
            Log::error('ExtraController => reporteExtraFechas => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
    
}
