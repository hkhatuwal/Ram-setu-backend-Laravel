<!doctype>
<html>
    <head>
    <title>RaamSetu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ url('public/image/logo-icon.png') }}">
    <link href="{{ url('public/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('public/css/font-awesome.css') }}" rel="stylesheet" />
    <script src="{{ url('public/js/jquery.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ url('public/css/login-index.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('public/css/login-style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('public/css/login-media.css') }}">
    <style type="text/css">
    
  </style>  
    </head>
    <body>
    <div class="main-top">
        <div class="bg-color" style="background-color: #b50d0d14;">
            <!-- middle div start -->
            <div class="logo" style="visibility: hidden">
                <img src="{{ url('public/image/logo.png') }}" width="200px" height="200px">
            </div>
            <!--  -->
            <div class="land3">
                <div class="bg-div">
                    <div class="bg-image">
                        <div class="row top-row">
                            <div class="col-md-12 col-xs-12 col-sm-12 top-left-bg">
                              <div class="login-div">
                              <div class="login"><img src="{{ url('public/image/logo.png') }}" width="200px" height="200px"></div>
                         
                                <form id="UserLoginForm" method="post" accept-charset="utf-8" class="form-horizontal" method="POST" action="{{ route('login') }}">
                                    {{ csrf_field() }}

                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                                    <input id="email" type="email" placeholder="E-Mail Address" class="form-control" name="email" value="{{ old('email') }}"  data-toggle="tooltip" required="required" autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong id="error_found">{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                                    <input id="password" type="password" placeholder="Password"  required="required" class="form-control" name="password" >

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                    </div>
                                    <button type="submit" value="Login" class="btn btn-primary  form-control" id="userLogin"><i class="btn_font fa fa-sign-in" style="padding-right: 3px;" ></i> Login</button>
                                                            
                                </form>     
                              </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div align="center" class="copyright">
                </div>
            </div>
        </div>
    </div>
    </body>
</html>

