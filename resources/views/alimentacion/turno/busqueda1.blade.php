@extends('layouts.app')


<link rel="stylesheet" href="../bower_components/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="../bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">

<style>
    .content_calendario{
        overflow-x: auto;
    }
    #calendar{
        min-width: 600px;
    }
</style>

@section('content')
    <section class="content-header">
        <h1>
            Administrador de Turno
        </h1>

    </section>

    <section class="content">

        <div id="content_consulta-" >
            <div class="box ">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span id='cabecera_txt'> Persona</span>
                        <button id="cabecera_btn" style="display:none" class="btn btn-danger btn-xs" onclick="cancelar()">Volver</button>
                    </h3>
                </div>

                <div class="box-body" id="content_consulta">
                    <div>
                        <form id="frm_buscarPersona" class="form-horizontal" action="" autocomplete="off">
                            {{ csrf_field() }}
                            <div class="box-body">
                                <div id="smsAlert"></div>
                                <div class="form-group">
                                <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Criterio:</label>
                                <div class="col-sm-10" style="font-weight: normal;">                     
                                    <select data-placeholder="Busqueda por Cédula o Nombres de Persona" style="width: 100%;" class="form-control select2" onchange="buscarPersona()" id="cmb_persona" name="cmb_persona">
                                                
                                    </select>
                                </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div>
                        <table class="table table-bordered table-hover text-center" id="table_persona">
                            <thead class="th">
                            <tr>
                                <th>Id</th>
                                <th>Identificacion</th>
                                <th>Nombres Completos</th>
                                <th style="width: 10px">Opciones</th>
                            </tr>
                            </thead>
        
                            <tbody style="font-weight: normal" id="pac_body">
                                <tr>
                                <td colspan="4">Ningún dato disponible en esta tabla</td>
                                </tr>
                            </tbody>
        
                        </table>
                    </div>
                </div>

                <div id='cale'  style="display:none">
                    <div class="box-body" >
                    
                        
                        <div class="col-md-12">
                            <div class="row_">
                                <div class="col-md-6">
                                    <ul class="nav nav-pills nav-stacked"style="margin-left:30px">
                                        <li style="border-color: white"><a><i class="fa fa-credit-card text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Identificación</b>: <span  id="identificacion"> </span></a></li>
                                    
                                    </ul>
                                 
                                </div>     
                                <div class="col-md-6">
                                    <ul class="nav nav-pills nav-stacked" style="margin-left:30px">
                                        <li style="border-color: white"><a><i class="fa fa-user text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Nombres:</b> <span  id="empleado"> </span></a></li>
                                        
                                    </ul>
                                   
                                </div>  

                                <div class="col-md-6">
                                    <ul class="nav nav-pills nav-stacked"style="margin-left:30px">
                                        <li style="border-color: white"><a><i class="fa fa-credit-card text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Puesto</b>: <span  id="puesto"> </span></a></li>
                                    
                                    </ul>
                                 
                                </div>  

                                <div class="col-md-6">
                                    <ul class="nav nav-pills nav-stacked"style="margin-left:30px">
                                        <li style="border-color: white"><a><i class="fa fa-credit-card text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Área</b>: <span  id="area"> </span></a></li>
                                    
                                    </ul>
                                 
                                </div>  
                            </div>
                        </div>
                        <br><br> <br><br> 
                        <div class="col-md-12 content_calendario"  >   
                            <div id='calendar' ></div>
                        </div> 
                    </div>
                    
                    {{-- <div class="box-body" style="display:none">                
                        <div class="col-md-10">   
                            <div id='calendar'  style=""></div>
                        </div> 
                    </div> --}}
                </div>
            </div>
        </div>

       
        @include('alimentacion.turno.modal_tipo_turno')
    </section>
@endsection
@section('scripts')
<script src="../bower_components/moment/moment.js"></script>
<script src="../bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="../bower_components/fullcalendar/dist/locale/es.js"></script>
<script src="/js/alimentacion/turno/registro.js"></script>
    
<script>

 
    
</script>
@endsection