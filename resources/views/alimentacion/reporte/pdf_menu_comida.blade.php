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
                <th colspan="11" style="border-color:white;height:35px;text-align: center;border:0 px" width="100%"  >MENÚ DE ALIMENTACIÓN <br>
                DESDE {{date('d-m-Y',strtotime($fecha_ini))}} HASTA  {{date('d-m-Y',strtotime($fecha_fin))}}<br><br>
              
                </th>
            
            </tr>

            
          
        </table>
        <div style="margin-top:5px;">
            <table class="ltable"  border="0" width="100%" style="padding-bottom:2px !important">
                @foreach($lista_final_agrupada as $key => $info)

                    <tr style="font-size: 10px !important; background-color: #white;line-height:10px; "> 
                            
                        <th width="10%" colspan="2" style="border: 0px; ;border-color: #D3D3D3; text-align: center; line-height:15px">{{$key}}</th>

                    </tr>

                    <tr style="font-size: 10px !important; background-color: #D3D3D3;line-height:15px; "> 
                   
                        <th width="15%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">ALIMENTO</th>

                        <th width="15%" style="border: 0px; ;border-color: #D3D3D3; text-align: center">MENÚ</th>
                    </tr>
                
                    <tbody>
                        
                        @if(isset($lista_final_agrupada))
                        
                            @foreach($info as $e=>$dato)
                                <tr style="font-size: 10px !important; line-height:15px">                                    
                                    
                                   
                                    <td align="center" style="border-color: #D3D3D3">
                                        {{$dato->alimento->descripcion}}
                                    </td>
                                    
                                    <td align="left" style="border-color: #D3D3D3">
                                        <ul>
                                            @foreach($dato->detalle as $detalle_menu)
                                                <li>{{$detalle_menu->descripcion}}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                                
                            @endforeach		
                        @endif
                    </tbody>

                @endforeach
                
            </table>
        </div>

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