@extends('layouts.app')

@section('content')

<style>
      
    .mayusc {
        text-transform: uppercase;
    }

   
</style>
    <section class="content-header">
        <h1>
            Gestión Menú Día
        </h1>

    </section>

    <section class="content" id="content_form">

        <div class="box" id="listado_rol">
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

                {{-- <div class="col-md-12" style="text-align:right; margin-bottom:20px; margin-top:10px">
                    <button type="button" onclick="visualizarForm('N')" class="btn btn-primary btn-sm">Nuevo</button>
                </div> --}}

                <div class="table-responsive">
                    <table id="tabla_rol" width="100%"class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Alimento</th>
                                <th>Menú</th>
                                <th style="min-width: 30%">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4"><center>No hay Datos Disponibles</td>
                            </tr>
                            
                        </tbody>
                      
                    </table>  
                  </div>    

                
            </div>

        </div>

        @include('alimentacion.modal_menu_hoy')

    </section>

@endsection

@section('scripts')

   
    <script src="{{ asset('js/alimentacion/menu_dia.js?v='.rand())}}"></script>

    <script>
        llenar_tabla_listado_dia()
        limpiarCampos()
    </script>


@endsection
