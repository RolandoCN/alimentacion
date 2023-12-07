@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Listado de Alimentos por Fechas Tipo Dietas
        </h1>

    </section>

    <section class="content" id="arriba">

        <div id="content_consulta-" >
            <div class="box ">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <span id='cabecera_txt'>Reporte Consolidado</span>
                        <button id="cabecera_btn" style="display:none" class="btn btn-danger btn-xs" onclick="cancelar()">Volver</button>
                    </h3>
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
                                        
                                            <button type="button" onclick="buscarTurnos()" class="btn btn-success btn-sm">
                                                Generar
                                            </button>
                                          
                                        </div>
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

{{-- <script src="/js/alimentacion/reporte/entre_fecha.js"></script> --}}
<script src="{{ asset('js/alimentacion/reporte/entre_fecha_dieta.js?v='.rand())}}"></script>

 
@endsection