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
                <th colspan="11" style="border-color:white;height:35px;text-align: center;border:0 px" width="100%"  >INDIVIDUAL POR USUARIO Y FECHA<br>
                DESDE {{date('d-m-Y',strtotime($datos[0]->fecha_turno))}} HASTA  {{date('d-m-Y',strtotime($datos[0]->fecha_turno))}}<br><br>
              
                </th>
            
            </tr>

            <tr style="font-size: 11px"  class="fuenteSubtitulo " style=""> 

                <td  style="border-color:white;height:15px;text-align: left;border:0 px" width="10%" >
                    
                </td>

                <td  style="border-color:white;height:15px;text-align: left;border:0 px" width="40%"  ><b>Cédula: </b>{{$datos[0]->cedula}}
              
                </td>

                <td  style="border-color:white;height:15px;text-align: left;border:0 px" width="10%" >
                    
                </td>

                <td style="border-color:white;height:15px;text-align: left;border:0 px" width="40%"  ><b>Usuario: </b> {{$datos[0]->nombres}}
                  
                </td>
            
            </tr>

            <tr style="font-size: 11px"  class="fuenteSubtitulo " style=""> 

                <td  style="border-color:white;height:15px;text-align: left;border:0 px" width="10%" >
                    
                </td>

                <td  style="border-color:white;height:15px;text-align: left;border:0 px" width="40%"  ><b>Puesto: </b>{{$datos[0]->puesto}}
              
                </td>

                <td  style="border-color:white;height:15px;text-align: left;border:0 px" width="10%" >
                    
                </td>

                <td style="border-color:white;height:15px;text-align: left;border:0 px" width="40%"  ><b>Área: </b> {{$datos[0]->area}}
                </td>

            
            </tr>
          
        </table>
        <div style="margin-top:12px;">
            <table class="ltable"  border="0" width="100%" style="padding-bottom:2px !important">
                
                <tr style="font-size: 10px !important; background-color: #D3D3D3;line-height:10px; "> 
                    
                    <th width="40%" style="border: 0px; ;border-color: #D3D3D3; text-align: center; line-height:15px">FECHA</th>

                    <th width="15%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">DESAYUNO</th>

                    <th width="15%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">ALMUERZO</th>
                    <th width="15%" style="border-right: 0px;border-top: 0px; border-bottom:0px;border-color: #D3D3D3; text-align: center">MERIENDA</th>
                
                    <th width="15%" style="border: 0px; text-align: center">CENA</th>
             
                </tr>
            
                <tbody>
                    
                    @if(isset($lista))
                        @php
                            $cont_desayu=0;
                            $cont_almuerzo=0;
                            $cont_merienda=0;
                            $cont_cena=0;
                        @endphp
                        @foreach($lista as $e=>$dato)
                            <tr style="font-size: 10px !important; line-height:20px">                                    
                                
                                <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                    {{$e}}
                                </td>

                                @php
                                    $desayu='';
                                    $almuerzo='';
                                    $merienda='';
                                    $cena='';

                                    // $cont_desayu=0;
                                    // $cont_almuerzo=0;
                                    // $cont_merienda=0;
                                    // $cont_cena=0;
                                @endphp
                                @foreach($dato as $fila=> $comida_ser)
                                    @if($comida_ser->comida == "Desayuno")
                                        @php
                                            $desayu='X';
                                            $cont_desayu=$cont_desayu+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Almuerzo")
                                        @php
                                            $almuerzo='X';
                                            $cont_almuerzo=$cont_almuerzo+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Merienda")
                                        @php
                                            $merienda='X';
                                            $cont_merienda=$cont_merienda+1;
                                        @endphp
                                    @endif

                                    @if($comida_ser->comida == "Cena")
                                        @php
                                            $cena='X';
                                            $cont_cena=$cont_cena+1;
                                        @endphp
                                    @endif

                                @endforeach

                               
                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$desayu}}
                                    </td>
                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$almuerzo}}
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$merienda}}
                                    </td>

                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3">
                                        {{$cena}}
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

                    </tr>

                </tfoot>

            </table>
        </div>

        <p style="font-size: 10px; text-align:center; font-family:sans-serif;"><b  style="font-size: 10px;">TOTAL: {{sizeof($datos)}}</b></p>
        
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