<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Garanti Bank 3D Secure Doğrulama</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            color: #30d760;
            margin-bottom: 20px;
        }

        p {
            font-size: 14px;
            color: #02a330;
            margin-bottom: 30px;
        }

        /* Loading spinner */
        .spinner {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #28a745; /* yeşil renk */
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin: 0 auto;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        #threeDSecureContainer {
            display: none; /* içerik gizli, otomatik yönlendirme */
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Garanti Bank 3D Secure</h2>
    <p>3D Secure doğrulama sayfasına yönlendiriliyorsunuz...</p>
    <div class="spinner"></div>

    <div id="threeDSecureContainer">
        {!! base64_decode($threeDSHtmlContent) !!}
    </div>
</div>

<script>
    // Otomatik form submit / yönlendirme
    const form = document.querySelector('#threeDSecureContainer form');
    if(form){
        setTimeout(() => {
            form.submit();
        }, 1500); // 1.5 saniye bekleyip gönder
    }
</script>
</body>
</html>
