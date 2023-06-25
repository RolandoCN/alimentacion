
<div class="modal fade_ detalle_class"  id="modal_Menu" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">MENÚ DEL ALIMENTO <span  id="ali_selecc" class="text-transform: uppercase !important"> </span> DEL DÍA {{date('d-m-Y')}} </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                   
                    <div class="col-md-12">
                        <form class="form-horizontal" id="form_registro_alimentos" autocomplete="off" method="post"
                            action="">
                            {{ csrf_field() }}
                            <div class="form-group">

                                <label for="inputPassword3" class="col-sm-3 control-label"></label>
                                <div class="col-sm-7">
                                    <input type="hidden" minlength="1" maxlength="100" onKeyPress="if(this.value.length==100) return false;" class="form-control" id="idalimento_menu_detalle" name="idalimento_menu_detalle" >
                                   
                                </div>
                            
                            </div>
                            
                            <div class="form-group">

                                <label for="inputPassword3" class="col-sm-3 control-label">Descripción</label>
                                <div class="col-sm-7">
                                    <textarea minlength="1" maxlength="100" onKeyPress="if(this.value.length==200) return false;" class="form-control" id="descripcion_ali" name="descripcion_ali" placeholder="Descripción"></textarea>

                                   
                                   
                                </div>
                            
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 col-md-offset-3 " >
                                
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <span id="nombre_btn_form"></span>
                                    </button>
                                    <button type="button" onclick="visualizarListado()" class="btn btn-danger btn-sm">Cancelar</button>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                    
                    <div class="table-responsive col-md-12">
                        <table id="tabla_menu" width="100%"class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descripción</th>
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
         
        </div>

    </div>

</div>
