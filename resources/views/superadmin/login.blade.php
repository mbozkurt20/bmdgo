<!DOCTYPE html>
<html lang="tr" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Üst Yönetici Girişi</title>

    <link rel="shortcut icon" type="image/png" href="{{config('site.logo')}}">
    <link href="{{asset('theme/login/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('theme/login/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/pages/restaurants/login/index.css')}}">
</head>

<body>
<div class="login-container">
    <div class="login-box">
        <div class="logo">
            <a href="{{route('restaurant.login')}}">
                <img src="{{config('site.logo')}}" alt="Logo">
            </a>
        </div>

        <h2 class="form-title" id="formTitle">Üst Yönetici Girişi</h2>

        <form method="POST" action="{{route('superadmin.auth')}}">
            @csrf
            <input type="hidden" name="user_type" id="userTypeInput" value="restaurant">

            <div class="mb-3">
                <input type="text" name="email" id="emailInput" class="form-control" placeholder="E-posta Adresiniz" required>
            </div>

            <div class="mb-4">
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Şifreniz" required>
            </div>

            <button type="submit" class="btn btn-login">Giriş Yap</button>
        </form>
    </div>
</div>

<script src="{{asset('theme/login/js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>
