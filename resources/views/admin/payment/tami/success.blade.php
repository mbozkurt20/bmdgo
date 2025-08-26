{{-- resources/views/payment/success.blade.php --}}
    <!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Başarılı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f0f8ff;
        }
        h1 {
            color: #28a745;
            font-size: 48px;
        }
        pre {
            text-align: left;
            display: inline-block;
            margin-top: 20px;
            padding: 15px;
            background: #e9ecef;
            border-radius: 8px;
            overflow-x: auto;
        }
    </style>
    <script>
        // 4 saniye sonra admin sayfasına yönlendir
        setTimeout(function() {
            window.location.href = '/admin';
        }, 4000);
    </script>
</head>
<body>
<h1>Ödeme Başarılı ✅</h1>

<p>Yönlendiriliyorsunuz...</p>
</body>
</html>
