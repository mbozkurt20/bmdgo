@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Müşteriler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/restaurant/customers">Müşteriler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Düzenle</a></li>
                </ol>
            </div>
        </div>
        @if(session()->has('message'))
            <div class="custom-alert success">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('message') }}</span>
            </div>
        @endif

        @if(session()->has('test') )
            <div class="custom-alert error">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('test') }}</span>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Müşteri Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" class="repeater" action="{{ route('restaurant.customers.update') }}">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="id" value="{{ $customer->id }}">
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Müşteri Adı</label>
                                        <input type="text" class="form-control" name="name" placeholder="Müşteri Adı"
                                            value="{{ $customer->name }}" required>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon Numarası</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ $customer->phone }}" placeholder="Telefon Numarası" required>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon Numarası 2</label>
                                        <input type="text" class="form-control" name="mobile"
                                            value="{{ $customer->mobile }}" placeholder="Diğer Telefon Numarası 2">
                                    </div>

                                </div>
                                <hr>
                                <div class="mt-4">
                                        <!-- Repeater Heading -->
                                        <div class="repeater-heading">
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    <h5 class="pull-left">Adres Ekle</h5>
                                                </div>
                                                <div class="col-lg-2" style="text-align: right">
                                                    <a class="special-ok-button-small btn-xs repeater-add-btn" data-repeater-create>+ Yeni Ekle
                                                    </a>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="clearfix"></div>
                                        <!-- Repeater Items -->
                                        <div data-repeater-list="address">
                                            @foreach (\App\Models\CustomerAddress::where('customer_id', $customer->id)->get() as $adres)
                                                <!-- Repeater Content -->
                                                <div data-repeater-item class="item-content row"
                                                    style="background: #f4f4f4;margin: 15px 0px  10px;padding:10px 0px;border-radius: 10px">
                                                    <input req type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                    <input type="hidden" name="id" value="{{ $adres->id }}">
                                                    <input type="hidden" name="type" value="up">
                                                    <div class="mb-3 col-md-5">
                                                        <input type="text" class="form-control" name="name" required
                                                            placeholder="Adres Başlığı" value="{{ $adres->name }}">
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <input type="text" class="form-control" name="sokak_cadde" required
                                                            placeholder="Sokak/Cadde" value="{{ $adres->sokak_cadde }}">
                                                    </div>
                                                    <div class="mb-3 col-md-1">
                                                        <div class="pull-right repeater-remove-btn">
                                                            <a id="remove-btn" style="font-size: 20px;cursor: pointer"
                                                                class="text-danger" data-repeater-delete>
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <input type="text" class="form-control" name="bina_no" required
                                                            value="{{ $adres->bina_no }}" placeholder="Bina No">
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <input type="text" class="form-control" name="kat" required
                                                            value="{{ $adres->kat }}" placeholder="Kat">
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <input type="text" class="form-control" name="daire_no" required
                                                            value="{{ $adres->daire_no }}" placeholder="Daire No">
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <input type="text" class="form-control" name="mahalle" required
                                                            value="{{ $adres->mahalle }}" placeholder="Mahalle">
                                                    </div>
                                                    <div class="mb-3 col-md-12">
                                                        <input type="text" name="adres_tarifi" class="form-control"
                                                            value="{{ $adres->adres_tarifi }}"
                                                            placeholder="Adres Tarifi">
                                                    </div>

                                                </div>
                                                <!-- Repeater Remove Btn -->

                                                <div class="clearfix"></div>
                                            @endforeach
                                        </div>
                                    </div>

                                <button type="submit" class="special-button float-end mt-4">Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
