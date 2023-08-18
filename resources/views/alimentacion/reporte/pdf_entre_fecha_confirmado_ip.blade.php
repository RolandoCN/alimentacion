<!DOCTYPE html>
<html>
<head>
  <title></title>

     <style type="text/css">
        @page {
            margin-top: 8em;
            margin-left: 3em;
            margin-right:3em;
            margin-bottom: 5em;
        }
        header { position: fixed;  top: -100px; left: 0px; right: 0px; background-color: white; height: 60px; margin-right: 99px}
       
       

        .ltable
        {
            border-collapse: collapse;
            font-family: sans-serif;
        }
        td, th /* Asigna un borde a las etiquetas td Y th */
        {
            border: 1px solid white;
        }

        .sinbordeencabezado /* Asigna un borde a las etiquetas td Y th */
        {
            border: 0px solid black;
        }
        .fuenteSubtitulo{
            font-size: 11px;
        }
        .pad{
            padding-left:5px;
            padding-right:5px;
        }

        
     </style>
      <style type="text/css">
        .preview_firma{
            width: 156px;
            border: solid 1px #000;
        }
        .img_firma{
            width: 80px;
        }
        .btn_azul{
            color: #fff;
            background-color: #337ab7;
            border-color: #2e6da4;
        }

    </style>

  
</head>

