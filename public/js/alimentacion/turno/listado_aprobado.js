

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


    $('#fecha_turno').html('')
    $('#comida_turno').html('')
    $('#total_turno').html('')
    $('#usuario_apr').html('')
    $('#fecha_apr').html('')
 
    $.get('turno-fecha-aprob/'+fecha_selecc+'/'+idalimento, function(data){
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
            $('#usuario_apr').html(data.resultado[0].nombre_user_apr + " " +data.resultado[0].apellido_user_apr)
            $('#fecha_apr').html(data.resultado[0].fecha_aprobacion)
            
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


function descargarAprobacion(){
    
    
    var comida_sel=$('#idalimento').val()
    var fecha_sele=$('#txt_fecha').val()

    

    vistacargando("m","Espere por favor");           

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        type: "POST",
        url: 'descargar-aprobacion',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        fecha_sele:fecha_sele, comida_sel:comida_sel},
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
    $('html,body').animate({scrollTop:$('#content_consulta').offset().top},400);
}