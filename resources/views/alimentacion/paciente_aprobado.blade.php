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
            Alimento Aprobados Pacientes
        </h1>

    </section>

    <section class="content" id="content_form">

        <div class="box" id="listado_empleado">
            <div class="box-header with-border">
                <h3 class="box-title">Reporte de Alimento </h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                        title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    
                </div>
 
              
            </div>

            <div class="box-body" id="content_consulta">
                <div class="row">


                    <div class="col-md-12">
                        <form id="frm_buscarAliFechas" class="form-horizontal" action="" autocomplete="off">
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
                                    
                                        <button type="button" onclick="generarPdf()" class="btn btn-success btn-sm">
                                            Descargar
                                        </button>
                                      
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                    </div>

                 
                </div>
            </div>

            

        </div>


        

    </section>

@endsection
@section('scripts')

    <script src="{{ asset('js/alimentacion/paciente.js?v='.rand())}}"></script>

    <script>
        // llenar_tabla_paciente()
        // limpiarCampos()
    </script>


@endsection
