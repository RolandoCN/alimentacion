


function llenar_tabla_tipo_ali(){
    var num_col = $("#tabla_tipo_ali thead tr th").length; //obtenemos el numero de columnas de la tabla
	$("#tabla_tipo_ali tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center><span class="spinner-border" role="status" aria-hidden="true"></span><b> Obteniendo información</b></center></td></tr>`);
   
    
    $.get("listado-tipo-alimentos/", function(data){
      
        if(data.error==true){
            alertNotificar(data.mensaje,"error");
            $("#tabla_tipo_ali tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
            return;   
        }
        if(data.error==false){
            
            if(data.resultado.length <= 0){
                $("#tabla_tipo_ali tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
                alertNotificar("No se encontró datos","error");
                return;  
            }
         
            $('#tabla_tipo_ali').DataTable({
                "destroy":true,
                pageLength: 10,
                autoWidth : true,
                order: [[ 1, "asc" ]],
                sInfoFiltered:false,
                language: {
                    url: 'json/datatables/spanish.json',
                },
                columnDefs: [
                    { "width": "35%", "targets": 0 },
                    { "width": "15%", "targets": 1 },
                    { "width": "15%", "targets": 2 },
                    { "width": "15%", "targets": 3 },
                    { "width": "20%", "targets": 4 },
                   
                ],
                data: data.resultado,
                columns:[
                        {data: "descripcion"},
                        {data: "hora_min" },
                        {data: "hora_max"},
                        {data: "hora_max_aprobacion"},
                        {data: "descripcion"},
                    
                ],    
                "rowCallback": function( row, data, index ) {
                    // $('td', row).eq(0).html(index+1)
                    $('td', row).eq(4).html(`
                                  
                                            <button type="button" class="btn btn-danger btn-xs" onclick="editarHorario('${data.idalimento}','${data.descripcion}','${data.hora_max_aprobacion}')">Cambiar Hora Aprob</button>

                                    
                    `); 
                }             
            });
        }
    }).fail(function(){
        $("#tabla_tipo_ali tbody").html(`<tr><td colspan="${num_col}" style="padding:40px; 0px; font-size:20px;"><center>No se encontraron datos</center></td></tr>`);
        alertNotificar("Se produjo un error, por favor intentelo más tarde","error");  
    });


}

$('.collapse-link').click();
$('.datatable_wrapper').children('.row').css('overflow','inherit !important');

$('.table-responsive').css({'padding-top':'12px','padding-bottom':'12px', 'border':'0', 'overflow-x':'inherit'});

function editarHorario(id, descripcion, hora){
    let h_sugerida=""
    if(descripcion=="Almuerzo"){
        h_sugerida="11:00"
    }else if(descripcion=="Merienda"){
        h_sugerida="16:30"
    }else if(descripcion=="Cena"){
        h_sugerida="17:30"
    }
 
    $('#descripcion').val(descripcion)
    $('#hora_sugerida').val(h_sugerida)
    $('#hora_aprobacion').val(hora)
    $('#modal_Alimento').modal('show')

    globalThis.IdAliEdit=id
}

function actualizarHoraAp(){
    vistacargando("m","Espere por favor")
    let hora_cambia=$('#hora_aprobacion').val()
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $.ajax({
        type: "POST",
        url: 'cambia-hora-aprob',
        data: { _token: $('meta[name="csrf-token"]').attr('content'),
        IdAliEdit:IdAliEdit, hora_cambia:hora_cambia },
        success: function(data){
           
            vistacargando("");                
            if(data.error==true){
                alertNotificar(data.mensaje,'error');
                return;                      
            }
            alertNotificar(data.mensaje,"success")
            llenar_tabla_tipo_ali()
            cerrar()
                            
        }, error:function (data) {
            vistacargando("");
            alertNotificar('Ocurrió un error','error');
        }
    });
    
}

function cerrar(){
    $('#modal_Alimento').modal('hide')
}