

function buscarTurnos(){
    let fecha_selecc=$('#txt_fecha').val()
    let idalimento=$('#idalimento').val()
    if(fecha_selecc==""){ 
        alertNotificar("Seleccione una fecha")
        return 
    }
    if(idalimento==""){ 
        alertNotificar("Seleccione un alimento")
        return 
    }
    $('.btn_aprobacion').hide()
    
    $('#content_consulta').hide()
    $('#listado_turno').show()
    
    $('#cabecera_txt').hide()
    $('#cabecera_btn').show()

    $('#pac_body').html('');

	$('#table_persona').DataTable().destroy();
	$('#table_persona tbody').empty(); 
    
    // limpiarCampos()
    var num_col = $("#table_persona thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#table_persona tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);

    $.get('turno-fecha/'+fecha_selecc+'/'+idalimento, function(data){
        if(data.error==true){
			$("#table_persona tbody").html('');
			$("#table_persona tbody").html(`<tr><td colspan="${num_col}">No existen registros</td></tr>`);
			alertNotificar(data.mensaje,"error");
			return;   
		}
		if(data.error==false){
			if(data.resultado.length==0){
				$("#table_persona tbody").html('');
				$("#table_persona tbody").html(`<tr><td colspan="${num_col}">No existen registros</td></tr>`);
				alertNotificar("No se encontró información","error");
                cancelar()
				return;
			}
			
			$("#table_persona tbody").html('');

            $('#fecha_turno').html(fecha_selecc)
            $('#comida_turno').html($("#idalimento :selected").text())
            $('#total_turno').html(data.resultado.length)
            
            let contador=0
			$.each(data.resultado,function(i, item){

                let estado=""
               
                if(item.estado_turno=="Generado"){
                    estado="Pendiente"
                   
                }else{
                    estado="Aprobado"
                    contador=contador+1
                }
				$('#table_persona').append(`<tr>
                                                <td style="width:10%">
                                                    ${item.cedula}
                                                    <input type="hidden" name="idturno_comida[]"  value="${item.idturno}">
                                                </td>
                                                <td style="width:30%; text-align:left">${item.nombres}</td>
                                                <td style="width:20%; text-align:left">${item.puesto}</td>
                                                <td style="width:20%; text-align:left">${item.area}</td>
                                                <td style="width:10%">${item.hora_ini} - ${item.hora_fin}</td>
                                                <td style="width:10%">
                                                    
                                                    ${estado}
                                                </td>
											
										</tr>`);
			})
            if(contador>0){
                $('.btn_aprobacion').hide()
            }else{
                $('.btn_aprobacion').show()
            }
		  
			cargar_estilos_datatable('table_persona');
		}
    })  

}


function aprobarTurno(){
    
    var array_turnos=[];
    $("input[name='idturno_comida[]']").each(function(indice, elemento) {
        array_turnos.push($(elemento).val());
    });
    console.log(array_turnos)

    var comida_sel=$('#idalimento').val()

    vistacargando("m","Espere por favor");           

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        type: "POST",
        url: 'aprobar-turno',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        array_turnos:array_turnos, comida_sel:comida_sel},
        success: function(data){
           
            vistacargando("");                
            if(data.error==true){
                alertNotificar(data.mensaje,'error');
                return;                      
            }
            alertNotificar(data.mensaje,"success");
            cancelar()
                            
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
    $('html,body').animate({scrollTop:$('#content_consulta').offset().top},400);
}