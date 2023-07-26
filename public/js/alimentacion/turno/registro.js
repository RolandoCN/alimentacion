
function detalleTurno(id_empleado){
    limpiarDatosEmpleadoSelecc()
    vistacargando("m", "Espere por favor")
   
    let eventosArray=[]
    $.get('fullcalender_/'+id_empleado, function(data){
     
        vistacargando("")
        let color_est=""
        $.each(data.data, function (i, item){
            if(item.estado=="A"){
                color_est="#28a745"
            }else{
                color_est=""
            }
       
            eventosArray.push({
                id:item.id,
                title:item.title,
                start: item.start,
                end: item.end,
                color: color_est,
               
            });
        })

        console.log(eventosArray)
        // eventosArray=data.data

        verCale()

        $('#calendar').fullCalendar('removeEvents'); // limpiamos todos los eventos asignados
        $('#calendar').fullCalendar('addEventSource', eventosArray); // agregamos los nuevos eventos
        $('#calendar').fullCalendar('refetchEvents'); // refrescamos el calendario

        if(data.empleado!=null){
            $('#identificacion').html(data.empleado.cedula)
            $('#empleado').html(data.empleado.nombres)
            $('#puesto').html(data.empleado.puesto)
            $('#area').html(data.empleado.area)

            $('#empleado_seleccionado').val(data.empleado.nombres)
            $('#puesto_seleccionado').val(data.empleado.puesto)

        }
        globalThis.PersonaSeleccionada=id_empleado

       
    }).fail(function(){
        vistacargando("")
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
   
    
}

function limpiarDatosEmpleadoSelecc(){
    $('#identificacion').html('')
    $('#empleado').html('')
    $('#puesto').html('')
    $('#area').html('')

    $('#empleado_seleccionado').val('')
    $('#puesto_seleccionado').val('')
    $('#fecha_ini').val('')
    $('#idturno').val('').trigger('change.select2')

}


function verCale(){

    $('#cabecera_txt').hide()
    $('#cabecera_btn').show()

    $('#cale').show()
    $('#content_consulta').hide()

    $('#calendar').fullCalendar({
        header: {
            left: 'today, prev,next',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        // defaultDate: '20-07-09',
        buttonIcons: true,
        weekNumbers: false,
        editable: true,
        eventLimit: true,
        locale: 'es',
        // events:"/fullcalender/"+idpersona,
        // events:dataArray,
        height: 650,
        width:95,
        
       
        dayClick: function (date, jsEvent, view) {
          
            $('#idturno').val('').trigger('change.select2')
            $('#modal_Tipo').modal('show')
            $('#fecha_ini').val(date.format())
        }, 
        eventClick: function (calEvent, jsEvent, view) {

            swal({
                title: "¿Quieres eliminar el registro?",
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
                    vistacargando("m", "Espere por favor")
                    $.ajaxSetup({
                        headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        url: 'eliminar-turno-comida',
                        data: {
                            id: calEvent.id,
                            type: 'delete'
                        },
                        success: function (response) {
                            vistacargando("")

                            if(response.error==true){
                                
                                alertNotificar(response.mensaje, "error");
                            }else{
                                $('#calendar').fullCalendar('removeEvents', calEvent.id);
                                alertNotificar(response.mensaje, "success");
                            }


                        }, error: function(e){
                            vistacargando("")
                            $('#calendar').fullCalendar('refetchEvents');
                            alertNotificar(" Inconvenientes al procesar la solicitud intente nuevamente","error")
                        
                        }
                    });
                }
                sweetAlert.close();   // ocultamos la ventana de pregunta
            });     
 
            // swal({
            //     title: "¿Quieres eliminar el registro?",
            //     text: "Motivo:",
            //     type: "input",
            //     showCancelButton: true,
            //     confirmButtonClass: "btn-danger",
            //     cancellButtonClass: "btn-primary",
            //     confirmButtonText: "Si",
            //     cancelButtonText: "No",
            //     closeOnConfirm: false,
            //     animation: "slide-from-top",
            //     inputPlaceholder: "Ingrese el motivo"
            // },
            // function(inputValue){
            //     if (inputValue === null) return false;
                
            //     if (inputValue === "") {
            //         swal.showInputError("Necesitas ingresar el motivo!");
            //         return false
            //     }
            //     if(inputValue!=""){
            //         sweetAlert.close();
                   
            //         vistacargando("m", "Espere por favor")
            //         $.ajaxSetup({
            //             headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             }
            //         });
            //         $.ajax({
            //             type: "POST",
            //             url: 'eliminar-turno-comida',
            //             data: {
            //                 id: calEvent.id,
            //                 motivo: inputValue,
            //                 type: 'delete'
            //             },
            //             success: function (response) {
            //                 vistacargando("")

            //                 if(response.error==true){
                                
            //                     alertNotificar(response.mensaje, "error");
            //                 }else{
            //                     $('#calendar').fullCalendar('removeEvents', calEvent.id);
            //                     alertNotificar(response.mensaje, "success");
            //                 }


            //             }, error: function(e){
            //                 vistacargando("")
            //                 $('#calendar').fullCalendar('refetchEvents');
            //                 alertNotificar(" Inconvenientes al procesar la solicitud intente nuevamente","error")
                        
            //             }
            //         });
                   
            //     }
                
            // });
        },  
        eventDrop: function (event, delta) {
            
          
            var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
            vistacargando("m", "Espere por favor")

                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url:  'actualizar-turno-comida',
                    data: {
                        title: event.title,
                        start: start,
                        // end: end,
                        id: event.id,
                        type: 'update'
                    },
                    type: "POST",
                    success: function (response) {
                        vistacargando("")

                        if(response.error==true){
                            if(response.dataArray.length==0){
                                alertNotificar(response.mensaje, "error");
                                cancelar()
                                return
                            }
                            let eventoArray=[]
                            let color_est=""
                            $.each(response.dataArray.data, function (i, item){
                                if(item.estado=="A"){
                                    color_est="#28a745"
                                }else{
                                    color_est=""
                                }
                                eventoArray.push({
                                    id:item.id,
                                    title:item.title,
                                    start: item.start,
                                    end: item.end,
                                    color: color_est,
                                   
                                });
                            

                            })
                               
                            $('#calendar').fullCalendar('removeEvents'); 
                            $('#calendar').fullCalendar('addEventSource',eventoArray); 
                            $('#calendar').fullCalendar('refetchEvents'); 
                           
                            alertNotificar(response.mensaje, "error");
                        }else{
                            alertNotificar(response.mensaje, "success");
                        }
                        
                    },
                    error: function(e){
                        vistacargando("")
                        console.log(e)
                        if(response.dataArray.length==0){
                            alertNotificar(response.mensaje, "error");
                            cancelar()
                            return
                        }
                       
                        alertNotificar(" Inconvenientes al procesar la solicitud intente nuevamente","error")
                       
                    }
    
                });
        },
            
    });
  }

