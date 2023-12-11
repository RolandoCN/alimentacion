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

class ReporteController extends Controller
{
    //vista para generar el reporte por usuario y fecha
    public function informeUsuario(){
       
        return view('alimentacion.reporte.usuario_fecha');
    }


    //listado de los alimentos servidos x fecha y empleado
    public function alimentoServidoInd($fecha_ini, $fecha_fin, $idempleado){
        
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tu.id_persona', $idempleado)
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start')
            ->get();

            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ReporteController => alimentoServidoInd => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //test reporte individual_empleado, fechas
    public function testReporte($fecha_ini, $fecha_fin, $idempleado){
        
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tu.id_persona', $idempleado)
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno')
            ->get();

           

            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->fecha_turno])) {
                    $lista_final_agrupada[$item->fecha_turno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->fecha_turno], $item);
                }
            }
           
          
            $nombre="reporte_usuario_fecha_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_individual_fecha',['datos'=>$turnos,'lista'=>$lista_final_agrupada]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

            return $pdf->stream($nombre."_".date('YmdHis').'.pdf');


            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ReporteController => testReporte => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //reporte entre fechas x usuario
    public function descargarAprobacionFechaInd(Request $request){
        
        try{
            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
            $idempleado=$request->id_empleado_rep;

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tu.id_persona', $idempleado)
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno')
            ->get();

            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->fecha_turno])) {
                    $lista_final_agrupada[$item->fecha_turno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->fecha_turno], $item);
                }
            }

            $nombrePDF="reporte_usuario_fecha_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";
          

            $pdf=PDF::LoadView('alimentacion.reporte.pdf_individual_fecha',['datos'=>$turnos,'lista'=>$lista_final_agrupada]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => descargarAprobacionFechaInd => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //vista para visualizar los datos de comidas entre fechas (consolidado)
    public function informePeriodo(){
        return view('alimentacion.reporte.entre_fecha');
    }

    //test reporte x fechas
    public function testReporteFecha($fecha_ini, $fecha_fin){
        
        try{

            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno')
            ->get();


            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->fecha_turno])) {
                    $lista_final_agrupada[$item->fecha_turno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->fecha_turno], $item);
                }
            }
        
            // enviamos a la vista para crear el documento que los datos repsectivos
            $crearpdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $crearpdf->setPaper("A4", "landscape");

           
            $nombre="reporte_entre_fecha_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";
            //creamos el objeto            

            return $crearpdf->stream($nombre."_".date('YmdHis').'.pdf');


            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ReporteController => testReporte => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //listado de los alimentos servidos x fechas (consolidado)
    public function alimentoServidoPeriodo($fecha_ini, $fecha_fin){
        
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start')
            ->get();

            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ReporteController => alimentoServidoPeriodo => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //reporte entre fechas personal
    public function reportePeriodo(Request $request){
        
        try{
            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
           
            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno')
            ->orderBy('fecha_turno','asc')
            ->get();

         
            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->fecha_turno])) {
                    $lista_final_agrupada[$item->fecha_turno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->fecha_turno], $item);
                }
            }

            $nombrePDF="reporte_entre_fecha_".date('d-m-Y').".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reportePeriodo => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //vista para visualizar los datos de comidas entre fechas (aprobadas)
    public function informePeriodoAprobados(){
        return view('alimentacion.reporte.entre_fecha_aprobados');
    }

    //listado de los alimentos aprobados x fechas (consolidado)
    public function alimentoAprobadoPeriodo($fecha_ini, $fecha_fin, $estado){
        
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where(function ($query2) use($estado) {
                if($estado=="Si"){
                    $query2->where('tc.estado_retira_comida', $estado);
                }
                if($estado=="No"){
                    $query2->where('tc.estado_retira_comida', null);
                }                    
            })
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start', 'tc.ip_confirma', 'tc.fecha_hora_confirma_emp', 'tc.estado_retira_comida')
            ->get();

            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ReporteController => alimentoAprobadoPeriodo => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //reporte entre fechas aprobados  empleado y sistema
    public function reportePeriodoAprob(Request $request){
        
        try{
            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
            $estado=$request->estado;
           
            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where(function ($query2) use($estado) {
                if($estado=="Si"){
                    $query2->where('tc.estado_retira_comida', $estado);
                }
                if($estado=="No"){
                    $query2->where('tc.estado_retira_comida', null);
                }                    
            })
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno', 'tc.estado_retira_comida')
            ->orderBy('fecha_turno','asc')
            ->get();

         
            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->fecha_turno])) {
                    $lista_final_agrupada[$item->fecha_turno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->fecha_turno], $item);
                }
            }

            $nombrePDF="reporte_entre_fecha_aprobados".date('d-m-Y').".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_aprobado',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin, "estado"=>$estado]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reportePeriodoAprob => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function reportePeriodoAprobTodos(Request $request){
        
        try{
            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
            $estado=$request->estado;
           
            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno', 'tc.estado_retira_comida')
            ->orderBy('fecha_turno','asc')
            ->get();

         
            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->fecha_turno])) {
                    $lista_final_agrupada[$item->fecha_turno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->fecha_turno], $item);
                }
            }

            $nombrePDF="reporte_entre_fecha_aprobados".date('d-m-Y').".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_aprobado_todos',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin, "estado"=>$estado]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reportePeriodoAprob => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
    //confirmado x el empleado, aprobado x sistema, no retirados en comedor
    public function reporteAprobadoNoRetirado(Request $request){
        try{
            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=',null)   // no retirado desde comedor   
            ->where('tc.confirma_empleado','=','Si') //confirmado  
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno', 'tc.ip_confirma')
            ->orderBy('fecha_turno','asc')
            ->orderBy('a.id_area','asc')
            ->get();

            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->idturno])) {
                    $lista_final_agrupada[$item->idturno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->idturno], $item);
                }
            }

            #agrupamos por area
            $lista_final_agrupada_area=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada_area[$item->area])) {
                    $lista_final_agrupada_area[$item->area]=array($item);
            
                }else{
                    array_push($lista_final_agrupada_area[$item->area], $item);
                }
            }
            // dd($lista_final_agrupada_area);
           
            
            $nombrePDF="reporte_fechas_detallado_no_retirado".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_det_no_ret',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin,'lista_area'=>$lista_final_agrupada_area]);
            $pdf->setPaper("A4", "landscape");
            $estadoarch = $pdf->stream();
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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reporteAprobadoNoRetirado => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //confirmado x el empleado, aprobado x sistema,  retirados en comedor x area
    public function reporteAprobadoRetiradoArea(Request $request){
        try{
            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   // retirado desde comedor   
            ->where('tc.confirma_empleado','=','Si') //confirmado  
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno', 'tc.ip_confirma')
            ->orderBy('fecha_turno','asc')
            ->orderBy('a.id_area','asc')
            ->get();

            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->idturno])) {
                    $lista_final_agrupada[$item->idturno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->idturno], $item);
                }
            }

            #agrupamos por area
            $lista_final_agrupada_area=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada_area[$item->area])) {
                    $lista_final_agrupada_area[$item->area]=array($item);
            
                }else{
                    array_push($lista_final_agrupada_area[$item->area], $item);
                }
            }
            // dd($lista_final_agrupada_area);
           
            
            $nombrePDF="reporte_fechas_detallado_retirado".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_det_ret',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin,'lista_area'=>$lista_final_agrupada_area]);
            $pdf->setPaper("A4", "landscape");
            $estadoarch = $pdf->stream();
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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reporteAprobadoRetiradoArea => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //confirmado x el empleado detallado x ip
    public function reporteConfirmadoIp(Request $request){
        try{
            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado  
            ->where('tc.confirma_empleado','=','Si') //confirmado  
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno', 'tc.ip_confirma','tc.estado_retira_comida')
            ->orderBy('fecha_turno','asc')
            ->orderBy('a.id_area','asc')
            ->get();

            #agrupamos por ip
            $lista_final_agrupada_ret=[];
            $lista_final_agrupada_no_ret=[];
            foreach ($turnos as $key => $item){ 
                if($item->estado_retira_comida=="Si"){
                    if(!isset($lista_final_agrupada_ret[$item->ip_confirma])) {
                        $lista_final_agrupada_ret[$item->ip_confirma]=array($item);
                
                    }else{
                        array_push($lista_final_agrupada_ret[$item->ip_confirma], $item);
                    }
                }else{
                    if(!isset($lista_final_agrupada_no_ret[$item->ip_confirma])) {
                        $lista_final_agrupada_no_ret[$item->ip_confirma]=array($item);
                
                    }else{
                        array_push($lista_final_agrupada_no_ret[$item->ip_confirma], $item);
                    }
                }              
                    
            }

            // dd($lista_final_agrupada_no_ret);
            $nombrePDF="reporte_fechas_detallado_confirmado_ip".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_confirmado_ip',['datos'=>$turnos,'lista_ip_no_ret'=>$lista_final_agrupada_no_ret, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin,'lista_ip_ret'=>$lista_final_agrupada_ret]);
            $pdf->setPaper("A4", "landscape");
            $estadoarch = $pdf->stream();
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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reporteConfirmadoIp => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }


    //vista para visualizar los datos de comidas entre fechas (detallado)
    public function informeDetallado(){
        return view('alimentacion.reporte.entre_fecha_detallado');
    }

    //listado de los alimentos servidos x fechas (detallado)
    public function alimentoServidoDetallado($fecha_ini, $fecha_fin){
        
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start')
            ->get();

            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ReporteController => alimentoServidoDetallado => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //test reporte x fechas detallado
    public function testReporteFechaDet($fecha_ini, $fecha_fin){
        
        try{

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno')
            ->get();

            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->idturno])) {
                    $lista_final_agrupada[$item->idturno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->idturno], $item);
                }
            }
           
            
            $nombre="reporte_fechas_detallado_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_det',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $pdf->setPaper("A4", "landscape");
            // $estadoarch = $pdf->stream();


            return $pdf->stream($nombre."_".date('YmdHis').'.pdf');


            return response()->json([
                'error'=>false,
                'resultado'=>$turnos
            ]);
        }catch (\Throwable $e) {
            Log::error('ReporteController => testReporteFechaDet => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function reporteDetallado(Request $request){
        try{
            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;

            $turnos=DB::table('al_turno_comida as tc')
            ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
            ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
            ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
            ->leftJoin('empleado as e', 'e.id_empleado','tu.id_persona')
            ->leftJoin('puesto as pu', 'pu.id_puesto','e.id_puesto')
            ->leftJoin('area as a', 'a.id_area','e.id_area')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('tu.start', '>=', $fecha_ini)
                ->whereDate('tu.start', '<=', $fecha_fin);
            })
            ->where('tc.estado','=','Aprobado') //aprobado
            ->where('tc.estado_retira_comida','=','Si')   //retirado desde comedor     
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno','tu.id_persona', 'tu.start as fecha_turno')
            ->orderBy('fecha_turno','asc')
            ->get();

            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->idturno])) {
                    $lista_final_agrupada[$item->idturno]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->idturno], $item);
                }
            }
           
            
            $nombrePDF="reporte_fechas_detallado_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_det',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $pdf->setPaper("A4", "landscape");
            $estadoarch = $pdf->stream();
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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reporteDetallado => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //vista para visualizar los datos de comidas entre fechas (consolidado paciente)
    public function informePeriodoPaciente(){
        return view('alimentacion.reporte.entre_fecha_paciente');
    }

    //reporte entre fechas paciente
    public function reportePeriodoPaciente(Request $request){
        
        try{
            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
           
            $turnos=DB::table('al_alimentos_pacientes as ali_pac')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('ali_pac.fecha', '>=', $fecha_ini)
                ->whereDate('ali_pac.fecha', '<=', $fecha_fin);
            })
            ->where('ali_pac.estado','=','Aprobado') //aprobado
            ->select('ali_pac.tipo as comida', 'ali_pac.fecha as fecha', 'ali_pac.dieta')
            ->orderBy('ali_pac.fecha','asc')
            ->orderBy('ali_pac.dieta','asc')
            ->get();
        
            #agrupamos por dias
            $lista_final_agrupada=[];
            foreach ($turnos as $key => $item){                
                if(!isset($lista_final_agrupada[$item->fecha])) {
                    $lista_final_agrupada[$item->fecha]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->fecha], $item);
                }
            }

            $nombrePDF="reporte_entre_fecha_paciente".date('d-m-Y').".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_paciente',['datos'=>$turnos,'lista'=>$lista_final_agrupada, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reportePeriodoPaciente => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //vista para visualizar los datos por peridodo de las dietas
    public function informePeriodoDietaPaciente(){
        return view('alimentacion.reporte.entre_fecha_dietas');
    }

    //reporte entre fechas tipo dietas
    public function reportePeriodoDietaPaciente(Request $request){
        
        try{            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
           
            $listar=DB::table('al_alimentos_pacientes as ali_pac')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('ali_pac.fecha', '>=', $fecha_ini)
                ->whereDate('ali_pac.fecha', '<=', $fecha_fin);
            })
            ->where('ali_pac.estado','=','Aprobado') //aprobado
            ->select('ali_pac.tipo as comida', 'ali_pac.fecha as fecha', 'ali_pac.dieta')
            ->orderBy('ali_pac.fecha','asc')
            ->orderBy('ali_pac.dieta','asc')
            ->get();
        
             #agrupamos por tipo dieta
            $lista_dieta=[];
            foreach ($listar as $key => $item){                
                 if(!isset($lista_dieta[$item->dieta])) {
                     $lista_dieta[$item->dieta]=array($item);
             
                 }else{
                     array_push($lista_dieta[$item->dieta], $item);
                 }
             }

            $nombrePDF="reporte_entre_fecha_dieta".date('d-m-Y').".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_dieta',['dieta'=>$lista_dieta, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reportePeriodoDietaPaciente => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //reporte entre fechas tipo dietas profesional
    public function reportePeriodoDietaPacienteProfes(Request $request){
        
        try{            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
           
            $listar=DB::table('al_alimentos_pacientes as ali_pac')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('ali_pac.fecha', '>=', $fecha_ini)
                ->whereDate('ali_pac.fecha', '<=', $fecha_fin);
            })
            ->where('ali_pac.estado','=','Aprobado') //aprobado
            ->select('ali_pac.tipo as comida', 'ali_pac.fecha as fecha', 'ali_pac.dieta', 'ali_pac.responsable', 'ali_pac.paciente')
            ->orderBy('ali_pac.fecha','asc')
            ->orderBy('ali_pac.dieta','asc')
            ->get();
        
            #agrupamos por tipo dieta
            $lista_dieta=[];
            foreach ($listar as $key => $item){                
                if(!isset($lista_dieta[$item->dieta])) {
                    $lista_dieta[$item->dieta]=array($item);
            
                }else{
                    array_push($lista_dieta[$item->dieta], $item);
                }
            }
           
            #agrupamos por responsable
            $lista_responsable=[];
            foreach ($listar as $key => $item){                
                $responsable = $item->responsable;
                $dieta = $item->dieta;
                $valor = $item->dieta;
            
                // Crear una estructura de agrupación si no existe
                if (!isset($grupos[$responsable])) {
                    $grupos[$responsable] = array();
                }
            
                if (!isset($grupos[$responsable][$dieta])) {
                    $grupos[$responsable][$dieta] = array();
                }
        
                $grupos[$responsable][$dieta][] = $valor;

            }
            // dd($grupos);
            $nombrePDF="reporte_entre_fecha_dieta".date('d-m-Y').".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_dieta_profesional',['dieta'=>$lista_dieta, 'responsable'=>$lista_responsable, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin, 'grupos'=>$grupos]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reportePeriodoDietaPacienteProfes => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function vistaNutricion(){
        
        $area=DB::table('al_alimentos_pacientes')
        ->select('servicio')
        ->distinct('servicio')
        ->get();

        $respo=DB::table('al_alimentos_pacientes')
        ->select('json_dieta')
        ->distinct('json_dieta')
        ->get();

        $array_id_resp=[];
        foreach($respo as $data){
            $json= $data->json_dieta;
            $json=json_decode($json, true);
           
            array_push($array_id_resp, $json['idresponsable']);
           
        }
        //consultamos si es nutricionista
        $nutricionista=DB::table('al_nutricionista')
        ->whereIn('id_sys_persona',$array_id_resp)
        ->select('id_sys_persona','nombres','cedula')
        ->distinct('id_sys_persona')
        ->get();
                        
        return view('alimentacion.reporte.nutricion',[
            "area"=>$area,
            "nutricionista"=>$nutricionista
        ]);
    }

    //reporte entre fechas tipo dietas x area
    public function reportePeriodoDietaArea(Request $request){
        
        try{            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
            $filtro=$request->cmb_filtra_area;
            $area=$request->area;
           
            $listar=DB::table('al_alimentos_pacientes as ali_pac')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('ali_pac.fecha', '>=', $fecha_ini)
                ->whereDate('ali_pac.fecha', '<=', $fecha_fin);
            })
            ->where(function($cQ)use($filtro, $area) {
                if($filtro=="F"){
                    $cQ->where('ali_pac.servicio', $area);
                }
            })
            ->where('ali_pac.estado','=','Aprobado') //aprobado
            ->select('ali_pac.tipo as comida', 'ali_pac.fecha as fecha', 'ali_pac.dieta','ali_pac.servicio','ali_pac.tipo')
            ->orderBy('ali_pac.fecha','asc')
            ->orderBy('ali_pac.tipo','asc')
            ->get();
            
            #agrupamos por area y fecha
            foreach ($listar as $key => $item){                
                $servicio = $item->servicio;
                $fecha = $item->fecha;
                $valor = $item->tipo;
            
                // Crear una estructura de agrupación si no existe
                if (!isset($grupos[$servicio])) {
                    $grupos[$servicio] = array();
                }
            
                if (!isset($grupos[$servicio][$fecha])) {
                    $grupos[$servicio][$fecha] = array();
                }
        
                $grupos[$servicio][$fecha][] = $valor;

            }

           
            $nombrePDF="reporte_entre_fecha_dieta".date('d-m-Y').".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_area',['grupos'=>$grupos, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reportePeriodoDietaPaciente => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    //reporte entre fechas tipo dietas x area
    public function reportePeriodoDietaNutricionista(Request $request){
        
        try{            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$request->fecha_inicial_rep;
            $fecha_fin=$request->fecha_final_rep;
            $filtro=$request->cmb_filtra_nutri;
            $nutri=$request->nutri;
           
            $array_id_resp=[];
            if($filtro=="T"){
                $nutricionista=DB::table('al_nutricionista')
                ->select('id_sys_persona','nombres','cedula')
                ->distinct('id_sys_persona')
                ->get();

                $array_id_resp=[];
                foreach($nutricionista as $data){
                    array_push($array_id_resp, $data->nombres);            
                }
            }
                
            $listar=DB::table('al_alimentos_pacientes as ali_pac')
            ->where(function($c)use($fecha_ini, $fecha_fin) {
                $c->whereDate('ali_pac.fecha', '>=', $fecha_ini)
                ->whereDate('ali_pac.fecha', '<=', $fecha_fin);
            })
            ->where(function($cQ)use($filtro, $nutri, $array_id_resp) {
                if($filtro=="F"){
                    $cQ->where('ali_pac.responsable', $nutri);
                }else {
                    $cQ->whereIn('ali_pac.responsable', $array_id_resp);
                }
            })
            ->where('ali_pac.estado','=','Aprobado') //aprobado
            ->select('ali_pac.fecha as fecha','ali_pac.servicio','ali_pac.responsable','ali_pac.tipo')
            ->orderBy('ali_pac.fecha','asc')
            ->orderBy('ali_pac.responsable','asc')
            ->get();
         
            #agrupamos por prof y fecha
            foreach ($listar as $key => $item){                
                $responsable = $item->responsable;
                $servicio = $item->servicio;
                $valor = $item->tipo;
            
                // Crear una estructura de agrupación si no existe
                if (!isset($grupos[$responsable])) {
                    $grupos[$responsable] = array();
                }
            
                if (!isset($grupos[$responsable][$servicio])) {
                    $grupos[$responsable][$servicio] = array();
                }
        
                $grupos[$responsable][$servicio][] = $valor;

            }
            // dd($grupos);
            $nombrePDF="reporte_entre_fecha_nutri".date('d-m-Y').".pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_entre_fecha_nutri',['grupos'=>$grupos, 'desde'=>$fecha_ini, 'hasta'=>$fecha_fin]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

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
        }catch (\Throwable $e) {
            Log::error('ReporteController => reportePeriodoDietaPaciente => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
}