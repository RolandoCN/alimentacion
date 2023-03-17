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

                <div class="col-md-12" style="text-align:right; margin-bottom:20px; margin-top:10px">
                    <button type="button" onclick="visualizarForm('N')" class="btn btn-primary btn-sm">Nuevo</button>
                </div>

                <div class="table-responsive">
                    <table id="tabla_empleado" width="100%"class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombres</th>
                                <th>Puesto</th>
                                <th>Área</th>
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
            <form class="form-horizontal" id="form_registro_empleado" autocomplete="off" method="post"
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
                            <label for="inputPassword3" class="col-sm-3 control-label">Cédula</label>
                            <div class="col-sm-8">
                                <input type="number" minlength="1" maxlength="10" onKeyPress="if(this.value.length==10) return false;"  class="form-control" id="cedula" name="cedula" placeholder="Cedula">
                                <span class="invalid-feedback" role="alert" style="color:red; display:none
                                " id="error_cedula">
                                    <strong id="txt_error_cedula"></strong>
                                </span>
                            </div>
                            
                        </div>

                        <div class="form-group">

                            <label for="inputPassword3" class="col-sm-3 control-label">Nombres</label>
                            <div class="col-sm-8">
                                <input type="text" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="nombres" name="nombres" placeholder="Nombres">
                                <span class="invalid-feedback" role="alert" style="color:red; display:none
                                " id="error_nombres">
                                    <strong id="txt_error_nombres"></strong>
                                </span>
                            </div>
                           
                        </div>


                        <div class="form-group">

                            <label for="inputPassword3" class="col-sm-3 control-label">Puesto</label>
                            <div class="col-sm-8">
                                <select data-placeholder="Seleccione Una Puesto" style="width: 100%;" class="form-control select2" name="idpuesto" id="idpuesto" >
                                
                                    @foreach ($puesto as $dato)
                                        <option value=""></option>
                                        <option value="{{ $dato->id_puesto}}" >{{ $dato->nombre }} </option>
                                    @endforeach
                                </select>
                            </div>
                           
                        </div>

                        <div class="form-group">

                            <label for="inputPassword3" class="col-sm-3 control-label">Área</label>
                            <div class="col-sm-8">
                                <select data-placeholder="Seleccione Una Área" style="width: 100%;" class="form-control select2" name="idarea" id="idarea" >
                                
                                    @foreach ($area as $dato)
                                        <option value=""></option>
                                        <option value="{{ $dato->id_area}}" >{{ $dato->nombre }} </option>
                                    @endforeach
                                </select>
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


    </section>

@endsection
@section('scripts')

    <script src="{{ asset('js/alimentacion/empleado.js?v='.rand())}}"></script>

    <script>
        llenar_tabla_empleado()
        limpiarCampos()
    </script>


@endsection
