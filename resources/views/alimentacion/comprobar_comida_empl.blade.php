<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SisAlim | Comprobar</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{asset('plugins/sweetalert/sweetalert.css')}}">
    <link rel="stylesheet" href="{{asset('css/spinners.css')}}">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">

    <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css')}}">

    <link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css')}}">

    <link rel="stylesheet" href="{{asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">

    <link href="{{asset('bower_components/pnotify/dist/pnotify.css')}}" rel="stylesheet">
    <link href="{{asset('bower_components/pnotify/dist/pnotify.buttons.css')}}" rel="stylesheet">
    <link href="{{asset('bower_components/pnotify/dist/pnotify.nonblock.css')}}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css')}}">
    
    <link rel="icon" href="{{asset('logo_icono.png')}}" sizes="32x32" />


    <style>
        .login-page,
        .register-page {
            height: 90vh;
        }

        .login-logo,
        .register-logo {
            margin-bottom: 15px
        }

        .color_aprobacion{
            background: #d7f7e7 !important
        }

        .color_elim{
            background:#e3c8cb !important
        }
    </style>
</head>


<body style="background: #ecf0f5" onload="mueveReloj()">
    <div class="container">
        <div class="col-md-1"></div>
        <div class="col-md-10 " style="margin-top: 80px">
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <a href="{{asset('/')}}"><img class="profile-user-img img-responsive img-circle" src="{{ asset('dist/img/logomsp.png')}}" alt="User profile picture"></a>
                    <h3 class="profile-username text-center" style="font-weight:500">Hospital General Dr. Napoleón Dávila Córdova</h3>
                    <p class="text-muted text-center" style="color:black;font-weight:520">Confirmación Alimentación</p>
                    
                    <div class="" style="margin-bottom:12px;text-align:center" >
                       <input type="text" style="text-align:center;color:black; font-weight:600; font-size:17px"  name="reloj" id="reloj" size="20"  disabled>
                    </div>
                       
                    <ul class="list-group list-group-unbordered">
                        @foreach($alimento as $ali)                        
                            <li class="list-group-item">
                                <b style="margin-left:70px">{{$ali->descripcion}}</b> 
                                <b class="pull-right"  style="margin-right:70px">{{$ali->hora_min}} -- {{$ali->hora_max}}</b>
                            </li>
                        @endforeach
                    </ul>

                    <div style="margin-top:12px; margin-bottom:15px "class="col-md-10 col-md-offset-1">
                        <form id="form_valida" autocomplete="off" method="post"
                        action="">
                            {{ csrf_field() }}
                            <div class="form-group has-feedback">
                                <input id="cedula_func" type="number" class="form-control" name="cedula_func" minlength="1" maxlength="10" onKeyPress="if(this.value.length==10) return false;"  required autocomplete="tx_login" autofocus placeholder="Ingrese su número de cédula">
                                <span class="glyphicon glyphicon-envelope form-control-feedback" ></span>
                              
                            </div>
                            <button type="submit" class="btn btn-primary btn-block" ><b>Consultar</b></button>

                           
                        </form>
                    </div>

                    
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>
    @include('divcargando')

    {{-- modal detalle aprobado --}}
    <div class="modal fade"  data-keyboard="false" data-backdrop="static" id="modal_aprobacion" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">CONFIRMACIÓN DE ASISTENCIA ALIMENTACIÓN</h4>
                    
                </div>
                <div class="modal-body">
                    <div class="row ">
                        
                        <div class="col-md-12 col-sm-12" id="seccion_apr">
    
                         
                                <div id="div_infor_apr">
                                    
                                    <div class="row_">
                                        <div id="sms_errores" class="col-md-12">
                                            
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="nav nav-pills nav-stacked"style="margin-left:0px">
                                                <li style="border-color: white"><a><i class="fa fa-credit-card text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Identificación</b>: <span  id="identificacion_Apr">  </span></a></li>
                                               
                                            </ul>

                                            <ul class="nav nav-pills nav-stacked"style="margin-left:0px">
                                                <li style="border-color: white"><a><i class="fa fa-calendar text-blue"></i> <b class="text-black" style="font-weight: 650 !important"> Fecha</b>: <span  id="fecha_Act"></span></a></li>
                                               
                                            </ul>
                                            <ul class="nav nav-pills nav-stacked"style="margin-left:0px">
                                                <li style="border-color: white"><a><i class="fa fa-briefcase text-blue"></i> <b class="text-black" style="font-weight: 650 !important"> Puesto</b>: <span  id="puesto_Apr"></span></a></li>
                                               
                                            </ul>
                                          
                                        </div>     
                                        <div class="col-md-6">
                                            <ul class="nav nav-pills nav-stacked" style="margin-left:0px">
                                                <li style="border-color: white"><a><i class="fa fa-user text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Usuario:</b> <span  id="user_Apr"> </span></a></li>
                                                
                                            </ul>
                                          

                                            <ul class="nav nav-pills nav-stacked" style="margin-left:0px">
                                                <li style="border-color: white"><a><i class="fa fa-clock-o text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Horario:</b> <span  id="horario_Apr"> </span></a></li>
                                                
                                            </ul>

                                            <ul class="nav nav-pills nav-stacked" style="margin-left:0px">
                                                <li style="border-color: white"><a><i class="fa fa-home text-blue"></i> <b class="text-black" style="font-weight: 650 !important">Area:</b> <span  id="area_Apr"> </span></a></li>
                                                
                                            </ul>
                                        </div>  
                                        
                                       
                                        <div class="table-responsive col-md-12">
                                            <table id="tabla_menu_comida" width="100%"class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th class="text-center">Alimento</th>
                                                        <th class="text-center">Menú</th>
                                                        <th style="min-width: 30%">Opciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="body_tabla">
                                                    <tr>
                                                        <td colspan="4"><center>No hay Datos Disponibles</td>
                                                    </tr>
                                                    
                                                </tbody>
                                              
                                            </table>  
                                        </div> 

                                        <table>
                                            <tbody id="comida_chequeada">
                                              
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                </div>   
                                                                
                               

                                <div class="col-md-12" style="margin-top: 15px !important">
                                    <center>
                                        <button onclick="aprobarConfirmacion()"  id="btn_aprobar" type="button" class="btn btn-success" ><span class="fa fa-check-circle-o"></span> Aprobar</button>
                                        <button onclick="cerrar()"  id="btn_cancelar_" type="button" class="btn btn-danger" ><span class="fa fa-times"></span> Cerrar</button>
                                           
    
                                    </center>
                                </div>
                          
                        </div>
    
                       
                    </div>
    
                   
                </div>
             
            </div>
    
        </div>
    
    </div>
     {{-- fin modal detalle aprobado --}}
    
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js')}}"></script>

    <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
        {{-- PNotify --}}
        <script src="{{asset('bower_components/pnotify/dist/pnotify.js')}}"></script>
        <script src="{{asset('bower_components/pnotify/dist/pnotify.buttons.js')}}"></script>

    <script src="{{ asset('js/alimentacion/consultarComidaEmpl.js?v='.rand())}}"></script>
    <script src="{{asset('plugins/sweetalert/sweetalert.js')}}"></script>

    <script src="{{asset('bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        function mueveReloj(){
            momentoActual = new Date()
            hora = momentoActual.getHours()
            minuto = momentoActual.getMinutes()
            segundo = momentoActual.getSeconds()

            if(hora<10){
                hora="0"+hora
            }

            if(minuto<10){
                minuto="0"+minuto
            }

            if(segundo<10){
                segundo="0"+segundo
            }

            horaImprimible = hora + " : " + minuto + " : " + segundo

            // document.form_reloj.reloj.value = horaImprimible
            $('#reloj').val(horaImprimible)

            setTimeout("mueveReloj()",1000)
        }
    </script>
</body>

</html>
