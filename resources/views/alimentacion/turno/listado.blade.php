@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{asset('plugins/sweetalert/sweetalert.css')}}">
    <style>
      
      .color_aprobacion{
        background: #d7f7e7 !important
    }

    .color_confirmado{
       
        background: #9ae5f7  !important
    }

    .color_pendiente{
        background: #f3eaea  !important
    }

    .label-disabled{
        background: #efb6af !important
    }
    </style>
    <section class="content-header">
        <h1>
            Listado de Turno
        </h1>

    </section>

    <section class="content" id="arriba">

        <div id="content_consulta-" >
            <div class="box ">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span id='cabecera_txt'> Aprobación de Turnos</span>
                        <button id="cabecera_btn" style="display:none" class="btn btn-danger btn-xs" onclick="cancelar()">Volver</button>
                    </h3>
                </div>

                <div class="box-body" id="content_consulta">
                    <div class="row">


                        <div class="col-md-12">
                            <form id="frm_buscarPersona" class="form-horizontal" action="" autocomplete="off">
                                {{ csrf_field() }}
                                <div class="box-body">

                                    <div class="form-group">
                                        <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Fecha:</label>
                                        
                                        <div class="col-sm-10" style="font-weight: normal;">                     
                                            <input type="date"  class="form-control" id="txt_fecha"  name="txt_fecha" placeholder="Descripción">
                                        </div>
                                                
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Alimento:</label>
                                        
                                        <div class="col-sm-10" style="font-weight: normal;">                     
                                            <select data-placeholder="Seleccione Un Opción" style="width: 100%;" class="form-control select2" name="idalimento" id="idalimento" >
                                        
                                                @foreach ($alimento as $dato)
                                                    <option value=""></option>
                                                    <option value="{{ $dato->idalimento }}" >{{$dato->descripcion}}</option>
                                                @endforeach
        
                                            </select>
                                        </div>
                                                
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12 col-md-offset-2" >
                                        
                                            <button type="button" onclick="buscarTurnos()" class="btn btn-success btn-sm">
                                                Buscar
                                            </button>
                                          
                                        </div>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>

                     
                    </div>
                </div>

                <div class="box-body" id="listado_turno" style="display:none" >
                    <div class="row">

                        <div class="col-md-6">
                            <ul class="nav nav-pills nav-stacked"style="margin-left:50px">
                                <li style="border-color: white"><a><i class="fa fa-calendar text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Fecha</b>: <span  id="fecha_turno"> </span></a></li>
                            
                            </ul>
                            
                        </div>     
                        <div class="col-md-6">
                            <ul class="nav nav-pills nav-stacked" style="margin-left:50px">
                                <li style="border-color: white"><a><i class="fa fa-cutlery text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Comida:</b> <span  id="comida_turno"> </span></a></li>
                                
                            </ul>
                            
                        </div>  

                      
                        <div class="col-md-6">
                            <ul class="nav nav-pills nav-stacked"style="margin-left:50px">
                                <li style="border-color: white"><a><i class="fa fa-user text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Usuario</b>: <span  id="usuario">{{auth()->user()->persona->nombres}} {{auth()->user()->persona->apellidos}} </span></a></li>
                            
                            </ul>
                            
                        </div> 

                        <div class="col-md-6">
                            <ul class="nav nav-pills nav-stacked"style="margin-left:50px">
                                <li style="border-color: white"><a><i class="fa fa-sort-numeric-asc text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Pendientes</b>: <span  id="total_pendientes"> </span></a></li>
                            
                            </ul>
                            
                        </div>  

                        <div class="col-md-6 confir_secc">
                            <ul class="nav nav-pills nav-stacked"style="margin-left:50px">
                                <li style="border-color: white"><a><i class="fa fa-edit text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Confirmados</b>: <span  id="total_confirmados"> </span></a></li>
                            
                            </ul>
                            
                        </div>  

                        <div class="col-md-6">
                            <ul class="nav nav-pills nav-stacked"style="margin-left:50px">
                                <li style="border-color: white"><a><i class="fa fa-check-square text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Aprobados</b>: <span  id="total_apro"> </span></a></li>
                            
                            </ul>
                            
                        </div> 

                        <div class="col-md-12 text-center" style="margin-top:10px; margin-bottom:10px">
                            <button type="button" class="btn btn-success btn-sm btn_aprobacion" onclick="aprobarTurno()">Aprobar</button>
                        </div>
                     
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover text-center" id="table_persona">
                                <thead class="th">
                                <tr>
                                    <th>Cédula</th>
                                    <th>Funcionario</th>
                                    <th>Puesto</th>
                                    <th>Área</th>
                                    <th>Horario</th>
                                    <th>Estado</th>
                                    <th></th>
                                   
                                </tr>
                                </thead>
            
                                <tbody style="font-weight: normal" id="pac_body">
                                    <tr>
                                    <td colspan="7">Ningún dato disponible en esta tabla</td>
                                    </tr>
                                </tbody>
            
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal fade_ detalle_class"  id="modal_Detalle_Eli" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">MOTIVO ELIMINACIÒN</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                           
                            <div class="col-md-12">
                                <form id="frm_Motivo" class="form-horizontal" action="" autocomplete="off">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id_turno_ali_sel"  id="id_turno_ali_sel">
                                    <div class="form-group">
                                        <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Motivo:</label>
                                        
                                        <div class="col-sm-10" style="font-weight: normal;">                     
                                            <textarea  class="form-control" id="motivo_elim"  name="motivo_elim" placeholder="Ingrese el motivo"></textarea>
                                        </div>
                                                
                                    </div>

                                    
                                    <div class="form-group">
                                        <div class="col-sm-12 col-md-offset-2" >
                                        
                                            <button type="button" onclick="eliminacion_comida()" class="btn btn-success btn-sm">
                                                Aceptar
                                            </button>
                                            <button type="button" onclick="cancelaEliminacion()" class="btn btn-danger btn-sm">
                                                Cancelar
                                            </button>
                                            
                                        </div>
                                    </div>                                        
                                    
                                </form>
                            </div>
                        </div>
        
                       
                    </div>
                 
                </div>
        
            </div>
        
        </div>

    </section>
@endsection
@section('scripts')

{{-- <script src="/js/alimentacion/turno/listado.js"></script> --}}
<script src="{{asset('plugins/sweetalert/sweetalert.js')}}"></script>

<script src="{{ asset('js/alimentacion/turno/listado.js?v='.rand())}}"></script>
    
<script>

 
    
</script>
@endsection