@extends('admin.layouts.app')
@section('content')
    <style>
        #map {
            border: #259a38 solid 2px;
            height: 500px;
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .map-search-container {
            margin-bottom: 15px;
        }
    </style>

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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Kurye Güncelle</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{ route('admin.couriers.update') }}">
                                @csrf
                                <input type="hidden" name="id" value="{{$courier->id}}">
                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Kurye Adı</label>
                                        <input required type="text" class="form-control" value="{{$courier->name}}" name="name" placeholder="Kurye Adı">
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Telefonu</label>
                                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => $courier->phone])
                                    </div>
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Şifresi</label>
                                        <input type="text" class="form-control" name="password" placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label for="price-type" class="form-label fs-14 text-dark">Ödeme Türü </label>
                                        <select class="form-control" name="price_type" id="price-type">
                                            <option {{$courier->price_type == 'package' ? 'selected' : ''}} value="package">Paket Başı</option>
                                            <option {{$courier->price_type == 'fixed' ? 'selected' : ''}} value="fixed">Sabit + Km Ücreti</option>
                                        </select>
                                    </div>

                                    <div id="fixed-fields" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Sabit Ücret</label>
                                        <input value="{{$courier->fixed_price}}" type="text" class="form-control" name="fixed_price">
                                    </div>
                                    <div id="fixed-fields2" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Km Ücreti</label>
                                        <input value="{{$courier->km_price}}" type="text" class="form-control" name="km_price">
                                    </div>
                                    <div id="fixed-fields3" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Km sonrası hesapla</label>
                                        <input value="{{$courier->km_distance_later}}" type="number" class="form-control" name="km_distance_later">
                                    </div>
                                    <div id="package-fields" class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark">Paket Baş. Ücreti </label>
                                        <input value="{{$courier->price}}" type="text" class="form-control" name="price">
                                    </div>

                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label fs-14 text-dark d-block">Durum</label>
                                        @php $statuses = [\App\Helpers\CourierStatus::active => 'Müsait', \App\Helpers\CourierStatus::service => 'Serviste', \App\Helpers\CourierStatus::break => 'Molada', \App\Helpers\CourierStatus::passive => 'Kapalı']; @endphp
                                        @foreach($statuses as $val => $label)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="status_{{$val}}" value="{{$val}}" {{ $courier->status == $val ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status_{{$val}}">{{$label}}</label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mt-4 mb-3 col-md-12">
                                        <label class="form-label fw-bold text-primary">Kurye Mevcut Konumu / Güncelle</label>
                                        <div class="map-search-container">
                                            <input id="pac-input" class="form-control mb-2" type="text" placeholder="Adres veya bölge ara...">
                                        </div>
                                        <div id="map"></div>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label text-dark">Enlem</label>
                                        <input required value="{{$courier->latitude}}" type="text" class="form-control" name="latitude" id="lat" readonly>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label text-dark">Boylam</label>
                                        <input required value="{{$courier->longitude}}" type="text" class="form-control" name="longitude" id="lng" readonly>
                                    </div>
                                </div>

                                <button type="submit" class="special-button btn btn-primary mt-4 float-end">Kaydı Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap" async defer></script>

    <script>
        // Ücret Türü Alanları Yönetimi
        document.addEventListener('DOMContentLoaded', function () {
            const priceTypeSelect = document.getElementById('price-type');
            const packageFields = document.getElementById('package-fields');
            const fixedFields = [document.getElementById('fixed-fields'), document.getElementById('fixed-fields2'), document.getElementById('fixed-fields3')];

            function toggleFields() {
                const isPackage = priceTypeSelect.value === 'package';
                packageFields.style.display = isPackage ? 'block' : 'none';
                packageFields.querySelector('input').required = isPackage;

                fixedFields.forEach(field => {
                    field.style.display = isPackage ? 'none' : 'block';
                    field.querySelector('input').required = !isPackage;
                });
            }
            priceTypeSelect.addEventListener('change', toggleFields);
            toggleFields();
        });

        // Google Maps Yönetimi
        let map, marker, autocomplete;

        function initMap() {
            const existingPos = {
                lat: parseFloat("{{ $courier->latitude }}") || 37.1502,
                lng: parseFloat("{{ $courier->longitude }}") || 38.7790
            };

            map = new google.maps.Map(document.getElementById("map"), {
                center: existingPos,
                zoom: 15,
                mapTypeControl: true
            });

            marker = new google.maps.Marker({
                position: existingPos,
                map: map,
                draggable: true
            });

            // Arama Kutusu
            const input = document.getElementById("pac-input");
            input.addEventListener("keydown", (e) => { if (e.key === "Enter") e.preventDefault(); });

            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);
                updateInputs(place.geometry.location.lat(), place.geometry.location.lng());
            });

            // Tıklama ve Sürükleme
            map.addListener("click", (e) => {
                marker.setPosition(e.latLng);
                updateInputs(e.latLng.lat(), e.latLng.lng());
            });

            marker.addListener("dragend", () => {
                const pos = marker.getPosition();
                updateInputs(pos.lat(), pos.lng());
            });
        }

        function updateInputs(lat, lng) {
            document.getElementById("lat").value = lat;
            document.getElementById("lng").value = lng;
        }
    </script>
@endsection
