@extends('layouts.app')

@section('content')

    
    <section class="content-header">
        <h1>
            Gestión Tipo Alimento
        </h1>

    </section>

    <section class="content" id="content_form">

        <div class="box" id="listado_horario">
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
                    <table id="tabla_tipo_ali" width="100%"class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Hora Min</th>
                                <th>Hora Max</th>
                                <th>Hora Max Aprobacion</th>
                                <th style="min-width: 30%">Opciones</th>
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


        <div id="form_ing" style="display:none">
            
        </div>

        
        <div class="modal fade_ detalle_class"  id="modal_Alimento" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">EDICION HORA DE APROBACION</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form class="form-horizontal" id="form_registro_horario" autocomplete="off" method="post"
                            action="">
                            {{ csrf_field() }}
                
                              

                                <div class="form-group">

                                    <label for="inputPassword3" class="col-sm-3 control-label">Descripción</label>
                                    <div class="col-sm-8">
                                        <input type="text" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="descripcion" name="descripcion" placeholder="Descripción" readonly>
                                       
                                    </div>
                                
                                </div>

                                <div class="form-group">

                                    <label for="hora_ini" class="col-sm-3 control-label">Hora Aprobacion Sugerida</label>
                                    <div class="col-sm-8">
                                        <input type="time" readonly minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="hora_sugerida" name="hora_sugerida" placeholder="Hora Sugerida">
                                    
                                    </div>
                                </div>

                                <div class="form-group">

                                   
                                    <label for="hora_fin" class="col-sm-3 control-label">Hora Aprobacion Actual</label>
                                    <div class="col-sm-8">
                                        <input type="time" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="hora_aprobacion" name="hora_aprobacion" placeholder="Hora Aprobacion" value="">
                                    
                                    </div>
                                </div>

                                <hr>
                                <div class="form-group">
                                    <div class="col-sm-12 text-center" >
                                    
                                        <button type="button" onclick="actualizarHoraAp()" class="btn btn-success btn-sm">
                                           Actualizar
                                        </button>
                                        <button type="button" onclick="cerrar()" class="btn btn-danger btn-sm">Cancelar</button>
                                    </div>
                                </div>
                        
                  
                            </form>
                           
                        </div>

                    
                    </div>
                
                </div>

            </div>

        </div>


    </section>

@endsection

@section('scripts')

    <script src="{{ asset('js/alimentacion/tipo_alimento.js?v='.rand())}}"></script>

    <script>
        llenar_tabla_tipo_ali()
        // limpiarCampos()
    </script>


@endsection
