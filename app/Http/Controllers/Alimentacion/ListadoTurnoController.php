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
use Mail;

class ListadoTurnoController extends Controller
{
    //vista para buscar y aprobar 
    public function index(){

       /* $turno=Turno::with('detalle')->where('start','2023-10-07')
        ->where('estado','P')->get();
        foreach($turno as $data){
            $turnoComida=TurnoComida::where('estado','Generado')
            ->where('id_turno',$data->id)
            ->where('id_alimento',2)
            ->first();
            if(!is_null($turnoComida)){
                $turnoComida->confirma_empleado="Si";
                $turnoComida->ip_confirma="0.0.0.0";
                $turnoComida->estado="Confirmado";
                $turnoComida->fecha_hora_confirma_emp=date("Y-m-d H:i:s");
                $turnoComida->save();
            }
           
        }
        dd("ss");*/

       
        $alimento=DB::table('alimento')->where('estado','A')->get();
        return view('alimentacion.turno.listado',[
            "alimento"=>$alimento
        ]);
    }

    //vista para ver el listado sin opcion a aprobar
    public function turnosGenerados(){
        $alimento=DB::table('alimento')->where('estado','A')->get();
        return view('alimentacion.turno.listado_generado',[
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
            ->select('e.cedula', 'e.nombres', 'pu.nombre as puesto','a.nombre as area','h.hora_ini as hora_ini', 'h.hora_fin as hora_fin', 'tu.id as idturno', 'al.descripcion as comida', 'tc.estado as estado_turno', 'tc.id_turno_comida')
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
                return [
                    'error'=>true,
                    'mensaje'=>'No se encontró información disponibles para aprobar'
                ];
            }

            $nombrePDF="reporte_listado_comida_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";
           
            // enviamos a la vista para crear el documento que los datos repsectivos
            $crearpdf=PDF::loadView('alimentacion.turno.pdf_aprobado_dia_alimento',['datos'=>$turnos]);
            $crearpdf->setPaper("A4", "portrait");
            $estadoarch = $crearpdf->stream();

            //lo guardamos en el disco temporal
            Storage::disk('public')->put(str_replace("", "",$nombrePDF), $estadoarch);
            $exists_destino = Storage::disk('public')->exists($nombrePDF); 
            if($exists_destino){ 
                return [
                    'error'=>false,
                    'pdf'=>$nombrePDF
                ];
            }else{
                return [
                    'error'=>true,
                    'mensaje'=>'No se pudo crear el documento'
                ];
            }

            // return $crearpdf->stream($nombre."_".date('YmdHis').'.pdf');




        }catch (\Throwable $e) {
            Log::error('ListadoTurnoController => descargarAprobacionFechaInd => mensaje => '.$e->getMessage());
            return [
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ];
            
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
                              
                $valida_estado=Turno::whereIn('id', $request->array_turnos)
                ->where('estado','!=', 'E') //solo si no ha sido eliminado
                ->first();

                $fecha_turno=$valida_estado->start;
            
                //validamos para no permitir aprobar turnos con fecha anteriot a la actual
                if(strtotime($fecha_turno) < strtotime(date('Y-m-d'))){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'La fecha de aprobación no puede ser menor a la fecha actual'
                    ]);
                }

                $id_comida=$request->comida_sel;

                //comprobamos si no ha sido aporbado
                $valida_aprobado=TurnoComida::with('usuario_aprueba')
                ->whereIn('id_turno', $request->array_turnos)
                ->where('estado','=', 'Aprobado') 
                ->where('id_alimento','=', $id_comida) 
                ->first();
               
