<!DOCTYPE html>
<html lang="tr" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('site.name')}} - Partner Girişi</title>

    <link rel="shortcut icon" type="image/png" href="{{config('site.logo')}}">
    <link href="{{asset('theme/login/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('theme/login/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/pages/restaurants/login/index.css')}}">
    <style>
        .custom-alert {
            padding: 10px 14px;
            border-radius: 12px;
            margin: 10px 0;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        .custom-alert.success {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
        }

        .custom-alert.error {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            color: #fff;
        }

        .close-btn {
            position: absolute;
            top: 6px;
            right: 8px;
            font-size: 16px;
            color: rgba(255,255,255,0.8);
            cursor: pointer;
            transition: color 0.2s;
        }
        .close-btn:hover {
            color: #fff;
        }

        /* küçük animasyon */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>
<div class="login-container">
    <div class="login-box">
        <div class="logo">
            <a href="{{route('restaurant.login')}}">
                <img src="{{config('site.logo')}}" alt="Logo">
            </a>
        </div>

        @if(session()->has('message'))
            <div class="custom-alert success">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('message') }}</span>
            </div>
        @endif

        @if(session()->has('test') )
            <div class="custom-alert error">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('test') }}</span>
            </div>
        @endif

        <h2 class="form-title" id="formTitle">Partner  Girişi</h2>

        <form method="POST" action="{{route('dealer.auth')}}">
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
