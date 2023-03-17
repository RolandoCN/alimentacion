

$("#form_registro_empleado").submit(function(e){
    e.preventDefault();
    
    //validamos los campos obligatorios
    let cedula=$('#cedula').val()
    let nombres=$('#nombres').val()
    let idpuesto=$('#idpuesto').val()
    let idarea=$('#idarea').val()
        
    if(cedula=="" || cedula==null){
        alertNotificar("Debe ingresar la cédula","error")
        $('#cedula').focus()
        return
    } 

    if(nombres=="" || nombres==null){
        alertNotificar("Ingrese los nombres","error")
        $('#nombres').focus()
        return
    } 

    if(idpuesto=="" || idpuesto==null){
        alertNotificar("Seleccione el puesto","error")
        $('#idpuesto').focus()
        return
    } 

    if(idarea=="" || idarea==null){
        alertNotificar("Seleccione el area","error")
        $('#idarea').focus()
        return
    } 


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
        url_form="/guardar-empleado"
    }else{
        tipo="PUT"
        url_form="/actualizar-empleado/"+idEmpleadoEditar
    }
  
    var FrmData=$("#form_registro_empleado").serialize();
    console.log(FrmData)
    $.ajax({
            
        type: tipo,
        url: url_form,
        method: tipo,             
		data: FrmData,      
		
        processData:false, 

        success: function(data){
            console.log(data)
            // vistacargando("");                
            if(data.error==true){
                alertNotificar(data.mensaje,'error');
                return;                      
            }
            limpiarCampos()
            alertNotificar(data.mensaje,"success");
            $('#form_ing').hide(200)
            $('#listado_empleado').show(200)
            llenar_tabla_empleado()
                            
        }, error:function (data) {
            console.log(data)

            // vistacargando("");
            alertNotificar('Ocurrió un error','error');
        }
    });
})

function limpiarCampos(){
    $('#cedula').val('')
    $('#nombres').val('')
    $('#idpuesto').val('').trigger('change.select2')
    $('#idarea').val('').trigger('change.select2')
}

function llenar_tabla_empleado(){
    var num_col = $("#tabla_empleado thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_empleado tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
   
    
    $.get("/listado-empleado/", function(data){
        console.log(data)
      
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_empleado tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_empleado tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }
         
            $('#tabla_empleado').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                order: [[ 1, "desc" ]],
                sInfoFiltered:false,
                language: {
                    url: '/json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "10%", "targets": 0 },
                    { "width": "30%", "targets": 1 },
                    { "width": "10%", "targets": 2 },
                    { "width": "25%", "targets": 3 },
                    { "width": "15%", "targets": 4 },
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "cedula"},
                        {data: "nombres" },
                        {data: "puesto"},
                        {data: "area"},
                        {data: "cedula" },
                ],    
                "rowCallback": function( row, data ) {
                    console.log(data.puesto)
                  
                    $('td', row).eq(4).html(`
                                  
                                            <button type="button" class="btn btn-primary btn-xs" onclick="editarEmpleado(${data.id_empleado })">Editar</button>
                                                                                
                                        
                                       
                                    
                    `); 
                }             
            });
        }
    }).fail(function(){
        $("#tabla_empleado tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });


}

$('.collapse-link').click();
$('.datatable_wrapper').children('.row').css('overflow','inherit !important');

$('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0', 'overflow-x':'inherit'});



function editarEmpleado(id_empleado){
    $.get("/editar-empleado/"+id_empleado, function(data){
        console.log(data)
      
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            return;   
        }
        if(data.resultado==null){
            alertNotificar("El empleado ya no se puede editar","error");
            return;   
        }


        $('#cedula').val(data.resultado.cedula)
        $('#nombres').val(data.resultado.nombres)
        $('#idpuesto').val(data.resultado.id_puesto).trigger('change.select2')
        $('#idarea').val(data.resultado.id_area).trigger('change.select2')
       

        visualizarForm('E')
        globalThis.idEmpleadoEditar=id_empleado



       
    }).fail(function(){
       
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}

function visualizarForm(tipo){
    $('#form_ing').show(200)
    $('#listado_empleado').hide(200)
    globalThis.AccionForm="";
    if(tipo=='N'){
        $('#titulo_form').html("Registro Empleado")
        $('#nombre_btn_form').html('Registrar')
        AccionForm="R"
    }else{
        $('#titulo_form').html("Actualización Empleado")
        $('#nombre_btn_form').html('Actualizar')
        AccionForm="E"
    }
}

function visualizarListado(){
    $('#form_ing').hide(200)
    $('#listado_empleado').show(200)
    limpiarCampos()
}

function btn_eliminar_tarea(id_empleado){
    if(confirm('¿Quiere eliminar el registro?')){
        $.get("/eliminar-empleado/"+id_empleado, function(data){
            console.log(data)
          
            if(data.error==true){
                alertNotificar(data.mensaje,"error");
                return;   
            }
    
            alertNotificar(data.mensaje,"success");
            llenar_tabla_empleado()
           
        }).fail(function(){
           
            alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
        });
    }
   
}