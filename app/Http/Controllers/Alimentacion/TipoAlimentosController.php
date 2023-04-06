<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion\Alimento;
use \Log;
use Illuminate\Http\Request;
use DB;

class TipoAlimentosController extends Controller
{
    public function index(){
       
        return view('alimentacion.tipo_alimento.index');
    }


    public function listar(){
        try{
            $horario=Alimento::where('estado','=','A')->get();
            return response()->json([
                'error'=>false,
                'resultado'=>$horario
            ]);
        }catch (\Throwable $e) {
            Log::error('TipoAlimentosController => listar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function actualizaHoraAprob($idali, $hora){
        try{
            $horario=Alimento::find($idali);
            $horario->hora_max_aprobacion=$hora;
            $horario->save();
            return response()->json([
                'error'=>false,
                'resultado'=>$horario
            ]);
        }catch (\Throwable $e) {
            Log::error('TipoAlimentosController => actualizaHoraAprob => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
}