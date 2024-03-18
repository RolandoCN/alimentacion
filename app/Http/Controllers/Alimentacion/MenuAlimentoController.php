<?php

namespace App\Http\Controllers\Alimentacion;
use App\Http\Controllers\Controller;
use App\Models\Alimentacion\MenuCabecera;
use App\Models\Alimentacion\MenuDetalle;
use \Log;
use Illuminate\Http\Request;
use DB;
use PDF;
use Storage;
use SplFileInfo;

class MenuAlimentoController extends Controller
{
      
    public function index(){
        return view('alimentacion.menu_dia');
    }

    //funcion para ingresar los tipos alimentos diarios automaticamente
    public function registroMenuCabeceraJob(){
        try{
            $fecha_actual=date('Y-m-d');
            $contador_regis=0;
            //id almuerzo=2, id merienda=3, id cena=3
            //consultamos si hay registros en la tabla cabera del menu (fecha y tipo alimento)
            $id_ali_au=2;
            for($i=0; $i<=2; $i++){
                             
                $exite_ali_hoy=MenuCabecera::where('fecha', date('Y-m-d'))
                ->where('estado','A')
                ->where('id_alimento',$id_ali_au)->first();
                if(is_null($exite_ali_hoy)){
                    $guarda_ali_cab=new MenuCabecera();
                    $guarda_ali_cab->id_alimento=$id_ali_au;
                    $guarda_ali_cab->fecha=$fecha_actual;
                    $guarda_ali_cab->id_usuario_reg=1;
                    $guarda_ali_cab->estado='A';
                    $guarda_ali_cab->fecha_reg=date('Y-m-d H:i:s');
                    $guarda_ali_cab->save();
                    $contador_regis=$contador_regis+1;
                    $id_ali_au=$id_ali_au+1;
                }
            }
            if($contador_regis==0){
                return 'No se pudo realizar el registro de la cabecera del menú del día '.date('d-m-Y') ;
                Log::error('No se pudo realizar el registro de la cabecera del menú del día '.date('d-m-Y'));
            }else if($contador_regis==3){
                return 'Menu cabecera del dia '.date('d-m-Y'). ' fue realizado exitosamente' ;
                Log::info( 'Menu cabecera del dia '.date('d-m-Y'). ' fue realizado exitosamente');
            }else{
                return 'Fue realizado'. $contador_regis.' exitosamente y en los otros no se pudo realizar el registro cabecera del dia '.date('d-m-Y'). ' fue realizado exitosamente' ;
                Log::info( 'Fue realizado'. $contador_regis.' exitosamente y en los otros no se pudo realizar el registro cabecera del dia '.date('d-m-Y'). ' fue realizado exitosamente' );
            }
        }catch (\Throwable $e) {
            Log::error('MenuAlimentoController => registroMenuCabecera => mensaje => '.$e->getMessage().  'linea => '.$e->getLine());
            return 'Ocurrió un error';
            
        }

    }


