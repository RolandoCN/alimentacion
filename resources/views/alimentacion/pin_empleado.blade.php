@extends('layouts.app')

@section('content')

    
    <section class="content-header">
        <h1>
            Gestión Empleado
        </h1>

    </section>

    <section class="content" id="content_form">

        <div class="box" id="listado_empleado">
            <div class="box-header with-border">
                <h3 class="box-title">Listado </h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                        title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                    
                </div>

              
            </div>
            <div class="box-body">


                <div class="table-responsive">
                    <table id="tabla_empleado" width="100%"class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombres</th>
                                <th>Área</th>
                                <th>Telefono</th>
                                <th>Pin</th>
                                <th>Notificado</th>
                                <th style="min-width: 30%">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7"><center>No hay Datos Disponibles</td>
                            </tr>
                            
                        </tbody>
                      
                    </table>  
                  </div>    

                
            </div>

        </div>


    </section>

@endsection
@section('scripts')

    <script src="{{ asset('js/alimentacion/empleado_pin.js?v='.rand())}}"></script>

    <script>
        llenar_tabla_empleado()
        limpiarCampos()
    </script>


@endsection
