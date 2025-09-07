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
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'today']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Bugün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'yesterday']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Dün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'this_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Bu Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'last_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Geçen Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('orders.filter', ['date' => 'last_month']) }}"
                           class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Geçen Ay</span>
                        </a>
                    </div>
                    <div class="customer-search mb-sm-0 ">
                        <div class="input-group search-area">
                            <input style="font-size: 0.8rem;width:200px" type="text" class="form-control"
                                   id="custom-filter" placeholder="Sipariş ara..">
                            <span class="input-group-text"><a href="javascript:void(0)"><i
                                        class="flaticon-381-search-2"></i></a></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="orders-section" style="margin-bottom: 10px">
                    <h4>Siparişler</h4>
                    <div class="row g-3">
                        <!-- All Orders Button -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #ec691e">

                                <span class="fw-bold">
                                    <i class="fa-solid fa-box"
                                       style="color: #fffdfd;font-size:18px;padding-right:10px"></i>
                                    Tüm Siparişler</span>
                                <span class="badge bg-white text-dark order-number">{{ count($tumu) }}</span>
                            </button>
                        </div>
                        <!-- Getir Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #4927b3">
                                <img src="{{ asset('theme/images/GetirYemek_Logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="77px"
                                     height="14px" alt="">
                                <span class="fw-bold">Getir Siparişleri</span>
                                <span class="badge bg-white text-dark order-number">{{ count($getiryemek) }}</span>
                            </button>
                        </div>
                        <!-- Trendyol Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: orangered">
                                <img src="{{ asset('theme/images/trendyolyemek.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="71px"
                                     height="14px" alt="">
                                <span class="fw-bold">Trendyol Siparişleri</span>
                                <span class="badge bg-white text-dark order-number">{{ count($trendyol) }}</span>
                            </button>
                        </div>

                        <!-- Yemeksepeti Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #F90050">
                                <img src="{{ asset('theme/images/Yemeksepeti_Logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="79px"
                                     height="15px" alt="">
                                <span class="fw-bold">Yemeksepeti Siparişleri</span>
                                <span class="badge bg-white text-dark order-number">{{ count($yemeksepeti) }}</span>
                            </button>
                        </div>
                        <!-- Migros Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #363A86">
                                <img src="{{ asset('theme/images/MigrosYemek_logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="69px"
                                     height="28px" alt="">
                                <span class="fw-bold">Migros Yemek Siparişleri</span>
                                <span class="badge bg-white text-dark order-number">{{ $migros }}</span>
                            </button>
                        </div>
                        <!-- Phone Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #259a38">
                                <i class="fa-solid fa-phone" style="color: #fff;font-size:18px;padding-left:10px"></i>
                                <span class="fw-bold">Telefonla Gelen Sipariş</span>
                                <span class="badge bg-white text-dark order-number">{{ count($telefonsiparis) }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="performance-section mb-4">
                    <h4 class="mb-3">Satış Performansı</h4>
                    <div class="row g-3">
                        <!-- Ciro -->
                        <div class="col-4">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Ciro</p>
                                <p class="card-value text-black fw-bold h5 mb-0">{{ $formattedExpense }} ₺</p>
                            </div>
                        </div>
                        <!-- Sipariş Sayısı -->
                        <div class="col-4">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Sipariş Sayısı</p>
                                <p class="card-value text-black fw-bold h5 mb-0">{{ count($tumu) }} Adet</p>
                            </div>
                        </div>
                        <!-- Ortalama Sipariş Tutarı -->
                        <div class="col-4">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Ortalama Sipariş Tutarı</p>
                                <p class="card-value text-black fw-bold h5 mb-0">{{ $formattedAverageExpense }} ₺</p>
                            </div>
                        </div>
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
