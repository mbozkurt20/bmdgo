@extends('dealer.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Raporlar</h1>

        {{-- Filtre --}}
        <form method="GET" class="row mb-4">
            <div class="col-md-3">
                <label>Başlangıç Tarihi</label>
                <input type="date" name="start_date" class="form-control" value="{{ \Carbon\Carbon::parse($startDate)->toDateString() }}">
            </div>
            <div class="col-md-3">
                <label>Bitiş Tarihi</label>
                <input type="date" name="end_date" class="form-control" value="{{ \Carbon\Carbon::parse($endDate)->toDateString() }}">
            </div>
            <div class="col-md-2 mt-4">
                <button class="btn btn-primary mt-2" type="submit">Filtrele</button>
            </div>
        </form>

        {{-- Cards --}}
        <div class="row">
            @php
                $cards = [
                    ['title' => 'Toplam Sipariş', 'value' => $totalOrders],
                    ['title' => 'Toplam Kurye', 'value' => $totalCouriers],
                    ['title' => 'Toplam Admin', 'value' => $totalAdmins],
                    ['title' => 'Toplam Restoran', 'value' => $totalRestaurants],
                    ['title' => 'Toplam Müşteri', 'value' => $totalCustomers],
                    ['title' => 'Toplam Sub Amount', 'value' => number_format($totalSubAmount, 2)],
                    ['title' => 'Toplam İndirim', 'value' => number_format($totalDiscount, 2)],
                    ['title' => 'Toplam Net Tutar', 'value' => number_format($totalAmount, 2)],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $card['title'] }}</h5>
                            <h3 class="card-text">{{ $card['value'] }}</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Grafik --}}
        <div class="row">
            <div class="col-md-12">
                <canvas id="ordersChart" height="100"></canvas>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('ordersChart').getContext('2d');
        const ordersChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($ordersByDate->toArray())) !!},
                datasets: [{
                    label: 'Günlük Sipariş Sayısı',
                    data: {!! json_encode(array_values($ordersByDate->toArray())) !!},
                    fill: true,
                    borderColor: 'rgba(75,192,192,1)',
                    backgroundColor: 'rgba(75,192,192,0.2)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Tarih Bazlı Sipariş Grafiği'
                    }
                }
            }
        });
    </script>
@endsection
