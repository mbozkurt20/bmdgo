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

        @if(session()->has('location_errors'))
            <div class="custom-alert error">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <div class="alert-message">
                    <strong>Bazı adreslerin konumu bulunamadı:</strong>
                    <ul>
                        @foreach(session()->get('location_errors') as $error)
                            <li>
                                "{{ $error['input'] }}" => {{ $error['message'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
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
                                    <div class="repeater-heading mb-5">

                                        <div class="row">
                                            <div class="col-lg-10">
                                                <h5 class="pull-left">Müşteri Adresleri Güncelleyiniz</h5>
                                            </div>
                                            <div class="col-lg-2" style="text-align: right">
                                                <a class="special-ok-button-small btn-xs repeater-add-btn" data-repeater-create>+ Yeni Ekle
                                                </a>
                                            </div>
                                        </div>

                                        <p style="font-weight: bold" class="text-danger font-weight-bold">!!! Lütfen bilgilerinizi eksiksiz giriniz, bu bilgileri referans alınarak kuryelerimize yön veriyoruz....</p>
                                    </div>

                                    <div class="clearfix"></div>

                                    <!-- Repeater Items -->
                                    <div data-repeater-list="address">
                                        @foreach (\App\Models\CustomerAddress::where('customer_id', $customer->id)->get() as $index => $address)
                                            <div data-repeater-item class="item-content mb-3 border p-3 rounded">
                                                <input name="id" value="{{$address->id}}" style="display: none" type="text">
                                                <h5 style="color: #e7004d" class="fw-semibold  mb-3">Adres Bilgileri</h5>

                                                <hr>

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="me-3 fw-bold" style="color:#0d2646; font-size:1rem;">Adres Başlığı:</span>
                                                    <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                           name="address[{{ $index }}][name]"
                                                           value="{{ $address->name }}"
                                                           placeholder="Adres Başlığı" required>
                                                </div>

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="me-3 fw-bold" style="color:#0d2646; font-size:1rem;">İlçe:</span>
                                                    <select class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                            name="address[{{ $index }}][ilce]" required>
                                                        @foreach(\App\Models\District::where('city_id', \App\Models\Admin::find(auth()->user()->admin_id)->city_id)->get() as $district)
                                                            <option value="{{ $district->id }}" {{ $district->id == $address->district_id ? 'selected' : '' }}>
                                                                {{ $district->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="me-3 fw-bold" style="color:#0d2646; font-size:1rem;">Mah.</span>
                                                    <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                           name="address[{{ $index }}][mahalle]"
                                                           value="{{ $address->mahalle }}"
                                                           placeholder="Örn: Ankara" required>
                                                </div>

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="me-3 fw-bold" style="color:#0d2646; font-size:1rem;">Sok.</span>
                                                    <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                           name="address[{{ $index }}][sokak_cadde]"
                                                           value="{{ $address->sokak_cadde }}"
                                                           placeholder="Örn: 5021" required>
                                                </div>

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="me-3 fw-bold" style="color:#0d2646; font-size:1rem;">Apt Adı.</span>
                                                    <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                           name="address[{{ $index }}][bina_no]"
                                                           value="{{ $address->bina_no }}"
                                                           placeholder="Örn: Deniz Apt." required>
                                                </div>

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="me-3 fw-bold" style="color:#0d2646; font-size:1rem;">Kat:</span>
                                                    <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                           name="address[{{ $index }}][kat]"
                                                           value="{{ $address->kat }}"
                                                           placeholder="Örn: 3" required>
                                                </div>

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="me-3 fw-bold" style="color:#0d2646; font-size:1rem;">Daire:</span>
                                                    <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                           name="address[{{ $index }}][daire_no]"
                                                           value="{{ $address->daire_no }}"
                                                           placeholder="Örn: 5" required>
                                                </div>

                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="me-3 fw-bold" style="color:#0d2646; font-size:1rem;">Adres Tarifi:</span>
                                                    <input type="text" class="flex-grow-1 border-0 border-bottom bg-transparent"
                                                           name="address[{{ $index }}][adres_tarifi]"
                                                           value="{{ $address->adres_tarifi }}"
                                                           placeholder="Örn: Parkın karşısı">
                                                </div>

                                                <!-- Silme butonu -->
                                                <div class="text-end mt-2">
                                                    <a style="font-size: 20px; cursor: pointer" class="text-danger" data-repeater-delete>
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>

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
