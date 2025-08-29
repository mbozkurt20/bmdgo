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
            <img src="{{config('site.logo')}}" alt="Logo">
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

        <h2 class="form-title" id="formTitle">Restaurant Girişi</h2>

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
    const formTitle = document.getElementById('formTitle');

    const routeMap = {
        admin: "{{ route('admin.auth') }}",
        restaurant: "{{ route('restaurant.auth') }}"
    };

    const testMode = {{ env('TEST_MODE') ? 'true' : 'false' }};

    function setTestCredentials(type) {
        if (!testMode) return;

        if (type === 'admin') {
            emailInput.value = 'test@admin.com';
            passwordInput.value = 'test';
        } else if (type === 'restaurant') {
            emailInput.value = 'test@restaurant.com';
            passwordInput.value = 'test';
        }
    }

    function updateFormTitle(type) {
        const titles = {
            admin: 'Yönetici Girişi',
            restaurant: 'Restaurant Girişi'
        };
        formTitle.textContent = titles[type] || 'Giriş';
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
            updateFormTitle(selectedType); // ← bunu ekle
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
