@extends('layouts.app')
@section('content')

<style type="text/css">
    #table_buscarContribuyente_filter{
      float: right;
    }
</style>
<link rel="stylesheet" href="{{asset('css/spinners.css')}}">

<section class="content-header" style="margin-bottom:10px;">
      <h1>
        Reporteria Nutricion
       
      </h1>
     
</section>

<section class="content">
    @if(Session()->has('mensajePInfoDatosVacios_'))
      <div id="mensajeGeneral" class="form-group">
          <div class="col-md-12 col-sm-12 col-xs-12" >
              <div class="alert alert-{{session('estado')}} alert-dismissible fade in" role="  alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">  <span aria-hidden="true">×</span>
                  </button>
                  <strong>Información: </strong><br>
                  <ul>
                    <li>{{session('mensajePInfoDatosVacios_')}}</li>
                  </ul>
              </div>
          </div>
      </div>
    @endif

    @if($errors->any())
      <div class="form-group">
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="alert alert-danger alert-dismissible fade in" role="  alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">  <span aria-hidden="true">×</span>
                </button>
                <strong> <i class="fa fa-info"></i>nformación: </strong><br>
                @foreach( $errors->all() as $error)
                  <li>{{ $error}}</li>
                @endforeach
            </div>
        </div>
      </div>
    @endif

    <div class="row ">
        <div class="col-xs-12 ">
            <div class="box">
                <div class="box-header " style="margin-top:13px;">
                <h3 class="box-title">Dieta Pacientes</h3>
                </div>
                <div class="box-body ">
                    <div class="" role="tabpanel" data-example-id="togglable-tabs">
                        <ul id="myTab"  class="nav nav-tabs bar_tabs ul_mobil" role="tablist">
                            <li role="presentation" class="active first_li">                            
                                <a id="docum" href="#area" role="tab" data-toggle="tab" aria-expanded="true">
                                   <span>Area</span>
                                </a>
                            </li>
                            <li role="presentation" class="">                        
                                <a id="hist" href="#nutricionista" role="tab" data-toggle="tab" aria-expanded="true">
                                    <span>Nutricionista</span>
                                </a>
                            </li>       
                            <li role="presentation" class="">                        
                                <a id="hist" href="#global" role="tab" data-toggle="tab" aria-expanded="true">
                                    <span>Global</span>
                                </a>
                            </li>                             
                        </ul>    
                    </div>

                    <div id="myTabContent" class="tab-content">
                        <div id="area" role="tabpanel" class="tab-pane fade active in" aria-labelledby="home-tab">
                            <div class="panel-body" style="padding-top: 0px;">
                                 @include('alimentacion.reporte.area_dieta_paciente')                         
                            </div>
                        </div>                        
                        <div id="nutricionista" role="tabpanel" class="tab-pane fade" aria-labelledby="home-tab">
                            <div class="panel-body" style="padding-top: 0px;">
                                @include('alimentacion.reporte.area_nutricionista')                                         
                            </div>
                        </div>
                        <div id="global" role="tabpanel" class="tab-pane fade" aria-labelledby="home-tab">
                            <div class="panel-body" style="padding-top: 0px;">
                                @include('alimentacion.reporte.global_paciente')                                         
                            </div>
                        </div>                                        
                    </div>
                </div>         
            </div>          
        </div>
    </div>       
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/alimentacion/reporte/nutricionista.js?v='.rand())}}"></script>


  
@endsection

