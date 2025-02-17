
<!DOCTYPE html>
<html lang="tr" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="robots" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ParsKurye">
    <meta property="og:title" content="ParsKurye">
    <meta property="og:description" content="ParsKurye">
    <meta property="og:image" content="https://salero.dexignzone.com/xhtml/social-image.png">
    <meta name="format-detection" content="telephone=no">

    <!-- PAGE TITLE HERE -->
    <title>ParsKurye - Admin</title>

    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="{{asset('theme/login/img/favicon.png')}}">
    <link href="{{asset('theme/login/css/bootstrap-select.min.css')}}" rel="stylesheet">
    <link href="{{asset('theme/login/css/style.css')}}" rel="stylesheet">

</head>

<body class="vh-100">
<div class="page-wraper">

    <!-- Content -->
    <div class="browse-job login-style3">
        <!-- Coming Soon -->
        <div class="bg-img-fix overflow-hidden" style="background:#fff url({{asset('theme/login/img/bg6.jpg')}}); height: 100vh;">
            <div class="row gx-0">
                <div class="col-xl-4 col-lg-5 col-md-6 col-sm-12 vh-100 bg-login ">
                    <div id="mCSB_1" class="mCustomScrollBox mCS-light mCSB_vertical mCSB_inside" style="max-height: 653px;" tabindex="0">
                        <div id="mCSB_1_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                            <div class="login-form style-2">


                                <div class="card-body">
                                    <div class="logo-header">
                                        <a href="{{route('restaurant.login')}}" class="logo"><img style="height: 110px" src="https://i.hizliresim.com/oefzi6j.png" alt="" class="width-230 light-logo"></a>
                                        <a href="{{route('restaurant.login')}}" class="logo"><img style="height: 110px" src="https://i.hizliresim.com/oefzi6j.png" alt="" class="width-230 dark-logo"></a>
                                    </div>

                                    <nav>
                                        <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">

                                            <div class="tab-content w-100" id="nav-tabContent">
                                                <div class="tab-pane fade show active" id="nav-personal" role="tabpanel" aria-labelledby="nav-personal-tab">
                                                    <form method="POST" action="{{ route('admin.auth') }}" class=" dz-form pb-3">
                                                        @csrf
                                                        <h3 class="form-title m-t0">Admin Girişi</h3>
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
                                                            <div class="col-lg-6 text-right">
                                                                <button class="nav-link m-auto btn tp-btn-light btn-primary forget-tab " id="nav-forget-tab" data-bs-toggle="tab"
                                                                        data-bs-target="#nav-forget" type="button" role="tab" aria-controls="nav-forget" aria-selected="false" ></button>
                                                            </div>
                                                        </div>

                                                    </form>

                                                </div>

                                            </div>

                                        </div>
                                    </nav>
                                </div>
                                <div class="card-footer">
                                    <div class=" bottom-footer clearfix m-t10 m-b20 row text-center">
                                        <div class="col-lg-12 text-center">
												<span> © ParsKurye</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div id="mCSB_1_scrollbar_vertical" class="mCSB_scrollTools mCSB_1_scrollbar mCS-light mCSB_scrollTools_vertical" style="display: block;">
                            <div class="mCSB_draggercontainer">
                                <div id="mCSB_1_dragger_vertical" class="mCSB_dragger" style="position: absolute; min-height: 0px; display: block; height: 652px; max-height: 643px; top: 0px;">
                                    <div class="mCSB_dragger_bar" style="line-height: 0px;"></div><div class="mCSB_draggerRail"></div></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Full Blog Page Contant -->
    </div>
    <!-- Content END-->
</div>

<!--**********************************
	Scripts
***********************************-->
<!-- Required vendors -->
<script src="{{asset('theme/login/js/global.min.js')}}"></script>
<script src="{{asset('theme/login/js/bootstrap-select.min.js')}}"></script>
<script src="{{asset('theme/login/js/deznav-init.js')}}"></script>
<script src="{{asset('theme/login/js/custom.js')}}"></script>
<script src="{{asset('theme/login/js/demo.js')}}"></script>
<script src="{{asset('theme/login/js/styleSwitcher.js')}}"></script>
</body>
</html>
