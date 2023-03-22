

$("#form_registro_horario").submit(function(e){
    e.preventDefault();
    
    //validamos los campos obligatorios
    let descripcion=$('#descripcion').val()
    let codigo=$('#codigo').val()
    let hora_ini=$('#hora_ini').val()
    let hora_fin=$('#hora_fin').val()

    if(codigo=="" || codigo==null){
        alertNotificar("Debe ingresar el código","error")
        $('#codigo').focus()
        return
    } 

    if(descripcion=="" || descripcion==null){
        alertNotificar("Debe ingresar la descripcion","error")
        $('#descripcion').focus()
        return
    } 

    if(hora_ini=="" || hora_ini==null){
        alertNotificar("Debe ingresar la hora de inicio","error")
        $('#hora_ini').focus()
        return
    } 

    if(hora_fin=="" || hora_fin==null){
        alertNotificar("Debe ingresar la hora de fin","error")
        $('#hora_fin').focus()
        return
    } 

    if(hora_ini >= hora_fin){
        alertNotificar("La hora final debe ser mayor a la inicial","error")
        $('#fecha_fin').focus()
        return
    }
    vistacargando("m","Espere por favor")
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //comprobamos si es registro o edicion
    let tipo=""
    let url_form=""
    if(AccionForm=="R"){
        tipo="POST"
        url_form="guardar-horario"
    }else{
        tipo="PUT"
        url_form="actualizar-horario/"+idHorarioEditar
    }
  
    var FrmData=$("#form_registro_horario").serialize();

    $.ajax({
            
        type: tipo,
        url: url_form,
        method: tipo,             
		data: FrmData,      
		
        processData:false, 

        success: function(data){

            vistacargando("");                
            if(data.error==true){
                alertNotificar(data.mensaje,'error');
                return;                      
            }
            limpiarCampos()
            alertNotificar(data.mensaje,"success");
            $('#form_ing').hide(200)
            $('#listado_horario').show(200)
            llenar_tabla_horario()
                            
        }, error:function (data) {
            console.log(data)

            vistacargando("");
            alertNotificar('Ocurrió un error','error');
        }
    });
})

function limpiarCampos(){
    $('#descripcion').val('')
    $('#codigo').val('')
    $('#hora_ini').val('')
    $('#hora_fin').val('')
}

