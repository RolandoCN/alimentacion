
function buscaServicio(){
    var servicio=$('#cmb_servicio').val()
    llenar_tabla_paciente(servicio)
}

function llenar_tabla_paciente(servicio){
   
    var num_col = $("#tabla_paciente thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_paciente tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
   
    
    $.get("listado-paciente-ali-visor/"+servicio, function(data){
        console.log(data)
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_paciente tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_paciente tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }

            $('#tabla_paciente').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                order: [[ 2, "asc" ]],
                sInfoFiltered:false,
                language: {
                    url: 'json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "8%", "targets": 0 },
                    { "width": "25%", "targets": 1 },
                    { "width": "10%", "targets": 2 },
                    { "width": "25%", "targets": 3 },
                    { "width": "15%", "targets": 4 },
                    { "width": "10%", "targets": 5 },
                    { "width": "8%", "targets": 6 },
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "fecha_solicita"},
                        {data: "paciente" },
                        {data: "servicio"},
                        {data: "responsable"},
                        {data: "dieta"},
                        {data: "estado"},
                        {data: "estado"},
                     
                ],    
                "rowCallback": function( row, data ) {
                    
                    if(data.estado=="Solicitado"){
                        $('td', row).eq(0).addClass('color_pendiente')
                        $('td', row).eq(1).addClass('color_pendiente')
                        $('td', row).eq(2).addClass('color_pendiente')
                        $('td', row).eq(3).addClass('color_pendiente')
                        $('td', row).eq(4).addClass('color_pendiente')
                        $('td', row).eq(5).addClass('color_pendiente')
                        $('td', row).eq(6).addClass('color_pendiente')
                    }else{
                        $('td', row).eq(0).addClass('color_aprobacion')
                        $('td', row).eq(1).addClass('color_aprobacion')
                        $('td', row).eq(2).addClass('color_aprobacion')
                        $('td', row).eq(3).addClass('color_aprobacion')
                        $('td', row).eq(4).addClass('color_aprobacion')
                        $('td', row).eq(5).addClass('color_aprobacion')
                        $('td', row).eq(6).addClass('color_aprobacion')
                    }
                    $('td', row).eq(6).html(`
                                  
                        <button type="button" class="btn btn-primary btn-xs" onclick="historial(${data.id_paciente })">Historial</button>
                       
                    `); 
                   
                }             
            });
        }
    }).fail(function(){
        $("#tabla_paciente tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });


}

$('.collapse-link').click();
$('.datatable_wrapper').children('.row').css('overflow','inherit !important');

$('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0', 'overflow-x':'inherit'});


function historial(idpaciente){

    var num_col = $("#tabla_historial thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_historial tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);

    vistacargando("m","Espere por favor");
    $.get("historial-paciente-ali/"+idpaciente, function(data){
        console.log(data)
        vistacargando("");
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_historial tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_historial tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }
            $('#modal_Historial').modal('show')
            $('.ci_paciente').html(data.resultado[0].cedula)
            $('.paciente_alim').html(data.resultado[0].paciente)
            $('.edad_paciente').html(data.resultado[0].edad)
            $('#tabla_historial').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                order: [[ 1, "desc" ]],
                sInfoFiltered:false,
                language: {
                    url: 'json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "5%", "targets": 0 },
                    { "width": "10%", "targets": 1 },
                    { "width": "15%", "targets": 2 },
                    { "width": "25%", "targets": 3 },
                    { "width": "20%", "targets": 4 },
                    { "width": "25%", "targets": 5 },
                   
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "fecha_solicita"},
                        {data: "fecha_solicita"},
                        {data: "servicio" },
                        {data: "responsable"},
                        {data: "dieta"},
                        {data: "observacion"},
                        
                     
                ],    
                "rowCallback": function( row, data ,i) {
                    $('td', row).eq(0).html(i+1)
                   
                }             
            });
        }
    }).fail(function(){
        vistacargando("");
        $("#tabla_historial tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");    
    });

    
}

