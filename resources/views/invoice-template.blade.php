<!-- resources/views/invoice-template.blade.php -->

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <title>Fatura</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

        body {
            font-family: 'Montserrat', Arial, sans-serif;
            margin: 40px;
            color: #333;
            background: #f9f9f9;
        }

        .invoice-container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #0d2646;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .logo {
            height: 60px;
        }

        .company-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0d2646;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .invoice-title {
            text-align: center;
            margin-bottom: 10px;
            font-size: 1.5rem;
            font-weight: 600;
            color: #222;
        }

        .invoice-date {
            text-align: center;
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 40px;
        }

        .invoice-details p {
            margin: 6px 0;
            font-size: 1rem;
        }

        .invoice-details strong {
            color: #555;
            width: 150px;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th {
            background-color: #0d2646;
            color: white;
            padding: 12px;
            font-weight: 600;
            text-align: left;
        }

        td {
            padding: 12px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tfoot td {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .total-row {
            background-color: #e8f5e9;
        }
    </style>
</head>

<body>
<div class="invoice-container">
    <header>
        <img src="{{ config('site.invoice_logo') }}" alt="Logo" class="logo" />
        <div class="company-name">{{ env('APP_NAME') }}</div>
    </header>

    <div class="invoice-title">Fatura Detayları</div>
    <div class="invoice-date">{{ now()->format('d.m.Y H:i') }}</div>

    <div class="invoice-details">
        <p class="mb-1"><strong>Sipariş Numarası:</strong> {{ $order->tracking_id }}</p>
        <p class="mb-1"><strong>Müşteri Adı:</strong> {{ $order->full_name }}</p>
        <p class="mb-1"><strong>Adres:</strong> {{ $order->address }}</p>
        <p class="mb-1"><strong>Ödeme Yöntemi:</strong> {{ $order->payment_method }}</p>
        <p class="mb-1"><strong>Sipariş Zamanı:</strong> {{ $date }}</p>
    </div>

    <h3>Ürünler</h3>
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
        <tfoot>
        <tr class="total-row">
            <td colspan="3" style="text-align:right;">Toplam Tutar:</td>
            <td>{{ number_format($order->amount, 2, ',', '.') }} TL</td>
        </tr>
        </tfoot>
    </table>
</div>
</body>

</html>
