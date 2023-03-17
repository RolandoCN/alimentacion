
<div class="modal fade_ detalle_class"  id="modal_Tipo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">AGREGAR TURNO</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    
                    <div class="col-md-12"> 
                        <form id="form_turno" class="form-horizontal" autocomplete="off"  enctype="multipart/form-data"> 
                            {{csrf_field()}}

                            <div class="col-md-2  col-sm-12 col-xs-12"></div>
                            <div class="col-md-8  col-sm-12 col-xs-12">

                                <div class="form-group">
                                    <label>Turno:</label>
                                    <select data-placeholder="Seleccione Un Turno" style="width: 100%;" class="form-control select2" name="idturno" id="idturno" >
                                        
                                        {{-- <option value=""></option>
                                        <option value="Desayuno" >Desayuno</option>
                                        <option value="Almuerzo" >Almuerzo</option>
                                        <option value="Merienda" >Merienda</option>
                                        <option value="Todos" >Todos</option> --}}

                                        @foreach ($TipoTurno as $dato)
                                            <option value=""></option>
                                            <option value="{{ $dato->id_horario }}" >{{$dato->codigo}} -- [{{$dato->hora_ini}}-{{$dato->hora_fin}}] </option>
                                        @endforeach

                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Persona:</label>
                                    <input type="text" autocomplete="off" name="empleado_seleccionado" id="empleado_seleccionado" class="form-control" required  readonly value="" >
                                </div>

                                <div class="form-group">
                                    <label>Puesto:</label>
                                    <input type="text" autocomplete="off" name="puesto_seleccionado" id="puesto_seleccionado" class="form-control" required  readonly  >
                                </div>

                                <div class="form-group">
                                    <label>Fecha:</label>
                                    <input type="date" autocomplete="off" name="fecha_ini" id="fecha_ini" class="form-control" required  readonly  >
                                </div>

                               
                                
                            </div>
                            <div class="col-md-2 col-sm-12 col-xs-12"></div>
                            <div class="col-md-12" style="margin-top: 15px !important">
                                <center>

                                    <button id="btn_submit-" type="button" onclick="guardarTurno()"  class="btn btn-success"><span class="fa fa-save"></span> Aceptar</button>

                                    <button type="button" class="btn btn-warning" data-dismiss="modal" value="Cancel" id="cerra_modal"><i class="fa fa-times"></i> Cerrar</button>
                                   
                                    
                                </center>
                            </div>
                        </form>
                        
                    </div>

                </div>

               
            </div>
         
        </div>

    </div>

</div>

<script>

    

</script>
