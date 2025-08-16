@extends('admin.layouts.app')
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
                            <button style="background: #e7004d;color:#fff;font-size: 0.8rem" type="submit"
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
                    <div class="row g-3">
                        <!-- All Orders Button -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100" style="background: #e7004d">

                                <span class="fw-bold">
                                    <i class="fa-solid fa-box" style="color: #fffdfd;font-size:18px;padding-right:10px"></i>
                                    Tüm Siparişler</span>
                                <span class="badge bg-white text-dark order-number">{{ count($tumu) }}</span>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #0d2646">
                                <i class="fa-solid fa-phone" style="color: #fff;font-size:18px;padding-left:10px"></i>
                                <span class="fw-bold">Telefon Sipariş</span>
                                <span class="badge bg-white text-dark order-number">{{ count($telefonsiparis) }}</span>
                            </button>
                        </div>

                        <!-- Getir Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100" style="background: #4927b3">
                                <img src="{{ asset('theme/images/GetirYemek_Logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="77px"
                                     height="14px" alt="">
                                <span class="badge bg-white text-dark order-number">{{ count($getiryemek) }}</span>
                            </button>
                        </div>
                        <!-- Trendyol Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100" style="background: orangered">
                                <img src="{{ asset('theme/images/trendyolyemek.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="71px"
                                     height="14px" alt="">
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
                                <span class="badge bg-white text-dark order-number">{{ $migros }}</span>
                            </button>
                        </div>
                        <!-- Phone Orders -->

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

            @include('admin.home_table')
        </div>
    </div>
@endsection
