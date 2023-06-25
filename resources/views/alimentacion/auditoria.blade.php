@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Auditoria
        </h1>

    </section>

    <section class="content" id="arriba">

        <div id="content_consulta-" >
            <div class="box ">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span id='cabecera_txt'>Turno</span>
                        <button id="cabecera_btn" style="display:none" class="btn btn-danger btn-xs" onclick="cancelar()">Volver</button>
                    </h3>
                </div>

                <div class="box-body" id="content_consulta">
                    <div class="row">


                        <div class="col-md-12">
                            <form id="frm_buscarAuditoria" class="form-horizontal" action="" autocomplete="off">
                                {{ csrf_field() }}
                                <div class="box-body">

                                    <div class="form-group">
                                        <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Fecha Inicio:</label>
                                        
                                        <div class="col-sm-10" style="font-weight: normal;">                     
                                            <input type="date"  class="form-control" id="fecha_ini"  name="fecha_ini" >
                                        </div>
                                                
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Fecha Fin:</label>
                                        
                                        <div class="col-sm-10" style="font-weight: normal;">                     
                                            <input type="date"  class="form-control" id="fecha_fin"  name="fecha_fin" >
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
                                <li style="border-color: white"><a><i class="fa fa-calendar text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Fecha Inicial</b>: <span  id="fecha_ini_rep"> </span></a></li>
                            
                            </ul>
                            
                        </div>  
                        
                        <div class="col-md-6">
                            <ul class="nav nav-pills nav-stacked"style="margin-left:50px">
                                <li style="border-color: white"><a><i class="fa fa-calendar text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Fecha Fin</b>: <span  id="fecha_fin_rep"> </span></a></li>
                            
                            </ul>
                            
                        </div>  
                      
                        <div class="col-md-12" style="margin-top:22px">
                            <table class="table table-bordered table-hover text-center" id="tabla_auditoria">
                                <thead class="th">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Funcionario </th>
                                    <th>Horario</th>
                                    <th>Ingreso</th>
                                    <th>Actualización</th>
                                    <th>Estado</th>
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
                                <ul class="nav nav-pills nav-stacked"style="margin-left:12px">
                                    <li style="border-color: white"><a><i class="fa fa-edit text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Motivo</b>: <span  id="motivo_txt">  </span></a></li>
                                   
                                </ul>
                            </div>
                        </div>
        
                       
                    </div>
                 
                </div>
        
            </div>
        
        </div>

    </section>
@endsection
@section('scripts')

<script src="{{ asset('js/alimentacion/auditoria.js?v='.rand())}}"></script>

    
<script>

 
    
</script>
@endsection