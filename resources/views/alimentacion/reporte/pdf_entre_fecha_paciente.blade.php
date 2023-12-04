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
            font-size: 12px;
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

   
    <div style="margin-bottom:30px; margin-top:12px;">

        <table class="ltable" style="" border="0" width="100%" style="padding-bottom:2px !important">
          
            <tr style="font-size: 11px"  class="fuenteSubtitulo " style=""> 
                <th colspan="11" style="border-color:white;height:35px;text-align: center;border:0 px" width="100%"  >CONSOLIDADO DE ALIMENTACIÓN PACIENTES <br>
                DESDE {{date('d-m-Y',strtotime($desde))}} HASTA  {{date('d-m-Y',strtotime($hasta))}}<br><br>
              
                </th>
            
            </tr>

            
          
        </table>
        <div style="margin-top:12px;">
            <table class="ltable"  border="0" width="100%" style="padding-bottom:2px !important">
                
                <tr style="font-size: 10px !important; background-color: #D3D3D3;line-height:10px; "> 
                    
                    <th width="10%" style="border: 0px; ;border-color: #D3D3D3; text-align: center; line-height:15px">FECHA</th>

                    <th width="15%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">DESAYUNO</th>

                    <th width="15%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">COLACION1</th>

                    <th width="15%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">ALMUERZO</th>

                    <th width="15%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">COLACION2</th>

                    <th width="15%" style="border-right: 0px;border-top: 0px; border-bottom:0px;border-color: #D3D3D3; text-align: center">MERIENDA</th>

                    <th width="15%" style="border-right: 0px;border-top: 0px; border-bottom:0px;border-color: #D3D3D3; text-align: center">LIQUIDA</th>

                    <th width="15%" style="border: 0px; text-align: center">POR DÍA</th>
             
                </tr>
            
                <tbody>
                    
                    @if(isset($lista))
                        @php
                            $cont_desayu=0;
                            $cont_almuerzo=0;
                            $cont_merienda=0;
                            $cont_colacion1=0;
                            $cont_colacion2=0;
                            $cont_liquida=0;
                            $cont_npo=0;
                        @endphp
                        @foreach($lista as $e=>$dato)
                            @php
                                $fecha_comida=date('d-m-Y', strtotime($e));
                            @endphp
                            <tr style="font-size: 10px !important; line-height:20px">                                    
                                
                                <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                    {{$fecha_comida}}
                                </td>

                                @php
                                    $desayu='';
                                    $almuerzo='';
                                    $merienda='';
                                    $colacion1='';
                                    $colacion2='';
                                    $liquida='';

                                    $cont_desayu_int=0;
                                    $cont_almuerzo_int=0;
                                    $cont_merienda_int=0;
                                    $cont_colacion1_int=0;
                                    $cont_colacion2_int=0;
                                    $cont_dias_int=0;
                                    $cont_liquida_int=0;

                                @endphp
                                @foreach($dato as $fila=> $comida_ser)

                    
                                    {{-- @if($comida_ser->dieta!="NADA POR VIA ORAL" && ($comida_ser->dieta=="LIQUIDA ACALORICA" || $comida_ser->dieta=="LIQUIDA ESTRICTA" || $comida_ser->dieta=="LIQUIDA AMPLIA")) --}}
                                    @if($comida_ser->dieta=="LIQUIDA ACALORICA" || $comida_ser->dieta=="LIQUIDA ESTRICTA" || $comida_ser->dieta=="LIQUIDA AMPLIA")
                                        @php
                                            $liquida='X';
                                            $cont_liquida=$cont_liquida+1;
                                            $cont_liquida_int=$cont_liquida_int+1;
                                        @endphp
                                    @elseif($comida_ser->dieta=="NADA POR VIA ORAL")
                                        @php
                                            $cont_npo=$cont_npo+1;;
                                        @endphp
                                    @else
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

                                        @if($comida_ser->comida == "Colacion 1" || $comida_ser->comida == "Colacion")
                                            @php
                                                $colacion1='X';
                                                $cont_colacion1=$cont_colacion1+1;
                                                $cont_colacion1_int=$cont_colacion1_int+1;
                                            @endphp
                                        @endif

                                        @if($comida_ser->comida == "Colacion 2")
                                            @php
                                                $colacion2='X';
                                                $cont_colacion2=$cont_colacion2+1;
                                                $cont_colacion2_int=$cont_colacion2_int+1;
                                            @endphp
                                        @endif
                                    @endif

                                    @php
                                        $cont_dias=$cont_desayu + $cont_almuerzo + $cont_merienda + $cont_colacion1 + $cont_colacion2 + $cont_liquida;

                                        $cont_dias_int=$cont_desayu_int + $cont_almuerzo_int + $cont_merienda_int +$cont_colacion1_int + $cont_colacion2_int + $cont_liquida_int;
                                    @endphp

                                @endforeach

                               
                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_desayu_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_colacion1_int}} 
                                    </td>


                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_almuerzo_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_colacion2_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_merienda_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_liquida_int}} 
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cont_dias_int}} 
                                    </td>

                                 
                              
                        @endforeach		
                    @endif
                </tbody>
                <tfoot >
                    <tr style="font-size:10px !important;line-height:25px" style="">
                        <td  colspan="1"style="font-size:9px;border: 0px; border-color: #D3D3D3;  text-align: right;">
                            <b>TOTAL</b>
                        </td>
                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:10px">
                           {{$cont_desayu}} 
                           
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:10px">
                            {{$cont_colacion1}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:10px">
                            {{$cont_almuerzo}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:10px">
                            {{$cont_colacion2}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:10px">
                            {{$cont_merienda}} 
                        </td>

                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:10px">
                            {{$cont_liquida}} 
                        </td>


                        <td style="border: 0px;border-color: #D3D3D3;  text-align: center; font-size:10px">
                            {{$cont_dias}} 
                        </td>

                    </tr>

                </tfoot> 

            </table>
        </div>

        @php
            $total_plato=sizeof($datos) -$cont_npo;
        @endphp

        <p style="font-size: 10px; text-align:center; font-family:sans-serif;"><b  style="font-size: 10px;">TOTAL: {{$total_plato}}</b></p>
        
        {{-- $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
        $pdf->text(490, 820, "Página $PAGE_NUM de $PAGE_COUNT", $font, 9); --}}
       
    </div>

   
  <script type="text/php">
    if ( isset($pdf) ) {
        $pdf->page_script('
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $pdf->text(490, 820, "Página $PAGE_NUM de $PAGE_COUNT", $font, 9); 
        ');
    }
</script>
</body>
</html>