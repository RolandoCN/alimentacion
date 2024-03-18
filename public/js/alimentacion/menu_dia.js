

$("#form_registro_alimentos").submit(function(e){
    e.preventDefault();
    
    //validamos los campos obligatorios
    let descripcion=$('#descripcion_ali').val()
   
    if(descripcion=="" || descripcion==null){
        alertNotificar("Debe ingresar la descripcion","error")
        $('#descripcion').focus()
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
        url_form="guardar-menu-ali"
    }else{
        tipo="PUT"
        url_form="actualizar-menu-ali/"+idMenuAliEditar
    }
  
    var FrmData=$("#form_registro_alimentos").serialize();

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
           
            alertNotificar(data.mensaje,"success");
           
            llenar_tabla_listado_dia()

            let ali_actual=$('#ali_selecc').html()
            let idal_menu_comida_actual=$('#idalimento_menu_detalle').val()
            verAli(ali_actual,idal_menu_comida_actual, null)
            visualizarForm('N')

            limpiarCampos()              
        }, error:function (data) {
            console.log(data)

            vistacargando("");
            alertNotificar('Ocurrió un error','error');
        }
    });
})

function limpiarCampos(){
    $('#descripcion_ali').val('')
    $('#idalimento_menu_detalle').val('')
}

function llenar_tabla_listado_dia(){
    var num_col = $("#tabla_rol thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_rol tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
   
    
    $.get("listado-menu-dia/", function(data){
      
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_rol tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_rol tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }
         
            $('#tabla_rol').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                // order: [[ 1, "desc" ]],
                sInfoFiltered:false,
                language: {
                    url: 'json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "10%", "targets": 0 },
                    { "width": "20%", "targets": 1 },
                    { "width": "50%", "targets": 2 },
                    { "width": "20%", "targets": 3 },
                   
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "fecha"},
                        {data: "alimento.descripcion" },
                        {data: "fecha" },
                        {data: "fecha"},
                    
                ],    
                "rowCallback": function( row, data, index ) {

                    var set=[''];
                    var hr='';
       
                    $.each(data.detalle,function(i,item2){
                    
                        if(i>=0){hr=`<li style="padding-bottom:10px">`;}
        
                        set[i]= ` ${hr} ${item2.descripcion}`;
        
                    });
                 
                    $('td', row).eq(2).html(set)
                    $('td', row).eq(3).html(`
                                  
                                           
                                            <button type="button" class="btn btn-primary btn-xs" onclick="verAli('${data.alimento.descripcion }','${data.idal_menu_comida }')">Editar</button>
                                                                                
                                       
                                       
                                    
                    `); 
                }             
            });
        }
    }).fail(function(){
        $("#tabla_rol tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });


}

$('.collapse-link').click();
$('.datatable_wrapper').children('.row').css('overflow','inherit !important');

$('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0', 'overflow-x':'inherit'});


function verAli(alimento,idal_menu_comida, abiertaModal=null){
    visualizarForm('N')
    $('#idalimento_menu_detalle').val()  
    $('#ali_selecc').html('')
    AccionForm="R"
    var num_col = $("#tabla_menu thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_menu tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
   
    $.get("menu-alimento/"+idal_menu_comida, function(data){
        console.log(data)
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_menu tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_menu tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                // alertNotificar("No se encontró datos","error");
                // return;  
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
                    { "width": "10%", "targets": 0 },
                    { "width": "70%", "targets": 1 },
                    { "width": "20%", "targets": 2 },
                 
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "idal_menu_detalle"},
                        {data: "descripcion" },
                        {data: "idal_menu_detalle" },
                       
                ],    
                "rowCallback": function( row, data, index ) {
                    $('td', row).eq(0).html(index+1)
                    let perm=""
                  
                    $('td', row).eq(2).html(`
                                  
                        <button type="button" class="btn btn-primary btn-xs" onclick="editarMenuAli(${data.idal_menu_detalle })">Editar</button>            
                                        
                        <a onclick="eliminarMenuAli(${data.idal_menu_detalle })" class="btn btn-danger btn-xs"> Eliminar </a>
                                                  
                                    
                    `); 
                }             
            });
            // globalThis.PerfilSeleccionado=id_perfil
            if(abiertaModal!="S"){
                $('#modal_Menu').modal('show')
            }

            $('#ali_selecc').html(alimento)
            $('#ali_selecc').addClass('mayusc')
            $('#idalimento_menu_detalle').val(idal_menu_comida)   
        }

     

       
    }).fail(function(){
       
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}



function editarMenuAli(idal_menu_detalle){
    vistacargando("m","Espere por favor")
    $.get("editar-menu-ali/"+idal_menu_detalle, function(data){
        vistacargando("")
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            return;   
        }
        if(data.resultado==null){
            alertNotificar("La persona ya no se puede editar","error");
            return;   
        }

        $('#descripcion_ali').val(data.resultado.descripcion)

        visualizarForm('E')
        globalThis.idMenuAliEditar=idal_menu_detalle

       
    }).fail(function(){
        vistacargando("")
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}

function visualizarForm(tipo){
   
    globalThis.AccionForm="";
    if(tipo=='N'){
      
        $('#nombre_btn_form').html('Registrar')
        AccionForm="R"
    }else{
      
        $('#nombre_btn_form').html('Actualizar')
        AccionForm="E"
    }
}

function visualizarListado(){
    $('#form_ing').hide(200)
    $('#listado_rol').show(200)
    limpiarCampos()
}

function eliminarMenuAli(id_perfil){
    if(confirm('¿Quiere eliminar el registro?')){
        vistacargando("")
        $.get("eliminar-menu-ali/"+id_perfil, function(data){
            vistacargando("")
            if(data.error==true){
                alertNotificar(data.mensaje,"error");
                return;   
            }
    
            alertNotificar(data.mensaje,"success");
            llenar_tabla_listado_dia()

            let ali_actual=$('#ali_selecc').html()
            let idal_menu_comida_actual=$('#idalimento_menu_detalle').val()
            verAli(ali_actual,idal_menu_comida_actual, null)
            visualizarForm('N')
           
        }).fail(function(){
            vistacargando("")
            alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
        });
    }
   
}

function irFormReporte(){
    var desde=$('#desde').val('')
    var hasta=$('#hasta').val('')
    $('#modal_Reporte').modal('show')
}

function reporteMenu(){
    var desde=$('#desde').val()
    var hasta=$('#hasta').val()

    if(desde==""){
        alertNotificar("Ingrese fecha inicio", "error")
        $('#desde').focus()
        return
    }
    if(hasta==""){
        alertNotificar("Ingrese fecha fin", "error")
        $('#hasta').focus()
        return
    }

    if(desde>hasta){
        alertNotificar("La fecha inicio debe ser menor o igual a la final", "error")
        return
    }

    vistacargando("m", "Espere por favor")
    $.get("reporte-menu-ali/"+desde+"/"+hasta, function(data){
        vistacargando("")
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            return;   
        }

        alertNotificar("El documento se descargara en unos segundos...","success")
        window.location.href="descargar-reporte/"+data.pdf
        
    }).fail(function(){
        vistacargando("")
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}