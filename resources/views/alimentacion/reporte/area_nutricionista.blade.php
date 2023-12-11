<!-- Librerias para Sweet Alert -->
<link rel="stylesheet" href="{{asset('plugins/sweetalert/sweetalert.css')}}">

@if(Session()->has('mensajePInfoDatosRepetidos'))

    <div id="mensajeGeneralRep" class="form-group">
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="alert alert-{{session('estado')}} alert-dismissible fade in" role="  alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">  <span aria-hidden="true">×</span>
                </button>
                <strong>Información: </strong><br>
                    <ul>
                        <li>{{session('mensajePInfoDatosRepetidos')}}</li>
                  </ul>
            </div>
        </div>
    </div>

@endif    
<div class="row">  
    <div id="generar_repetido_2" style="margin-top:15px">
        <div class="col-md-12">
            <form id="frm_buscarAliFechaNutricionista" class="form-horizontal" action="" autocomplete="off">
                {{ csrf_field() }}
                <div class="box-body">

                    <div class="form-group">
                        <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Fecha Inicio:</label>
                        
                        <div class="col-sm-10" style="font-weight: normal;">                     
                            <input type="date"  class="form-control" id="fecha_ini_nutricionista"  name="fecha_ini" >
                        </div>
                                
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Fecha Fin:</label>
                        
                        <div class="col-sm-10" style="font-weight: normal;">                     
                            <input type="date"  class="form-control" id="fecha_fin_nutricionista"  name="fecha_fin" >
                        </div>
                                
                    </div>

                   

                    <div class="form-group">
                        <label for="inputEmail3" id="label_crit" class="col-sm-2 control-label" >Area:</label>
                        
                        <div class="col-sm-10" style="font-weight: normal;">                     
                            <select data-placeholder="Seleccione Una Area" style="width: 100%;" class="form-control select2" name="area_" id="area_" >
                                
                                @foreach ($area as $dato)
                                    <option value=""></option>
                                    <option value="{{ $dato->servicio}}" >{{ $dato->servicio }} </option>
                                @endforeach
                            </select>
                        </div>
                                
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 col-md-offset-2" >
                        
                            <button type="button" onclick="buscarAreaz()" class="btn btn-success btn-sm">
                                Descargar
                            </button>

                        </div>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
  
</div>

 <!-- Librerias para Sweet Alert -->
 <script src="{{asset('plugins/sweetalert/sweetalert.js')}}"></script>