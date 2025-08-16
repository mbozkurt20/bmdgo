@extends('admin.layouts.app')
@section('content')
    <style>
        .custom-card {
            background-color: #f8f9fa;
            border-left: 6px solid #e7004d;
        }
        .card-title {
            color: #0d2646;
        }
        .highlight-text {
            font-size: 2rem;
            font-weight: 700;
            color: #e7004d;
        }
    </style>

    <div class="card card-body py-4">
        <h2 class="mb-4 text-dark fw-bold">İstatistikler</h2>

        <!-- Tarih Filtreleri -->
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Başlangıç Tarihi</label>
                <input type="date" id="startDate" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Bitiş Tarihi</label>
                <input type="date" id="endDate" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button id="filterBtn" class="btn w-100" style="background-color: #e7004d; color: white;">Filtrele</button>
            </div>
        </div>

        <!-- Kartlar -->
        <div class="row mb-5">
            @php
                $cards = [
                    ['title' => 'Toplam Sipariş', 'value' => $totalOrders, 'canvas' => 'ordersMiniChart'],
                    ['title' => 'Toplam Restoran', 'value' => $totalRestaurants, 'canvas' => 'restaurantsMiniChart'],
                    ['title' => 'Toplam Kurye', 'value' => $totalCouriers, 'canvas' => 'couriersMiniChart'],
                ];
            @endphp

            @foreach($cards as $card)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm custom-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $card['title'] }}</h5>
                            <p class="highlight-text">{{ $card['value'] }}</p>
                            <canvas id="{{ $card['canvas'] }}" height="80"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Çizgi Grafik -->
        <div class="card shadow-sm custom-card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Günlük Siparişler (Platform Bazlı)</h5>
                <canvas id="orderLineChart" height="400"></canvas>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const orderStats = {!! json_encode($orderStats) !!};
        const labels = {!! json_encode($labels) !!};
        const platforms = {!! json_encode($platforms) !!};
        const totalOrders = {{ $totalOrders }};
        const totalRestaurants = {{ $totalRestaurants }};
        const totalCouriers = {{ $totalCouriers }};

        const mainColors = ['#e7004d', '#0d2646', '#fb9d03', '#2763f1', '#9966ff', '#1a1414'];

        // Line Chart
        const datasets = platforms.map((platform, i) => ({
            label: platform.charAt(0).toUpperCase() + platform.slice(1),
            data: labels.map(label => orderStats[platform][label] ?? 0),
            borderColor: mainColors[i % mainColors.length],
            backgroundColor: mainColors[i % mainColors.length] + '33',
            fill: true,
            tension: 0.4
        }));

        new Chart(document.getElementById('orderLineChart').getContext('2d'), {
            type: 'line',
            data: { labels: labels, datasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#0d2646' } },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y} sipariş`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#0d2646' },
                        grid: { color: '#ddd' }
                    },
                    x: {
                        ticks: { color: '#0d2646' },
                        grid: { display: false }
                    }
                }
            }
        });

        // Mini Doughnut
        const createMiniDoughnut = (canvasId, value, max, color) => {
            new Chart(document.getElementById(canvasId), {
                type: 'doughnut',
                data: {
                    labels: ['Tamamlanan', 'Kalan'],
                    datasets: [{
                        data: [value, Math.max(0, max - value)],
                        backgroundColor: [color, '#f0f0f0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '75%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    responsive: false
                }
            });
        };

        createMiniDoughnut('ordersMiniChart', totalOrders, 1000, mainColors[0]);
        createMiniDoughnut('restaurantsMiniChart', totalRestaurants, 100, mainColors[1]);
        createMiniDoughnut('couriersMiniChart', totalCouriers, 50, mainColors[2]);

        // Filtreleme
        document.getElementById('filterBtn').addEventListener('click', () => {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            if (start && end) {
                const url = new URL(window.location.href);
                url.searchParams.set('start_date', start);
                url.searchParams.set('end_date', end);
                window.location.href = url.toString();
            }
        });

        // SweetAlert
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('filter')) {
            Swal.fire({
                icon: 'success',
                title: 'Filtrelendi',
                text: 'Filtre uygulandı.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
</script>
