<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion\AlimentoPaciente;
use \Log;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator; 
use Storage;
use PDF;
use SplFileInfo;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Mail;

class AlimentosPacientesController extends Controller
{
    private $clientHosp = null;

    public function __construct(){
        
        try{
           
            $url=DB::table('parametro')
            ->where('estado', 'A')
            ->where('codigo', 'URL_API')
            ->select('valor')
            ->first();
           
            $this->clientHosp = new Client([
                'base_uri' =>$url->valor.'/sisInv',
                // 'base_uri' =>'localhost/sisInv',
                'verify' => false,
            ]);
        }catch(Exception $e){
            Log::error($e->getMessage());
        }
    }

    public function index(){
        try{
            $hoy=date('Y-m-d');
            $servicio_data=AlimentoPaciente::select('servicio')->distinct('servicio')
            ->whereDate('fecha_solicita',$hoy)
            ->get();

            return view('alimentacion.paciente',["servicio_data"=>$servicio_data]);
        } catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return (['mensaje'=>'Ocurrió un error,intentelo más tarde','error'=>true]); 
        }

    }

    public function historialAliPaciente($idpaciente){
        try{
            $listaAliPaciente = $this->clientHosp->request('GET', "sisInv/api/historial-comida-paciente/{$idpaciente}",[
                'headers' => [
                    'Authorization' => ''
                ] ,
                'connect_timeout' => 10,
                'timeout' => 10
            ]);
           
         
            $info= json_decode((string) $listaAliPaciente->getBody());
            if($info->error==true){
                return [
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error al consultar la informacion de los alimentos'
                ];
            }
           
            $lista=[];
            foreach($info->data as $item){                
               
                $fecha_soli=$item->feha_registro;
                $fecha_soli=date('d-m-Y H:i:s', strtotime($fecha_soli));
                $cedula=$item->cedula;
                $edad=$item->edad_actual;
                $paciente=$item->paciente_nombres;
                $responsable=$item->resp_ingresa;
                $dieta=$item->tipodieta;
                $estado="Solicitado";
                $servicio=$item->detalle_serv;
                // $tipo=$tipo_ali;
                $observacion=$item->observacion;
                $id_paciente=$item->id_paciente;      
                
                array_push($lista,["fecha_solicita"=>$fecha_soli, "paciente"=>$paciente, "responsable"=>$responsable, "dieta"=>$dieta, "estado"=>$estado, "servicio"=>$servicio, "observacion"=>$observacion, "id_paciente"=>$id_paciente, "cedula"=>$cedula, "edad"=>$edad]);
               
              
            }
           
            return[
                'error'=>false,
                'resultado'=>$lista
            ];
            
        }catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return [
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ];
             
        }
    }

    public function listarVisor($filtra){
        try{
            $listaAliPaciente = $this->clientHosp->request('GET', "sisInv/api/comidas-pacientes",[
                'headers' => [
                    'Authorization' => ''
                ] ,
                'connect_timeout' => 10,
                'timeout' => 10
            ]);
           
         
            $info= json_decode((string) $listaAliPaciente->getBody());
            if($info->error==true){
                return [
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error al consultar la informacion de los alimentos'
                ];
            }
           
                      
            $hora=date('H');
            $hora=intval($hora);
            if($hora<6){
                $tipo_ali="Desayuno";
            }else if($hora>=6 && $hora<9){
                $tipo_ali="Colacion 1";
            }else if($hora>=9 && $hora<11){
                $tipo_ali="Almuerzo";
            }else if($hora>=11 && $hora<16){
                $tipo_ali="Colacion 2";
            }else{
                $tipo_ali="Merienda";
            }
            $lista=[];
            foreach($info->data as $item){                
               
                $fecha_soli=$item->fecha;
                $fecha_soli=date('H:i:s', strtotime($fecha_soli));
                $paciente=$item->paciente_nombres;
                $responsable=$item->responsable;
                $dieta=$item->tipodieta;
                $estado="Solicitado";
                $servicio=$item->detalle_serv;
                $tipo=$tipo_ali;
                $observacion=$item->observacion;
                $id_paciente=$item->id_paciente;      
                
                // if($tipo!="Colacion 1" && $servicio!="Dialisis"){
                if($filtra=="T"){
                    array_push($lista,["fecha_solicita"=>$fecha_soli, "paciente"=>$paciente, "responsable"=>$responsable, "dieta"=>$dieta, "estado"=>$estado, "servicio"=>$servicio, "tipo"=>$tipo, "observacion"=>$observacion, "id_paciente"=>$id_paciente]);
                }else{
                    if($filtra==$servicio){
                        array_push($lista,["fecha_solicita"=>$fecha_soli, "paciente"=>$paciente, "responsable"=>$responsable, "dieta"=>$dieta, "estado"=>$estado, "servicio"=>$servicio, "tipo"=>$tipo, "observacion"=>$observacion, "id_paciente"=>$id_paciente]);
                    }
                }
                   
                // }
                    
              
            }
           
            return[
                'error'=>false,
                'resultado'=>$lista
            ];
            
        }catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return [
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ];
             
        }
    }

    public function listar(){
        try{
            $listaAliPaciente = $this->clientHosp->request('GET', "sisInv/api/comidas-pacientes",[
                'headers' => [
                    'Authorization' => ''
                ] ,
                'connect_timeout' => 10,
                'timeout' => 10
            ]);
           
         
            $info= json_decode((string) $listaAliPaciente->getBody());
          
            if($info->error==true){
                return [
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error al consultar la informacion de los alimentos'
                ];
            }
           
            $hora=date('H');
            if($hora=="06" || $hora==6){
                $tipo_ali="Desayuno";
            }else if($hora=="09" || $hora==9 || $hora==10 || $hora=="10"){
                $tipo_ali="Colacion 1";
            }else if($hora=="11" || $hora==11){
                $tipo_ali="Almuerzo";
            }else if($hora=="14" || $hora==14){
                $tipo_ali="Colacion 2";
            }else{
                $tipo_ali="Merienda";
            }
           
            //eliminamos todos los solicitado en estado solicitado
            $alimentosElim=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Solicitado')
            ->where('tipo',$tipo_ali)
            ->delete();
            foreach($info->data as $item){
                
                //comprobamos si no esta registrado y aprobado
                $alimentos=AlimentoPaciente::where('id_registro', $item->id_registro)
                ->whereIn('estado',['Aprobado','Solicitado'])
                ->where('tipo',$tipo_ali)
                ->where('servicio','!=','EMERGENCIA')
                ->first();
              
                if(is_null($alimentos)){
                   
                    $fecha_soli=$item->fecha;
                    $fecha_soli=date('Y-m-d H:i:s', strtotime($fecha_soli));
                    $alimentoPac=new AlimentoPaciente();
                    $alimentoPac->json_dieta=json_encode($item);
                    $alimentoPac->paciente=$item->paciente_nombres;
                    $alimentoPac->responsable=$item->responsable;
                    $alimentoPac->fecha_solicita=$fecha_soli;
                    $alimentoPac->dieta=$item->tipodieta;
                    $alimentoPac->estado="Solicitado";
                    $alimentoPac->fecha=date('Y-m-d');
                    $alimentoPac->id_registro=$item->id_registro;
                    $alimentoPac->servicio=$item->detalle_serv;
                    $alimentoPac->tipo=$tipo_ali;
                    $alimentoPac->observacion=$item->observacion;
                    if($alimentoPac->servicio!="EMERGENCIA" && $item->alta=="N"){
                        $alimentoPac->save();
                    }
                        
                }

              
            }
            $alimentoPac=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Solicitado')
            ->where('tipo',$tipo_ali)
            ->get();
            return[
                'error'=>false,
                'resultado'=>$alimentoPac
            ];
            
        }catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return [
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ];
             
        }
    }

    //job para aprobar alimentos de pacientes dialisis 9:30 AM Y 11:00
    public function aprobarAliPaciente(){
       
        $transaction=DB::transaction(function() { 
            try{
                
                $consultaPendiente=$this->listar();
                if($consultaPendiente['error']==true){
                    return [
                        'error'=>true,
                        'mensaje'=>'Ocurrió un error'
                    ];
                    Log::error('Aprobacion Alimento Paciente '.$consultaPendiente["mensaje"]);   
                    return $consultaPendiente["mensaje"];  
                }
                
                $hora=date('H');
                if($hora=="09" || $hora==9){
                    $tipo="Colacion 1";
                }else{
                    $tipo="Almuerzo";
                }
                $tipo_ali=$tipo;

                $listar=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                ->where('estado','Solicitado')
                ->where('servicio','DIALISIS')
                 ->where('tipo',$tipo)
                // ->distinct('paciente','fecha_solicita')
                ->get();

                if(sizeof($listar)==0){
                    Log::error('No existen pacientes con solicitud a alimentacion');   
                    return 'No existen pacientes con solicitud a alimentacion';  
                }

                #agrupamos por area
                $lista_final_agrupada=[];
                foreach ($listar as $key => $item){                
                    if(!isset($lista_final_agrupada[$item->servicio])) {
                        $lista_final_agrupada[$item->servicio]=array($item);
                
                    }else{
                        array_push($lista_final_agrupada[$item->servicio], $item);
                    }
                }

                #agrupamos por tipo dieta
                $lista_dieta=[];
                foreach ($listar as $key => $item){                
                    if(!isset($lista_dieta[$item->dieta])) {
                        $lista_dieta[$item->dieta]=array($item);
                
                    }else{
                        array_push($lista_dieta[$item->dieta], $item);
                    }
                }
              
                $area=$listar[0]->servicio;
                
                $nombrePDF="reporte_listado_comida_pac.pdf";             

                if($tipo_ali=="Colacion 1"){
                    $tipo_ali="Corte 1";
                }else{
                    $tipo_ali="Corte 2";
                }
            
                // enviamos a la vista para crear el documento que los datos repsectivos
                $crearpdf=PDF::loadView('alimentacion.pdf_aprobado_paciente',['datos'=>$lista_final_agrupada,"f_aprobacion"=>date('Y-m-d H:i:s'), 'dieta'=>$lista_dieta, 'tipo'=>$tipo_ali]);
                $crearpdf->setPaper("A4", "landscape");
                $estadoarch = $crearpdf->stream();

                //lo guardamos en el disco temporal
                Storage::disk('public')->put(str_replace("", "",$nombrePDF), $estadoarch);
                $exists_destino = Storage::disk('public')->exists($nombrePDF); 
                if($exists_destino){ 

                    $generarAprobacion=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                    ->where('estado','Solicitado')
                    ->where('servicio','DIALISIS')
                    ->update(['fecha_aprobacion'=>date('Y-m-d H:i:s'), 'estado'=>'Aprobado', 'entregado'=>'S', 'tipo'=>$tipo_ali]);
                   
                    //se creo lo enviamos
                    $fecha_apr=date('d-m-Y');
                    $archivo=Storage::disk('public')->get($nombrePDF);
                    $nombrearchivo=$nombrePDF;

                    //consultamos el correo donde enviaremos el documento...
                    $correo_param=DB::table('al_parametros')
                    ->where('codigo','ECAA')->first();
                    if(!is_null($correo_param)){
                        $correo_db_par=$correo_param->valor;
                    }else{
                        $correo_db_par="juanrolandocn@gmail.com";
                    }
                
                    //correos parametrizados
                    $correos_enviar=explode(",", $correo_db_par);
                    $nuevo_correo = "jeny.zambrano@hospitalgeneralchone.gob.ec";
                    // Agregar el nuevo correo al array
                    array_push($correos_enviar, $nuevo_correo);
                
                    try{
                        
                        foreach($correos_enviar as $email){

                            $correo_envio=$email;
                           
                            Mail::send('email_documentos.aprobacion_ali_paciente', ["fecha_apr"=>$fecha_apr, "correo"=>$correo_envio, "area"=>$area], function ($m) use ($correo_envio,$archivo, $nombrearchivo, $fecha_apr,$area) {
                                $m->to($correo_envio)
                                ->subject("Aprobación de alimentos de pacientes de " .$area. " del ".$fecha_apr)
                                
                                ->attachData($archivo, $nombrearchivo, [
                                    'mime' => 'application/pdf',
                                ]);
                            
                            });  
                        }

                        $archivo=Storage::disk('public')->delete($nombrearchivo);

                        Log::info('Información aprobada y enviada exitosamente desde JOB, del alimento de paciente de '.$area. ' del día '.date('d-m-Y'));
                       
                        return 'Información aprobada y enviada exitosamente desde JOB alimento paciente '.$area;

                    } catch (\Throwable $th) {
                        $archivo=Storage::disk('public')->delete($nombrearchivo);
                        Log::error('AlimentosPacientesController, aprobarAliPaciente '.$th->getMessage()." Linea ".$th->getLine());
                        return 'Información fué aprobada exitosamente, pero no se pudo enviar al correo ';
                    }

                 
                }else{
                    DB::Rollback();
                    Log::error('No se pudo crear el documento');   
                    return 'No se pudo crear el documento';                          
                }
               
            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
                log::error('Ocurrio un error al aprobar el listado de pacientes con servicio a alimentos');
                return 'Ocurrio un error al aprobar el listado de pacientes con servicio a alimentos';
                
            }
        });
        return $transaction;
    }


    //job para aprobar alimentos de pacientes HOSPITALIZADOS (diferente d dialisis)
    public function aprobarAliPacienteHosp(){
        $transaction=DB::transaction(function() { 
            try{
                
                set_time_limit(0);
                ini_set("memory_limit",-1);
                ini_set('max_execution_time', 0);
                $consultaPendiente=$this->listar();
                if($consultaPendiente['error']==true){
                    return [
                        'error'=>true,
                        'mensaje'=>'Ocurrió un error'
                    ];
                    Log::error('Aprobacion Alimento Paciente '.$consultaPendiente["mensaje"]);   
                    return $consultaPendiente["mensaje"];  
                }
              
                $hora=date('H');
                if($hora=="06" || $hora==6){
                    $tipo="Desayuno";
                }else if($hora=="09" || $hora==9){
                    $tipo="Colacion 1";
                }else if($hora=="11" || $hora==11){
                    $tipo="Almuerzo";
                }else if($hora=="14" || $hora==14){
                    $tipo="Colacion 2";
                }else{
                    $tipo="Merienda";
                }

                $listar=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                ->where('estado','Solicitado')
                ->where('servicio','!=','DIALISIS')
                ->where('tipo',$tipo)
                ->orderBy('fecha', 'asc')  
                ->orderBy('dieta', 'asc')
                // ->select('fecha','paciente','dieta','observacion','fecha_solicita','responsable')
                // ->distinct('paciente') 
                // ->distinct('paciente','fecha_solicita')
                ->get();

                if(sizeof($listar)==0){
                    Log::error('No existen pacientes con solicitud a alimentacion');   
                    return 'No existen pacientes con solicitud a alimentacion';  
                }

                $area=$listar[0]->servicio;

                #agrupamos por area
                $lista_final_agrupada=[];
                foreach ($listar as $key => $item){                
                    if(!isset($lista_final_agrupada[$item->servicio])) {
                        $lista_final_agrupada[$item->servicio]=array($item);
                
                    }else{
                        array_push($lista_final_agrupada[$item->servicio], $item);
                    }
                }

                #agrupamos por tipo dieta
                $lista_dieta=[];
                foreach ($listar as $key => $item){                
                    if(!isset($lista_dieta[$item->dieta])) {
                        $lista_dieta[$item->dieta]=array($item);
                
                    }else{
                        array_push($lista_dieta[$item->dieta], $item);
                    }
                }
                
                $nombrePDF="reporte_listado_comida_pac.pdf";

            
                // enviamos a la vista para crear el documento que los datos repsectivos
                $crearpdf=PDF::loadView('alimentacion.pdf_aprobado_paciente_hosp',['datos'=>$lista_final_agrupada,"f_aprobacion"=>date('Y-m-d H:i:s'),'tipo'=>$tipo, 'dieta'=>$lista_dieta]);
                $crearpdf->setPaper("A4", "portrait");
                $estadoarch = $crearpdf->stream();

                //lo guardamos en el disco temporal
                Storage::disk('public')->put(str_replace("", "",$nombrePDF), $estadoarch);
                $exists_destino = Storage::disk('public')->exists($nombrePDF); 
                if($exists_destino){ 
                    
                    $generarAprobacion=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                    ->where('estado','Solicitado')
                    ->where('servicio','!=','DIALISIS')
                    ->update(['fecha_aprobacion'=>date('Y-m-d H:i:s'), 'estado'=>'Aprobado', 'entregado'=>'S', 'tipo'=>$tipo]);
                   
                    //se creo lo enviamos
                    $fecha_apr=date('d-m-Y');
                    $archivo=Storage::disk('public')->get($nombrePDF);
                    $nombrearchivo=$nombrePDF;

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
                           
                            Mail::send('email_documentos.aprobacion_ali_paciente_hosp', ["fecha_apr"=>$fecha_apr, "correo"=>$correo_envio, "area"=>$area,"tipo"=>$tipo], function ($m) use ($correo_envio,$archivo, $nombrearchivo, $fecha_apr,$area,$tipo) {
                                $m->to($correo_envio)
                                ->subject("Aprobación de alimentos de pacientes hospitalizados de ".$tipo."  del ".$fecha_apr)
                                
                                ->attachData($archivo, $nombrearchivo, [
                                    'mime' => 'application/pdf',
                                ]);
                            
                            });  
                        }

                        $archivo=Storage::disk('public')->delete($nombrearchivo);

                        Log::info('Información aprobada y enviada exitosamente desde JOB, del alimento de paciente de '.$area. ' del día '.date('d-m-Y'));
                       
                        return 'Información aprobada y enviada exitosamente desde JOB alimento paciente '.$area;

                    } catch (\Throwable $th) {
                        $archivo=Storage::disk('public')->delete($nombrearchivo);
                        Log::error('AlimentosPacientesController, aprobarAliPaciente '.$th->getMessage()." Linea ".$th->getLine());
                        return 'Información fué aprobada exitosamente, pero no se pudo enviar al correo ';
                    }

                 
                }else{
                    DB::Rollback();
                    Log::error('No se pudo crear el documento');   
                    return 'No se pudo crear el documento';                          
                }
               
            }catch (\Throwable $e) {
                DB::Rollback();
                Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
                log::error('Ocurrio un error al aprobar el listado de pacientes con servicio a alimentos');
                return 'Ocurrio un error al aprobar el listado de pacientes con servicio a alimentos';
                
            }
        });
        return $transaction;
    }

    public function reportePdfAliServicio($serv){
        try{
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);
          
            $consultaPendiente=$this->listarVisor($serv);
            if($consultaPendiente['error']==true){
                return [
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error'
                ];
            }

            $lista_final_agrupada=[];
            foreach ($consultaPendiente['resultado'] as $key => $item){                
                if(!isset($lista_final_agrupada[$item['servicio']])) {
                    $lista_final_agrupada[$item['servicio']]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item['servicio']], $item);
                }
            }
           
            $nombrePDF="reporte_listado_comida.pdf";
           
            // enviamos a la vista para crear el documento que los datos repsectivos
            $crearpdf=PDF::loadView('alimentacion.pdf_alimento_solicitado',['datos'=>$lista_final_agrupada]);
            $crearpdf->setPaper("A4", "landscape");
            $estadoarch = $crearpdf->stream();

            //lo guardamos en el disco temporal
            Storage::disk('public')->put(str_replace("", "",$nombrePDF), $estadoarch);
            $exists_destino = Storage::disk('public')->exists($nombrePDF); 
            if($exists_destino){ 

                // $generarAprobacion=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                // ->where('estado','Solicitado')->update(['fecha_aprobacion'=>date('Y-m-d H:i:s'), 'estado'=>'Aprobado', 'entregado'=>'S']);

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

        }catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
             
        }
    }

    public function reportePdfAliPaciente(){
        try{
            $pendiente=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Solicitado')->get();

            if(sizeof($pendiente)==0){
                return [
                    'error'=>true,
                    'mensaje'=>'No existen pacientes con solicitud a alimentacion'
                ];
            }

            $consultaPendiente=$this->listar();
            if($consultaPendiente['error']==true){
                return [
                    'error'=>true,
                    'mensaje'=>'Ocurrió un error'
                ];
            }
          
           

            $listar=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Solicitado')
            // ->where('entregado','S')
            ->get();
            

            $nombrePDF="reporte_listado_comida_pac.pdf";
           
            // enviamos a la vista para crear el documento que los datos repsectivos
            $crearpdf=PDF::loadView('alimentacion.pdf_aprobado_paciente',['datos'=>$listar]);
            $crearpdf->setPaper("A4", "landscape");
            $estadoarch = $crearpdf->stream();

            //lo guardamos en el disco temporal
            Storage::disk('public')->put(str_replace("", "",$nombrePDF), $estadoarch);
            $exists_destino = Storage::disk('public')->exists($nombrePDF); 
            if($exists_destino){ 

                // $generarAprobacion=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                // ->where('estado','Solicitado')->update(['fecha_aprobacion'=>date('Y-m-d H:i:s'), 'estado'=>'Aprobado', 'entregado'=>'S']);

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

        }catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
             
        }
    }

    public function vistaAprobado(){
        return view('alimentacion.paciente_aprobado');
    }

    //por rango fecha
    public function reportePdfAliPacienteAprobado($inicio, $final, $serv,$tipo){
        try{
           
            $listar=AlimentoPaciente::where(function($query)use($serv,$tipo,$inicio, $final){
                if($serv=="Dialisis"){
                    if(strtotime($inicio) >= strtotime(date('2024-06-05'))){
                        // $query->where('servicio','=','Dialisis')
                        // ->where('tipo',$tipo);

                        if($tipo=="Corte 1"){
                            $query->where('servicio','=','Dialisis')
                            ->whereIn('tipo',['Colacion 1','Corte 1']);
                        }else{
                            $query->where('servicio','=','Dialisis')
                            ->where('tipo','Corte 2');
                        }

                    }else{
                        if($tipo=="Colacion 1"){
                            $query->where('servicio','=','Dialisis')
                            ->where('tipo','Colacion');
                        }else{
                            $query->where('servicio','=','Dialisis')
                            ->where('tipo',$tipo);
                        }
                    }
                   
                }else{
                    $query->where('servicio','!=','Dialisis')
                    ->where('tipo',$tipo);
                }
                $query->whereDate('fecha_solicita','>=',$inicio)
                ->whereDate('fecha_solicita','<=',$final);
            })
            ->where('estado','Aprobado')
            // ->orderBy('fecha', 'asc')  
            // ->orderBy('dieta', 'asc') 
            // ->select('id_registro','fecha_solicita','paciente','dieta','servicio','responsable','observacion')
            // ->distinct('paciente')
            ->selectRaw('
                MAX(id_registro) AS id_registro,
                MAX(fecha_solicita) AS fecha_solicita,
                paciente,
                MAX(dieta) AS dieta,
                MAX(servicio) AS servicio,
                MAX(responsable) AS responsable,
                MAX(observacion) AS observacion
            ')
            ->groupBy('paciente')
            ->orderBy(DB::raw('MAX(fecha)'), 'asc')
            ->orderBy(DB::raw('MAX(dieta)'), 'asc')
            ->get();

          
            if(sizeof($listar)==0){
                return [
                    'error'=>true,
                    'mensaje'=>'No se encontro alimentos aprobados el dia de hoy'
                ];
            }
          

            #agrupamos por area
            $lista_final_agrupada=[];
            foreach ($listar as $key => $item){                
                if(!isset($lista_final_agrupada[$item->servicio])) {
                    $lista_final_agrupada[$item->servicio]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->servicio], $item);
                }
            }

            #agrupamos por tipo dieta
            $lista_dieta=[];
            foreach ($listar as $key => $item){                
                if(!isset($lista_dieta[$item->dieta])) {
                    $lista_dieta[$item->dieta]=array($item);
            
                }else{
                    array_push($lista_dieta[$item->dieta], $item);
                }
            }

            if($serv=="Dialisis"){
                // if($tipo=="Colacion 1"){
                if($tipo=="Colacion 1" || $tipo=="Corte 1"){
                    $tipo="Corte 1";
                }else{
                    $tipo="Corte 2";
                }
            }
           
            $nombrePDF="reporte_listado_comida_pac_dia.pdf";
            if($serv=="Dialisis"){
                // enviamos a la vista para crear el documento que los datos repsectivos
                $crearpdf=PDF::loadView('alimentacion.pdf_aprobado_paciente',['datos'=>$lista_final_agrupada,'ini'=>$inicio, 'fin'=>$final,"f_aprobacion"=>0, 'tipo'=>$tipo,'dieta'=>$lista_dieta]);
            }else{
                // enviamos a la vista para crear el documento que los datos repsectivos
                $crearpdf=PDF::loadView('alimentacion.pdf_aprobado_paciente_hosp',['datos'=>$lista_final_agrupada,'ini'=>$inicio, 'fin'=>$final,"f_aprobacion"=>0,'tipo'=>$tipo, 'dieta'=>$lista_dieta]);
            }
            
            // $crearpdf->setPaper("A4", "landscape");
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

        }catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
             
        }
    }

    public function visualizarDoc($documentName){
        try {
             
            $info = new SplFileInfo($documentName);
            $extension = $info->getExtension();
            if($extension!= "pdf" && $extension!="PDF"){
                return \Storage::disk('public')->download($documentName);
            }else{
                // obtenemos el documento del disco en base 64
                $documentEncode= base64_encode(\Storage::disk('public')->get($documentName));
                return view("alimentacion.vistaPrevia")->with([
                    "documentName"=>$documentName,
                    "documentEncode"=>$documentEncode
                ]);        
            }            
        } catch (\Throwable $th) {
            Log::error("AprobacionEntregaController =>visualizardoc => Mensaje =>".$th->getMessage());
            abort("404");
        }

    }

    //por rango fecha rollo
    public function reportePdfAliPacienteRollo($inicio, $final, $serv,$tipo){
        try{
            // dd($tipo);
            $listar=AlimentoPaciente::where(function($query)use($serv,$tipo,$inicio,$final){

                if($serv=="Dialisis"){
                    if(strtotime($inicio) >= strtotime(date('2024-06-05'))){
                        // $query->where('servicio','=','Dialisis')
                        // ->where('tipo',$tipo);
                        if($tipo=="Colacion 1" || $tipo=="Corte 1"){
                            $query->where('servicio','=','Dialisis')
                            ->whereIn('tipo',['Colacion 1','Corte 1']);
                        }else{
                            $query->where('servicio','=','Dialisis')
                            ->where('tipo','Corte 2');
                        }
                    }else{
                        if($tipo=="Colacion 1"){
                            $query->where('servicio','Dialisis');
                        }else{
                            $query->where('servicio','=','Dialisis')
                            ->where('tipo',$tipo);
                        }
                    }
                   
                }else{
                    $query->where('servicio','!=','Dialisis')
                    ->where('tipo',$tipo);
                }


                // if($serv=="Dialisis"){
                //     $query->where('servicio','Dialisis');
                // }else{
                //     $query->where('servicio','!=','Dialisis')
                //     ->where('tipo',$tipo);
                // }

                $query->whereDate('fecha_solicita','>=',$inicio)
                ->whereDate('fecha_solicita','<=',$final);
            })
            // ->where('dieta','!=','NADA POR VIA ORAL')
            // ->select('id_registro','fecha_solicita','paciente','dieta','servicio','responsable','observacion')
            // ->distinct('id_registro')

            ->selectRaw('
                MAX(id_registro) AS id_registro,
                MAX(fecha_solicita) AS fecha_solicita,
                paciente,
                MAX(dieta) AS dieta,
                MAX(servicio) AS servicio,
                MAX(responsable) AS responsable,
                MAX(observacion) AS observacion
            ')
            ->groupBy('paciente')
            ->distinct('paciente')
            ->where('estado','Aprobado')
            ->get();
          
            if(sizeof($listar)==0){
                return [
                    'error'=>true,
                    'mensaje'=>'No se encontro alimentos aprobados el dia de hoy'
                ];
            }
           
            $nombrePDF="reporte_listado_comida_pac_dia.pdf";
            if($serv=="Dialisis"){
                // enviamos a la vista para crear el documento que los datos repsectivos
                $crearpdf=PDF::loadView('alimentacion.reporte.rollo_dieta_paciente',['datos'=>$listar,'ini'=>$inicio, 'fin'=>$final,"f_aprobacion"=>0]);
            }else{
                // enviamos a la vista para crear el documento que los datos repsectivos
                $crearpdf=PDF::loadView('alimentacion.reporte.rollo_dieta_paciente',['datos'=>$listar,'ini'=>$inicio, 'fin'=>$final,"f_aprobacion"=>0,'tipo'=>$tipo]);
            }
            
            $crearpdf->setPaper([0, 0, 101.6,  152.4]);
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

        }catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
             
        }
    }
}