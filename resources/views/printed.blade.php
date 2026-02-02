<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sipariş Fişi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        #invoice-POS {
            width: 80mm;
            margin: auto;
            padding: 10px;
        }

        img {
            display: block;
            margin: 0 auto 10px auto;
            height: 75px;
        }

        .restaurant {
            font-weight: bold;
            text-align: center;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .adres, .order_time, .customer-info, .payment-info {
            font-size: 12px;
            margin-bottom: 3px;
        }

        .section {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table td {
            padding: 3px 0;
        }

        .tabletitle {
            font-weight: bold;
            border-bottom: 1px solid #000;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
        }

        .legalcopy {
            font-size: 10px;
            text-align: center;
            margin-top: 10px;
        }

        @media print {
            body {
                width: 80mm;
            }
        }
    </style>
</head>
<body onload="window.print();">

<div id="invoice-POS">
    <img src="{{ config('site.logo') }}" alt="Logo">

    <div class="restaurant">{{ $restaurant->name }}</div>

    <div class="adres"><b>Adres:</b> {{ $restaurant->address }}</div>
    <div class="adres"><b>İletişim:</b> {{ $restaurant->phone }}</div>
    <div class="order_time"><b>Sipariş Zamanı:</b> {{ \Carbon\Carbon::parse($order->created_at)->format('d.m.Y H:i:s') }}</div>
    <div class="order_time"><b>Sipariş No:</b> {{ $order->tracking_id }}</div>

    <div class="section"></div>

    <div class="customer-info"><b>Müşteri Adı:</b> {{ $order->full_name }}</div>
    <div class="customer-info"><b>Adres:</b> {{ $order->address }}</div>
    @if($order->notes)
        <div class="customer-info"><b>Notlar:</b> {{ $order->notes }}</div>
    @endif
    <div class="customer-info"><b>İletişim:</b> {{ $order->phone }}</div>

    <div class="section"></div>

    <table>
        <tr class="tabletitle">
            <td>Adet</td>
            <td>Ürün</td>
            <td>Fiyat</td>
            <td class="text-right">Tutar</td>
        </tr>
        @foreach($items as $item)
            <tr>
                <td>{{ $adet }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->price }} ₺</td>
                <td class="text-right">{{ $tutar }} ₺</td>
            </tr>
        @endforeach
    </table>

    <div class="section"></div>

    <table>
        <tr>
            <td>Ara Toplam:</td>
            <td class="text-right">{{ $order->sub_amount }} ₺</td>
        </tr>
        <tr>
            <td>İndirim:</td>
            <td class="text-right">{{ $order->sub_amount - $order->amount }} ₺</td>
        </tr>
        <tr class="total">
            <td>Toplam:</td>
            <td class="text-right">{{ $order->amount }} ₺</td>
        </tr>
    </table>

    <div class="section"></div>

    <div class="payment-info"><b>Ödeme Şekli:</b> {{ $order->payment_method }}</div>

    <div class="section"></div>

    <div class="legalcopy">
        - {{ env('APP_NAME') }} Bizi Tercih Ettiğiniz İçin Teşekkür Ederiz -
    </div>
</div>

</body>
</html>
