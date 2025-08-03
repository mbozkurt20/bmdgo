<!DOCTYPE html>
<html lang="tr" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{env('APP_NAME')}} - Restaurant Girişi</title>

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

        <h2 class="form-title">Restaurant Girişi</h2>

        <div class="user-type-selector">
            <p class="selector-label">Giriş Türünü Seçin:</p>
            <div class="button-group" id="userTypeButtons">
                <button type="button" data-type="admin" class="type-btn">🗯️ Yönetici</button>
                <button type="button" data-type="restaurant" class="type-btn active">🍽️ Restaurant</button>
            </div>
        </div>

        <form id="loginForm" method="POST" action="">
            @csrf
            <input type="hidden" name="user_type" id="userTypeInput" value="restaurant">

            <div class="mb-3">
                <input type="email" name="email" id="emailInput" class="form-control" placeholder="E-posta Adresiniz" required>
            </div>

            <div class="mb-4">
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Şifreniz" required>
            </div>

            <button type="submit" class="btn btn-login">Giriş Yap</button>
        </form>

        <a href="#" class="download-button"> Masaüstü Uygulamasını İndir</a>
    </div>
</div>

<script>
    const typeButtons = document.querySelectorAll('.type-btn');
    const userTypeInput = document.getElementById('userTypeInput');
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');

    const routeMap = {
        admin: "{{ route('admin.auth') }}",
        restaurant: "{{ route('restaurant.auth') }}"
    };

    const testMode = {{ env('TEST_MODE') ? 'true' : 'false' }};

    function setTestCredentials(type) {
        if (!testMode) return;

        if (type === 'admin') {
            emailInput.value = 'admin@admin.com';
            passwordInput.value = 'test';
        } else if (type === 'restaurant') {
            emailInput.value = 'test@restaurant.com';
            passwordInput.value = 'test';
        }
    }

    // Sayfa yüklendiğinde restaurant varsayılan olarak seçilsin ve test bilgileri ayarlansın
    document.addEventListener('DOMContentLoaded', () => {
        setTestCredentials('restaurant');
    });

    typeButtons.forEach(button => {
        button.addEventListener('click', () => {
            typeButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const selectedType = button.getAttribute('data-type');
            userTypeInput.value = selectedType;

            setTestCredentials(selectedType);
        });
    });

    loginForm.addEventListener('submit', function(e) {
        const selectedType = userTypeInput.value;
        if (!selectedType) {
            e.preventDefault();
            alert('Lütfen giriş türünü seçiniz.');
            return;
        }
        this.action = routeMap[selectedType];
    });
</script>

<script src="{{asset('theme/login/js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>
