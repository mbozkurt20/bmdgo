@extends('restaurant.layouts.app')
@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>

    <style>
        .custom-range-slider {
            height: 1.5rem;
            padding: 0;
            background: transparent;
        }
        .form-range::-webkit-slider-runnable-track {
            background-color: #dee2e6;
            border-radius: 1rem;
            height: 0.5rem;
        }
        .form-range::-webkit-slider-thumb {
            background-color: #259a38; /* Senin yeşil rengin */
            margin-top: -0.25rem;
        }

        #userMap {
            border: #259a38 solid 2px;
            height: 500px;
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        /* Harita üzerindeki arama kutusu için stil */
        #map-search-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 500px;
            margin-top: 10px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }
    </style>

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
                <div class="mb-4 w-full ">
                    <p class="text-primary fw-bold mb-3">
                        <i class="fa fa-info-circle me-1"></i>
                        Konumunuzu bulmak için harita üzerinden arama yapabilir veya haritaya tıklayarak kırmızı işareti taşıyabilirsiniz.
                    </p>

                    <input id="map-search-input" type="text" placeholder="Restoran adresini arayın...">
                    <div id="userMap"></div>
                </div>

                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title fw-bold">Profil Bilgileri</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="{{ route('restaurant.profile.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark fw-bold" for="name">İsim</label>
                                        <input type="text" name="name" class="form-control border border-light"
                                               value="{{ old('name', auth()->user()->name) }}">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark fw-bold" for="phone">Telefon</label>
                                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => auth()->user()->phone])
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark fw-bold" for="password">Yeni Şifre (Boş bırakılırsa değişmez)</label>
                                        <input type="password" name="password" class="form-control border border-light">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark fw-bold" for="latitude">Enlem (Latitude)</label>
                                        <input required type="text" name="latitude" id="latitude"
                                               value="{{ old('latitude', auth()->user()->latitude) }}" class="form-control  border border-light" >
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark fw-bold" for="longitude">Boylam (Longitude)</label>
                                        <input required type="text" name="longitude" id="longitude"
                                               value="{{ old('longitude', auth()->user()->longitude) }}" class="form-control border border-light" >
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

                                    <div class="col-12 mt-3">
                                        <button type="submit" class="special-button w-100">Profilimi Güncelle</button>
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
            // Kullanıcının mevcut koordinatları veya varsayılan (Urfa)
            const existingLat = parseFloat("{{ auth()->user()->latitude }}") || 37.1502;
            const existingLng = parseFloat("{{ auth()->user()->longitude }}") || 38.7790;
            const initialPos = { lat: existingLat, lng: existingLng };

            // Harita Oluşturma
            map = new google.maps.Map(document.getElementById("userMap"), {
                center: initialPos,
                zoom: 15,
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true
            });

            // Marker Oluşturma
            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true, // Marker sürüklenebilir
                animation: google.maps.Animation.DROP
            });

            // Inputları doldur
            updateInputs(existingLat, existingLng);

            // 1. Tıklama ile Konum Belirleme
            map.addListener("click", (e) => {
                const clickedPos = e.latLng;
                marker.setPosition(clickedPos);
                updateInputs(clickedPos.lat(), clickedPos.lng());
            });

            // 2. Marker Sürükleme Bittiğinde
            marker.addListener("dragend", (e) => {
                const draggedPos = marker.getPosition();
                updateInputs(draggedPos.lat(), draggedPos.lng());
            });

            // 3. Arama Kutusu (Autocomplete)
            const input = document.getElementById("map-search-input");
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                marker.setPosition(place.geometry.location);
                updateInputs(place.geometry.location.lat(), place.geometry.location.lng());
            });
        }

        function updateInputs(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);
        }

        // Sayfa yüklendiğinde haritayı başlat
        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
@endsection
