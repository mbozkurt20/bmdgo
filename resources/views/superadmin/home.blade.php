@extends('superadmin.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/admin/home/index.css')}}">
    <div class="container-fluid">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" >
                <div class="w-100 d-flex align-items-center justify-content-between">
                    <form method="GET" action="{{ route('admin.filterByDate') }}"
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
                                Filtrele</button>
                        </div>
                    </form>
                    <div class="date-filters d-flex align-items-center gap-1 ">
                        <a style="font-size:0.8rem;font-weight: 300" href="{{ route('admin.filter', ['date' => 'today']) }}"
                           class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Bugün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'yesterday']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Dün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'this_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Bu Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'last_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Geçen Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'last_month']) }}" class="date-filter custom-link">
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
                    <style>
                        .minimal-row {
                            background: #ffffff;
                            padding: 20px;
                            border-radius: 15px;
                        }

                        .stat-item {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                            padding: 10px;
                            transition: all 0.3s ease;
                            border-right: 1px solid #f0f0f0; /* Ayırıcı ince çizgi */
                        }

                        .stat-item:last-child {
                            border-right: none;
                        }

                        .stat-value {
                            font-size: 22px;
                            font-weight: 800;
                            color: #2d3436;
                            margin-top: 8px;
                        }

                        .stat-label {
                            font-size: 20px;
                            text-transform: uppercase;
                            letter-spacing: 1px;
                            color: #a0aec0;
                            font-weight: 700;
                        }

                        .platform-icon {
                            height: 52px;
                            width: auto;
                            transition: all 0.3s ease;
                        }

                        .stat-item:hover .platform-icon {
                            filter: grayscale(0%);
                            opacity: 1;
                            transform: scale(1.1);
                        }

                        /* Sipariş varsa sayıyı renklendir */
                        .has-count {
                            color: #ec691e !important;
                        }
                    </style>

                    <div class="minimal-row shadow-sm border">
                        <div class="row align-items: center;">

                            <div class="col">
                                <div class="stat-item">
                                    <span class="stat-label">Toplam</span>
                                    <span class="stat-value {{ count($tumu) > 0 ? 'has-count' : '' }}">{{ count($tumu) }}</span>
                                </div>
                            </div>

                            <div class="col">
                                <div class="stat-item">
                                    <span class="stat-label"><i class="fa-solid fa-phone"></i></span>
                                    <span class="stat-value">{{ count($telefonsiparis) }}</span>
                                </div>
                            </div>

                            <div class="col">
                                <div class="stat-item">
                                    <img src="{{ asset('theme/images/platforms/getir.png') }}" class="platform-icon" alt="Getir">
                                    <span class="stat-value">{{ count($getiryemek) }}</span>
                                </div>
                            </div>

                            <div class="col">
                                <div class="stat-item">
                                    <img src="{{ asset('theme/images/platforms/trendyol.png') }}" class="platform-icon" alt="Trendyol">
                                    <span class="stat-value">{{ count($trendyol) }}</span>
                                </div>
                            </div>

                            <div class="col">
                                <div class="stat-item">
                                    <img src="{{ asset('theme/images/platforms/yemeksepeti.png') }}" class="platform-icon" alt="Yemeksepeti">
                                    <span class="stat-value">{{ count($yemeksepeti) }}</span>
                                </div>
                            </div>

                            <div class="col">
                                <div class="stat-item">
                                    <img src="{{ asset('theme/images/platforms/migros.png') }}" class="platform-icon" alt="Migros">
                                    <span class="stat-value">{{ $migros }}</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Section -->
            <div class="col-lg-6">
                <!-- Satış Performansı -->
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

                <!-- Kurye Performansı -->
                <div class="performance-section">
                    <h4 class="mb-3">Kurye Performansı</h4>
                    <div class="row g-3">
                        <!-- Toplam -->
                        <div class="col-3">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Toplam</p>
                                <p class="card-value text-black fw-bold h6 mb-0">{{ $totalCouriers }} Kurye</p>
                            </div>
                        </div>
                        <!-- Müsait -->
                        <div class="col-3">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Müsait</p>
                                <p class="card-value text-black fw-bold h6 mb-0">{{ $idleCouriers }} Kurye</p>
                            </div>
                        </div>
                        <!-- Serviste -->
                        <div class="col-3">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Serviste</p>
                                <p class="card-value text-black fw-bold h6 mb-0">{{ $serviceCouriers }} Kurye</p>
                            </div>
                        </div>
                        <!-- Molada -->
                        <div class="col-3">
                            <div class="order-card-custom text-center bg-white p-3 rounded shadow-sm">
                                <p class=" text-danger fw-bold mb-2">Molada</p>
                                <p class="card-value text-black fw-bold h6 mb-0">{{ $breakCouriers }} Kurye</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
