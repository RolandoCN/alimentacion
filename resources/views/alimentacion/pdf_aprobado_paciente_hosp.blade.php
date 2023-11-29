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
        @php
            $titulo="";
            if(isset($ini)){
                $ini=date('d-m-Y', strtotime($ini));
                $fin=date('d-m-Y', strtotime($fin));
                $titulo="DESDE EL ".$ini. " HASTA EL ".$fin;
            }else{
                $titulo=" DEL ".date('d-m-Y');
            }
        @endphp
        <table class="ltable" style="" border="0" width="100%" style="padding-bottom:2px !important">
          
            <tr style="font-size: 11px"  class="fuenteSubtitulo " style=""> 
                <th colspan="11" style="border-color:white;height:35px;text-align: center;border:0 px" width="100%"  >
                   ALIMENTACIÓN PACIENTES {{$titulo}} <br>  {{mb_strtoupper($tipo)}}            
                </th>
             
            </tr>

            <tr style="font-size: 11px"  class="fuenteSubtitulo " style=""> 
                <td colspan="6" style="border-color:white;height:35px;text-align: center;border:0 px" width="100%"  > <b>APROBADO POR: </b> SISTEMA             
                </td>

                @if($f_aprobacion<>0)
                    <td colspan="5" style="border-color:white;height:35px;text-align: center;border:0 px" width="100%"  ><b>FECHA APROBACION: </b>{{date('d-m-Y H:i:s', strtotime($f_aprobacion))}}              
                    </td>
                @else
                    <td colspan="5" style="border-color:white;height:35px;text-align: center;border:0 px" width="100%"  ><b>FECHA IMPRESION: </b>{{date('d-m-Y H:i:s')}}              
                    </td>
                @endif
             
            </tr>
     
          
        </table>
        <div style="margin-top:5px;">


                <table class="ltable"  border="0" width="100%" style="padding-bottom:2px !important">
                    @php
                        $cont=0;
                    @endphp
                    @foreach($datos as $key => $lista)

                        <tr style="font-size: 10px !important;line-height:10px;  "> 
                            
                            <th colspan="6" style="border: 0px; ;border-color: #D3D3D3; text-align: center; line-height:15px; margin-top:1px !important;margin-bottom:1px !important;">HOSPITALIZACION - {{$key}}</th>
                            
                        </tr>

                        <tr style="font-size: 10px !important; background-color: #D3D3D3;line-height:10px; "> 
                            
                            <th width="7%" style="border: 0px; text-align: center">FECHA</th>

                            <th width="10%" style="border: 0px; ;border-color: #D3D3D3; text-align: center; line-height:15px">HORA SOLIC.</th>

                            <th width="25%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">PACIENTE</th>

                            
                        
                            <th width="17%" style="border: 0px; text-align: center">DIETA</th>

                            <th width="30%" style="border: 0px; text-align: center">SOLICTADO</th>

                            <th width="36%" style="border: 0px; text-align: center">OBSERVACIÓN</th>
                    
                        </tr>

                        @foreach($lista as $e=>$dato)   
                            @php
                                $cont=$cont+1;;
                            @endphp         
                            <tbody>                                                                        
                                <tr style="font-size: 9px !important; line-height:22px !important">   
                                    
                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3; vertical-align:middle">
                                    
                                        {{date('d-m-Y', strtotime($dato->fecha_solicita))}}
                                   </td>


                                    
                                    <td align="center" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3; vertical-align:middle">
                                    
                                         {{date('H:i', strtotime($dato->fecha_solicita))}}
                                    </td>

                                        
                                    <td align="left" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3;vertical-align:middle">
                                      {{$dato->paciente}}
                                    
                                    </td>

                                    

                                    <td align="left" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3;vertical-align:middle">
                                    
                                    {{$dato->dieta}}
                                    </td>

                                    <td align="left" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3;vertical-align:middle">
                                    
                                       {{$dato->responsable}}
                                    </td>

                                    <td align="left" style="border-top: 0px;border-left: 0px; border-bottom: 0px;border-center:0px;border-right:0px;border-color: #D3D3D3;vertical-align:middle">
                                        {{$dato->observacion}}
                                    </td>

                                </tr>                                
                            </tbody>
                        @endforeach

                        <tr style="font-size: 10px !important;line-height:64px !important;  "> 
                            
                            <th colspan="6" style="border: 0px; ;border-color: #D3D3D3; text-align: center; line-height:15px; margin-top:1px !important;margin-bottom:1px !important;"><span style="color:white">z</span></th>
                            
                        </tr>

                    @endforeach

                </table>
        </div>

        <p style="font-size: 10px; text-align:center; font-family:sans-serif;"><b  style="font-size: 10px;">TOTAL: {{$cont}}</b></p>
        
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