function llenar_tabla_horario(){
    var num_col = $("#tabla_horario thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_horario tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
   
    
    $.get("listado-horario-alimentos/", function(data){
      
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_horario tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_horario tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }
         
            $('#tabla_horario').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                order: [[ 1, "desc" ]],
                sInfoFiltered:false,
                language: {
                    url: 'json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "15%", "targets": 0 },
                    { "width": "35%", "targets": 1 },
                    { "width": "15%", "targets": 2 },
                    { "width": "15%", "targets": 3 },
                    { "width": "20%", "targets": 4 },
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "codigo"},
                        {data: "descripcion" },
                        {data: "hora_ini"},
                        {data: "hora_fin"},
                        {data: "descripcion"},
                    
                ],    
                "rowCallback": function( row, data, index ) {
                    // $('td', row).eq(0).html(index+1)
                    $('td', row).eq(4).html(`
                                  
                                            <button type="button" class="btn btn-primary btn-xs" onclick="editarHorario(${data.id_horario })">Editar</button>

                                            <button type="button" class="btn btn-success btn-xs" onclick="alimentos(${data.id_horario })">Alimentos</button>
                                                                                
                                            <a onclick="eliminarHorario(${data.id_horario })" class="btn btn-danger btn-xs"> Eliminar </a>
                                       
                                    
                    `); 
                }             
            });
        }
    }).fail(function(){
        $("#tabla_horario tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });


}

$('.collapse-link').click();
$('.datatable_wrapper').children('.row').css('overflow','inherit !important');

$('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0', 'overflow-x':'inherit'});


function alimentos(id_horario, abiertaModal=null){
    $('#codigo_modal').html('')
    $('#inicia_modal').html('')
    $('#descripcion_modal').html('')
    $('#fin_modal').html('')
    
    $.get("horario-alimentos/"+id_horario, function(data){
        
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_menu tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_menu tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }
         
            $('#tabla_menu').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                order: [[ 1, "desc" ]],
                sInfoFiltered:false,
                language: {
                    url: 'json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "20%", "targets": 0 },
                    { "width": "55%", "targets": 1 },
                    { "width": "25%", "targets": 2 },
                
                ],
                data: data.resultado,
                columns:[
                        {data: "idalimento"},
                        {data: "descripcion" },
                        {data: "idalimento"},
                ],    
                "rowCallback": function( row, data, index ) {
                    $('td', row).eq(0).html(index+1)
                    let perm=""
                    if(data.accesoPerm=="S"){
                        perm="checked"
                    }else{
                        perm=""
                    }
                    $('td', row).eq(2).html(`
                                  
                                            
                                            <input type="checkbox" onclick="accionAcceso(${data.idalimento})"class="acces_check" id="check_${data.idalimento}" name="acces_check" value="${data.idalimento}"  ${perm}>
                                       
                                    
                    `); 
                }             
            });
            globalThis.HorarioSeleccionado=id_horario
            $('#codigo_modal').html(data.horario.codigo)
            $('#inicia_modal').html(data.horario.hora_ini)
            $('#descripcion_modal').html(data.horario.descripcion)
            $('#fin_modal').html(data.horario.hora_fin)

            if(abiertaModal!="S"){
                $('#modal_Alimento').modal('show')
            }
               
        }

     

       
    }).fail(function(){
       
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}

function accionAcceso(id){
   
    if( $('#check_'+id).is(':checked') ){
        // mandamos a guardar ese menu al perfil
        AggQuitarAlimentoHorario(id,'A')
    } else {
        // mandamos a quitar
        AggQuitarAlimentoHorario(id,'Q')
    }
}

function AggQuitarAlimentoHorario(id_alim, tipo){
    vistacargando("m","Espere por favor")
    $.get("alimento-por-horario/"+id_alim+"/"+tipo+"/"+HorarioSeleccionado, function(data){
        vistacargando("")
        if(data.error==true){
            if(tipo=="A"){
                $('#check_'+id_alim).prop('checked', false)
            }else{
                $('#check_'+id_alim).prop('checked', true)
            }
            alertNotificar(data.mensaje,"error");
            return;   
        }
       
        alertNotificar(data.mensaje,"success")
        // alimentos(HorarioSeleccionado,'S')

       
    }).fail(function(){
        if(tipo=="A"){
            $('#check_'+id_alim).prop('checked', false)
        }else{
            $('#check_'+id_alim).prop('checked', true)
        }
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
        vistacargando("")
    });
}


function editarHorario(id_horario){
    vistacargando("m","Espere por favor")
    $.get("editar-horario/"+id_horario, function(data){
        vistacargando("")
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            return;   
        }
        if(data.resultado==null){
            alertNotificar("El horario ya no se puede editar","error");
            return;   
        }

        $('#descripcion').val(data.resultado.descripcion)
        $('#codigo').val(data.resultado.codigo)
        $('#hora_ini').val(data.resultado.hora_ini)
        $('#hora_fin').val(data.resultado.hora_fin)

        visualizarForm('E')
        globalThis.idHorarioEditar=id_horario

       
    }).fail(function(){
        vistacargando("")
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}

function visualizarForm(tipo){
    $('#form_ing').show(200)
    $('#listado_horario').hide(200)
    globalThis.AccionForm="";
    if(tipo=='N'){
        $('#titulo_form').html("Registro Horario")
        $('#nombre_btn_form').html('Registrar')
        AccionForm="R"
    }else{
        $('#titulo_form').html("Actualizar Horario")
        $('#nombre_btn_form').html('Actualizar')
        AccionForm="E"
    }
}

function visualizarListado(){
    $('#form_ing').hide(200)
    $('#listado_horario').show(200)
    limpiarCampos()
}

function eliminarHorario(id_horario){
    if(confirm('¿Quiere eliminar el registro?')){
        vistacargando("m","Espere por favor")
        $.get("eliminar-horario/"+id_horario, function(data){
            vistacargando("")
            if(data.error==true){
                alertNotificar(data.mensaje,"error");
                return;   
            }
    
            alertNotificar(data.mensaje,"success");
            llenar_tabla_horario()
           
        }).fail(function(){
            vistacargando("")
            alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
        });
    }
   
}