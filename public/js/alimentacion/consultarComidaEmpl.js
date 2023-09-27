
$(document).keyup(function(event) {
    if (event.which === 13) {
        $("#form_valida").submit()
    }
});

globalThis.AbrirModal="S";
globalThis.fueAprobado='N'
$("#form_valida").submit(function(e){

   
    e.preventDefault();
    let cedula_func=$('#cedula_func').val()
    let telef_pin=$('#telef_pin').val()
   
    if(cedula_func.length!=10){
        alertNotificar("Debe ingresar un número de cédula válido de 10 dígitos","error")
        return
    }

    if(telef_pin==""){
        alertNotificar("Debe ingresar su numero telefono o pin de acceso","error")
        return
    }

    vistacargando("m","Espere por favor")

    $('#body_tabla').html('');
    $('#tabla_menu_comida').DataTable().destroy();
    $('#tabla_menu_comida tbody').empty();  

    var num_col = $("#tabla_menu_comida thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_menu_comida tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
   
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    console.log("token "+$('meta[name="csrf-token"]').attr('content'))
    if(AbrirModal=="S"){
        limpiar()
    }
    var FrmData=$("#form_valida").serialize();
   
    $.ajax({
            
       type: "POST",
       url: "consulta-comida-empleado",
       method: "POST",             
       data: FrmData,      
       
       processData:false, 

       success: function(data){
            console.log(data)
            vistacargando("");                
            if(data.error==true){ 
                // $('#cedula_func').val('')   
                //mostramos una ventana principal el error x 8 segundos
                $('.confirm').prop('disabled',false)
              
                swal(data.mensaje, "¡Ocurrió un error!", "error");
                setTimeout(() => {
                    $('.confirm').prop('disabled',true)
                    $('.confirm').click();
                }, "8000");
              
                alertNotificar(data.mensaje,'error');

                return;                      
            }
           
            if(AbrirModal=="S"){
                $('#modal_aprobacion').modal({backdrop: 'static', keyboard: false})
            }
               
            $('#identificacion_Apr').html(data.data.cedula)
            $('#puesto_Apr').html(data.data.puesto)
            $('#fecha_Act').html(data.detalleMenu.fecha)
            $('#user_Apr').html(data.data.nombres)
            $('#area_Apr').html(data.data.area)
            $('#horario_Apr').html(data.data.hora_ini+" -- "+data.data.hora_fin)

            if(data.detalleMenu.Menu.length <= 0){
                $("#tabla_menu_comida tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }
            let confirmado=0
            $("#tabla_menu_comida tbody").html('');
            $.each(data.detalleMenu.Menu, function(i, item){
                var set=[''];
                var hr='';
    
                $.each(item.detalle,function(i,item2){
                
                    if(i>=0){hr=`<li style="padding-bottom:5px">`;}
    
                    set[i]= ` ${hr} ${item2.descripcion}`;
    
                });
                let checkear=""
                let clase="";
               
                // if(item.estado_comida=="Confirmado"){
                if(item.confirma_empleado=="Si"  && item.estado_comida!="Eliminado"){
                    checkear="checked"
                    clase="color_aprobacion"
                    confirmado=confirmado+1
                }else if(item.estado_comida=="Eliminado"){
                    checkear=""
                    clase="color_elim"
                }else{
                    checkear=""
                    clase=""
                    
                }

               
              
                $('#tabla_menu_comida').append(`<tr role="row" class="odd ${clase}" id="fila_sele_${item.id_turno_comida}">

                    <td  width="5%" colspan="1"  style=" vertical-align: middle; text-align:center"  class="paddingTR">
                        ${i+1}
                    </td>

                    <td  width="20%" colspan="1"  style=" vertical-align: middle; text-align:center"  class="paddingTR">
                        ${item['comida']}
                    </td>

                    
                    <td width="60%">
                        ${set}
                    </td>
                    <td  width="15%"style=" vertical-align: middle; text-align:center"  class="paddingTR">
                        <input type="checkbox"  onclick="accionVerifica('${item.id_turno_comida}','${item.estado_comida}','${item.motivo_eliminacion}')"  name="array_alim[]" id="id_alime_${item.id_turno_comida}" value="${item.id_turno_comida}" ${checkear}>
                    </td> 


                    
                </tr>  `);

                          
                       
            });
           
            if(confirmado>0){
                $('#btn_aprobar').prop('disabled', true)
                fueAprobado="S"
            }else{
                $('#btn_aprobar').prop('disabled', false)
            }

            cargar_estilos_tabla("tabla_menu_comida",10);
           
           //bloqueamos el boton de cerrar x 10 segundos para que le de chance de verficar la aprobacion
            setTimeout(() => {
                $('#btn_cancelar').prop('disabled',false)
                // cerrar()
            }, "5000");

            $('#btn_cancelar').prop('disabled',true)
         
       }, error:function (data) {
            console.log(data)
            alertNotificar('Ocurrió un error','error');
            if(data.status==419){
                window.location.href=""
            }

           vistacargando("");
           
       }
   });
})

function cargar_estilos_tabla(idtabla,pageLength){

    $(`#${idtabla}`).DataTable({
        dom: ""
        +"<'row' <'form-inline' <'col-sm-6 inputsearch'f>>>"
        +"<rt>"
        +"<'row'<'form-inline'"
        +" <'col-sm-6 col-md-6 col-lg-6'l>"
        +"<'col-sm-6 col-md-6 col-lg-6'p>>>",
        "destroy":true,
        // order: [[ 0, "desc" ]],
        "ordering":false,"searching": false, "paging": false,"info": false,
        pageLength: pageLength,
        sInfoFiltered:false,
        "language": lenguajeTabla
    });

    // para posicionar el input del filtro
    $(`#${idtabla}_filter`).css('float', 'left');
    $(`#${idtabla}_filter`).children('label').css('width', '100%');
    $(`#${idtabla}_filter`).parent().css('padding-left','0');
    $(`#${idtabla}_wrapper`).css('margin-top','10px');
    $(`input[aria-controls="${idtabla}"]`).css({'width':'100%'});
    $(`input[aria-controls="${idtabla}"]`).parent().css({'padding-left':'10px'});
    //buscamos las columnas que deceamos que sean las mas angostas
    $(`#${idtabla}`).find('.col_sm').css('width','1px');
    $(`#${idtabla}`).find('.resp').css('width','150px');  
    $(`#${idtabla}`).find('.flex').css('display','flex');   
    $('[data-toggle="tooltip"]').tooltip();
    
}

//ESTILOS DE TABLA

var lenguajeTabla = {
    "lengthMenu": 'Mostrar <select class="form-control input-sm">'+
                        '<option value="5">5</option>'+
                        '<option value="10">10</option>'+
                        '<option value="20">20</option>'+
                        '<option value="30">30</option>'+
                        '<option value="40">40</option>'+
                        '<option value="-1">Todos</option>'+
                '</select> registros',
    "search": "Buscar:",
    "searchPlaceholder": "Ingrese un criterio de busqueda",
    "zeroRecords": "No se encontraron registros coincidentes",
    "infoEmpty": "No hay registros para mostrar",
    "infoFiltered": " - filtrado de MAX registros",
    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
    "paginate": {
        "previous": "Anterior",
        "next": "Siguiente"
    }
};

function aprobarConfirmacion(){
   
    $("#sms_errores").html('')
    $("#sms_errores").hide()
    if(fueAprobado=='S'){
        alertNotificar("La confirmación del/los alimento(s) del día ya fué realizada","error")
        $('#btn_aprobar').prop('disabled',true)
        return
    }
    var alimentos_chequeados=[];
    $("input[name='comida_chequeada[]']").each(function(indice, elemento) {
        console.log(elemento)
        alimentos_chequeados.push($(elemento).val());
    });

    if(alimentos_chequeados.length<=0){
        alertNotificar("Seleccione al menos un alimento","error")
        return
    }
    let txt= "¿Desea confirmar el/los "+alimentos_chequeados.length+ " alimentos seleccionados"
    swal({
        title: txt,
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
            realizar_confirmacion();
        }
        sweetAlert.close();   // ocultamos la ventana de pregunta
    });     
}
function realizar_confirmacion(){
    vistacargando("m","Espere por favor");  
    $("#sms_errores").html('')
    $("#sms_errores").hide()
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    var alimentos_chequeados=[];
    $("input[name='comida_chequeada[]']").each(function(indice, elemento) {
        console.log(elemento)
        alimentos_chequeados.push($(elemento).val());
    });

    if(alimentos_chequeados.length<=0){
        alertNotificar("Seleccione al menos un alimento","error")
        return
    }
    $('#body_tabla').html('');
    $('#tabla_menu_comida').DataTable().destroy();
    $('#tabla_menu_comida tbody').empty();  

    var num_col = $("#tabla_menu_comida thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_menu_comida tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
             
    $('#btn_aprobar').prop('disabled', true)
    $.ajax({
       
        type: "POST",
        url: "confirmados-empleado",
        method: "POST",             
        data: {alimentos_chequeados:alimentos_chequeados},     
        success: function(data){
            console.log(data)

            vistacargando("");   
                
            if(data.error==true){
                let sms_error=""
                if(data.inconsistencia=="S"){
                    var set=[''];
                    var hr='';
                    $.each(data.mensaje, function(i, item){
                           
                        if(i>=0){hr=`<li style="padding-bottom:5px">`;}
                        set[i]= ` ${hr} ${item}`;
                            
                    });
                    sms_error=set
                    alertNotificar("No se pudo aprobar ningun alimento", "error")
                }else{
                    sms_error=data.mensaje
                }
               
                $("#sms_errores").append(`<div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> Mensaje!</h4>
                        ${sms_error}
                    </div>`)
                $("#sms_errores").show()
                $("input[name='comida_chequeada[]']").val('')
                $("#comida_chequeada").html('')
                AbrirModal="N"
                $("#form_valida").submit();
                return;                      
            }
            let sms_error=""
            if(data.inconsistencia=="S"){
               
                var set=[''];
                var hr='';
                $.each(data.lista_error, function(i, item){
                       
                    if(i>=0){hr=`<li style="padding-bottom:5px">`;}
                    set[i]= ` ${hr} ${item}`;
                        
                });
                sms_error=set
                var error_sms="info"
                var icono="fa-ban"
            }else{
                var error_sms="success"
                var icono="fa-check"
                sms_error=""
            }          
            $("#sms_errores").append(`<div class="alert alert-${error_sms} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa ${icono}"></i> Mensaje!</h4>
                    ${data.mensaje}  ${sms_error}
            </div>`)
            AbrirModal="N"
            $("#sms_errores").show()
            $("#form_valida").submit();

                            
        }, error:function (data) {
            vistacargando("");
            alertNotificar('Ocurrió un error, intentelo más tarde','error');
            AbrirModal="N"
            $("#form_valida").submit();
        }
    });
}

function accionVerifica(id, estado, motivo){

    if( $('#id_alime_'+id).is(':checked') ){

        if(fueAprobado=='S'){
            $('#fila_sele_'+id).removeClass('color_aprobacion');
            $("#id_alime_"+id).prop('checked', false)
        }else{        
            $('#fila_sele_'+id).addClass('color_aprobacion');
        }

        if(estado=="Eliminado"){
            // alert("Eliminadp")
            swal('¡Mensaje!', motivo, "info");
        }
        //añadimos
        $("#comida_chequeada").append(`
            <tr id="fila_${id}">
                <td>
                    <input type="hidden" name="comida_chequeada[]" value="${id}">
                </td>
            </tr>
        `);
       
    } else {
       
        if(fueAprobado=='S'){
           
            $('#fila_sele_'+id).addClass('color_aprobacion');
            $("#id_alime_"+id).prop('checked', true)
        }else{
            $('#fila_sele_'+id).removeClass('color_aprobacion');
        }

           
        $('#fila_'+id).remove();
    }
}

$("#modal_aprobacion").on("hidden.bs.modal", function () {
    AbrirModal="S"
    fueAprobado="N"
    $("#sms_errores").html('')
    $("#sms_errores").hide()
    $('#btn_aprobar').prop('disabled', false)
    $("input[name='comida_chequeada[]']").val('')
    $("#comida_chequeada").html('')
    $('#cedula_func').val('')
    $('#telef_pin').val('')
})
function limpiar(){
    AbrirModal="S"
    fueAprobado="N"
    $('#modal_aprobacion').modal('hide')
    $('#identificacion_Apr').html('')
    $('#puesto_Apr').html('')
    $('#fecha_Act').html('')
    $('#user_Apr').html('')
    $('#area_Apr').html('')
    $('#horario_Apr').html('')
    $("#sms_errores").html('')
    $("#sms_errores").hide()
    $('#btn_aprobar').prop('disabled', false)
    $("input[name='comida_chequeada[]']").val('')
    $("#comida_chequeada").html('')
}

function cerrar(){
    AbrirModal="S"
    fueAprobado="N"
    $('#modal_aprobacion').modal('hide')
    limpiar()
    $('#cedula_func').val('')
   
    $("#sms_errores").html('')
    $("#sms_errores").hide()
    $('#btn_aprobar').prop('disabled', false)
    $("input[name='comida_chequeada[]']").val('')
    $("#comida_chequeada").html('')
}

function alertNotificar(texto, tipo,time=7000){
    PNotify.removeAll()
    new PNotify({
        title: 'Mensaje de Información',
        text: texto,
        type: tipo,
        hide: true,
        delay: time,
        styling: 'bootstrap3',
        addclass: ''
    });
}


function vistacargando(estado, mensaje){
    mostarOcultarVentanaCarga(estado, mensaje);
}

function mostarOcultarVentanaCarga(estado, mensaje){
    //estado --> M:mostrar, otra letra: Ocultamos la ventana
    // mensaje --> el texto que se carga al mostrar la ventana de carga
    if(estado=='M' || estado=='m'){
        // console.log(mensaje);
        $('#modal_cargando_title').html(mensaje);
        $('#modal_cargando').show();
        $('body').css('overflow', 'hidden');
    }else{
        $('#modal_cargando_title').html('Cargando');
        $('#modal_cargando').hide();
        $('body').css('overflow', '');
    }
}