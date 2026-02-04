@extends('restaurant.layouts.app')

@section('content')
    <style>
        /* Ana Panel Kutusu */
        .main-content-wrapper { background-color: #ffffff; border-radius: 20px; border: 1px solid #edf2f7; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }

        /* Kart Tasarımı */
        .market-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; transition: all 0.3s ease; overflow: hidden; display: flex; flex-direction: column; }
        .market-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.07); }
        .card-header { display: flex; align-items: center; justify-content: center; height: 70px; border: none !important; }

        /* Operasyonel Ayarlar Kutusu */
        .op-settings { background: #f8fafc; border-radius: 12px; padding: 15px; margin-top: 15px; border: 1px solid #f1f5f9; }
        .op-title { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; display: block; text-align: center; }

        /* Form Elemanları */
        .form-label { font-size: 0.75rem; color: #475569; margin-bottom: 4px; font-weight: 700; }
        .form-control-sm { border-radius: 8px; padding: 8px 12px; border: 1.5px solid #e2e8f0 !important; background-color: #ffffff !important; font-size: 0.85rem; }

        /* Switch Tasarımı */
        .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
        .switch-text { font-size: 0.8rem; font-weight: 600; color: #1e293b; }

        .btn-update { border-radius: 10px; padding: 12px; font-size: 0.85rem; letter-spacing: 0.5px; border: none; transition: all 0.2s; margin-top: 20px; }
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

        @if(session()->has('error'))
            <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center" style="background: #dcfce7; color: #166534; border-radius: 12px;">
                <i class="fas fa-check-circle me-2"></i> {{ session()->get('error') }}
            </div>
        @endif

        <div class="row p-4 main-content-wrapper">
            @php
                $platforms = [
                    'gpsyemek' => ['name' => 'GPS Yemek', 'color' => '#14147a', 'bg' => '#ead9e1', 'img' => 'gpsyemek.png'],
                    'getir' => ['name' => 'Getir', 'color' => '#ffffff', 'bg' => '#5d3ebc', 'btn_bg' => '#5d3ebc', 'img' => 'GetirYemek_Logo.png'],
                    'yemeksepeti' => ['name' => 'Yemeksepeti', 'color' => '#ffffff', 'bg' => '#fb0050', 'btn_bg' => '#fb0050', 'img' => 'yemeksepeti.png'],
                    'trendyol' => ['name' => 'Trendyol', 'color' => '#ffffff', 'bg' => '#ff6000', 'btn_bg' => '#ff6000', 'img' => 'trendyolyemek.png'],
                    'migros' => ['name' => 'Migros', 'color' => '#ffffff', 'bg' => '#ff6000', 'btn_bg' => '#ff6000', 'img' => null]
                ];
            @endphp

            @foreach($platforms as $key => $info)
                @php $val = json_decode($restaurant->$key); @endphp
                <div class="col-xl-4 col-lg-6 mb-4">
                    <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}" class="h-100">
                        @csrf
                        <input type="hidden" name="platform" value="{{ $key }}">
                        <div class="market-card h-100">
                            <div class="card-header" style="background: {{ $info['bg'] }};">
                                @if($info['img'])
                                    <img src="{{ asset('theme/images/' . $info['img']) }}" style="height: {{ $key == 'gpsyemek' ? '30px' : '25px' }}">
                                @else
                                    <h4 class="mb-0 text-white fw-bold">MİGROS YEMEK</h4>
                                @endif
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <div class="api-fields">
                                    @if($key == 'gpsyemek')
                                        <label class="form-label">API Key</label>
                                        <input type="text" class="form-control form-control-sm" name="data[api_key]" value="{{ $val->api_key ?? $restaurant->gpsyemek_api_key }}" required>
                                    @else
                                        <label class="form-label">Restaurant ID</label>
                                        <input type="text" class="form-control form-control-sm mb-2" name="data[information][restaurantId]" value="{{ $val->information->restaurantId ?? '' }}" required>

                                        @if($key == 'getir')
                                            <label class="form-label">Secret Key</label>
                                            <input type="text" class="form-control form-control-sm" name="data[information][secretKey]" value="{{ $val->information->secretKey ?? '' }}" required>
                                        @elseif($key == 'yemeksepeti' || $key == 'migros')
                                            <label class="form-label">Chain ID</label>
                                            <input type="text" class="form-control form-control-sm" name="data[information][chainId]" value="{{ $val->information->chainId ?? '' }}" required>
                                            @if($key == 'migros')
                                                <label class="form-label mt-2">API Key</label>
                                                <input type="text" class="form-control form-control-sm" name="data[information][apiKey]" value="{{ $val->information->apiKey ?? '' }}" required>
                                            @endif
                                        @elseif($key == 'trendyol')
                                            <label class="form-label">Api Key</label>
                                            <input type="text" class="form-control form-control-sm mb-2" name="data[information][apiKey]" value="{{ $val->information->apiKey ?? '' }}" required>
                                            <label class="form-label">Supplier ID</label>
                                            <input type="text" class="form-control form-control-sm mb-2" name="data[information][supplierId]" value="{{ $val->information->supplierId ?? '' }}" required>
                                            <label class="form-label">API Secret Key</label>
                                            <input type="text" class="form-control form-control-sm" name="data[information][apiSecretKey]" value="{{ $val->information->apiSecretKey ?? '' }}" required>
                                        @endif
                                    @endif
                                </div>

                                <div class="op-settings">
                                    <span class="op-title">Çalışma Parametreleri</span>

                                    <div class="d-flex justify-content-between mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="data[status]" value="true" id="st_{{$key}}" {{ ($val->status ?? true) ? 'checked' : '' }}>
                                            <label class="switch-text ms-1" for="st_{{$key}}">Mağaza Açık</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="data[otomatikOnay]" value="true" id="auto_{{$key}}" {{ ($val->otomatikOnay ?? false) ? 'checked' : '' }}>
                                            <label class="switch-text ms-1" for="auto_{{$key}}">Oto. Onay</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Hazırlama Süresi (Dakika)</label>
                                        <input type="number" class="form-control form-control-sm" name="data[service]" value="{{ $val->service ?? 30 }}">
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label">Zil Notu</label>
                                            <input type="text" class="form-control form-control-sm" name="data[doNotKnock]" value="{{ $val->doNotKnock ?? 'Zil Çalma' }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Temassız Notu</label>
                                            <input type="text" class="form-control form-control-sm" name="data[dropOffAtDoor]" value="{{ $val->dropOffAtDoor ?? 'Temassız Teslimat' }}">
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label class="form-label">Eko-Servis Metni</label>
                                            <input type="text" class="form-control form-control-sm" name="data[isEcoFriendly]" value="{{ $val->isEcoFriendly ?? 'Servis İstemiyorum' }}">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-update w-100 fw-bold text-white" style="background: {{ $info['btn_bg'] ?? $info['bg'] }}; color: {{ $key == 'gpsyemek' ? '#000 !important' : '#fff' }}">
                                    {{ $info['name'] }} Güncelle
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endsection
