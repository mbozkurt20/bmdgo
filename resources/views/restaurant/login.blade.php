<!DOCTYPE html>
<html lang="tr" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="robots" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="VerGelsin - Restaurant Girişi">
    <meta property="og:title" content="VerGelsin - Restaurant Girişi">
    <meta property="og:description" content="VerGelsin- Restaurant Girişi">
    <meta property="og:image" content="https://salero.dexignzone.com/xhtml/social-image.png">
    <meta name="format-detection" content="telephone=no">

    <title>VerGelsin - Restaurant Girişi</title>

    <link rel="shortcut icon" type="image/png" href="{{asset('theme/login/img/favicon.png')}}">
    <link href="{{asset('theme/login/css/bootstrap-select.min.css')}}" rel="stylesheet">
    <link href="{{asset('theme/login/css/style.css')}}" rel="stylesheet">
    <style>
        .bg-login {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Ekran yüksekliğini kapsar */
        }

        .login-form {
            width: 100%;
            max-width: 400px; /* Formun genişliğini sınırlamak için */
            padding: 20px; /* İçerik boşlukları */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Gölgeler */
            background: #fff; /* Arka plan rengi */
            border-radius: 10px; /* Köşe yuvarlama */
        }

        .download-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .download-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body class="vh-100">
    <div class="page-wraper">
        <div class="browse-job login-style3">
            <div class="bg-img-fix overflow-hidden" style="background:#fff url({{asset('theme/login/img/bg6.jpg')}}); height: 100vh;">
                <div class="row gx-0">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 vh-100 bg-login ">
                        <div class="login-form style-2">
                            <div class="card-body">
                                <div class="logo-header">
                                    <a href="{{route('restaurant.login')}}" class="logo"><img style="height: 80px" src="https://i.hizliresim.com/erh6qtm.png" alt="" class="width-230 light-logo"></a>
                                    <a href="{{route('restaurant.login')}}" class="logo"><img style="height: 80px" src="https://i.hizliresim.com/erh6qtm.png" alt="" class="width-230 dark-logo"></a>
                                </div>
                                <nav>
                                    <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                                        <div class="tab-content w-100" id="nav-tabContent">
                                            <div class="tab-pane fade show active" id="nav-personal" role="tabpanel" aria-labelledby="nav-personal-tab">
                                                <form method="POST" action="{{ route('restaurant.auth') }}" class="dz-form pb-3">
                                                    @csrf
                                                    <h3 class="form-title m-t0">Restaurant Girişi</h3>
                                                    <div class="dz-separator-outer m-b5">
                                                        <div class="dz-separator bg-primary style-liner"></div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <input id="email" type="email" style="border-radius: 5px" placeholder="E-posta Adresiniz" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                        @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <input id="password" type="password" style="border-radius: 5px" placeholder="Şifreniz" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                                        @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group text-left mb-5 forget-main row">
                                                        <div class="col-lg-6">
                                                            <button type="submit" class="btn btn-primary" style="border-radius: 5px;height:50px;background: #fd683e;border-color:#fd683e;">Giriş Yap</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                            <div class="card-footer">
                                <div class="bottom-footer clearfix m-t10 m-b20 row text-center">
                                    <div class="col-lg-12 text-center">
                                        <span></span>
                                        <!-- Masaüstü Uygulamasını İndir Butonu -->
                                        <a href="#" class="download-button" download>Masaüstü Uygulamasını İndir</a>
                                    </div>
                                </div>
                            </div>
                        </div>
						

                    </div>
					
                </div>
				
            </div>
			
        </div>
		
    </div>

	
    <script src="{{asset('theme/login/js/global.min.js')}}"></script>
    <script src="{{asset('theme/login/js/bootstrap-select.min.js')}}"></script>
    <script src="{{asset('theme/login/js/deznav-init.js')}}"></script>
    <script src="{{asset('theme/login/js/custom.js')}}"></script>
    <script src="{{asset('theme/login/js/demo.js')}}"></script>
    <script src="{{asset('theme/login/js/styleSwitcher.js')}}"></script>
</body>
</html>
