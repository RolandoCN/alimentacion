
$(document).keyup(function(event) {
    if (event.which === 13) {
        $("#form_valida").submit()
    }
});
$("#form_valida").submit(function(e){
    e.preventDefault();
    let cedula_func=$('#cedula_func').val()
   
    if(cedula_func.length!=10){
        alertNotificar("Debe ingresar un número de cédula válido de 10 dígitos","error")
        return
    }

    vistacargando("m","Espere por favor")
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    console.log("token "+$('meta[name="csrf-token"]').attr('content'))
    
    limpiar()
    var FrmData=$("#form_valida").serialize();
   
    $.ajax({
           
       type: "POST",
       url: "valida-comida-empleado",
       method: "POST",             
       data: FrmData,      
       
       processData:false, 

       success: function(data){
          
           vistacargando("");                
           if(data.error==true){
                
                alertNotificar(data.mensaje,'error');
                $('#audio').attr("src", "./rechazado.mp4")
                audio.play();

                return;                      
           }
           $('#audio').attr("src", "./aprobado.mp4")
           alertNotificar("Aprobado exitosamente","success");
           $('#modal_aprobacion').modal({backdrop: 'static', keyboard: false})
           $('#identificacion_Apr').html(data.data.cedula)
           $('#puesto_Apr').html(data.data.puesto)
           $('#comida_Apr').html(data.data.comida)
           $('#user_Apr').html(data.data.nombres)
           $('#area_Apr').html(data.data.area)
           $('#horario_Apr').html(data.data.hora_ini+" -- "+data.data.hora_fin)
           audio.play();

           //bloqueamos el boton de cerrar x 10 segundos para que le de chance de verficar la aprobacion
            setTimeout(() => {
                $('#btn_cancelar').prop('disabled',false)
                cerrar()
            }, "10000");

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

function limpiar(){
    $('#modal_aprobacion').modal('hide')
    $('#identificacion_Apr').html('')
    $('#puesto_Apr').html('')
    $('#comida_Apr').html('')
    $('#user_Apr').html('')
    $('#area_Apr').html('')
    $('#horario_Apr').html('')
}

function cerrar(){
    $('#modal_aprobacion').modal('hide')
    limpiar()
    $('#cedula_func').val('')
    audio.pause();
    audio.currentTime = 0;
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

function vistacargando(estado){
    mostarOcultarVentanaCarga(estado,'');
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