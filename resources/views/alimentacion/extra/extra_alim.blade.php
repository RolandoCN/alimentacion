@extends('layouts.app')

@section('content')

    
    <section class="content-header">
        <h1>
            Gestión Alimentos Extra
        </h1>

    </section>

    <section class="content" id="content_form">

        <div class="box" id="listado_extra">
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
                    <table id="tabla_extra" width="100%"class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Cédula</th>
                                <th>Nombres</th>
                                <th>Alimento</th>
                                <th>Motivo</th>
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
            <form class="form-horizontal" id="form_extra" autocomplete="off" method="post"
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
                                <select data-placeholder="Busqueda por Cédula o Nombres de Persona" style="width: 100%;" class="form-control select2"  id="id_empleado" name="id_empleado">
                                                
                                </select>
                            </div>
                            
                        </div>

                        <div class="form-group">

                            <label for="inputPassword3" class="col-sm-3 control-label">Alimento</label>
                            <div class="col-sm-8">
                                <select data-placeholder="Seleccione Una Opción" style="width: 100%;" class="form-control select2" name="id_alimento" id="id_alimento" >
                                
                                    @foreach ($alimento as $dato)
                                        <option value=""></option>
                                        <option value="{{ $dato->idalimento }}" >{{ $dato->descripcion }} </option>
                                    @endforeach
                                </select>
                              
                            </div>
                           
                        </div>


                        <div class="form-group">

                            <label for="inputPassword3" class="col-sm-3 control-label">Motivo</label>
                            <div class="col-sm-8">
                                <input type="text" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;"  class="form-control" id="motivo" name="motivo" placeholder="Motivo">
                                
                            </div>
                           
                        </div>

                        <div class="form-group">

                            <label for="inputPassword3" class="col-sm-3 control-label">Fecha</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" id="fecha" name="fecha" placeholder="Fecha">
                              
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

    {{-- <script src="/js/alimentacion/alimento_extra.js"></script> --}}
    <script src="{{ asset('js/alimentacion/alimento_extra.js?v='.rand())}}"></script>


    <script>
        llenar_tabla_extra()
        limpiarCampos()
    </script>


@endsection
