@extends('admin.layouts.app')
@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            border: #259a38 solid 2px;
            height: 500px; /* ya da istediğin başka bir yükseklik */
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
    </style>
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restaurantlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/restaurants">Restaurantlar</a></li>
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
                        <h4 class="card-title">Yeni İşyeri Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('admin.restaurants.create')}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">İşyeri Adı  <small class="text-danger">*</small></label>
                                        <input  required type="text" class="form-control" name="restaurant_name"
                                               placeholder="İşyeri Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Yetkili Adı  <small class="text-danger">*</small></label>
                                        <input required type="text" class="form-control" name="name" placeholder="Yetkili Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">E-posta Adresi  <small class="text-danger">*</small></label>
                                        <input required type="email" class="form-control" name="email"
                                               placeholder="E-posta Adresi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon  <small class="text-danger">*</small></label>
                                        <input required type="text" class="form-control" name="phone" placeholder="Telefon">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Şifre  <small class="text-danger">*</small></label>
                                        <input required type="password" class="form-control" name="password" placeholder="Şifre">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Vergi Dairesi</label>
                                        <input type="text" class="form-control" name="tax_name"
                                               placeholder="Vergi Dairesi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Vergi Numarası</label>
                                        <input type="text" class="form-control" name="tax_number"
                                               placeholder="Vergi Numarası">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Paket Fiyatı</label>
                                        <input type="text" class="form-control" name="package_price"
                                               placeholder="Paket Fiyatı">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Adres <small class="text-danger">*</small></label>
                                        <textarea required rows="8" cols="8" class="form-control"  name="address"  placeholder="İşyeri Adresi"></textarea>
                                    </div>
                                    @php
                                        $city = \App\Models\City::find(\App\Models\Admin::find(auth()->id())->city_id);
                                    @endphp
                                    <div class="mb-3 col-md-4 d-none">
                                        <label class="form-label">Şehir</label>
                                        <select class="form-control" name="city_id" id="city-select">
                                            <option value="{{$city->id}}" data-lat="{{$city->lat}}" data-lng="{{$city->lng}}" selected>
                                                {{$city->name}}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">İlçe</label>
                                        <select required class="form-control select2" name="district_id" id="district-select">
                                            <option value="">İlçe Seç</option>
                                        </select>
                                    </div>

                                    <div class="mt-5 mb-3">
                                        <p class="text-danger fw-bold">Lütfen haritadan konum işaratlemesi yapınız.</p>
                                        <div id="map"></div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark" for="latitude">Enlem (Latitude)</label>
                                        <input required type="text" name="latitude" id="lat" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark" for="longitude">Boylam (Longitude)</label>
                                        <input required type="text" name="longitude" id="lng" class="form-control">
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="special-button float-end">Kaydet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        $(document).ready(function () {
            var cityId = $('#city-select').val();
            var selectedOption = $('#city-select').find('option:selected');
            var lat = selectedOption.data('lat');
            var lng = selectedOption.data('lng');

            // Harita başlangıç
            var map = L.map('map').setView([lat, lng], 13);
            var marker = L.marker([lat, lng]).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            $('#lat').val(lat);
            $('#lng').val(lng);

            map.on('click', function(e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;

                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker([lat, lng]).addTo(map);

                $('#lat').val(lat);
                $('#lng').val(lng);
            });

            // İlçeleri otomatik getir
            if (cityId) {
                $.ajax({
                    url: '/admin/get-districts/' + cityId,
                    type: 'GET',
                    success: function (data) {
                        $('#district-select').empty().append('<option value="">İlçe Seç</option>');
                        $.each(data, function (key, value) {
                            $('#district-select').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            }
        });
    </script>
@endsection


