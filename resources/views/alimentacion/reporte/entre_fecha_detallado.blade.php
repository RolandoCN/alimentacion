@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Listado de Alimentos por Fechas
        </h1>

    </section>

    <section class="content" id="arriba">

        <div id="content_consulta-" >
            <div class="box ">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span id='cabecera_txt'>Reporte Detallado</span>
                        <button id="cabecera_btn" style="display:none" class="btn btn-danger btn-xs" onclick="cancelar()">Volver</button>
                    </h3>
                </div>

                <div class="box-body" id="content_consulta">
                    <div class="row">


                        <div class="col-md-12">
                            <form id="frm_buscarAliFechasDet" class="form-horizontal" action="" autocomplete="off">
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
                      
                        <div class="col-md-12 text-center" style="margin-top:10px; margin-bottom:10px">
                            <button type="button" class="btn btn-success btn-sm btn_descargar" onclick="descargarAprobacion()">Descargar</button>
                        </div>
                     
                        <div class="col-md-12">
                            <table class="table table-bordered table-hover text-center" id="table_persona">
                                <thead class="th">
                                <tr>
                                    <th>Cédula</th>
                                    <th>Funcionario</th>
                                    <th>Fecha</th>
                                    <th>Alimento</th>
                                    <th>Horario</th>
                                    <th>Estado</th>
                                   
                                </tr>
                                </thead>
            
                                <tbody style="font-weight: normal" id="pac_body">
                                    <tr>
                                    <td colspan="6">Ningún dato disponible en esta tabla</td>
                                    </tr>
                                </tbody>
            
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>
@endsection
@section('scripts')

<script src="/js/alimentacion/reporte/detallado_entre_fecha.js"></script>
    
<script>

 
    
</script>
@endsection