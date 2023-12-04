
function buscarTurnos(){

    let fecha_inicial_rep=$('#fecha_ini').val()
    let fecha_final_rep=$('#fecha_fin').val()
    
    if(fecha_inicial_rep==""){ 
        alertNotificar("Seleccione una fecha inicial","error")
        return 
    }

    if(fecha_final_rep==""){ 
        alertNotificar("Seleccione una fecha final","error")
        $('#fecha_ini').focus()
        return 
    }

    if(fecha_inicial_rep > fecha_final_rep){
        alertNotificar("La fecha de inicio debe ser menor a la fecha final","error")
        $('#fecha_ini').focus()
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
        url: 'reporte-periodo-paciente',
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



