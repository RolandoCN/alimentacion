

function buscarTurnos(){
    let fecha_inicial=$('#fecha_ini').val()
    let fecha_final=$('#fecha_fin').val()
    let id_empleado=$('#id_empleado').val()
   
    if(fecha_inicial==""){ 
        alertNotificar("Seleccione una fecha inicial","error")
        return 
    }

    if(fecha_final==""){ 
        alertNotificar("Seleccione una fecha final","error")
        $('#fecha_ini').focus()
        return 
    }

    if(id_empleado=="" || id_empleado==null){ 
        alertNotificar("Seleccione un empleado","error")
        $('#fecha_fin').focus()
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

	$('#table_persona').DataTable().destroy();
	$('#table_persona tbody').empty(); 
    
    // limpiarCampos()
    var num_col = $("#table_persona thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#table_persona tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);


    $('#fecha_ini_rep').html('')
    $('#usuario_rep').html('')
    $('#fecha_fin_rep').html('')
    
    $.get('/alimento-servido-indiv/'+fecha_inicial+'/'+fecha_final+"/"+id_empleado, function(data){
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
            $('#usuario_rep').html($("#id_empleado :selected").text())
            $('#fecha_fin_rep').html(fecha_final)
          
            
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
                                                <td style="width:20%; text-align:center">${item.start}</td>
                                                <td style="width:20%; text-align:center">${item.comida}</td>
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

    let fecha_inicial_rep=$('#fecha_ini').val()
    let fecha_final_rep=$('#fecha_fin').val()
    let id_empleado_rep=$('#id_empleado').val()


    vistacargando("m","Espere por favor");           

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        type: "POST",
        url: '/reporte-individual',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        fecha_inicial_rep:fecha_inicial_rep, fecha_final_rep:fecha_final_rep, id_empleado_rep,id_empleado_rep },
        success: function(data){
           
            vistacargando("");                
            if(data.error==true){
                alertNotificar(data.mensaje,'error');
                return;                      
            }
            alertNotificar("El documento se descargará en unos segundos...","success");
            window.location.href="/descargar-reporte/"+data.pdf
                            
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
			url: '/json/datatables/spanish.json',
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

//Busqueda de persona por cedula o nombre
$('#id_empleado').select2({
    placeholder: 'Seleccione una opción',
    ajax: {
    url: '/buscar-persona',
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

