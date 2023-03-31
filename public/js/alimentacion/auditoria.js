

function buscarTurnos(){
    let fecha_inicial=$('#fecha_ini').val()
    let fecha_final=$('#fecha_fin').val()
    
   
    if(fecha_inicial==""){ 
        alertNotificar("Seleccione una fecha inicial","error")
        return 
    }

    if(fecha_final==""){ 
        alertNotificar("Seleccione una fecha final","error")
        $('#fecha_ini').focus()
        return 
    }

    if(fecha_inicial > fecha_final){
        alertNotificar("La fecha de inicio debe ser menor a la fecha final","error")
        $('#fecha_ini').focus()
        return
    }
   
    
    $('#content_consulta').hide()
    $('#listado_turno').show()
    
    $('#cabecera_txt').hide()
    $('#cabecera_btn').show()

    $('#pac_body').html('');

	$('#tabla_auditoria').DataTable().destroy();
	$('#tabla_auditoria tbody').empty(); 
    
    // limpiarCampos()
    var num_col = $("#tabla_auditoria thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_auditoria tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);


    $('#fecha_ini_rep').html('')
    $('#fecha_fin_rep').html('')
    
    $.get('auditoria-turnos/'+fecha_inicial+'/'+fecha_final, function(data){

        if(data.error==true){
			$("#tabla_auditoria tbody").html('');
			$("#tabla_auditoria tbody").html(`<tr><td colspan="${num_col}">No existen registros</td></tr>`);
			alertNotificar(data.mensaje,"error");
			return;   
		}
		if(data.error==false){
			if(data.resultado.length==0){
				$("#tabla_auditoria tbody").html('');
				$("#tabla_auditoria tbody").html(`<tr><td colspan="${num_col}">No existen registros</td></tr>`);
				alertNotificar("No se encontró información","error");
                cancelar()
				return;
			}
			
			$("#tabla_auditoria tbody").html('');
            $('#fecha_ini_rep').html(fecha_inicial)
            $('#fecha_fin_rep').html(fecha_final)
          
            
            let contador=0
			$.each(data.resultado,function(i, item){
                let estado=""
               
                if(item.estado_turno=="P"){
                    estado="Pendiente"
                   
                }else if(item.estado_turno=="A"){
                    estado="Aprobado"
                   
                }else{
                    estado="Eliminado"
                }
                let usuario_act=""
                if(item.nombre_user_actualiza==null){
                    usuario_act=""
                }else{
                    usuario_act=item.nombre_user_actualiza +" "+item.apellidos_user_actualiza
                    
                }

                let fecha_actual=""
                if(item.fecha_act==null){
                    fecha_actual=""
                }else{
                    fecha_actual=item.fecha_act 
                    
                }

				$('#tabla_auditoria').append(`<tr>
                                                <td style="width:8%">
                                                    ${item.fecha_comida}
                                                    
                                                </td>

                                                <td style="width:19%;  text-align:left">
                                                    ${item.nombre_empleado}
                                                </td>
                                                <td style="width:15%; text-align:center">
                                                    ${item.desc_horario} <br>  ${item.hora_ini}- ${item.hora_fin}
                                                </td>
                                                <td style="width:8%; text-align:center">
                                                    ${estado}
                                                </td>
                                                <td style="width:25%; text-align:left">
                                                    <li> <b>Usuario:</b>${item.nombre_user_ingresa} ${item.apellidos_user_ingresa}
                                                    <li> <b>Fecha:</b>  ${item.fecha_reg}
                                                </td>
                                               
                                                <td style="width:25%; text-align:left">
                                                    <li> <b>Usuario:</b>${usuario_act}
                                                    <li> <b>Fecha:</b> ${fecha_actual}
                                                </td>

                                              
											
										</tr>`);
			})
            if(contador>0){
                $('.btn_aprobacion').hide()
            }else{
                $('.btn_aprobacion').show()
            }
		  
			cargar_estilos_datatable('tabla_auditoria');
		}
    })  

}


function descargarAprobacion(){

    let fecha_inicial_rep=$('#fecha_ini').val()
    let fecha_final_rep=$('#fecha_fin').val()

    vistacargando("m","Espere por favor");           

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        type: "POST",
        url: 'reporte-detallado',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        fecha_inicial_rep:fecha_inicial_rep, fecha_final_rep:fecha_final_rep },
        success: function(data){
           
            vistacargando("");                
            if(data.error==true){
                alertNotificar(data.mensaje,'error');
                return;                      
            }
            alertNotificar("El documento se descargará en unos segundos...","success");
            window.location.href="descargar-reporte/"+data.pdf
                            
        }, error:function (data) {
            vistacargando("");
            alertNotificar('Ocurrió un error','error');
        }
    });

}
function cargar_estilos_datatable(idtabla){
	$("#"+idtabla).DataTable({
		'paging'      : true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : true,
		"destroy":true,
		pageLength: 10,
		sInfoFiltered:false,
		language: {
			url: 'json/datatables/spanish.json',
		},
	}); 
	$('.collapse-link').click();
	$('.datatable_wrapper').children('.row').css('overflow','inherit !important');

	$('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0', 'overflow-x':'inherit'});	
}

function cancelar(){
   
    $('#content_consulta').show()
    $('#listado_turno').hide()
   
    $('#cabecera_txt').show()
    $('#cabecera_btn').hide()

    $('html,body').animate({scrollTop:$('#arriba').offset().top},400);
}


