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
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $restaurant->name }}</a></li>
                </ol>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                </button> <a href="#"> {{ session()->get('message') }}</a>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">İşyeri Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{ route('admin.restaurants.update') }}">
                                @csrf
                                <div class="row">
                                    <input type="hidden" value="{{ $restaurant->id }}" name="id">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">İşyeri Adı</label>
                                        <input type="text" class="form-control" name="restaurant_name"
                                            value="{{ $restaurant->restaurant_name }}" placeholder="İşyeri Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Yetkili Adı</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $restaurant->name }}" placeholder="Yetkili Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">E-posta Adresi</label>
                                        <input type="email" class="form-control" name="email"
                                            value="{{ $restaurant->email }}" placeholder="E-posta Adresi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon</label>
                                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => $restaurant->phone])
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Şifre</label>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Vergi Dairesi</label>
                                        <input type="text" class="form-control" name="tax_name"
                                            value="{{ $restaurant->tax_name }}" placeholder="Vergi Dairesi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Vergi Numarası</label>
                                        <input type="text" class="form-control" name="tax_number"
                                            value="{{ $restaurant->tax_number }}" placeholder="Vergi Numarası">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Paket Fiyatı</label>
                                        <input type="text" class="form-control" name="package_price"
                                            value="{{ $restaurant->package_price }}" placeholder="Paket Fiyatı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" name="status">
                                            <option value="active"
                                                {{ $restaurant->status == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="deactive"
                                                {{ $restaurant->status == 'deactive' ? 'selected' : '' }}>Deactive</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Adres</label>
                                        <input type="text" class="form-control" name="address"
                                            value="{{ $restaurant->address }}" placeholder="İşyeri Adresi">
                                    </div>

                                    <div class="mt-5 mb-3">
                                        <p class="text-danger fw-bold">Lütfen haritadan konum işaratlemesi yapınız.</p>
                                        <div id="map"></div>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Enlem</label>
                                        <input required  value="{{$restaurant->latitude}}" type="text" class="form-control" name="latitude" id="lat"
                                               placeholder="Enlem Giriniz">
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="form-password" class="form-label fs-14 text-dark">Boylam</label>
                                        <input required  value="{{$restaurant->longitude}}" type="text" class="form-control" name="longitude" id="lng"
                                               placeholder="Boylam Giriniz">
                                    </div>
                                </div>

                                <button type="submit" class="special-button"> Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        var existingLat = {{$restaurant->latitude ?? '37.15026069044849' }};
        var existingLng = {{ $restaurant->longitude ?? '38.77905463205474' }};
        var map;

        if (existingLat && existingLng) {
            map = L.map('map').setView([existingLat, existingLng], 13);
            marker = L.marker([existingLat, existingLng]).addTo(map);
        } else {
            map = L.map('map').setView([39.9208, 32.8541], 6); // Türkiye geneli
        }

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng]).addTo(map);

            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        });

    </script>
@endsection