                if(!is_null($valida_aprobado)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'La informacion ya fue aprobada por '.$valida_aprobado->usuario_aprueba->persona->nombres." ".$valida_aprobado->usuario_aprueba->persona->apellidos." a las ".date("H:i", strtotime($valida_aprobado->fecha_aprobacion))
                    ]);
                }

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

                //comprobamos que la cantidad confirmada no haya cambiado durante el proceso de envio
                $turnos_confirmados=DB::table('al_turno_comida as tc')
                ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
                ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
                ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
                ->where('tc.id_alimento',$id_comida)
                ->whereDate('tu.start', $fecha_turno)
                ->where('tc.estado','Confirmado')        
                ->select('tu.id as idturno','tc.estado as estado_ap')
                ->get();

                if(sizeof($turnos_confirmados) != sizeof($request->array_turnos)){
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>'La cantidad de confirmados aumento por favor vuelve a enviar la solicitud',
                        'diferencia'=>'S'
                    ]);
                }             
                
                //cambiamos el estado de la tabla turno
                $aprobar=Turno::whereIn('id',$request->array_turnos)
                ->where('estado', 'P')
                ->update(["estado"=>'A', "fecha_act"=>date('Y-m-d H:i:s'),
                "id_usuario_act"=>auth()->user()->id]);

                //aprobamos la tabla turno_comida
                $aprobar_turno_comida=TurnoComida::whereIn('id_turno',$request->array_turnos)
                ->where('id_alimento',$id_comida)
                // ->where('estado', 'Generado')
                ->where('estado', 'Confirmado')
                ->update(['estado'=>'Aprobado', 'id_usuario_aprueba'=>auth()->user()->id,
                'fecha_aprobacion'=>date('Y-m-d H:i:s')]);

                //informacion de la comida
                $comida_con=DB::table('alimento')
                ->where('idalimento',$request->comida_sel)
                ->first();
                $comida=$comida_con->descripcion;
                //mandamos a generar el documento para enviarlo x correo
                $generaPdf=$this->descargarAprobacionFechaInd($request);
                if($generaPdf['error']==false){
                    
                    //se creo lo enviamos
                    $fecha_apr=date('d-m-Y',strtotime($request->fecha_sele));
                    $archivo=Storage::disk('public')->get($generaPdf['pdf']);
                    $nombrearchivo=$generaPdf['pdf'];
                    
                    //consultamos el correo donde enviaremos el documento
                    $correo_param=DB::table('al_parametros')
                    ->where('codigo','ECAA')->first();
                    if(!is_null($correo_param)){
                        $correo_db_par=$correo_param->valor;
                    }else{
                        $correo_db_par="juanrolandocn@gmail.com";
                    }
                    //correos parametrizados
                    $correos_enviar=explode(",", $correo_db_par);
                   
                    try{
                        
                        foreach($correos_enviar as $email){

                            $correo_envio=$email;

                            Mail::send('email_documentos.aprobacion_alimento', ['comida'=>$comida,"fecha_apr"=>$fecha_apr, "correo"=>$correo_envio], function ($m) use ($correo_envio,$archivo, $nombrearchivo, $comida, $fecha_apr) {
                                $m->to($correo_envio)
                                ->subject("Aprobación de ".$comida." del ".$fecha_apr)
                                
                                ->attachData($archivo, $nombrearchivo, [
                                    'mime' => 'application/pdf',
                                ]);
                            
                            });  
                        }

                        $archivo=Storage::disk('public')->delete($nombrearchivo);

                        return response()->json([
                            'error'=>false,
                            'mensaje'=>'Información aprobada y enviada exitosamente'
                        ]);

                    } catch (\Throwable $th) {
                       
                        $archivo=Storage::disk('public')->delete($nombrearchivo);

                        Log::error('ListadoTurnoController, enviarCorreoAprobacion '.$th->getMessage()." Linea ".$th->getLine());

                        return response()->json([
                            'error'=>false,
                            'mensaje'=>'Información fué aprobada exitosamente, pero no se pudo enviar al correo '
                        ]);
                    }
                }else{
                    return response()->json([
                        'error'=>true,
                        'mensaje'=>$generaPdf['mensaje']
                    ]); 
                }

            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error('ListadoTurnoController => aprobacionTurno => mensaje => '.$e->getMessage().' linea => '.$e->getLine());
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error'
                ]);
                
            }
        });
        return $transaction;
    }

    //para realizar la aprobacion desde el job en caso de que se olviden realizarlo 
    public function aprobarAlimentoJob($idalimento){
        $transaction=DB::transaction(function() use($idalimento){ 
            try{

                $fecha=date('Y-m-d');  
               
                //informacion de la comida
                $comida_con=DB::table('alimento')
                ->where('idalimento',$idalimento)
                ->first();
                $comida=$comida_con->descripcion;
               
                //consultamos los de estados generados y aprobado
                $turnos=DB::table('al_turno_comida as tc')
                ->leftJoin('alimento as al', 'al.idalimento','tc.id_alimento')
                ->leftJoin('al_turno as tu', 'tu.id','tc.id_turno')
                ->leftJoin('horario as h', 'h.id_horario','tu.id_horario')
                ->where('tc.id_alimento',$idalimento)
                ->whereDate('tu.start', $fecha)
                // ->whereIn('tc.estado',['Generado','Aprobado']) 
                ->whereIn('tc.estado',['Confirmado','Aprobado'])        
                ->select('tu.id as idturno','tc.estado as estado_ap')
                ->get();
                
               
                $id_turnos_array=[];
                foreach($turnos as $data){
                    //si encuentra uno aprobado 
                    if($data->estado_ap=='Aprobado'){
                       
                        Log::info('No se pudo realizar la aprobación mediante JOB del alimento '.$comida. ' del día '.date('d-m-Y'). ' ya que ya se encuentra aprobada manualmente');

                        return 'No se pudo realizar la aprobación mediante JOB del alimento '.$comida. ' del día '.date('d-m-Y'). ' ya que ya se encuentra aprobada manualmente';

                    }else{
                        array_push($id_turnos_array, $data->idturno);
                    }
                   
                }

                //si existen generados listos para aprobar
                if(sizeof($id_turnos_array)>0){

                    // cambiamos el estado de la tabla turno
                    $aprobar=Turno::whereIn('id',$id_turnos_array)
                    ->where('estado', 'P')
                    ->update(["estado"=>'A', "fecha_act"=>date('Y-m-d H:i:s'),
                    "id_usuario_act"=>1, "job_aprueba"=>'S']);

                    // aprobamos la tabla turno_comida
                    $aprobar_turno_comida=TurnoComida::whereIn('id_turno',$id_turnos_array)
                    ->where('id_alimento',$idalimento)
                    // ->where('estado', 'Generado')
                    ->where('estado', 'Confirmado')
                    ->update(['estado'=>'Aprobado', 'id_usuario_aprueba'=>1,
                    'fecha_aprobacion'=>date('Y-m-d H:i:s'), "job_aprueba"=>'S']);

                                
                    //mandamos a generar el documento para enviarlo x correo
                    $generaPdf=$this->generarPdfAprobacion($idalimento, $fecha);
                    if($generaPdf['error']==false){
                        
                        //se creo lo enviamos
                        $fecha_apr=date('d-m-Y',strtotime($fecha));
                        $archivo=Storage::disk('public')->get($generaPdf['pdf']);
                        $nombrearchivo=$generaPdf['pdf'];
                        
                        //ECAA==ENVIA CORREO APROBACION ALIMENTOS.
                        //consultamos el correo donde enviaremos el documento
                        $correo_param=DB::table('al_parametros')
                        ->where('codigo','ECAA')->first();
                        if(!is_null($correo_param)){
                            $correo_db_par=$correo_param->valor;
                        }else{
                            $correo_db_par="juanrolandocn@gmail.com";
                        }
                    
                        //correos parametrizados
                        $correos_enviar=explode(",", $correo_db_par);
                    
                        try{
                            
                            foreach($correos_enviar as $email){

                                $correo_envio=$email;

                                Mail::send('email_documentos.aprobacion_alimento', ['comida'=>$comida,"fecha_apr"=>$fecha_apr, "correo"=>$correo_envio], function ($m) use ($correo_envio,$archivo, $nombrearchivo, $comida, $fecha_apr) {
                                    $m->to($correo_envio)
                                    ->subject("Aprobación de ".$comida." del ".$fecha_apr)
                                    
                                    ->attachData($archivo, $nombrearchivo, [
                                        'mime' => 'application/pdf',
                                    ]);
                                
                                });  
                            }

                            $archivo=Storage::disk('public')->delete($nombrearchivo);

                            Log::info('Información aprobada y enviada exitosamente desde JOB, del alimento '.$comida. ' del día '.date('d-m-Y'));
                            return 'Información aprobada y enviada exitosamente desde JOB';

                        } catch (\Throwable $th) {
                        
                            $archivo=Storage::disk('public')->delete($nombrearchivo);

                            Log::error('ListadoTurnoController, enviarCorreoAprobacion '.$th->getMessage()." Linea ".$th->getLine());

                            return 'Información fué aprobada exitosamente, pero no se pudo enviar al correo ';
                        }
                    }else{
                        
                        Log::error('No se pudo generar ni enviar el pdf al correo de aprobacion mediante JOB, del alimento '.$comida. ' del día '.date('d-m-Y'));

                        return 'No se pudo generar ni enviar el pdf al correo de aprobacion mediante JOB, del alimento '.$comida. ' del día '.date('d-m-Y');

                    }
                }else{

                    Log::error('No existen turnos disponibles para aprobar mediante JOB, del alimento '.$comida. ' del día '.date('d-m-Y').' la información ya fué aprobada manualmente o no se realizó el registro de la misma');

                    return 'No existen turnos disponibles para aprobar mediante JOB, del alimento '.$comida. ' del día '.date('d-m-Y').' la información ya fué aprobada manualmente o no se realizó el registro de la misma';

                }
            } catch (\Throwable $e) {  
                DB::Rollback();                  
                Log::error('ListadoTurnoController => aprobarAlimentoJob => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine() );
                return 'Ocurrió un error, intentelo más tarde ';
            }
        });
        return $transaction;
    }

    public function generarPdfAprobacion($idalimento, $fecha){
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

            if(sizeof($turnos)==0){
                Log::error('ListadoTurnoController => generarPdfAprobacion => mensaje => No se encontró turnos de alimentos aprobados ---JOB--');
                return [
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error'
                ];
            }

            $nombrePDF="reporte_listado_comida_".date('d-m-Y',strtotime($turnos[0]->fecha_turno)).".pdf";
           
            // enviamos a la vista para crear el documento que los datos repsectivos
            $crearpdf=PDF::loadView('alimentacion.turno.pdf_aprobado_dia_alimento',['datos'=>$turnos]);
            $crearpdf->setPaper("A4", "portrait");
            $estadoarch = $crearpdf->stream();

            //lo guardamos en el disco temporal
            Storage::disk('public')->put(str_replace("", "",$nombrePDF), $estadoarch);
            $exists_destino = Storage::disk('public')->exists($nombrePDF); 
            if($exists_destino){ 
                return [
                    'error'=>false,
                    'pdf'=>$nombrePDF
                ];
            }else{
                Log::error('ListadoTurnoController => generarPdfAprobacion => mensaje =>No se pudo crear el documento ---JOB--');
                return [
                    'error'=>true,
                    'mensaje'=>'No se pudo crear el documento'
                ];
            }


        }catch (\Throwable $e) {
            Log::error('ListadoTurnoController => generarPdfAprobacion => mensaje => '.$e->getMessage());
            return [
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ];
            
        }
    }

    public function eliminacionTurnoComida(Request $request){
       
        try{
            
            //eliminados el turno comida
            $turno_comida=TurnoComida::where('id_turno_comida',$request->idturno_comida)
            ->first();
          
            if(!is_null($turno_comida)){
                if($turno_comida->estado=="Eliminado"){
                    return [
                        'error'=>true,
                        'mensaje'=>'La informacion ya fue eliminada'
                    ];
                }
                if($turno_comida->estado=="Generado"){
                    return [
                        'error'=>true,
                        'mensaje'=>'La informacion no ha sido confirmada'
                    ];
                }
               
                $turno_comida->estado="Eliminado";
                $turno_comida->motivo_eliminacion=$request->motivo_elim;
                $turno_comida->id_usuario_elim=auth()->user()->id;
                $turno_comida->fecha_elimina=date('Y-m-d H:i:s');
                $turno_comida->save();

                return [
                    'error'=>true,
                    'mensaje'=>'Informacion eliminada exitosamente'
                ];
            }
            return [
                'error'=>true,
                'mensaje'=>'No se encontro informacion'
            ];

        }catch (\Throwable $e) {
            Log::error('ListadoTurnoController => eliminacionTurnoComida => mensaje => '.$e->getMessage(). ' linea => '.$e->getLine());
            return [
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ];
            
        }
    }

}
