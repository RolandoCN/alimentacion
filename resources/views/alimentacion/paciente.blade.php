@extends('layouts.app')

@section('content')

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
            Alimento Solicitados Pacientes
        </h1>

    </section>

    <section class="content" id="content_form">

        <div class="box" id="listado_empleado">
            <div class="box-header with-border">
                <h3 class="box-title">Listado de Alimento del Dia {{date('d-m-Y')}} </h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                        title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    
                </div>

              
            </div>
            <div class="box-body">
                <div class="col-md-12 text-center" >

                    <form class="form-horizontal" id="form_gestion_menu" autocomplete="off" method="post"
                    action="">
                    {{ csrf_field() }}
                   
    
                            <div class="form-group">
    
                                <label for="inputPassword3" class="col-sm-3 control-label">Servicio</label>
                                <div class="col-sm-8">
                                    <select data-placeholder="Seleccione Una Servicio" style="width: 100%;" class="form-control select2" name="cmb_servicio" id="cmb_servicio" onchange="buscaServicio()">
                                        <option value="T">TODOS</option>
                                        @foreach ($servicio_data as $dato)
                                            
                                            <option value="{{ $dato->servicio}}" >{{ $dato->servicio }} </option>
                                        @endforeach
                                    </select>
                                   
                                </div>
                               
                            </div>
    
                            <div class="form-group">
                                <div class="col-sm-12 text-center" >
                                
                                    <button type="button" class="btn btn-success btn-sm" onclick="reporteAliPacSolicitado()">
                                        Descargar
                                    </button>
                                   
                                </div>
                            </div>
                            
                     
                
                </form>

                    {{-- <button type="button" class="btn btn-xs btn-success" onclick="llenar_tabla_paciente()">Actualizar</button> --}}
                  
                </div>

                <div class="table-responsive">
                    <table id="tabla_paciente" width="100%"class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Servicio</th>
                                <th>Responsable</th>
                                <th>Dieta</th>                                
                                <th>Estado</th>   
                                <th></th>    
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6"><center>No hay Datos Disponibles</td>
                            </tr>
                            
                        </tbody>
                      
                    </table>  
                  </div>    

                
            </div>

        </div>


        

    </section>
    @include('alimentacion.modal_doc')

    
    <div class="modal fade_ detalle_class"  id="modal_Historial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">HISTORIAL ALIMENTOS </h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div id="div_infor_apr">
                                    
                            <div class="row_" style="font-size: 13px">
                                <div class="col-md-6">
                                    <ul class="nav nav-pills nav-stacked" style="margin-left:0px">
                                        <li style="border-color: white"><a><i class="fa fa-credit-card text-blue"></i> <b class="text-black" style="font-weight: 650 !important">C.I. Paciente:</b> <span class="ci_paciente"></span></a></li>
                                        
                                    </ul>

                                    <ul class="nav nav-pills nav-stacked" style="margin-left:0px">
                                        <li style="border-color: white"><a><i class="fa fa-calendar-o text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Edad:</b> <span class="edad_paciente"></span></a></li>
                                        
                                    </ul>

                                </div>  
                                <div class="col-md-6">
                                    <ul class="nav nav-pills nav-stacked" style="margin-left:0px">
                                        <li style="border-color: white"><a><i class="fa fa-frown-o text-blue"></i> <b class="text-black" style="font-weight: 650 !important"> Paciente</b>: <span class="paciente_alim"></span></a></li>
                                    
                                    </ul>
                                   
                                </div>

                            </div>
                            
                        </div>
                        
                        <div class="table-responsive col-md-12">
                            <table id="tabla_historial" width="100%"class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Servicio</th>
                                        <th>Responsable</th>
                                        <th>Dieta</th>
                                        <th>Observacion</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6"><center>No hay Datos Disponibles</td>
                                    </tr>
                                    
                                </tbody>
                            
                            </table>  
                        </div>

                    </div>

                
                </div>
            
            </div>

        </div>

    </div>


@endsection
@section('scripts')

    <script src="{{ asset('js/alimentacion/paciente.js?v='.rand())}}"></script>

    <script>
        // llenar_tabla_paciente()
        buscaServicio()
        // limpiarCampos()
    </script>


@endsection
