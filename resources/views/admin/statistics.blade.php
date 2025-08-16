@extends('admin.layouts.app')
@section('content')
    <style>
        .custom-card {
            background-color: #f8f9fa;
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
                <button id="filterBtn" class="btn btn-dark w-100" style="background-color: #e7004d;">Filtrele</button>
            </div>
        </div>

        <!-- Toplamlar ve küçük grafikler -->
        <div class="row mb-5">
            <!-- Toplam Sipariş -->
            <div class="col-md-4 mb-3">
                <div class="card shadow border-0 custom-card">
                    <div class="card-body bg-light text-center">
                        <h5 class="text-dark">Toplam Sipariş</h5>
                        <p class="fs-3 fw-bold text-primary">{{ $totalOrders }}</p>
                        <canvas id="ordersMiniChart" height="80"></canvas>
                    </div>
                </div>
            </div>
            <!-- Toplam Restoran -->
            <div class="col-md-4 mb-3">
                <div class="card shadow border-0 custom-card">
                    <div class="card-body bg-light text-center">
                        <h5 class="text-dark">Toplam Restoran</h5>
                        <p class="fs-3 fw-bold text-primary">{{ $totalRestaurants }}</p>
                        <canvas id="restaurantsMiniChart" height="80"></canvas>
                    </div>
                </div>
            </div>
            <!-- Toplam Kurye -->
            <div class="col-md-4 mb-3">
                <div class="card shadow border-0 custom-card">
                    <div class="card-body bg-light text-center">
                        <h5 class="text-dark">Toplam Kurye</h5>
                        <p class="fs-3 fw-bold text-primary">{{ $totalCouriers }}</p>
                        <canvas id="couriersMiniChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Günlük sipariş platform bazlı grafik -->
        <div class="card shadow border-0 custom-card mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold text-dark mb-4">Günlük Siparişler (Platform Bazlı)</h5>
                <canvas id="orderLineChart" height="400"></canvas>
            </div>
        </div>
    </div>
@endsection

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Veriler
            const orderStats = {!! json_encode($orderStats) !!};
            const labels = {!! json_encode($labels) !!};
            const platforms = {!! json_encode($platforms) !!};
            const totalOrders = {{ $totalOrders }};
            const totalRestaurants = {{ $totalRestaurants }};
            const totalCouriers = {{ $totalCouriers }};

            // Renkler
            const colors = [
                'rgb(231,0,77)', // yemeği sepeti
                'rgb(73,39,179)', // getir
                'rgb(251,157,3)', // trendyol
                'rgba(39,99,241,0.66)', // migros
                'rgba(153, 102, 255, 0.6)', // adisyo
                'rgb(26,20,20)' // telefonsiparis
            ];

            // Platformlar için datasetler
            const datasets = platforms.map((platform, index) => {
                const data = labels.map(label => orderStats[platform][label] ?? 0);
                return {
                    label: platform.charAt(0).toUpperCase() + platform.slice(1),
                    data: data,
                    borderColor: colors[index % colors.length],
                    backgroundColor: colors[index % colors.length].replace('0.6', '0.2'),
                    tension: 0.4,
                    fill: true
                };
            });

            // Grafik: Günlük Platform Bazlı Siparişler
            const ctxLine = document.getElementById('orderLineChart').getContext('2d');
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    height: 200,
                    plugins: {
                        legend: {
                            labels: { color: '#333' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#333' }
                        },
                        x: {
                            ticks: { color: '#333' }
                        }
                    }
                }
            });

            // Mini grafikler (toplamlar için)
            const createMiniDoughnut = (canvasId, value, total, color) => {
                const ctxMini = document.getElementById(canvasId).getContext('2d');
                new Chart(ctxMini, {
                    type: 'doughnut',
                    data: {
                        labels: ['Değer', 'Kalan'],
                        datasets: [{
                            data: [value, Math.max(0, total - value)],
                            backgroundColor: [color, '#e0e0e0'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        },
                        cutout: '70%'
                    }
                });
            };

            // Toplamlar için grafikler
            createMiniDoughnut('ordersMiniChart', totalOrders, 1000, colors[0]);
            createMiniDoughnut('restaurantsMiniChart', totalRestaurants, 100, colors[1]);
            createMiniDoughnut('couriersMiniChart', totalCouriers, 50, colors[2]);

            // Filtreleme butonu
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

            // Eğer URL parametreleri varsa ve sayfa ilk yüklenmişse, SweetAlert göster
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
