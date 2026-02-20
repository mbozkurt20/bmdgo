@extends('admin.layouts.app')
@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>

    <style>
        #map {
            border: #259a38 solid 2px;
            height: 500px;
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        #map-search {
            margin-top: 10px;
            margin-left: 10px;
            width: 500px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 0 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 5;
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

        <div class="row">
            <div class="col-xl-10 col-lg-12">
                <div class="mb-4">
                    <p class="text-primary fw-bold mb-3">
                        <i class="fa fa-map-marker-alt me-1"></i> Konumunuzu arayarak veya haritaya tıklayarak güncelleyebilirsiniz.
                    </p>
                    <input id="map-search" type="text" placeholder="Adres veya mekan ara..." class="form-control">
                    <div id="map"></div>
                </div>

                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title fw-bold">Bilgileri Güncelle</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="{{ route('admin.profile.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark fw-bold">İsim</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark fw-bold">Telefon</label>
                                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => auth()->user()->phone])
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark fw-bold">Yeni Şifre</label>
                                        <input type="password" name="password" class="form-control" placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                    </div>

                                    <div class="row py-3">
                                        <div class="col-md-6 mb-4">
                                            <label class="text-dark fw-bold d-flex justify-content-between" for="distance_limit_km">
                                                <span><i class="fa fa-map-marked-alt text-primary me-2"></i>Maksimum Sipariş Mesafesi</span>
                                                <span id="dist_val" class="badge bg-primary rounded-pill">0 km</span>
                                            </label>
                                            <input required type="range" name="distance_limit_km" id="distance_limit_km"
                                                   min="1" max="100" step="1"
                                                   value="{{ old('distance_limit_km', auth()->user()->distance_limit_km ?? 20) }}"
                                                   class="form-range custom-range-slider">
                                            <div class="small text-muted mt-1">
                                                <i class="fa fa-info-circle me-1"></i>
                                                Restoranınızın hizmet vereceği **maksimum yarıçapı** belirler. Bu mesafeden uzak müşteriler sipariş veremez ve kurye ataması yapılmaz.
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-4">
                                            <label class="text-dark fw-bold d-flex justify-content-between" for="max_package_limit">
                                                <span><i class="fa fa-box-open text-warning me-2"></i>Maksimum Kurye Paket Ataması</span>
                                                <span id="pkg_val" class="badge bg-warning text-dark rounded-pill">0 Paket</span>
                                            </label>
                                            <input required type="range" name="max_package_limit" id="max_package_limit"
                                                   min="1" max="10" step="1"
                                                   value="{{ old('max_package_limit', auth()->user()->max_package_limit ?? 4) }}"
                                                   class="form-range custom-range-slider">
                                            <div class="small text-muted mt-1">
                                                <i class="fa fa-info-circle me-1"></i>
                                                Bir kurye henüz **"Yola Çıkmadı"** durumundayken, üzerine atanabilecek **en fazla** sipariş sayısıdır. Bu sınıra ulaşıldığında kurye otomatik olarak yola çıkarılır.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label fw-bold">Şehir</label>
                                        <select required class="form-control select2" name="city_id" id="city_id">
                                            <option value="">Şehir Seç</option>
                                            @foreach(\App\Models\City::all() as $city)
                                                <option {{$city->id == auth()->user()->city_id ? 'selected' : '' }}
                                                        value="{{$city->id}}"
                                                        data-lat="{{$city->lat}}"
                                                        data-lng="{{$city->lng}}">
                                                    {{$city->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label fw-bold">İlçe</label>
                                        <select required class="form-control select2" name="district_id" id="district_id">
                                            <option value="">İlçe Seç</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark fw-bold">Enlem (Latitude)</label>
                                        <input type="text" name="latitude" id="latitude" class="form-control border-dark" readonly
                                               value="{{ old('latitude', auth()->user()->latitude) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="text-dark fw-bold">Boylam (Longitude)</label>
                                        <input type="text" name="longitude" id="longitude" class="form-control border-dark" readonly
                                               value="{{ old('longitude', auth()->user()->longitude) }}">
                                    </div>

                                    <div class="col-12 mt-3">
                                        <button type="submit" class="special-button w-100">Profili Güncelle</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateRangeValues() {
            $('#dist_val').text($('#distance_limit_km').val() + ' km');
            $('#pkg_val').text($('#max_package_limit').val() + ' Paket');
        }

        $(document).on('input', '#distance_limit_km, #max_package_limit', function() {
            updateRangeValues();
        });

        $(document).ready(function() {
            updateRangeValues();
        });
    </script>

    <script>
        let map, marker, autocomplete;

        function initMap() {
            const existingLat = parseFloat("{{ auth()->user()->latitude }}") || 37.1502;
            const existingLng = parseFloat("{{ auth()->user()->longitude }}") || 38.7790;
            const initialPos = { lat: existingLat, lng: existingLng };

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 14,
                mapTypeControl: false,
                streetViewControl: false
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            // Tıklama ile konum güncelleme
            map.addListener("click", (e) => {
                updatePosition(e.latLng);
            });

            // Sürükleme ile konum güncelleme
            marker.addListener("dragend", (e) => {
                updatePosition(marker.getPosition());
            });

            // Arama Kutusu (Autocomplete)
            const input = document.getElementById("map-search");
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            autocomplete = new google.maps.places.Autocomplete(input);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                map.setCenter(place.geometry.location);
                map.setZoom(17);
                updatePosition(place.geometry.location);
            });
        }

        function updatePosition(latLng) {
            marker.setPosition(latLng);
            $('#latitude').val(latLng.lat().toFixed(8));
            $('#longitude').val(latLng.lng().toFixed(8));
        }

        $(document).ready(function () {
            $('.select2').select2();

            // Şehir değişince haritayı kaydır ve ilçeleri yükle
            $('#city_id').on('change', function () {
                const cityId = $(this).val();
                const selected = $(this).find('option:selected');
                const lat = parseFloat(selected.data('lat'));
                const lng = parseFloat(selected.data('lng'));

                if (lat && lng) {
                    const newPos = { lat: lat, lng: lng };
                    map.setCenter(newPos);
                    map.setZoom(12);
                    updatePosition(new google.maps.LatLng(lat, lng));
                }
                loadDistricts(cityId);
            });

            function loadDistricts(cityId, selectedDistrictId = null) {
                if (cityId) {
                    $.ajax({
                        url: '/admin/get-districts/' + cityId,
                        type: 'GET',
                        success: function (data) {
                            $('#district_id').empty().append('<option value="">İlçe Seç</option>');
                            $.each(data, function (key, value) {
                                var selected = (value.id == selectedDistrictId) ? 'selected' : '';
                                $('#district_id').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                            });
                        }
                    });
                }
            }

            const initialCityId = $('#city_id').val();
            const initialDistrictId = "{{ auth()->user()->district_id }}";
            if (initialCityId) loadDistricts(initialCityId, initialDistrictId);

            // Google Maps Başlat
            initMap();
        });
    </script>
@endsection
