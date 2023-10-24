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
                'verify' => false,
            ]);
        }catch(Exception $e){
            Log::error($e->getMessage());
        }
    }

    public function index(){
        try{
            return view('alimentacion.paciente');
        } catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            return (['mensaje'=>'Ocurrió un error,intentelo más tarde','error'=>true]); 
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
            //eliminamos todos los solicitado en estado solicitado
            $alimentosElim=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Solicitado')
            ->delete();
            foreach($info->data as $item){
                
                //comprobamos si no esta registrado y aprobado
                $alimentos=AlimentoPaciente::where('id_registro', $item->id_registro)
                ->where('estado','Aprobado')
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
                    $alimentoPac->observacion=$item->observacion;
                    $alimentoPac->save();
                }

              
            }
            $alimentoPac=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Solicitado')
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

    //job para aprobar alimentos de pacientes
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

                $listar=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                ->where('estado','Solicitado')
                ->get();

                if(sizeof($listar)==0){
                    Log::error('No existen pacientes con solicitud a alimentacion');   
                    return 'No existen pacientes con solicitud a alimentacion';  
                }

              
                $area=$listar[0]->servicio;
                
                $nombrePDF="reporte_listado_comida_pac.pdf";
            
                // enviamos a la vista para crear el documento que los datos repsectivos
                $crearpdf=PDF::loadView('alimentacion.pdf_aprobado_paciente',['datos'=>$listar,"f_aprobacion"=>date('Y-m-d H:i:s')]);
                $crearpdf->setPaper("A4", "landscape");
                $estadoarch = $crearpdf->stream();

                //lo guardamos en el disco temporal
                Storage::disk('public')->put(str_replace("", "",$nombrePDF), $estadoarch);
                $exists_destino = Storage::disk('public')->exists($nombrePDF); 
                if($exists_destino){ 

                    $generarAprobacion=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                    ->where('estado','Solicitado')->update(['fecha_aprobacion'=>date('Y-m-d H:i:s'), 'estado'=>'Aprobado', 'entregado'=>'S']);
                   
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

                $generarAprobacion=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
                ->where('estado','Solicitado')->update(['fecha_aprobacion'=>date('Y-m-d H:i:s'), 'estado'=>'Aprobado', 'entregado'=>'S']);

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
    public function reportePdfAliPacienteAprobado($inicio, $final){
        try{
          
            $listar=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Aprobado')->get();
            
            if(sizeof($listar)==0){
                return [
                    'error'=>true,
                    'mensaje'=>'No se encontro alimentos aprobados el dia de hoy'
                ];
            }
            
            $nombrePDF="reporte_listado_comida_pac_dia.pdf";
           
            // enviamos a la vista para crear el documento que los datos repsectivos
            $crearpdf=PDF::loadView('alimentacion.pdf_aprobado_paciente',['datos'=>$listar,'ini'=>$inicio, 'fin'=>$final]);
            $crearpdf->setPaper("A4", "landscape");
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
}