@extends('restaurant.layouts.app')

@section('content')
    <style>



        /* Ana Panel Kutusu */
        .main-content-wrapper {
            background-color: #ffffff;
            border-radius: 20px;
            border: 1px solid #edf2f7;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }

        /* Kart Tasarımı */
        .market-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .market-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.07);
            border-color: #cbd5e1;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 70px;
            border: none !important;
        }

        /* Form Elemanları */
        .form-label {
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
            font-size: 0.9rem;
            color: #1e293b !important;
        }

        .form-control:focus {
            background-color: #fff !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Butonlar */
        .btn-update {
            border-radius: 10px;
            padding: 12px;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
            transition: all 0.2s;
        }

        .btn-update:hover {
            filter: brightness(0.9);
            transform: scale(0.98);
        }

        /* Bilgi Kutusu */
        .alert-info-custom {
            background-color: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            color: #0369a1;
            font-size: 0.75rem;
            padding: 10px;
            border-radius: 8px;
        }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head integration-header">
            <h2 class="mb-3 me-auto fw-bold text-white">Entegrasyon Yönetimi</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Entegrasyonlar</a></li>
                    <li class="breadcrumb-item active">Güncelle</li>
                </ol>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="background: #dcfce7; color: #166534; border-radius: 12px;">
                <i class="fas fa-check-circle me-2"></i> {{ session()->get('message') }}
            </div>
        @endif

        <div class="row p-4 main-content-wrapper">

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}" class="h-100">
                    @csrf
                    <input type="hidden" name="platform" value="gpsyemek">
                    <div class="market-card h-100 d-flex flex-column">
                        <div class="card-header" style="background: #ead9e1;">
                            <img src="{{ asset('theme/images/gpsyemek.png') }}" style="height: 30px">
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-3">
                                <label class="form-label fw-bold">API Key</label>
                                <input type="text" class="form-control" name="data[api_key]" value="{{ json_decode($restaurant->gpsyemek)->api_key ?? $restaurant->gpsyemek_api_key }}" required>
                            </div>
                            <div class="alert-info-custom mb-4">
                                <i class="fas fa-info-circle me-1"></i> GPS Yemek için sadece API anahtarı yeterlidir.
                            </div>
                            <input type="hidden" name="data[status]" value="true">
                            <button type="submit" class="btn btn-update w-100 fw-bold mt-auto text-dark" style="background: #ead9e1;">GPS Yemek Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}" class="h-100">
                    @csrf
                    <input type="hidden" name="platform" value="getir">
                    <div class="market-card h-100 d-flex flex-column">
                        <div class="card-header" style="background: #5d3ebc;">
                            <img src="{{ asset('theme/images/GetirYemek_Logo.png') }}" style="height: 25px;">
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Restaurant ID</label>
                                <input type="text" class="form-control" name="data[information][restaurantId]" value="{{ json_decode($restaurant->getir)->information->restaurantId ?? '' }}" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Secret Key</label>
                                <input type="text" class="form-control" name="data[information][secretKey]" value="{{ json_decode($restaurant->getir)->information->secretKey ?? '' }}" required>
                            </div>
                            <input type="hidden" name="data[status]" value="true">
                            @foreach(['otomatikOnay' => 'false', 'service' => '30', 'isEcoFriendly' => 'Servis İstemiyorum', 'doNotKnock' => 'Zil Çalma', 'dropOffAtDoor' => 'Temassız Teslimat'] as $k => $v)
                                <input type="hidden" name="data[{{$k}}]" value="{{$v}}">
                            @endforeach
                            <button type="submit" class="btn btn-update w-100 fw-bold text-white mt-auto" style="background: #5d3ebc;">Getir Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}" class="h-100">
                    @csrf
                    <input type="hidden" name="platform" value="yemeksepeti">
                    <div class="market-card h-100 d-flex flex-column">
                        <div class="card-header" style="background: #fb0050;">
                            <img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height: 20px;">
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Restaurant ID</label>
                                <input type="text" class="form-control" name="data[information][restaurantId]" value="{{ json_decode($restaurant->yemeksepeti)->information->restaurantId ?? '' }}" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Chain ID</label>
                                <input type="text" class="form-control" name="data[information][chainId]" value="{{ json_decode($restaurant->yemeksepeti)->information->chainId ?? '' }}" required>
                            </div>
                            <input type="hidden" name="data[status]" value="true">
                            @foreach(['otomatikOnay' => 'false', 'service' => '30', 'isEcoFriendly' => 'Servis İstemiyorum', 'doNotKnock' => 'Zil Çalma', 'dropOffAtDoor' => 'Temassız Teslimat'] as $k => $v)
                                <input type="hidden" name="data[{{$k}}]" value="{{$v}}">
                            @endforeach
                            <button type="submit" class="btn btn-update w-100 fw-bold text-white mt-auto" style="background: #fb0050;">Yemeksepeti Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}" class="h-100">
                    @csrf
                    <input type="hidden" name="platform" value="trendyol">
                    <div class="market-card h-100 d-flex flex-column">
                        <div class="card-header" style="background: #ff6000;">
                            <img src="{{ asset('theme/images/trendyolyemek.png') }}" style="height: 20px;">
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-2">
                                <label class="form-label fw-bold">Restaurant ID</label>
                                <input type="text" class="form-control mb-2" name="data[information][restaurantId]" value="{{ json_decode($restaurant->trendyol)->information->restaurantId ?? '' }}" required>
                                <label class="form-label fw-bold">Supplier ID</label>
                                <input type="text" class="form-control mb-2" name="data[information][supplierId]" value="{{ json_decode($restaurant->trendyol)->information->supplierId ?? '' }}" required>
                                <label class="form-label fw-bold">API Secret Key</label>
                                <input type="text" class="form-control mb-4" name="data[information][apiSecretKey]" value="{{ json_decode($restaurant->trendyol)->information->apiSecretKey ?? '' }}" required>
                            </div>
                            <input type="hidden" name="data[status]" value="true">
                            @foreach(['otomatikOnay' => 'false', 'service' => '30', 'isEcoFriendly' => 'Servis İstemiyorum', 'doNotKnock' => 'Zil Çalma', 'dropOffAtDoor' => 'Temassız Teslimat'] as $k => $v)
                                <input type="hidden" name="data[{{$k}}]" value="{{$v}}">
                            @endforeach
                            <button type="submit" class="btn btn-update w-100 fw-bold text-white mt-auto" style="background: #ff6000;">Trendyol Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}" class="h-100">
                    @csrf
                    <input type="hidden" name="platform" value="migros">
                    <div class="market-card h-100 d-flex flex-column">
                        <div class="card-header" style="background: #ff6000;">
                            <h4 class="mb-0 text-white fw-bold">MİGROS YEMEK</h4>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="mb-2">
                                <label class="form-label fw-bold">Restaurant ID</label>
                                <input type="text" class="form-control mb-2" name="data[information][restaurantId]" value="{{ json_decode($restaurant->migros)->information->restaurantId ?? '' }}" required>
                                <label class="form-label fw-bold">Chain ID</label>
                                <input type="text" class="form-control mb-2" name="data[information][chainId]" value="{{ json_decode($restaurant->migros)->information->chainId ?? '' }}" required>
                                <label class="form-label fw-bold">API Key</label>
                                <input type="text" class="form-control mb-4" name="data[information][apiKey]" value="{{ json_decode($restaurant->migros)->information->apiKey ?? '' }}" required>
                            </div>
                            <input type="hidden" name="data[status]" value="true">
                            @foreach(['otomatikOnay' => 'false', 'service' => '30', 'isEcoFriendly' => 'Servis İstemiyorum', 'doNotKnock' => 'Zil Çalma', 'dropOffAtDoor' => 'Temassız Teslimat'] as $k => $v)
                                <input type="hidden" name="data[{{$k}}]" value="{{$v}}">
                            @endforeach
                            <button type="submit" class="btn btn-update w-100 fw-bold text-white mt-auto" style="background: #ff6000;">Migros Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
