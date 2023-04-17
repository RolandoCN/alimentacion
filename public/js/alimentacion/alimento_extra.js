
//Busqueda de persona por cedula o nombre
$('#id_empleado').select2({
    placeholder: 'Seleccione una opción',
    ajax: {
    url: 'buscar-persona',
    dataType: 'json',
    delay: 250,
    processResults: function (data) {
        return {
        results:  $.map(data, function (item) {
                return {
                    text: item.cedula+" - "+item.nombres,
                    id: item.id_empleado
                }
            })
        };
    },
    cache: true
    }
});

$("#form_extra").submit(function(e){
    e.preventDefault();
    
    //validamos los campos obligatorios
    let id_empleado=$('#id_empleado').val()
    let idalimento=$('#id_alimento').val()
    let motivo=$('#motivo').val()
    let fecha=$('#fecha').val()
        
    if(id_empleado=="" || id_empleado==null){
        alertNotificar("Debe seleccionar al empleado","error")
      
        return
    } 

    if(idalimento=="" || idalimento==null){
        alertNotificar("Debe seleccionar el alimento","error")
      
        return
    } 

    if(motivo=="" || motivo==null){
        alertNotificar("Ingrese el motivo","error")
        $('#motivo').focus()
        return
    } 

    if(fecha=="" || fecha==null){
        alertNotificar("Seleccione una fecha","error")
       
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
        url_form="guardar-extra"
    }else{
        tipo="PUT"
        url_form="actualizar-extra/"+idExtraEditar
    }
  
    var FrmData=$("#form_extra").serialize();
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
            $('#listado_extra').show(200)
            llenar_tabla_extra()
                            
        }, error:function (data) {

            vistacargando("");
            alertNotificar('Ocurrió un error','error');
        }
    });
})

function limpiarCampos(){
    $('#id_empleado').val('').trigger('change.select2')
    $('#id_alimento').val('').trigger('change.select2')
    $('#motivo').val('')
    $('#fecha').val('')

    
}

function llenar_tabla_extra(){
    var num_col = $("#tabla_extra thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_extra tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
   
    
    $.get("listado-extra/", function(data){
       
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_extra tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_extra tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }
         
            $('#tabla_extra').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                order: [[ 2, "desc" ]],
                sInfoFiltered:false,
                language: {
                    url: 'json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "30%", "targets": 0 },
                    { "width": "25%", "targets": 1 },
                    { "width": "15%", "targets": 2 },
                    { "width": "24%", "targets": 3 },
                    { "width": "5%", "targets": 4 },
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "cedula"},
                        {data: "nombres" },
                        {data: "alimento"},
                        {data: "motivo"},
                        {data: "idalimentos_extra"},
                ],    
                "rowCallback": function( row, data ) {
                    $('td', row).eq(2).html(`   
                                                <li><b>Fecha: </b>${data.fecha}
                                                <li><b>Alimento: </b>${data.alimento}</li>
                                            
                                            `)
                    $('td', row).eq(0).html(`   
                                            <li><b>Cedula: </b>${data.cedula}
                                            <li><b>Nombres: </b>${data.nombres}</li>
                                        
                                        `)

                    $('td', row).eq(1).html(`   
                                        <li><b>Usuario: </b>${data.nombre_u} ${data.apellido_u}
                                        <li><b>Fecha: </b>${data.fecha_reg}</li>
                                    
                                    `)
                    $('td', row).eq(4).html(`
                                  
                                            
                                            <a onclick="elimina_extra(${data.idalimentos_extra})" class="btn btn-danger btn-xs"> Eliminar </a>
                                       
                                    
                    `); 
                }             
            });
        }
    }).fail(function(){
        $("#tabla_extra tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });


}

$('.collapse-link').click();
$('.datatable_wrapper').children('.row').css('overflow','inherit !important');

$('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0', 'overflow-x':'inherit'});


function visualizarForm(tipo){
    $('#form_ing').show(200)
    $('#listado_extra').hide(200)
    globalThis.AccionForm="";
    if(tipo=='N'){
        $('#titulo_form').html("Registro Alimento Extra")
        $('#nombre_btn_form').html('Registrar')
        AccionForm="R"
    }else{
        $('#titulo_form').html("Actualización Alimento Extra")
        $('#nombre_btn_form').html('Actualizar')
        AccionForm="E"
    }
}

function visualizarListado(){
    $('#form_ing').hide(200)
    $('#listado_extra').show(200)
    limpiarCampos()
}

function elimina_extra(idextra){
    if(confirm('¿Quiere eliminar el registro?')){
        vistacargando("m","Espere por favor")
        $.get("eliminar-extra/"+idextra, function(data){
            vistacargando("")
            if(data.error==true){
                alertNotificar(data.mensaje,"error");
                return;   
            }
    
            alertNotificar(data.mensaje,"success");
            llenar_tabla_extra()
           
        }).fail(function(){
            vistacargando("")
            alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
        });
    }
   
}