    public function listar(){
        try{
            $menu=MenuCabecera::with('detalle','alimento')->where('estado','!=','I')
            ->where('fecha',date('Y-m-d'))
            ->get();
            return response()->json([
                'error'=>false,
                'resultado'=>$menu
            ]);
        }catch (\Throwable $e) {
            Log::error('MenuAlimentoController => listar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function menuAlimento($id){
        try{
            $menu=MenuDetalle::where('estado','!=','I')
            ->where('idal_menu_comida',$id)
            ->get();
            return response()->json([
                'error'=>false,
                'resultado'=>$menu
            ]);
        }catch (\Throwable $e) {
            Log::error('MenuAlimentoController => menuAlimento => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function editarMenuAli($id){
        try{
            $menuAli=MenuDetalle::where('estado','A')
            ->where('idal_menu_detalle', $id)
            ->first();
            
            return response()->json([
                'error'=>false,
                'resultado'=>$menuAli
            ]);
        }catch (\Throwable $e) {
            Log::error('MenuAlimentoController => editarMenuAli => mensaje => '.$e->getMessage(). 'linea => '.$e->getLine());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }
    
    public function guardarMenuAli(Request $request){
       
        $messages = [
            'descripcion_ali.required' => 'Debe ingresar la descripción',    
        ];
           

        $rules = [
            'descripcion_ali' =>"required|string|max:500",
        ];

        $this->validate($request, $rules, $messages);
        try{

            $guarda_menu_dia=new MenuDetalle();
            $guarda_menu_dia->descripcion=$request->descripcion_ali;
            $guarda_menu_dia->idal_menu_comida=$request->idalimento_menu_detalle;
            $guarda_menu_dia->id_usuario_reg=auth()->user()->id;
            $guarda_menu_dia->fecha_reg=date('Y-m-d H:i:s');
            $guarda_menu_dia->estado="A";

            //validar que el menu no se repita
            $valida_menu_dia=MenuDetalle::where('descripcion', $guarda_menu_dia->descripcion)
            ->where('estado','A')
            ->where('idal_menu_comida',$request->idalimento_menu_detalle)
            ->first();

            if(!is_null($valida_menu_dia)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La información ingresada ya existe'
                ]);
            }

            if($guarda_menu_dia->save()){
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
            Log::error('MenuAlimentoController => guardar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error'
            ]);
            
        }
    }

    public function actualizar(Request $request, $id){
       
        $messages = [
            'descripcion_ali.required' => 'Debe ingresar la descripción',    
        ];
           

        $rules = [
            'descripcion_ali' =>"required|string|max:500",
        ];

        $this->validate($request, $rules, $messages);
        try{

            $actualiza_menu_dia= MenuDetalle::find($id);
            $actualiza_menu_dia->descripcion=$request->descripcion_ali;
            $actualiza_menu_dia->idal_menu_comida=$request->idalimento_menu_detalle;
            $actualiza_menu_dia->id_usuario_reg=auth()->user()->id;
            $actualiza_menu_dia->fecha_reg=date('Y-m-d H:i:s');
            $actualiza_menu_dia->estado="A";

            //validar que el menu no se repita
            $valida_menu_dia=MenuDetalle::where('descripcion', $actualiza_menu_dia->descripcion)
            ->where('estado','A')
            ->where('idal_menu_detalle','!=',$id)
            ->where('idal_menu_comida',$request->idalimento_menu_detalle)
            ->first();

            if(!is_null($valida_menu_dia)){
                return response()->json([
                    'error'=>true,
                    'mensaje'=>'La información ingresada ya existe'
                ]);
            }

            if($actualiza_menu_dia->save()){
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
            Log::error('MenuAlimentoController => actualizar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function eliminarMenuAli($id){
        try{

            $menuAli=MenuDetalle::find($id);
            $menuAli->id_usuario_act=auth()->user()->id;
            $menuAli->fecha_act=date('Y-m-d H:i:s');
            $menuAli->estado="I";
            if($menuAli->save()){
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
            Log::error('MenuAlimentoController => eliminar => mensaje => '.$e->getMessage());
            return response()->json([
                'error'=>true,
                'mensaje'=>'Ocurrió un error, intentelo más tarde'
            ]);
            
        }
    }

    public function reporteMenuAli($desde, $hasta){
        try{            
            set_time_limit(0);
            ini_set("memory_limit",-1);
            ini_set('max_execution_time', 0);

            $fecha_ini=$desde;
            $fecha_fin=$hasta;

            $menuComida=MenuCabecera::with('detalle','alimento')->where('estado','!=','I')
            ->whereBetween('fecha',[$fecha_ini, $fecha_fin])
            ->get();

            #agrupamos por fecha
            $lista_final_agrupada=[];
            foreach ($menuComida as $key => $item){                
                if(!isset($lista_final_agrupada[$item->fecha])) {
                    $lista_final_agrupada[$item->fecha]=array($item);
            
                }else{
                    array_push($lista_final_agrupada[$item->fecha], $item);
                }
            }
           
            $nombrePDF="reporte_.pdf";

            $pdf=PDF::loadView('alimentacion.reporte.pdf_menu_comida',['fecha_ini'=>$fecha_ini, 'fecha_fin'=>$fecha_fin, 'data'=>$menuComida, 'lista_final_agrupada'=>$lista_final_agrupada]);
            $pdf->setPaper("A4", "portrait");
            $estadoarch = $pdf->stream();

            return $pdf->stream($nombrePDF);


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

}
