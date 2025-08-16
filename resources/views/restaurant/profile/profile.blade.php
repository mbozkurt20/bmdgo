@extends('restaurant.layouts.app')
@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #userMap {
            border: #0d2646 solid 2px;
            height: 500px;
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Profil Düzenle</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/couriers">Profil</a></li>
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
                <div class="">
                    <p class=" text-primary fw-bold mb-3">Konumunuzu bulup haritaya tıklayarak konumunuzu güncelleyebilirsiniz.</p>
                    <div id="userMap"></div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Profil Düzenle </h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="{{ route('restaurant.profile.update') }}" method="POST">
                                @csrf
                                <div class="row">

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark" for="name">İsim</label>
                                        <input type="text" name="name" class="form-control"
                                               value="{{ old('name', auth()->user()->name) }}">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark" for="phone">Telefon</label>
                                        <input type="text" value="{{ old('latitude', auth()->user()->phone) }}" name="phone" class="form-control">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark" for="password">Yeni Şifre</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark" for="latitude">Enlem (Latitude)</label>
                                        <input required type="text" name="latitude" id="latitude" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark" for="longitude">Boylam (Longitude)</label>
                                        <input required type="text" name="longitude" id="longitude" class="form-control">
                                    </div>

                                    <button type="submit" class="special-button mt-3">Güncelle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var existingLat = {{ auth()->user()->latitude ?? '37.15026069044849' }};
        var existingLng = {{ auth()->user()->longitude ?? '38.77905463205474' }};
        var map;
        var marker = null; // Marker'ı globalde tanımlıyoruz

        if (existingLat && existingLng) {
            map = L.map('userMap').setView([existingLat, existingLng], 13);
            marker = L.marker([existingLat, existingLng]).addTo(map);

            // İlk açılışta inputları doldur
            document.getElementById('latitude').value = existingLat;
            document.getElementById('longitude').value = existingLng;

        } else {
            map = L.map('userMap').setView([39.9208, 32.8541], 6); // Türkiye geneli
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        map.on('click', function(e) {
            console.log({er: e})
            let lat = e.latlng.lat;
            let lng = e.latlng.lng;

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng]).addTo(map);

        console.log({lat:lat})
        console.log({lng:lng})
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });
    </script>
@endsection
