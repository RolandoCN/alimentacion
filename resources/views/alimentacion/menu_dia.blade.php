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

                <div class="col-md-12" style="text-align:center; margin-bottom:20px; margin-top:10px">
                    <button type="button" onclick="irFormReporte()" class="btn btn-primary btn-sm">Reportes</button>
                </div>

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
        
        <div class="modal fade_ detalle_class"  id="modal_Reporte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">REPORTE MENU<span  id="ali_selecc" class="text-transform: uppercase !important"> </span> DEL DÍA {{date('d-m-Y')}} </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                        
                            <div class="col-md-12">
                                <form class="form-horizontal" id="form_registro_alimentos" autocomplete="off" method="post"
                                    action="">
                                    {{ csrf_field() }}
                                    <div class="form-group">

                                        <label for="inputPassword3" class="col-sm-2 control-label">Desde</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="desde" name="desde" >
                                        
                                        </div>
                                    
                                    </div>
                                    <div class="form-group">

                                        <label for="inputPassword3" class="col-sm-2 control-label">Hasta</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="hasta" name="hasta" >
                                        
                                        </div>
                                    
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 col-md-offset-2 " >
                                        
                                            <button type="button" onclick="reporteMenu()" class="btn btn-success btn-sm">
                                                Descargar
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

   
    <script src="{{ asset('js/alimentacion/menu_dia.js?v='.rand())}}"></script>

    <script>
        llenar_tabla_listado_dia()
        limpiarCampos()
    </script>


@endsection
