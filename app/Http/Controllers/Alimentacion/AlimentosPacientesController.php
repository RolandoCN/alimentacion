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
class AlimentosPacientesController extends Controller
{
    private $clientHosp = null;

    public function __construct(){
        
        try{
            $this->clientHosp = new Client([
                'base_uri' => '172.10.40.204/sisInv',
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
                    $alimentoPac->save();
                }

              
            }
            $alimentoPac=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))->get();
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
    //job
    public function aprobarAliPaciente(){
        try{
            $pendiente=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Solicitado')->get();

            $consultaPendiente=$this->listar();
            if($consultaPendiente['error']==true){
                log::error('Ocurrio un error al sincronizar el listado de pacientes con servicio a alimentos');
                return 'Ocurrio un error al sincronizar el listado de pacientes con servicio a alimentos';
            }
          
            $aprobado=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Aprobado')->get();
            if(sizeof($aprobado)==0){
                log::error('No existen alimentos aprobados');
                return 'No existen alimentos aprobados';
            }

            //procedemos a aprobar los alimentos de los pacientes
            $generarAprobacion=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Aprobado')->update(['fecha_aprobacion'=>date('Y-m-d H:i:s'), 'estado'=>'Aprobado']);
            log::info('Listado de alimentos de pacientes aprobados exitosamente');

        }catch (\Throwable $e) {
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage()." Linea =>".$e->getLine());
            log::error('Ocurrio un error al aprobar el listado de pacientes con servicio a alimentos');
            return 'Ocurrio un error al aprobar el listado de pacientes con servicio a alimentos';
             
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
          
            $generarAprobacion=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Solicitado')->update(['fecha_aprobacion'=>date('Y-m-d H:i:s'), 'estado'=>'Aprobado', 'entregado'=>'S']);

            $listar=AlimentoPaciente::whereDate('fecha_solicita',date('Y-m-d'))
            ->where('estado','Aprobado')
            ->where('entregado','S')
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

    public function reportePdfAliPacienteAprobado(){
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
            $crearpdf=PDF::loadView('alimentacion.pdf_aprobado_paciente',['datos'=>$listar]);
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
}