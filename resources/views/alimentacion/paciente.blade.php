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
                    <button type="button" class="btn btn-xs btn-success" onclick="llenar_tabla_paciente()">Actualizar</button>
                    <button type="button" class="btn btn-xs btn-primary" onclick="pdf_alimento_pac()">Generar</button>
                </div>

                <div class="table-responsive">
                    <table id="tabla_paciente" width="100%"class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Servicio</th>
                                <th>Responsable</th>
                                <th>Dieta</th>                                
                                <th>Estado</th>    
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5"><center>No hay Datos Disponibles</td>
                            </tr>
                            
                        </tbody>
                      
                    </table>  
                  </div>    

                
            </div>

        </div>


        

    </section>

@endsection
@section('scripts')

    <script src="{{ asset('js/alimentacion/paciente.js?v='.rand())}}"></script>

    <script>
        llenar_tabla_paciente()
        // limpiarCampos()
    </script>


@endsection
