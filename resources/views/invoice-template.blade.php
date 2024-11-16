<!-- resources/views/invoice-template.blade.php -->

<!DOCTYPE html>
<html>

<head>
    <title>Fatura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-details {
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="invoice-header">
        <h1>Fatura Detayları</h1>
        <p>{{ now()->format('d.m.Y H:i') }}</p>
    </div>

    <div class="invoice-details">
        <p><strong>Sipariş Numarası:</strong> {{ $order->tracking_id }}</p>
        <p><strong>Müşteri Adı:</strong> {{ $order->full_name }}</p>
        <p><strong>Adres:</strong> {{ $order->address }}</p>
        <p><strong>Toplam Tutar:</strong> {{ $order->amount }} TL</p>
        <p><strong>Ödeme Yöntemi:</strong> {{ $order->payment_method }}</p>
        <p><strong>Sipariş Zamanı:</strong> {{ $date }}</p>
    </div>

    <h3>Ürünler:</h3>
    <table>
        <thead>
            <tr>
                <th>Ürün Adı</th>
                <th>Miktar</th>
                <th>Birim Fiyatı</th>
                <th>Toplam Fiyat</th>
            </tr>
        </thead>
        <tbody>
            @foreach (json_decode($order->items) as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ count($item->items) }}</td>
                    <td>{{ number_format($item->price, 2, ',', '.') }} TL</td>
                    <td>{{ number_format($item->price * count($item->items), 2, ',', '.') }} TL</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
