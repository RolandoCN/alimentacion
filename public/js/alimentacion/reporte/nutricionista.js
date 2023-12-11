function seleccionaFiltroArea(){
   
    var cmb_filtra_area=$('#area_filtra').val()
    if(cmb_filtra_area==""){return}
    else if(cmb_filtra_area=="T"){
        $('#area_selecc').val('').change()
        $('.seccion_filtra_area').hide()
    }else{
        $('.seccion_filtra_area').show()
    }
}
function buscarArea(){

    let fecha_inicial_rep=$('#fecha_ini_area').val()
    let fecha_final_rep=$('#fecha_fin_area').val()
    let cmb_filtra_area=$('#area_filtra').val()
    let area= $('#area_selecc').val()
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
    
    if(cmb_filtra_area=="F"){
       if(area==""){
            alertNotificar("Seleccione una area","error")
            return
       }
    }
   
    vistacargando("m","Espere por favor");           

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        type: "POST",
        url: 'reporte-periodo-dieta-area',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        fecha_inicial_rep:fecha_inicial_rep, fecha_final_rep:fecha_final_rep,cmb_filtra_area:cmb_filtra_area, area:area },
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

function seleccionaFiltroNutri(){
    var cmb_filtra_nutri=$('#nutri_filtra').val()
    if(cmb_filtra_nutri==""){return}
    else if(cmb_filtra_nutri=="T"){
        $('#nutri_filtra_selecc').val('').change()
        $('.seccion_filtra_nutri_filtra').hide()
    }else{
        $('.seccion_filtra_nutri_filtra').show()
    }
}

function buscarGlobal(){
    alertNotificar("Pendiente","error")
    return
}
function buscarNutricionista(){

    let fecha_inicial_rep=$('#fecha_ini_nutricionista').val()
    let fecha_final_rep=$('#fecha_fin_nutricionista').val()
    let cmb_filtra_nutri=$('#nutri_filtra').val()
    let nutri= $('#nutri_filtra_selecc').val()
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
    
    if(cmb_filtra_nutri=="F"){
       if(nutri==""){
            alertNotificar("Seleccione un profesional","error")
            return
       }
    }
   
    vistacargando("m","Espere por favor");           

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        type: "POST",
        url: 'reporte-periodo-dieta-nutricionista',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        fecha_inicial_rep:fecha_inicial_rep, fecha_final_rep:fecha_final_rep,cmb_filtra_nutri:cmb_filtra_nutri, nutri:nutri },
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

function buscarPorProfesional(){
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
        url: 'reporte-periodo-dieta-profesional',
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


