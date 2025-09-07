@extends('admin.layouts.app')
@section('content')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        .page-header {
            background: #07004d;
            color: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(7,0,77,0.2);
        }

        .page-header h2 {
            font-weight: 700;
            margin: 0;
        }



        .card-custom {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 24px;
        }

        .card-custom h5 {
            font-weight: 600;
            color: #07004d;
        }

        .btn-primary {
            background-color: #ec691e;
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .bg-primary2 {
            background: #259a38;
            color: white;
        }
        .btn-primary:hover {
            background-color: #ca3768;
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background: #259a38;
            color: white;
        }

        .table tfoot {
            background: #f1f1f1;
        }

        .summary-box {
            background: #ec691e;
            color: white;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            font-weight: 600;
            margin-top: 16px;
        }
    </style>

    <div class="container-fluid">

        <!-- Sayfa Başlık -->
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kurye Raporu</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Kuryeler</a></li>
                    <li class="breadcrumb-item"><a href="#">{{$courier->name}}</a></li>
                </ol>
            </div>
        </div>

        <!-- Tarih Aralığı -->
        <form method="GET" class="card-custom p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" class="form-control"
                           value="{{ request('start_date', \Carbon\Carbon::parse($startDate)->toDateString()) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Bitiş Tarihi</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ request('end_date', \Carbon\Carbon::parse($endDate)->toDateString()) }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filtrele</button>
                </div>
            </div>
        </form>

        <!-- Kurye Bilgileri -->
        <div class="card-custom p-4 d-flex justify-content-between flex-wrap">
            <div>
                <h5>{{$courier->name}}</h5>
                <h6 >{{$courier->phone}}</h6>
            </div>
            @if ($courier->price_type == 'fixed')
                <div class="text-end">
                    <h6 class="fw-bold">Sabit Ücret</h6>
                    <span class="badge bg-primary2 fs-6">{{ $courier->fixed_price }} ₺</span>
                </div>
            @endif
        </div>

        <!-- Sipariş Tablosu -->
        <div class="card-custom p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>Platform</th>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Telefon</th>
                        <th>Tutar</th>
                        <th>Ödeme Yöntemi</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="font-weight-bold">{{$loop->iteration}}</td>
                            <td  class="font-weight-bold text-black">{{$order->platform}}</td>
                            <td  class="font-weight-bold text-black">{{$order->tracking_id}}</td>
                            <td  class="font-weight-bold text-black">{{$order->full_name}}</td>
                            <td  class="font-weight-bold text-black">{{$order->phone}}</td>
                            <td  class="font-weight-bold text-black"><strong>{{ $order->amount }} ₺</strong></td>
                            <td  class="font-weight-bold text-black">
                                @if($order->payment_method == "Kapıda Ticket ile Ödeme") Ticket
                                @elseif($order->payment_method == "Kapıda Nakit ile Ödeme") Nakit
                                @elseif($order->payment_method == "Kapıda Kredi Kartı ile Ödeme") Kredi Kartı
                                @else {{$order->payment_method}}
                                @endif
                            </td>
                            <td  class="font-weight-bold text-black">
                                @switch($order->status)
                                    @case('DELIVERED')
                                        Teslim Edildi
                                    @break
                                    @case('HANDOVER')
                                        Kuryede Yolda
                                    @break
                                    @case('UNSUPPLIED')
                                    İptal Edildi
                                    @break
                                @endswitch
                            </td>
                            <td  class="font-weight-bold text-black">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Bu tarih aralığında sipariş bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                    <tfoot>
                    <tr class="fw-bold text-center bg-primary2">
                        <td colspan="3">
                            Toplam Sipariş: {{ $summary['order_count'] }}
                        </td>
                        <td colspan="2">
                            Nakit ({{ $summary['cash_orders'] }}):
                            {{ number_format($totals['cash'], 2) }} ₺
                        </td>
                        <td colspan="2">
                            Kredi Kartı ({{ $summary['card_orders'] }}):
                            {{ number_format($totals['credit_card'], 2) }} ₺
                        </td>
                        <td colspan="2">
                            Ticket ({{ $summary['ticket_orders'] }}):
                            {{ number_format($totals['ticket'], 2) }} ₺
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Genel Toplam -->
        <div class="summary-box">
            Genel Toplam: {{ number_format($totals['overall'], 2) }} ₺
            <br>
            <small>
                Hesaplama Tipi:
                {{ $courier->price_type == 'package'
                    ? 'Paket Başı'
                    : 'Km Başı ('.$courier->km_price.' ₺/km)' }}
            </small>
        </div>

    </div>
@endsection