function guardarTurno(){

    let AF_ini=$("#fecha_ini").val()
    let AF_fin=$("#fecha_ini").val()
    let idTurno=$('#idturno').val()
    
    //validamos que haya seleccionado el turno
    if(idTurno=="" || idTurno==null){
        alertNotificar("Debe seleccionar un  turno","error")
        return;
    }
    vistacargando("m","Espere por favor")
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: "POST",
            url: 'asignar-turno',
            data:{ _token: $('meta[name="csrf-token"]').attr('content'),
           Turno:idTurno,fecha_inicio:AF_ini, fecha_fin:AF_fin, idpers:PersonaSeleccionada},
    
        success: function (data) {
            vistacargando("")

            if(data['error']==true){
                
                alertNotificar(data.mensaje,"error")
                
                return;
            }
            alertNotificar(data.mensaje,"success")
           
            $('#cerra_modal').click()
                   
            // detalleTurno(PersonaSeleccionada)

            $('#calendar').fullCalendar('renderEvent',data.dato); 

           
        },
        error: function(e){
        vistacargando("")
            alertNotificar(" Inconvenientes al procesar la solicitud intente nuevamente","error")
        }
    });
}

//Busqueda de persona por cedula o nombre
$('#cmb_persona').select2({
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

function buscarPersona(){
    let idPers=$('#cmb_persona').val()
    if(idPers==""){ return }

    $('#pac_body').html('');
	$('#table_persona').DataTable().destroy();
	$('#table_persona tbody').empty(); 
    
    // limpiarCampos()

    $.get('info-persona/'+idPers, function(data){
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
				return;
			}
			
			$("#table_persona tbody").html('');
           
			$.each(data.resultado,function(i, item){
				$('#table_persona').append(`<tr>
											<td>${item.id_empleado}</td>
                                            <td>${item.cedula}</td>
											<td>${item.nombres}</td>
											<td>
												<center>
													<button type="button" class="btn btn-sm btn-info" onclick="detalleTurno('${item.id_empleado}')">Detalle</button>  
												</center>
											</td>
										</tr>`);
			})
				
		  
			cargar_estilos_datatable('table_persona');
		}
    })  

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
    $('#cale').hide()
   
    $('#cabecera_txt').show()
    $('#cabecera_btn').hide()
    $('html,body').animate({scrollTop:$('#arriba').offset().top},400);
}