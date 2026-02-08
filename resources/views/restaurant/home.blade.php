@extends('restaurant.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/home/index.css')}}">
    <link rel="stylesheet" href="{{asset('css/pages/admin/home/index.css')}}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/chartjs-adapter-moment.min.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }

        .stats-card {
            margin-bottom: 20px;
        }
    </style>

    <div class="container-fluid" style="padding-top: 1.5rem">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <div class="w-100 d-flex align-items-center justify-content-between">
                    <form method="GET" action="{{ route('restaurant.filterByDate') }}"
                          class="d-flex  gap-3 align-items-center">
                        <div>
                            <input type="date" class="form-control custom-input" id="start_date" name="start_date"
                                   required>
                        </div>
                        <div>
                            <input type="date" class="form-control custom-input" id="end_date" name="end_date" required>
                        </div>
                        <div class="d-flex align-items-end">
                            <button style="background: #ec691e;color:#fff;font-size: 0.8rem" type="submit"
                                    class="btn custom-btn">
                                <i class="fas fa-calendar-day" style="padding-right:5px"></i>
                                Filtrele
                            </button>
                        </div>
                    </form>
                    <div class="date-filters d-flex align-items-center gap-1 ">
                        <a style="font-size:0.8rem;font-weight: 300;{{request()->date == 'today' ? 'background-color: #e7004d;color:white' : '' }}"
                           href="{{ route('orders.filter', ['date' => 'today']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Bugün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300;{{request()->date == 'yesterday' ? 'background-color: #e7004d;color:white' : '' }}"
                           href="{{ route('orders.filter', ['date' => 'yesterday']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Dün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300;{{request()->date == 'this_week' ? 'background-color: #e7004d;color:white' : '' }}"
                           href="{{ route('orders.filter', ['date' => 'this_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Bu Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300;{{request()->date == 'last_week' ? 'background-color: #e7004d;color:white' : '' }}"
                           href="{{ route('orders.filter', ['date' => 'last_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Geçen Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300;{{request()->date == 'last_month' ? 'background-color: #e7004d;color:white' : '' }}"
                           href="{{ route('orders.filter', ['date' => 'last_month']) }}"
                           class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Geçen Ay</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="orders-section p-4 border-0 rounded-4 bg-white shadow">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                        <h4 class="fw-bold text-dark m-0">Sipariş Paneli</h4>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                            Bugün: <strong>{{ count($tumu) }}</strong>
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-secondary-light text-dark fw-bolder shadow-sm transition-hover">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-white bg-opacity-10 p-2 rounded-3 me-3">
                                        <i class="fa-solid fa-layer-group fs-5"></i>
                                    </div>
                                    <span class="fw-semibold ">Genel Toplam</span>
                                </div>
                                <span class="fs-3 fw-bold">{{ count($tumu) }}</span>
                            </div>
                        </div>

                        @php
                            $platforms = [
                                ['title' => 'Telefon', 'count' => count($telefonsiparis), 'icon' => 'fa-phone', 'color' => '#198754', 'is_img' => false],
                                ['title' => 'GpsYemek', 'count' => count($gpsyemek), 'img' => 'gpsyemek.png', 'is_img' => true],
                                ['title' => 'Getir Yemek', 'count' => count($getiryemek), 'img' => 'getir.png', 'is_img' => true],
                                ['title' => 'Trendyol', 'count' => count($trendyol), 'img' => 'trendyol.png', 'is_img' => true],
                                ['title' => 'Y.Sepeti', 'count' => count($yemeksepeti), 'img' => 'yemeksepeti.png', 'is_img' => true],
                                ['title' => 'Migros', 'count' => $migros, 'img' => 'migros.png', 'is_img' => true],
                            ];
                        @endphp

                        @foreach($platforms as $p)
                            <div class="col-4 col-md-4 col-xl-2">
                                <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 rounded-4 platform-card">
                                    <div class="platform-logo-container mb-4 d-flex align-items-center justify-content-center bg-white shadow-sm rounded-circle mx-auto" style="width: 45px; height: 45px;">
                                        @if($p['is_img'])
                                            <img class="rounded-circle" src="{{ asset('theme/images/platforms/'.$p['img']) }}" style="width: 28px; height: auto;" alt="{{ $p['title'] }}">
                                        @else
                                            <i class="fa-solid {{ $p['icon'] }} fs-5" style="color: {{ $p['color'] }}"></i>
                                        @endif
                                    </div>
                                    <div class="fw-bold fs-5 text-dark">{{ $p['count'] }}</div>
                                    <div class="text-muted  text-primary fw-bold" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $p['title'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Performance Section -->
            <div class="col-lg-6">
                @if($dailyPreparedSpeed->isEmpty() && $dailyHandoverSpeed->isEmpty() && $dailyDeliverySpeed->isEmpty())
                    <div class="alert" style="background: #259a38;color: white">
                        Bu tarih aralığında veri bulunamadı.
                    </div>
                @else
                    <div class="row g-4">
                        <!-- Hazırlanma Hızı -->
                        <div class="col-md-4">
                            <div class="card stats-card shadow-sm border-0">
                                <div class="card-header  text-white text-center"
                                     style="background: #4927b3;color: white">
                                    <h6 class="text-white">Hazırlanma Hızı</h6>
                                </div>
                                <div class="card-body text-center">
                                    <canvas id="preparedSpeedChart" height="150"></canvas>
                                    <p class="mt-3 mb-0">Ortalama: <strong>{{ $stats['prepared']['avg'] }} dk</strong>
                                    </p>
                                    <p>En Hızlı: <strong>{{ $stats['prepared']['min'] }} dk</strong></p>
                                    <p>En Yavaş: <strong>{{ $stats['prepared']['max'] }} dk</strong></p>
                                    <p>Toplam Sipariş: <strong>{{ $stats['prepared']['total_orders'] }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <!-- Teslim Alma Hızı -->
                        <div class="col-md-4">
                            <div class="card stats-card shadow-sm border-0">
                                <div class="card-header text-white text-center"
                                     style="background: #ec691e;color: white">
                                    <h6 class="text-white">Teslim Alma Hızı</h6>
                                </div>
                                <div class="card-body text-center">
                                    <canvas id="handoverSpeedChart" height="150"></canvas>
                                    <p class="mt-3 mb-0">Ortalama: <strong>{{ $stats['handover']['avg'] }} dk</strong>
                                    </p>
                                    <p>En Hızlı: <strong>{{ $stats['handover']['min'] }} dk</strong></p>
                                    <p>En Yavaş: <strong>{{ $stats['handover']['max'] }} dk</strong></p>
                                    <p>Toplam Sipariş: <strong>{{ $stats['handover']['total_orders'] }}</strong></p>
                                </div>
                            </div>
                        </div>

                        <!-- Teslimat Hızı -->
                        <div class="col-md-4">
                            <div class="card stats-card shadow-sm border-0">
                                <div class="card-header text-white text-center"
                                     style="background: #30d760;color: white">
                                    <h6 class="text-white">Teslimat Hızı</h6>
                                </div>
                                <div class="card-body text-center">
                                    <canvas id="deliverySpeedChart" height="150"></canvas>
                                    <p class="mt-3 mb-0">Ortalama: <strong>{{ $stats['delivery']['avg'] }} dk</strong>
                                    </p>
                                    <p>En Hızlı: <strong>{{ $stats['delivery']['min'] }} dk</strong></p>
                                    <p>En Yavaş: <strong>{{ $stats['delivery']['max'] }} dk</strong></p>
                                    <p>Toplam Sipariş: <strong>{{ $stats['delivery']['total_orders'] }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @include('restaurant.partials.home_table')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function createSpeedChart(ctx, value, max, color) {
            return new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [value, max - value],
                        backgroundColor: [color, '#e9ecef'],
                        borderWidth: 0,
                        cutout: '75%'
                    }]
                },
                options: {
                    rotation: -90,
                    circumference: 180,
                    plugins: {
                        legend: {display: false},
                        tooltip: {enabled: false},
                    }
                }
            });
        }

        // Hazırlanma Hızı
        createSpeedChart(
            document.getElementById('preparedSpeedChart'),
            {{ $stats['prepared']['avg'] }},
            60, // Maks dakika
            '#4927b3'
        );

        // Teslim Alma Hızı
        createSpeedChart(
            document.getElementById('handoverSpeedChart'),
            {{ $stats['handover']['avg'] }},
            60,
            '#ec691e'
        );

        // Teslimat Hızı
        createSpeedChart(
            document.getElementById('deliverySpeedChart'),
            {{ $stats['delivery']['avg'] }},
            60,
            '#30d760'
        );
    </script>
@endsection
