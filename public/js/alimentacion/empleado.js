

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
        url_form="guardar-empleado"
    }else{
        tipo="PUT"
        url_form="actualizar-empleado/"+idEmpleadoEditar
    }
  
    var FrmData=$("#form_registro_empleado").serialize();

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
            $('#listado_empleado').show(200)
            llenar_tabla_empleado()
                            
        }, error:function (data) {
            console.log(data)

            vistacargando("");
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
   
    
    $.get("listado-empleado/", function(data){
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
            globalThis.SuperAdmin=data.superAdmin
            $('#tabla_empleado').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                order: [[ 4, "asc" ]],
                sInfoFiltered:false,
                language: {
                    url: 'json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "10%", "targets": 0 },
                    { "width": "20%", "targets": 1 },
                    { "width": "10%", "targets": 2 },
                    { "width": "20%", "targets": 3 },
                    { "width": "10%", "targets": 4 },
                    { "width": "10%", "targets": 5 },
                    { "width": "10%", "targets": 6 },
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "cedula"},
                        {data: "nombres" },
                        {data: "puesto"},
                        {data: "area"},
                        {data: "generad"},
                        {data: "notifi" },
                        {data: "cedula" },
                ],    
                "rowCallback": function( row, data ) {
                    let disabled=""
                    console.log(data.pin)
                    if(data.pin){
                       
                        disabled=""
                    }else{
                        
                        disabled="disabled"
                    }

                    // if(data.notificado){
                    //     $('td', row).eq(5).html("Si")
                       
                    // }else{
                    //     $('td', row).eq(5).html("No")
                        
                    // }

                    $('td', row).eq(6).html(`
                                           
                                            <button ${disabled} onclick="enlace('${data.pin}','${data.telefono}','${data.nombres}','${data.id_empleado}')" type="button" class="btn btn-success btn-xs">Notificar</button>
                                           

                                            <button type="button" class="btn btn-primary btn-xs" onclick="editarEmpleado(${data.id_empleado })">Editar</button>

                                            <button type="button" class="btn btn-danger btn-xs" onclick="eliminar(${data.id_empleado })">Eliminar</button>

                                                                                
                                        
                                       
                                    
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

function enlace(pin, tele, persona, id_empleado){
    if(SuperAdmin!="SuperAdmin"){
        alertNotificar("Accion permitida solo para superadmin","error")
    }
    vistacargando("m","Espere por favor")
    $.get("notifica-whatsapp/"+id_empleado, function(data){
        vistacargando("")
        llenar_tabla_empleado()
        window.open("https://api.whatsapp.com/send?phone="+tele+"&text=Estimado(a) compañero(a) "+persona+" , su pin de acceso para el sistema de alimentación es "+pin, "_blank")

    }).fail(function(){
        vistacargando("")
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });

   
}

function editarEmpleado(id_empleado){
    vistacargando("m","Espere por favor")
    $.get("editar-empleado/"+id_empleado, function(data){
        vistacargando("")
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
        vistacargando("")
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

function eliminar(id_empleado){
    if(confirm('¿Quiere eliminar el registro?')){
        vistacargando("m","Espere por favor")
        $.get("eliminar-empleado/"+id_empleado, function(data){
            vistacargando("")
            if(data.error==true){
                alertNotificar(data.mensaje,"error");
                return;   
            }
    
            alertNotificar(data.mensaje,"success");
            llenar_tabla_empleado()
           
        }).fail(function(){
            vistacargando("")
            alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
        });
    }
   
}