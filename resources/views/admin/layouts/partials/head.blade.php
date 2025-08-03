
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content>
    <meta name="author" content>
    <meta name="robots" content>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{env('APP_NAME')}} :  Admin Ekranı">
    <meta property="og:title" content="{{env('APP_NAME')}} :  Admin Ekranı">
    <meta property="og:description" content="{{env('APP_NAME')}} :  Admin Ekranı">
    <meta property="og:image" content="images/social-image.png">
    <meta name="format-detection" content="telephone=no">
    <!-- PAGE TITLE HERE -->
    <title>Yönetici - {{env('APP_NAME')}}</title>
    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="{{config('site.logo')}}">
    <link rel="stylesheet" href="{{ asset('theme/css/chartist.min.css') }}">
    <link href="{{ asset('theme/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/css/nice-select.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme/css/select2.min.css') }}">
    <link href="{{ asset('theme/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />


    @if ($_SERVER['REQUEST_URI'] == '/admin')
        <script type="text/javascript">
            $(document).ready(function() {
                setTimeout(function() {
                    location.reload();
                }, 60000)
            });
        </script>
    @endif

    <style>
        .special-button {
            background-color: #0d2646; /* Indigo-600 */
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 2.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .special-button:hover {
            background-color: #112b4c; /* Indigo-700 */
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        /* Genel alert stili */
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            max-width: 350px;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.5s ease forwards;
            cursor: default;
            z-index: 1000;
        }

        /* Başarılı alert */
        .custom-alert.success {
            background: linear-gradient(135deg, #11e65a, #3fd66b);
            border: 1px solid #28c76f;
        }

        /* Hata alert */
        .custom-alert.error {
            background: linear-gradient(135deg, #ff5858, #f09819);
            border: 1px solid #e74c3c;
        }

        .close-btn {
            margin-left: auto; /* Butonu sağa iter */
            color: #fff;
            font-weight: bold;
            font-size: 20px;
            cursor: pointer;
            user-select: none;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: #000000bb;
        }

        /* Alert mesajı */
        .alert-message {
            flex-grow: 1;
            font-size: 16px;
            padding-right: 10px;
        }

        /* Slide in animasyon */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

    </style>
</head>
