

function buscarTurnos(){
    let fecha_inicial=$('#fecha_ini').val()
    let fecha_final=$('#fecha_fin').val()
    let cmb_retirados=$('#cmb_retirados').val()
   
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
    if(cmb_retirados ==""){
        alertNotificar("Seleccione una opcion","error")
        
        return
    }
   
    
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


    $('#fecha_ini_rep').html('')
    $('#fecha_fin_rep').html('')
    $("#retirados").html("")
    $("#pendientes").html("")

    var pen=0;
    var pend_=0;
    
    $.get('alimento-aprobado-periodo/'+fecha_inicial+'/'+fecha_final+'/'+cmb_retirados, function(data){
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
            $('#fecha_ini_rep').html(fecha_inicial)
            $('#fecha_fin_rep').html(fecha_final) 
            
            if(cmb_retirados==1){
                $('.pdf_retirado').removeClass("ocultar");
                $('.pdf_retirado').addClass("ver");

                $('.pdf_no_retirado').removeClass("ocultar");
                $('.pdf_no_retirado').addClass("ver");

                $('.pdf_ip').removeClass("ocultar");
                $('.pdf_ip').addClass("ver");

            }else if(cmb_retirados=="Si"){
                $('.pdf_retirado').removeClass("ocultar");
                $('.pdf_retirado').addClass("ver");

                $('.pdf_no_retirado').removeClass("ver");
                $('.pdf_no_retirado').addClass("ocultar");

                $('.pdf_ip').removeClass("ver");
                $('.pdf_ip').addClass("ocultar");

            }else{
               
                $('.pdf_retirado').removeClass("ver");
                $('.pdf_retirado').addClass("ocultar");

                $('.pdf_no_retirado').removeClass("ocultar");
                $('.pdf_no_retirado').addClass("ver");

                $('.pdf_ip').removeClass("ver");
                $('.pdf_ip').addClass("ocultar");

               
            }
            
            let contador=0
            let servidos=0
			$.each(data.resultado,function(i, item){

                let hora_confirma_empl=item.fecha_hora_confirma_emp.split(" ");
                hora_confirma_empl=hora_confirma_empl[1];
                let estado=""
                let estado_serv=""
               
                if(item.estado_turno=="Generado"){
                    estado="Pendiente"
                   
                }else{
                    estado="Aprobado"
                    contador=contador+1
                }

                if(item.estado_retira_comida=="Si"){
                    estado_serv="Retirado"
                    servidos=servidos+1
                   
                }else{
                    estado_serv="Pendiente"
                    
                }

				$('#table_persona').append(`<tr>
                                                <td style="width:10%; vertical-align:middle">
                                                    ${item.cedula}
                                                    <input type="hidden" name="idturno_comida[]"  value="${item.idturno}">
                                                </td>
                                                <td style="width:25%; text-align:left; vertical-align:middle">${item.nombres}</td>
                                                <td style="width:15%; vertical-align:middle">                                            
                                                    ${item.area}
                                                </td>
                                                <td style="width:7%; text-align:center; vertical-align:middle">${item.start}</td>
                                                <td style="width:8%; text-align:center; vertical-align:middle">${item.comida}</td>
                                                <td style="width:10%; vertical-align:middle">${item.hora_ini} - ${item.hora_fin}</td>
                                                <td style="width:15%;text-align:left">                                                   
                                                    <li>IP: ${item.ip_confirma}</li>
                                                    <li>Hora: ${hora_confirma_empl}</li>
                                                </td>
                                                <td style="width:10%; vertical-align:middle">                                                    
                                                    ${estado_serv}
                                                </td>
                                               
											
										</tr>`);
			})
           
            pen=data.resultado.length;
            pend_=pen -servidos;
            
            $("#retirados").html(servidos)
            $("#pendientes").html(pend_)
		  
			cargar_estilos_datatable('table_persona');
		}
    })  

}


function descargarAprobacion(estado){

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
        url: 'reporte-periodo-aprobado',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        fecha_inicial_rep:fecha_inicial_rep, fecha_final_rep:fecha_final_rep, estado:estado },
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

function descargarAreaNoConf(estado){

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
        url: 'reporte-conf-no-retirado-area',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        fecha_inicial_rep:fecha_inicial_rep, fecha_final_rep:fecha_final_rep, estado:estado },
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

function descargarAreaConf(estado){

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
        url: 'reporte-conf-retirado-area',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        fecha_inicial_rep:fecha_inicial_rep, fecha_final_rep:fecha_final_rep, estado:estado },
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

function descargarAmbos(){
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
        url: 'reporte-periodo-aprobado-todos',
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

function descargarIpConf(){
    
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
        url: 'reporte-conf-ip',
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
        // order: [[ 1, "desc" ]],
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