<body>

  <header>
    <table class="ltable " width="112.5%"  >                
            <tr>
                <td height="50px" colspan="3" style="border: 0px;" align="left" >
                    <img src="logo.jpg" width="300px" height="80px">
                </td>
                <td height="60px" colspan="2" style="border: 0px;" align="center" ></td>
               
            </tr>             
        </table>
  </header>

   
    <div style="margin-bottom:20px; margin-top:12px;">

        <table class="ltable" style="" border="0" width="100%" style="padding-bottom:2px !important">
          
            <tr style="font-size: 10px"  class="fuenteSubtitulo " style=""> 
                <th colspan="11" style="border-color:white;height:35px;text-align: center;border:0 px" width="100%"  >INFORME DE ALIMENTOS APROBADOS RETIRADOS Y NO RETIRADOS  <br> POR RANGO FECHA 
               
              
                </th>
            
            </tr>

            
          
        </table>

        <div style="margin-top:2px;">
           
            <table class="ltable"  border="0" width="100%" style="padding-bottom:2px !important">

                <tr style="font-size: 10px !important; background-color: white;line-height:10px; "> 
                    
                    <th width="10%" colspan="6" style="border: 0px; ;border-color: white; text-align: center; line-height:10px">RESUMEN DE CONFIRMACION POR IP RETIRADOS DESDE {{date('d-m-Y',strtotime($desde))}} HASTA  {{date('d-m-Y',strtotime($hasta))}}</th>

                </tr>
                <tr style="font-size: 10px !important; background-color: white;line-height:10px; "> 
                    
                    <th width="10%" colspan="6" style="border: 0px; ;border-color: white; text-align: center; line-height:10px"></th>

                </tr>
                
                <tr style="font-size: 10px !important; background-color: #D3D3D3;line-height:10px; "> 
                    
                    <th width="10%" style="border: 0px; ;border-color: #D3D3D3; text-align: center; line-height:10px">IP</th>

                    <th width="8%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">DESAYUNO</th>

                    <th width="8%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">ALMUERZO</th>
                    <th width="8%" style="border-right: 0px;border-top: 0px; border-bottom:0px;border-color: #D3D3D3; text-align: center">MERIENDA</th>
                
                    <th width="8%" style="border: 0px; text-align: center">CENA</th>

                    <th width="8%" style="border: 0px; text-align: center">TOTAL</th>

                  
                </tr>
            
                <tbody>
                    
                    @if(isset($lista_ip_ret))
                        @php
                            $cont_desayu=0;
                            $cont_almuerzo=0;
                            $cont_merienda=0;
                            $cont_cena=0;
                            $cont_dias=0;
                        @endphp
                        @foreach($lista_ip_ret as $e=>$dato)
                            <tr style="font-size: 8px !important; line-height:18px">                                    
                                
                               

                                @php
                                    $desayu='';
                                    $almuerzo='';
                                    $merienda='';
                                    $cena='';

                                    $cont_desayu_int=0;
                                    $cont_almuerzo_int=0;
                                    $cont_merienda_int=0;
                                    $cont_cena_int=0;
                                    $cont_dias_int=0;

                                @endphp
                                @foreach($dato as $fila=> $comida_ser)
                                    @if($comida_ser->comida == "Desayuno")
                                        @php
                                            $desayu='X';
                                            $cont_desayu=$cont_desayu+1;
                                            $cont_desayu_int=$cont_desayu_int+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Almuerzo")
                                        @php
                                            $almuerzo='X';
                                            $cont_almuerzo=$cont_almuerzo+1;
                                            $cont_almuerzo_int=$cont_almuerzo_int+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Merienda")
                                        @php
                                            $merienda='X';
                                            $cont_merienda=$cont_merienda+1;
                                            $cont_merienda_int=$cont_merienda_int+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Cena")
                                        @php
                                            $cena='X';
                                            $cont_cena=$cont_cena+1;
                                            $cont_cena_int=$cont_cena_int+1;
                                        @endphp
                                    @endif

                                    @php
                                        $cont_dias=$cont_desayu + $cont_almuerzo + $cont_merienda +$cont_cena;

                                        $cont_dias_int=$cont_desayu_int + $cont_almuerzo_int + $cont_merienda_int +$cont_cena_int;
                                    @endphp

                                    @php
                                        $fecha_t=$comida_ser->fecha_turno;
                                        $cedula_per=$comida_ser->cedula;
                                        $empleado=$comida_ser->nombres;
                                        $area_em=$comida_ser->area;
                                        $ip_confirma=$comida_ser->ip_confirma;
                                    @endphp

                                @endforeach

                                    
                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$ip_confirma}} 
                                    </td>

                                    

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_desayu_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_almuerzo_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_merienda_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_cena_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_dias_int}} 
                                    </td>

                                 
                            
                        @endforeach		
                    @endif
                </tbody>
                <tfoot >
                    <tr style="font-size:10px !important;line-height:5px" style="">
                        <td  colspan="1"style="font-size:9px;border: 0px; border-color: #D3D3D3;  text-align: right;">
                            <b>TOTAL</b>
                        </td>
                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                           {{$cont_desayu}} 
                           
                        </td>
                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                            {{$cont_almuerzo}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                            {{$cont_merienda}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                            {{$cont_cena}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                            {{$cont_dias}} 
                        </td>

                    </tr>

                </tfoot>  

            </table>
        </div>

        <p style="font-size: 10px; text-align:center; font-family:sans-serif;"><b  style="font-size: 10px;">TOTAL PLATOS: {{$cont_dias}}</b></p>
       
       

       

        <div style="margin-top:12px;">
            {{-- <div style="page-break-after:always;"></div> --}}
            <table class="ltable"  border="0" width="100%" style="padding-bottom:2px !important">

                <tr style="font-size: 10px !important; background-color: white;line-height:10px; "> 
                    
                    <th width="10%" colspan="6" style="border: 0px; ;border-color: white; text-align: center; line-height:10px">RESUMEN DE CONFIRMACION POR IP NO RETIRADOS DESDE {{date('d-m-Y',strtotime($desde))}} HASTA  {{date('d-m-Y',strtotime($hasta))}}</th>

                </tr>
                <tr style="font-size: 10px !important; background-color: white;line-height:10px; "> 
                    
                    <th width="10%" colspan="6" style="border: 0px; ;border-color: white; text-align: center; line-height:10px"></th>

                </tr>
                
                <tr style="font-size: 10px !important; background-color: #D3D3D3;line-height:10px; "> 
                    
                    <th width="10%" style="border: 0px; ;border-color: #D3D3D3; text-align: center; line-height:10px">IP</th>

                    <th width="8%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">DESAYUNO</th>

                    <th width="8%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">ALMUERZO</th>
                    <th width="8%" style="border-right: 0px;border-top: 0px; border-bottom:0px;border-color: #D3D3D3; text-align: center">MERIENDA</th>
                
                    <th width="8%" style="border: 0px; text-align: center">CENA</th>

                    <th width="8%" style="border: 0px; text-align: center">TOTAL</th>

                  
                </tr>
            
                <tbody>
                    
                    @if(isset($lista_ip_no_ret))
                        @php
                            $cont_desayu=0;
                            $cont_almuerzo=0;
                            $cont_merienda=0;
                            $cont_cena=0;
                            $cont_dias=0;
                        @endphp
                        @foreach($lista_ip_no_ret as $e=>$dato)
                            <tr style="font-size: 8px !important; line-height:18px">                                    
                                
                               

                                @php
                                    $desayu='';
                                    $almuerzo='';
                                    $merienda='';
                                    $cena='';

                                    $cont_desayu_int=0;
                                    $cont_almuerzo_int=0;
                                    $cont_merienda_int=0;
                                    $cont_cena_int=0;
                                    $cont_dias_int=0;

                                @endphp
                                @foreach($dato as $fila=> $comida_ser)
                                    @if($comida_ser->comida == "Desayuno")
                                        @php
                                            $desayu='X';
                                            $cont_desayu=$cont_desayu+1;
                                            $cont_desayu_int=$cont_desayu_int+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Almuerzo")
                                        @php
                                            $almuerzo='X';
                                            $cont_almuerzo=$cont_almuerzo+1;
                                            $cont_almuerzo_int=$cont_almuerzo_int+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Merienda")
                                        @php
                                            $merienda='X';
                                            $cont_merienda=$cont_merienda+1;
                                            $cont_merienda_int=$cont_merienda_int+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Cena")
                                        @php
                                            $cena='X';
                                            $cont_cena=$cont_cena+1;
                                            $cont_cena_int=$cont_cena_int+1;
                                        @endphp
                                    @endif

                                    @php
                                        $cont_dias=$cont_desayu + $cont_almuerzo + $cont_merienda +$cont_cena;

                                        $cont_dias_int=$cont_desayu_int + $cont_almuerzo_int + $cont_merienda_int +$cont_cena_int;
                                    @endphp

                                    @php
                                        $fecha_t=$comida_ser->fecha_turno;
                                        $cedula_per=$comida_ser->cedula;
                                        $empleado=$comida_ser->nombres;
                                        $area_em=$comida_ser->area;
                                        $ip_confirma=$comida_ser->ip_confirma;
                                    @endphp

                                @endforeach

                                    
                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$ip_confirma}} 
                                    </td>

                                    

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_desayu_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_almuerzo_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_merienda_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_cena_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_dias_int}} 
                                    </td>

                                 
                            
                        @endforeach		
                    @endif
                </tbody>
                <tfoot >
                    <tr style="font-size:10px !important;line-height:5px" style="">
                        <td  colspan="1"style="font-size:9px;border: 0px; border-color: #D3D3D3;  text-align: right;">
                            <b>TOTAL</b>
                        </td>
                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                           {{$cont_desayu}} 
                           
                        </td>
                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                            {{$cont_almuerzo}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                            {{$cont_merienda}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                            {{$cont_cena}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:9px">
                            {{$cont_dias}} 
                        </td>

                    </tr>

                </tfoot>  

            </table>
        </div>

        <p style="font-size: 10px; text-align:center; font-family:sans-serif;"><b  style="font-size: 10px;">TOTAL PLATOS: {{$cont_dias}}</b></p>
        
        {{-- $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
        $pdf->text(490, 820, "Página $PAGE_NUM de $PAGE_COUNT", $font, 9); --}}
       
    </div>

   
  <script type="text/php">
    if ( isset($pdf) ) {
        $pdf->page_script('
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $pdf->text(370, 560, "Pág $PAGE_NUM de $PAGE_COUNT", $font, 7);
        ');
    }
</script>
</body>
</html>