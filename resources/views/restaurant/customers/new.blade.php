@extends('restaurant.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Müşteriler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/restaurant/customers">Müşteriler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
                </ol>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="custom-alert success">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('message') }}</span>
            </div>
        @endif
        @if (session()->has('test'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                </button>
                <a href="#"> {{ session()->get('message') }}</a>
            </div>
        @endif
        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Müşteri Formu</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="repeater" action="{{ route('restaurant.customers.create') }}">
                            @csrf
                            <div class="row">
                                <!-- Müşteri Adı -->
                                <div class="mb-3 col-md-12">
                                    <label class="form-label text-dark">Müşteri Adı <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control border border-light" name="name" placeholder="Müşteri Adı" required>
                                </div>

                                <!-- Telefon -->
                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-dark">Telefon Numarası <small class="text-danger">*</small></label>
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-dark">Telefon Numarası (opsiyonel)</label>
                                    @include('components.phone',['key' => 'mobile', 'required' => false,'value' => null])
                                </div>
                            </div>

                            <hr>
                            <!-- Adresler -->
                            <div class="p-3">
                                <div class="repeater-heading mb-5">

                                    <div class="row">
                                        <div class="col-lg-10">
                                            <h5 class="pull-left">Müşteri Adresleri Ekleyiniz</h5>
                                        </div>
                                        <div class="col-lg-2" style="text-align: right">
                                            <a class="special-ok-button-small btn-xs repeater-add-btn" data-repeater-create>+ Yeni Ekle
                                            </a>
                                        </div>
                                    </div>

                                    <p style="font-weight: bold" class="text-danger font-weight-bold">!!! Lütfen bilgilerinizi eksiksiz giriniz, bu bilgileri referans alınarak kuryelerimize yön veriyoruz....</p>
                                </div>

                                <div data-repeater-list="address">
                                    <div data-repeater-item class="item-content mb-3 border p-3 rounded">

                                        <h5 class="fw-semibold text-black mb-3">Adres Bilgileri</h5>
                                        <hr>
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-3 fw-bold" style="color:#259a38; font-size:1rem;">Adres Başlığı:</span>
                                            <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                   name="name" placeholder="Adres Başlığı" required>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-3 fw-bold" style="color:#259a38; font-size:1rem;">İlçe:</span>
                                            <select class="flex-grow-1 border-0 border-bottom bg-transparent" name="ilce" required>
                                                @foreach(\App\Models\District::where('city_id', \App\Models\Admin::find(auth()->user()->admin_id)->city_id)->get() as $district)
                                                    <option value="{{ $district->id }}">
                                                        {{ $district->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-3 fw-bold" style="color:#259a38; font-size:1rem;">Mah.</span>
                                            <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                   name="mahalle" placeholder="Mahalle" required>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-3 fw-bold" style="color:#259a38; font-size:1rem;">Sok.</span>
                                            <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                   name="sokak_cadde" placeholder="Sokak / Cadde" required>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-3 fw-bold" style="color:#259a38; font-size:1rem;">Apt Adı.</span>
                                            <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                   name="bina_no" placeholder="Bina No / Apartman Adı" required>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-3 fw-bold" style="color:#259a38; font-size:1rem;">Kat:</span>
                                            <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                   name="kat" placeholder="Kat" required>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-3 fw-bold" style="color:#259a38; font-size:1rem;">Daire:</span>
                                            <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                   name="daire_no" placeholder="Daire No" required>
                                        </div>

                                        <div class="d-flex align-items-center mb-3">
                                            <span class="me-3 fw-bold" style="color:#259a38; font-size:1rem;">Adres Tarifi:</span>
                                            <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                   name="adres_tarifi" placeholder="Adres Tarifi" required>
                                        </div>

                                        <!-- Silme butonu -->
                                        <div class="text-end mt-2">
                                            <a style="font-size: 20px; cursor: pointer" class="text-danger" data-repeater-delete>
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <button type="submit" class="special-button float-end mt-4">Kaydı Tamamla</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
