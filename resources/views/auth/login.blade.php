<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SisAlim | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="../../bower_components/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="../../bower_components/font-awesome/css/font-awesome.min.css">

    <link rel="stylesheet" href="../../bower_components/Ionicons/css/ionicons.min.css">

    <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">


    <style>
        .login-page,
        .register-page {
            height: 90vh;
        }
        .login-logo, .register-logo {
          margin-bottom: 15px
        }
    </style>
</head>


<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="../../index2.html"><b style="font-size: 16px; color:black ">Hospital General Dr. Napoleón Dávila Córdova</a>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">SisAlim</p>
            <form action="{{ route('login') }}" method="post">
              @csrf
                <div class="form-group has-feedback">
                  <input id="tx_login" type="text" class="form-control @error('tx_login') is-invalid @enderror" name="tx_login" value="{{ old('tx_login') }}" required autocomplete="tx_login" autofocus placeholder="Usuario">
                  <span class="glyphicon glyphicon-envelope form-control-feedback" ></span>
                  @error('tx_login')
                    <span class="invalid-feedback" role="alert" style="color:red; font-size:12px">
                        <strong >{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                <div class="form-group has-feedback">
                  <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Contraseña">
                  <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                  @error('password')
                    <span class="invalid-feedback" role="alert" style="color:red; font-size:12px">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror

                </div>
                <div class="row">
                    
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Ingresar</button>
                    </div>

                </div>
            </form>
           
        </div>

    </div>


    <script src="../../bower_components/jquery/dist/jquery.min.js"></script>

    <script src="../../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

   
</body>

</html>
