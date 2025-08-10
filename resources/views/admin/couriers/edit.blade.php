@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kuryeler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/couriers">Kuryeler</a></li>
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
                        <h4 class="card-title">Kurye Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{ route('admin.couriers.update') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{$courier->id}}">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label for="form-text" class="form-label fs-14 text-dark">Kurye Adı</label>
                                        <input required type="text" class="form-control" value="{{$courier->name}}" name="name" id="form-text" placeholder="Kurye Adı">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Telefonu</label>
                                        <input required type="text" class="form-control" value="{{$courier->phone}}" name="phone" id="form-text" placeholder="0532 532 0000">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Şifresi</label>
                                        <input type="text" class="form-control" value="" name="password" id="form-text" placeholder="Şifre belirleyin">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label for="price-type" class="form-label fs-14 text-dark">Ödeme Türü </label>
                                        <select class="form-control" name="price_type" id="price-type">
                                            <option {{$courier->price_type == 'package' ? 'selected' : null}} value="package">Paket Başı</option>
                                            <option {{$courier->price_type == 'fixed' ? 'selected' : null}} value="fixed">Sabit + Km Ücreti</option>
                                        </select>
                                    </div>

                                    <div id="fixed-fields" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Sabit Ücret</label>
                                        <input value="{{$courier->fixed_price}}" required type="text" class="form-control" name="fixed_price" placeholder="25.000">
                                    </div>
                                    <div id="fixed-fields2" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Km Ücreti (1 km göre giriniz)</label>
                                        <input value="{{$courier->km_price}}" required type="text" class="form-control" name="km_price" placeholder="4,00">
                                    </div>

                                    <div id="package-fields" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Paket Baş. Ücreti </label>
                                        <input value="{{$courier->price}}" required type="text" class="form-control" name="price" placeholder="10,00">
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Enlem</label>
                                        <input required  value="{{$courier->latitude}}" type="text" class="form-control" name="latitude" id="form-text"
                                               placeholder="Enlem Giriniz">
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Boylam</label>
                                        <input required  value="{{$courier->longitude}}" type="text" class="form-control" name="longitude" id="form-text"
                                               placeholder="Boylam Giriniz">
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Durum</label>
                                        <select class="form-control" name="situation" id="">
                                            <option  {{$courier->situation == 'active' ? 'selected' : null}} value="active">Aktif</option>
                                            <option {{$courier->situation == 'passive' ? 'selected' : null}} value="passive">Pasif</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="special-button mt-4 float-end">Kaydı Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const priceTypeSelect = document.getElementById('price-type');
            const packageFields = document.getElementById('package-fields');
            const fixedFields = document.getElementById('fixed-fields');
            const fixedFields2 = document.getElementById('fixed-fields2');

            const packageInput = packageFields.querySelector('input');
            const fixedInput1 = fixedFields.querySelector('input');
            const fixedInput2 = fixedFields2.querySelector('input');

            function toggleFields() {
                const selectedType = priceTypeSelect.value;

                if (selectedType === 'package') {
                    // Göster
                    packageFields.style.display = 'block';
                    // Gizle
                    fixedFields.style.display = 'none';
                    fixedFields2.style.display = 'none';

                    // Required ayarları
                    packageInput.required = true;
                    fixedInput1.required = false;
                    fixedInput2.required = false;
                } else {
                    // Göster
                    fixedFields.style.display = 'block';
                    fixedFields2.style.display = 'block';
                    // Gizle
                    packageFields.style.display = 'none';

                    // Required ayarları
                    packageInput.required = false;
                    fixedInput1.required = true;
                    fixedInput2.required = true;
                }
            }

            // Seçim değiştiğinde çalıştır
            priceTypeSelect.addEventListener('change', toggleFields);

            // Sayfa ilk yüklendiğinde çalıştır
            toggleFields();
        });
    </script>
@endsection