function reporteAliPacSolicitado(){
    var servicio=$('#cmb_servicio').val()
    vistacargando("m","Espere por favor");
    $.get("pdf-paciente-ali-solicitado/"+servicio, function(data){
       
        vistacargando("");
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            return;   
        }
        if(data.error==false){
            alertNotificar("El documento se descargará en unos segundos...","success");
            verpdf(data.pdf)
            
        }
    }).fail(function(){
        vistacargando("");
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}
function pdf_alimento_pac(){
    vistacargando("m","Espere por favor");
    $.get("pdf-paciente-ali", function(data){
       
        vistacargando("");
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            return;   
        }
        if(data.error==false){
            llenar_tabla_paciente()
            alertNotificar("El documento se descargará en unos segundos...","success");
            // window.location.href="descargar-reporte/"+data.pdf
            verpdf(data.pdf)
            
        }
    }).fail(function(){
        vistacargando("");
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}

function servicioCombo(){
    var serv=$('#servicio_cmb').val()
    if(serv==""){return}
    $('#tipo').val('').change()
    if(serv=="Dialisis"){
        // $('#com_hosp').hide()
        // $('#tipo').val('').change()
        $('#com_hosp').show()
    }else{
        $('#com_hosp').show()
    }
}

function tipoComida(){
    var tipo=$('#tipo').val()
    var serv=$('#servicio_cmb').val()
    if(serv==""){
        alertNotificar("Debe seleccionar primero el servicio")
        return
    }

    if(serv=="Dialisis"){
        if(tipo=="Desayuno" || tipo=="Almuerzo" || tipo=="Merienda" || tipo=="Colacion 2" || tipo=="Colacion 1" ){
            alertNotificar("El servicio de Dialisis, solo tiene el tipo Colacion 1 y Colacion 2", "error")
            $('#tipo').val('').change()
            return
        }
    }

}

function verpdf(ruta){
    var iframe=$('#iframePdf');
    iframe.attr("src", "visualizardoc/"+ruta);   
    $("#vinculo").attr("href", 'descargar-reporte/'+ruta);
    $("#documentopdf").modal("show");
}

$('#documentopdf').on('hidden.bs.modal', function (e) {
     
    var iframe=$('#iframePdf');
    iframe.attr("src", null);

});

$('#descargar').click(function(){
    $('#documentopdf').modal("hide");
});

function generarPdf(){
    var ini=$('#fecha_ini').val()
    var fin=$('#fecha_fin').val()
    var servicio=$('#servicio_cmb').val()
    var tipo=$('#tipo').val()
    if(ini==""){
        alertNotificar("Debe seleccionar la fecha de inicio","error")
        $('#fecha_ini').focus()
        return
    }

    if(fin==""){
        alertNotificar("Debe seleccionar la fecha de fin","error")
        $('#fecha_fin').focus()
        return
    }

    if(ini>fin){
        alertNotificar("La fecha de inicio debe ser menor a la final","error")
        return
    }

    if(servicio=="" ){
        alertNotificar("Debe seleccionar el servicio","error")
        return
    }

    if(servicio=="Otros"){
        if(tipo=="" ){
            alertNotificar("Debe seleccionar un tipo","error")
            return
        }
    }else{
        // tipo="N";
    }


    vistacargando("m","Espere por favor");
    $.get("pdf-paciente-ali-dia/"+ini+"/"+fin+"/"+servicio+"/"+tipo, function(data){
       
        vistacargando("");
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            return;   
        }
        if(data.error==false){
            alertNotificar("El documento se descargará en unos segundos...","success");
            window.location.href="descargar-reporte/"+data.pdf
            
        }
    }).fail(function(){
        vistacargando("");
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}


function generarPdfRollo(){
    var ini=$('#fecha_ini').val()
    var fin=$('#fecha_fin').val()
    var servicio=$('#servicio_cmb').val()
    var tipo=$('#tipo').val()
    if(ini==""){
        alertNotificar("Debe seleccionar la fecha de inicio","error")
        $('#fecha_ini').focus()
        return
    }

    if(fin==""){
        alertNotificar("Debe seleccionar la fecha de fin","error")
        $('#fecha_fin').focus()
        return
    }

    if(ini>fin){
        alertNotificar("La fecha de inicio debe ser menor a la final","error")
        return
    }

    if(servicio=="" ){
        alertNotificar("Debe seleccionar el servicio","error")
        return
    }

    if(servicio=="Otros"){
        if(tipo=="" ){
            alertNotificar("Debe seleccionar un tipo","error")
            return
        }
    }else{
        // tipo="N";
    }


    vistacargando("m","Espere por favor");
    $.get("impresion-rollo-ali/"+ini+"/"+fin+"/"+servicio+"/"+tipo, function(data){
       
        vistacargando("");
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            return;   
        }
        if(data.error==false){
            alertNotificar("El documento se descargará en unos segundos...","success");
            window.location.href="descargar-reporte/"+data.pdf
            
        }
    }).fail(function(){
        vistacargando("");
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });
}