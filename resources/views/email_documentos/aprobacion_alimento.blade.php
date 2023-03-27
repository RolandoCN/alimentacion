<!DOCTYPE html>
<html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title></title>
    <!-- Bootstrap -->
    <link href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- stylo formato letras --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


    </head>
    <body >
       
        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 col-md-offset-4" style="border:5px solid #fff;height:auto;margin-top: 8%;border-radius: 10px;box-shadow: 2px 2px 13px #999;float:center">
            
            <div class="panel" style="background:white;padding: 5%;text-center;float:center ">
                
                <h2>HOSPITAL DR. NAPOLEÓN DÁVILA CÓRDOVA</h2>
                <hr class="bg-black">

                <h3> APROBACIÓN DE {{$comida}} del día {{$fecha_apr}}</h3>

                <p>Usted ha recibido el documento de aprobación de  {{$comida}} del día {{$fecha_apr}} </p>

                <p><b>Correo generado desde sistema, contacto a {{$correo}}</b></p>
                
               
            </div>
            
        </div>
       
    </body>
</html>