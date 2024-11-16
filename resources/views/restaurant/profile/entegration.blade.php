@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Entegrasyonlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Entegrasyonlar</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
                </ol>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                </button>
                <a href="#"> {{ session()->get('message') }}</a>
            </div>
        @endif

        <form method="post" class="repeater" action="{{ route('restaurant.entegrations.entegrastion_update') }}">
            @csrf
            <div class="row">

                <div class="col-xl-4 col-lg-12">
                    <div class="card">
                        <div class="card-header" style="background: #fb0050;color:#fff">
                            <h4 class="card-title"><img src="{{ asset('theme/images/yemeksepeti.png') }}"
                                    style="border-radius:5px;height: 24px;width: auto"> Entegrasyon Bilgileri</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">E-posta Adresi</label>
                                        <input type="email" class="form-control" name="yemeksepeti_email"
                                            placeholder="E-posta Adresi" value="{{ $restaurant->yemeksepeti_email }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Şifresi</label>
                                        <input type="password" class="form-control" name="yemeksepeti_password"
                                            placeholder="Şifresi" value="{{ $restaurant->yemeksepeti_password }}">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-12">
                    <div class="card">
                        <div class="card-header" style="background: #fd683e;color:#fff">
                            <h4 class="card-title"><img src="{{ asset('theme/images/trendyolyemek.png') }}"
                                    style="border-radius:5px;height: 24px;width: auto"> Entegrasyon Bilgileri</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Satıcı ID</label>
                                        <input type="text" class="form-control" name="trendyol_satici_id"
                                            placeholder="Satıcı ID" value="{{ $restaurant->trendyol_satici_id }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Şube ID (Opsiyonel)</label>
                                        <input type="text" class="form-control" name="trendyol_sube_id"
                                            placeholder="Şube ID" value="{{ $restaurant->trendyol_sube_id }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">API Key</label>
                                        <input type="text" class="form-control" name="trendyol_api_key"
                                            placeholder="API Key" value="{{ $restaurant->trendyol_api_key }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Secret Key</label>
                                        <input type="text" class="form-control" name="trendyol_secret_key"
                                            placeholder="Secret Key" value="{{ $restaurant->trendyol_secret_key }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- TODO : Burda Bir UI Eklemem Gerekiyor-->

                <div class="col-xl-4 col-lg-12">
                    <div class="card">
                        <div class="card-header" style="background: #C92C2C;color:#fff">
                            <h4 class="card-title"><img src="{{ asset('theme/images/adisyoFull.png') }}"
                                    style="border-radius:5px;height: 24px;width: auto"> Entegrasyon Bilgileri</h4>
                        </div>
                        <div class="card-body">
                            <div class="basic-form">
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">API Key</label>
                                        <input type="text" class="form-control" name="adisyo_api_key"
                                            placeholder="API Key" value="{{ $restaurant->adisyo_api_key }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Secret Key</label>
                                        <input type="text" class="form-control" name="adisyo_secret_key"
                                            placeholder="Secret Key" value="{{ $restaurant->adisyo_secret_key }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Consumer Name</label>
                                        <input type="text" class="form-control" name="adisyo_consumer_adi"
                                            placeholder="Consumer Name" value="{{ $restaurant->adisyo_consumer_adi }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <button type="submit" class="btn btn-primary">Bilgileri Güncelle</button>
        </form>

    </div>
@endsection
