@extends('restaurant.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto text-black fw-bold text-white">Entegrasyon Yönetimi</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Entegrasyonlar</a></li>
                    <li class="breadcrumb-item active text-white"><a href="javascript:void(0)">Güncelle</a></li>
                </ol>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm mb-4 w-auto flex">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row p-4 rounded-4" style="background-color: #1a1a1a;">
            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}">
                    @csrf
                    <input type="hidden" name="platform" value="gpsyemek"> <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header py-3" style="background: #ead9e1;">
                            <img src="{{ asset('theme/images/gpsyemek.png') }}" style="height: 30px">
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">API Key</label>
                                <input type="text" class="form-control border-secondary" name="data[api_key]" value="{{ json_decode($restaurant->gpsyemek)->api_key ?? $restaurant->gpsyemek_api_key }}" required>
                            </div>

                            <div class="alert alert-info py-2 small">
                                <i class="fas fa-info-circle me-1"></i> GPS Yemek entegrasyonu için sadece API anahtarı gereklidir.
                            </div>

                            <input type="hidden" name="data[status]" value="true">

                            <button type="submit" class="btn w-100 fw-bold text-dark" style="background: #ead9e1;">GPS Yemek Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}">
                    @csrf
                    <input type="hidden" name="platform" value="getir">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header py-3" style="background: #5d3ebc;">
                            <img src="{{ asset('theme/images/GetirYemek_Logo.png') }}" style="height: 25px;">
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Restaurant ID</label>
                                <input type="text" class="form-control border-secondary" name="data[information][restaurantId]" value="{{ json_decode($restaurant->getir)->information->restaurantId ?? '' }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Secret Key</label>
                                <input type="text" class="form-control border-secondary" name="data[information][secretKey]" value="{{ json_decode($restaurant->getir)->information->secretKey ?? '' }}" required>
                            </div>

                            <input type="hidden" name="data[status]" value="true">
                            <input type="hidden" name="data[otomatikOnay]" value="false">
                            <input type="hidden" name="data[service]" value="30">
                            <input type="hidden" name="data[isEcoFriendly]" value="Servis İstemiyorum">
                            <input type="hidden" name="data[doNotKnock]" value="Zil Çalma">
                            <input type="hidden" name="data[dropOffAtDoor]" value="Temassız Teslimat">

                            <button type="submit" class="btn w-100 fw-bold text-white" style="background: #5d3ebc;">Getir Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}">
                    @csrf
                    <input type="hidden" name="platform" value="yemeksepeti">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header py-3" style="background: #fb0050;">
                            <img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height: 20px; ">
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Restaurant ID</label>
                                <input type="text" class="form-control border-secondary" name="data[information][restaurantId]" value="{{ json_decode($restaurant->yemeksepeti)->information->restaurantId ?? '' }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Chain ID</label>
                                <input type="text" class="form-control border-secondary" name="data[information][chainId]" value="{{ json_decode($restaurant->yemeksepeti)->information->chainId ?? '' }}" required>
                            </div>

                            <input type="hidden" name="data[status]" value="true">
                            @foreach(['otomatikOnay' => 'false', 'service' => '30', 'isEcoFriendly' => 'Servis İstemiyorum', 'doNotKnock' => 'Zil Çalma', 'dropOffAtDoor' => 'Temassız Teslimat'] as $k => $v)
                                <input type="hidden" name="data[{{$k}}]" value="{{$v}}">
                            @endforeach

                            <button type="submit" class="btn w-100 fw-bold text-white" style="background: #fb0050;">Yemeksepeti Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}">
                    @csrf
                    <input type="hidden" name="platform" value="trendyol">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header py-3" style="background: #ff6000;">
                            <img src="{{ asset('theme/images/trendyolyemek.png') }}" style="height: 20px; ">
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Restaurant ID</label>
                                <input type="text" class="form-control border-secondary" name="data[information][restaurantId]" value="{{ json_decode($restaurant->trendyol)->information->restaurantId ?? '' }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Supplier (Satıcı) ID</label>
                                <input type="text" class="form-control border-secondary" name="data[information][supplierId]" value="{{ json_decode($restaurant->trendyol)->information->supplierId ?? '' }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">API Secret Key</label>
                                <input type="text" class="form-control border-secondary" name="data[information][apiSecretKey]" value="{{ json_decode($restaurant->trendyol)->information->apiSecretKey ?? '' }}" required>
                            </div>

                            <input type="hidden" name="data[status]" value="true">
                            @foreach(['otomatikOnay' => 'false', 'service' => '30', 'isEcoFriendly' => 'Servis İstemiyorum', 'doNotKnock' => 'Zil Çalma', 'dropOffAtDoor' => 'Temassız Teslimat'] as $k => $v)
                                <input type="hidden" name="data[{{$k}}]" value="{{$v}}">
                            @endforeach

                            <button type="submit" class="btn w-100 fw-bold text-white" style="background: #ff6000;">Trendyol Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-xl-4 col-lg-6 mb-4">
                <form method="POST" action="{{ route('restaurant.entegrations.entegrastion_update') }}">
                    @csrf
                    <input type="hidden" name="platform" value="migros">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header py-3" style="background: #ff6000;">
                            <h4 class="card-title mb-0 text-white fw-bold">MİGROS YEMEK</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Restaurant ID</label>
                                <input type="text" class="form-control border-secondary" name="data[information][restaurantId]" value="{{ json_decode($restaurant->migros)->information->restaurantId ?? '' }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Chain ID</label>
                                <input type="text" class="form-control border-secondary" name="data[information][chainId]" value="{{ json_decode($restaurant->migros)->information->chainId ?? '' }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">API Key</label>
                                <input type="text" class="form-control border-secondary" name="data[information][apiKey]" value="{{ json_decode($restaurant->migros)->information->apiKey ?? '' }}" required>
                            </div>

                            <input type="hidden" name="data[status]" value="true">
                            @foreach(['otomatikOnay' => 'false', 'service' => '30', 'isEcoFriendly' => 'Servis İstemiyorum', 'doNotKnock' => 'Zil Çalma', 'dropOffAtDoor' => 'Temassız Teslimat'] as $k => $v)
                                <input type="hidden" name="data[{{$k}}]" value="{{$v}}">
                            @endforeach

                            <button type="submit" class="btn w-100 fw-bold text-white" style="background: #ff6000;">Migros Güncelle</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
