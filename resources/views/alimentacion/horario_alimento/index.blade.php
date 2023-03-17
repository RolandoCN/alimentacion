@extends('layouts.app')

@section('content')

    
    <section class="content-header">
        <h1>
            Gestión Horario Alimento
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

                <div class="col-md-12" style="text-align:right; margin-bottom:20px; margin-top:10px">
                    <button type="button" onclick="visualizarForm('N')" class="btn btn-primary btn-sm">Nuevo Horario</button>
                </div>

                <div class="table-responsive">
                    <table id="tabla_horario" width="100%"class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Hora Inicia</th>
                                <th>Hora Fin</th>
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
            <form class="form-horizontal" id="form_registro_horario" autocomplete="off" method="post"
                action="">
                {{ csrf_field() }}
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title" id="titulo_form"> </h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                title="Collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                            
                        </div>
                    </div>
                    <div class="box-body">

                        <div class="form-group">

                            <label for="codigo" class="col-sm-3 control-label">Código</label>
                            <div class="col-sm-8">
                                <input type="text" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="codigo" name="codigo" placeholder="Código">
                               
                            </div>
                        </div>
                           

                        <div class="form-group">

                            <label for="inputPassword3" class="col-sm-3 control-label">Descripción</label>
                            <div class="col-sm-8">
                                <input type="text" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="descripcion" name="descripcion" placeholder="Descripción">
                                <span class="invalid-feedback" role="alert" style="color:red; display:none
                                " id="error_descripcion">
                                    <strong id="txt_error_descripcion"></strong>
                                </span>
                            </div>
                           
                        </div>

                        <div class="form-group">

                            <label for="hora_ini" class="col-sm-3 control-label">Hora Inicia</label>
                            <div class="col-sm-8">
                                <input type="time" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="hora_ini" name="hora_ini" placeholder="Hora Inicia">
                               
                            </div>
                        </div>

                        <div class="form-group">

                            <label for="hora_fin" class="col-sm-3 control-label">Hora Fin</label>
                            <div class="col-sm-8">
                                <input type="time" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="hora_fin" name="hora_fin" placeholder="Hora Fin">
                               
                            </div>
                        </div>

                        <hr>
                        <div class="form-group">
                            <div class="col-sm-12 text-center" >
                            
                                <button type="submit" class="btn btn-success btn-sm">
                                    <span id="nombre_btn_form"></span>
                                </button>
                                <button type="button" onclick="visualizarListado()" class="btn btn-danger btn-sm">Cancelar</button>
                            </div>
                        </div>
                        
                    </div>

                </div>
            
            </form>
        </div>
        @include('alimentacion.horario_alimento.modal_alimento')

    </section>

@endsection

@section('scripts')

    {{-- <script src="/js/alimentacion/horario_alimento.js"></script> --}}
    <script src="{{ asset('js/alimentacion/horario_alimento.js?v='.rand())}}"></script>


    <script>
        llenar_tabla_horario()
        limpiarCampos()
    </script>


@endsection
