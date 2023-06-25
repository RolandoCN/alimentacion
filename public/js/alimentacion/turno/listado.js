

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
            let contador_conf=0
            let contador_pend=0
            globalThis.IdturnosArray=[]
            console.log(data)
			$.each(data.resultado,function(i, item){

                             
                let estado=""
                let color_fila=""
                let elimina_accion=""
                let accionX=""
                if(item.estado_turno=="Generado"){
                    estado="Pendiente" 
                    contador_pend=contador_pend+1
                    color_fila="color_pendiente"
                    elimina_accion=`eliminaConfirmado('G')`
                    accionX="label-disabled"
                }else if(item.estado_turno=="Confirmado"){
                    estado="Confirmado"
                    color_fila="color_confirmado"
                    contador_conf=contador_conf+1
                    IdturnosArray.push(item.idturno)
                    elimina_accion=`eliminaConfirmado(${item.id_turno_comida})`
                    accionX="label-danger"
                }else{
                    estado="Aprobado"
                    contador=contador+1
                    color_fila="color_aprobacion"
                    elimina_accion=`eliminaConfirmado('A')`
                    accionX="label-disabled"
                }
				$('#table_persona').append(`<tr class="${color_fila}">
                                                <td style="width:10%">
                                                    ${item.cedula}
                                                   
                                                </td>
                                                <td style="width:25%; text-align:left">${item.nombres}</td>
                                                <td style="width:20%; text-align:left">${item.puesto}</td>
                                                <td style="width:20%; text-align:left">${item.area}</td>
                                                <td style="width:10%">${item.hora_ini} - ${item.hora_fin}</td>
                                                <td style="width:10%">
                                                    
                                                    ${estado}
                                                </td>

                                                <td style="width:5%">
                                                    
                                                    <span onclick="${elimina_accion}" style="min-width: 90p !important;font-size: 12px" class="label ${accionX} estado_validado"><i class="fa fa-times"></i> </span>
                                                </td>
											
										</tr>`);
			})

            if(contador>0){
                $('.btn_aprobacion').hide()
                $('.confir_secc').hide()
            }else{
                $('.btn_aprobacion').show()
                $('.confir_secc').show()
            }
		  
			cargar_estilos_datatable('table_persona');

            $('#total_confirmados').html(contador_conf)
            $('#total_pendientes').html(contador_pend)
            $('#total_apro').html(contador)
		}
    })  

}
function eliminaConfirmado(valor){
    $('#id_turno_ali_sel').val('')
    $('#motivo_elim').val('')

    if(valor=="G"){
        alertNotificar("No es posible eliminar datos que no hayan sido confirmados", "error")
        return
    }else if(valor=="A"){
        alertNotificar("No es posible eliminar datos que hayan sido aprobados", "error")
        return
    }else{
        $('#id_turno_ali_sel').val(valor)
        $('#modal_Detalle_Eli').modal('show')
    }
}

function cancelaEliminacion(){
    $('#modal_Detalle_Eli').modal('hide')
}
function aprobarTurno(){
    if(IdturnosArray.length==0){
        alertNotificar("No existen turnos confirmados para aprobar", "error")
        return
    }
    let sms="¿Desea aprobar los "+IdturnosArray.length+" turnos confirmados?"
    swal({
        title: sms,
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, continuar",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) { 
            realizar_aprobacion();
        }
        sweetAlert.close();   // ocultamos la ventana de pregunta
    });      
}   

function eliminacion_comida(){
    var motivo_elim=$('#motivo_elim').val()
    
    if(motivo_elim==""){
        alertNotificar("Debe ingresar el motivo", "error")
        return
    }
    swal({
        title: "¿Desea eliminar la informacion?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, continuar",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) { 
            procede_eliminacion();
        }
        sweetAlert.close();   // ocultamos la ventana de pregunta
    });      
} 
function procede_eliminacion(){

    var motivo_elim=$('#motivo_elim').val()
    var idturno_comida=$('#id_turno_ali_sel').val()
    if(motivo_elim==""){
        alertNotificar("Debe ingresar el motivo", "error")
        return
    }
    vistacargando("m","Espere por favor");           

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
     
    $.ajax({
        type: "POST",
        url: 'elimina-turno-comida',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        motivo_elim:motivo_elim, idturno_comida:idturno_comida},
        success: function(data){
            $('#modal_Detalle_Eli').modal('hide')
            vistacargando("");                
            if(data.error==true){
                alertNotificar(data.mensaje,'error');
                buscarTurnos()
                
                return;                      
            }
            alertNotificar(data.mensaje,"success");
            buscarTurnos()
                            
        }, error:function (data) {
            vistacargando("");
            // buscarTurnos()
            alertNotificar('Ocurrió un error','error');
        }
    });

}


function realizar_aprobacion(){

    array_turnos=IdturnosArray
   
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
        url: 'aprobar-turno',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        array_turnos:array_turnos, comida_sel:comida_sel, fecha_sele:fecha_sele},
        success: function(data){
           
            vistacargando("");                
            if(data.error==true){
                if(data.diferencia=="S"){
                    alertNotificar(data.mensaje,'error');
                    buscarTurnos()
                    return;    
                }
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