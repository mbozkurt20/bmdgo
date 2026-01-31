@extends('superadmin.layouts.app')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        #map {
            border: #259a38 solid 2px;
            height: 500px;
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        /* Arama kutusunun harita üzerinde şık durması için */
        .map-search-container {
            margin-bottom: 15px;
        }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Yönetici Ekle</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yönetici</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Yönetici Formu</h4>
                    </div>
                    <div class="card-body">
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

                        <div class="basic-form">
                            <form method="post" action="{{route('superadmin.admin_create_request')}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label text-success fw-bold">Tanımlı Kontör Ücreti (1 Kontör)</label>
                                        <input required type="text" class="border border-success form-control text-black fw-bold" name="top_up_price" placeholder="Kontör Ücreti">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Yönetici Adı</label>
                                        <input required type="text" class="form-control" name="name" placeholder="Yönetici Adı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Email</label>
                                        <input required type="email" class="form-control" name="email" placeholder="Email">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Telefon Numarası</label>
                                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Parola</label>
                                        <input required type="text" class="form-control" name="password" placeholder="Şifre Giriniz">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Şehir</label>
                                        <select required class=" form-control select2" name="city_id" id="city-select">
                                            <option value="">Şehir Seç</option>
                                            @foreach($cities as $city)
                                                <option value="{{$city->id}}" data-lat="{{$city->lat}}" data-lng="{{$city->lng}}">{{$city->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">İlçe</label>
                                        <select required class="form-control select2" name="district_id" id="district-select">
                                            <option value="">İlçe Seç</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <div class="mt-4 mb-3">
                                    <label class="form-label fw-bold text-primary">Haritada Adres Ara</label>
                                    <div class="map-search-container">
                                        <input id="pac-input" class="form-control mb-2" type="text" placeholder="Adres, mahalle veya mekan arayın...">
                                    </div>
                                    <p class="text-danger fw-bold"><i class="fa fa-map-marker"></i> Aradığınız yeri seçin veya haritaya tıklayarak işaretleyin.</p>
                                    <div id="map"></div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label text-black">Lokasyon (Latitude)</label>
                                        <input required type="text" class="form-control" id="latit" name="lat" readonly>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label text-black">Lokasyon (Longitude)</label>
                                        <input required type="text" id="longi" class="form-control" name="lng" readonly>
                                    </div>
                                </div>

                                <div class="mb-3 mt-3">
                                    <label for="adres" class="form-label text-black">Tam Adres (Haritadan otomatik gelir, düzenleyebilirsiniz)</label>
                                    <textarea id="address_field" rows="4" name="address" class="form-control"></textarea>
                                </div>

                                <button type="submit" class="float-end special-button btn btn-success">Kaydı Tamamla</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places&callback=initMap" async defer></script>
    <script>
        let map;
        let marker;
        let autocomplete;
        let geocoder; // Adres bulucu eklendi

        function initMap() {
            geocoder = new google.maps.Geocoder();

            const initialPos = {
                lat: {{ $dealer->latitude ?? 37.1502 }},
                lng: {{ $dealer->longitude ?? 38.7790 }}
            };

            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 13,
                mapTypeControl: true,
                streetViewControl: false
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true
            });

            // Autocomplete
            const input = document.getElementById("pac-input");
            autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo("bounds", map);
            input.addEventListener("keydown", function(e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            });

            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                updateLocationInputs(place.geometry.location);
                if(place.formatted_address) {
                    document.getElementById("address_field").value = place.formatted_address;
                }
            });

            // Haritaya tıklandığında
            map.addListener("click", (event) => {
                updateLocationInputs(event.latLng);
                geocodeAddress(event.latLng); // Tıklanan yerin adresini al
            });

            // Marker sürüklendiğinde
            marker.addListener("dragend", () => {
                updateLocationInputs(marker.getPosition());
                geocodeAddress(marker.getPosition()); // Sürüklenen yerin adresini al
            });
        }

        // Koordinattan adres bulma fonksiyonu
        function geocodeAddress(latLng) {
            geocoder.geocode({ location: latLng }, (results, status) => {
                if (status === "OK") {
                    if (results[0]) {
                        document.getElementById("address_field").value = results[0].formatted_address;
                    }
                }
            });
        }

        function updateLocationInputs(location) {
            marker.setPosition(location);

            // Check if .lat is a function (Google object) or a number (Plain object)
            const lat = typeof location.lat === 'function' ? location.lat() : location.lat;
            const lng = typeof location.lng === 'function' ? location.lng() : location.lng;

            document.getElementById("latit").value = lat;
            document.getElementById("longi").value = lng;
        }

        $(document).ready(function () {
            $('.select2').select2();

            $('#city-select').on('change', function () {
                const cityId = $(this).val();
                var selectedOption = $(this).find('option:selected');
                var lat = parseFloat(selectedOption.data('lat'));
                var lng = parseFloat(selectedOption.data('lng'));

                if (lat && lng && map) {
                    const newPos = { lat: lat, lng: lng };
                    map.setCenter(newPos);
                    map.setZoom(12);
                    updateLocationInputs(newPos);
                    geocodeAddress(newPos);
                }

                if (cityId) {
                    console.log(cityId)
                    $.ajax({
                        url: '/superadmin/get-districts/' + cityId,
                        type: 'GET',
                        beforeSend: function() {
                            $('#district-select').html('<option>Yükleniyor...</option>');
                        },
                        success: function (data) {
                            let options = '<option value="">İlçe Seç</option>';
                            $.each(data, function (key, value) {
                                options += `<option value="${value.id}">${value.name}</option>`;
                            });
                            $('#district-select').html(options).trigger('change');
                        },
                        error: function() {
                            $('#district-select').html('<option value="">Hata Oluştu</option>');
                        }
                    });
                } else {
                    $('#district-select').html('<option value="">Önce Şehir Seçin</option>').trigger('change');
                }
            });
        });
    </script>
@endsection
