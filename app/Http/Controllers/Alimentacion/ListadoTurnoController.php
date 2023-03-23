<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion\HorarioAlimento;
use App\Models\Alimentacion\Turno;
use App\Models\Alimentacion\TurnoComida;
use \Log;
use Illuminate\Http\Request;
use DB;
use PDF;
use Storage;
use SplFileInfo;

class ListadoTurnoController extends Controller
{
    //vista para buscar y aprobar 
    public function index(){
        $alimento=DB::table('alimento')->where('estado','A')->get();
        return view('alimentacion.turno.listado',[
            "alimento"=>$alimento
        ]);
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

    //vista de turnos comidas ya aprobados
    public function vistaAprobado(){
        $alimento=DB::table('alimento')->where('estado','A')->get();
        return view('alimentacion.turno.listado_aprobados',[
            "alimento"=>$alimento
        ]);
    }

    //listado de los turnos comidas aprobados
    public function comidasAprobadas($fecha, $idalimento){
       
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('users as u', 'u.id','tc.id_usuario_aprueba')
            ->leftJoin('persona as p', 'p.idpersona','u.id_persona')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where('tc.id_alimento',$idalimento)
            ->whereDate('tu.start', $fecha)
            ->where('tc.estado','=','Aprobado')        
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno', 'p.nombres as nombre_user_apr', 'p.apellidos as apellido_user_apr', 'u.id as iduser', 'tc.fecha_aprobacion')
           
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

    public function descargar($fecha, $idalimento){
       
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('users as u', 'u.id','tc.id_usuario_aprueba')
            ->leftJoin('persona as p', 'p.idpersona','u.id_persona')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where('tc.id_alimento',$idalimento)
            ->whereDate('tu.start', $fecha)
            ->where('tc.estado','=','Aprobado')        
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'tu.start as fecha_turno' , 'al.descripcion as comida', 'tc.estado as estado_turno', 'p.nombres as nombre_user_apr', 'p.apellidos as apellido_user_apr', 'u.id as iduser', 'tc.fecha_aprobacion')
            ->get();
                        
          
            $nombre="reporte_listado_comida_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";
            //creamos el objeto
            // $pdf=new PDF();
            //habilitamos la opcion php para mostrar la paginacion
            // $crearpdf=$pdf::setOptions(['isPhpEnabled'=>true]);
            // enviamos a la vista para crear el documento que los datos repsectivos
            $crearpdf->loadView('alimentacion.turno.pdf_aprobado_dia_alimento',['datos'=>$turnos]);
            $crearpdf->setPaper("A4", "landscape");

            return $crearpdf->stream($nombre."_".date('YmdHis').'.pdf');

            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ListadoTurnoController => turnosFecha => mensaje => '.$e->getMessage(). ' Linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function descargarAprobacionFechaInd(Request $request){
        try{
            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('users as u', 'u.id','tc.id_usuario_aprueba')
            ->leftJoin('persona as p', 'p.idpersona','u.id_persona')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where('tc.id_alimento',$request->comida_sel)
            ->whereDate('tu.start', $request->fecha_sele)
            ->where('tc.estado','=','Aprobado')        
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'tu.start as fecha_turno' , 'al.descripcion as comida', 'tc.estado as estado_turno', 'p.nombres as nombre_user_apr', 'p.apellidos as apellido_user_apr', 'u.id as iduser', 'tc.fecha_aprobacion')
            ->get();

            if(sizeof($turnos)==0){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error'
                ]);
            }

            $nombrePDF="reporte_listado_comida_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";
           
            // enviamos a la vista para crear el documento que los datos repsectivos
            $crearpdf=PDF::loadView('alimentacion.turno.pdf_aprobado_dia_alimento',['datos'=>$turnos]);
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

            // return $crearpdf->stream($nombre."_".date('YmdHis').'.pdf');




        }catch (\Throwable $e) {
            Log::error('ListadoTurnoController => descargarAprobacionFechaInd => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function descargarPdf($archivo){
        try{   
        
            $exists_destino = Storage::disk('public')->exists($archivo); 

            if($exists_destino){
                return response()->download( storage_path('app/public/'.$archivo))->deleteFileAfterSend(true);
            }else{
                return back()->with(['error'=>'Ocurrió un error','estadoP'=>'danger']);
            } 

        } catch (\Throwable $th) {
            Log::error("ListadoTurnoController =>descargarPdf =>sms => ".$th->getMessage());
            return back()->with(['error'=>'Ocurrió un error','estadoP'=>'danger']);
        } 
    }

   
    public function aprobacionTurno(Request $request){
        
        $transaction=DB::transaction(function() use($request){ 
            try{
              
                //validaciones

                $valida_estado=Turno::whereIn('id', $request->array_turnos)
                ->where('estado','!=', 'E') //solo si no ha sido eliminado
                ->first();

                $fecha_turno=$valida_estado->start;
            
                //validamos para no permitir aprobar turnos con fecha anteriot a la actual
                if(strtotime($fecha_turno) < strtotime(date('Y-m-d'))){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'La fecha de aprobación no puede menor a la fecha actual'
                    ]);
                }

                $id_comida=$request->comida_sel;

                //validamos que segun el tipo de comida eleccionada controle la hora maxima de aprobacion
                $hora_valida=DB::table('alimento')
                ->where('estado','A')
                ->where('idalimento', $id_comida)
                ->select('hora_max_aprobacion')
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
                        return response()->json([
                            'error'=>true,
                            'mensaje'=>'La hora máxima de aprobación para el tipo de alimento seleccionado es '.$hora_valida->hora_max_aprobacion. " ".$formato
                        ]);
                    }
                }
                //cambiamos el estado de la tabla turno
                $aprobar=Turno::whereIn('id',$request->array_turnos)
                ->update(["estado"=>'A', "fecha_act"=>date('Y-m-d H:i:s'),
                "id_usuario_act"=>auth()->user()->id]);

                //aprobamos la tabla turno_comida
                $aprobar_turno_comida=TurnoComida::whereIn('id_turno',$request->array_turnos)
                ->where('id_alimento',$id_comida)
                ->update(['estado'=>'Aprobado', 'id_usuario_aprueba'=>auth()->user()->id,
                'fecha_aprobacion'=>date('Y-m-d H:i:s')]);

                return response()->json([
                    'error'=>false,
                    'mensaje'=>'Información aprobada exitosamente'
                ]);
    
            
            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error('ListadoTurnoController => aprobacionTurno => mensaje => '.$e->getMessage());
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error'
                ]);
                
            }
        });
        return $transaction;
    }


    public function actualizar(Request $request, $id){
        $messages = [
            'descripcion.required' => 'Debe ingresar la descripción',
            'url.required' => 'Debe ingresar la url',           
        ];
           

        $rules = [
            'descripcion' =>"required|string|max:100",
            'url' =>"required|string|max:100",
        ];
        $this->validate($request, $rules, $messages);
        try{

            $actualiza_menu= Menu::find($id);
            $actualiza_menu->descripcion=$request->descripcion;
            $actualiza_menu->url=$request->url;
            $actualiza_menu->id_usuario_act=auth()->user()->id;
            $actualiza_menu->fecha_actualiza=date('Y-m-d H:i:s');
            $actualiza_menu->estado="A";

            //validar que el menu no se repita
            $valida_menu=Menu::where('descripcion', $actualiza_menu->descripcion)
            ->where('estado','A')
            ->where('id_menu','!=',$id)
            ->first();

            if(!is_null($valida_menu)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La gestión ya existe'
                ]);
            }

            //validar que la url no se repita
            $valida_url=Menu::where('url', $actualiza_menu->url)
            ->where('estado','A')
            ->where('id_menu','!=',$id)
            ->first();

            if(!is_null($valida_url)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La url ya existe'
                ]);
            }

           
            if($actualiza_menu->save()){
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
            Log::error('ListadoTurnoController => actualizar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function eliminar($id){
        try{
            $menu=Menu::find($id);
            $menu->id_usuario_act=auth()->user()->id;
            $menu->fecha_actualiza=date('Y-m-d H:i:s');
            $menu->estado="I";
            if($menu->save()){
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
            Log::error('ListadoTurnoController => eliminar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

}
