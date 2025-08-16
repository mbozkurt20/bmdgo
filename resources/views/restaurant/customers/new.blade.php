@extends('restaurant.layouts.app')
@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .map-container {
            border: #0d2646 solid 2px;
            height: 350px;
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
    </style>

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
                                    <input type="text" class="form-control" name="name" placeholder="Müşteri Adı" required>
                                </div>

                                <!-- Telefon -->
                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-dark">Telefon Numarası <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" name="phone" placeholder="Telefon Numarası" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-dark">Telefon Numarası (opsiyonel)</label>
                                    <input type="text" class="form-control" name="mobile" placeholder="Diğer Telefon Numarası">
                                </div>
                            </div>

                            <hr>

                            <!-- Adresler -->
                            <div class="p-3" style="background: #f4f0f0">
                                <div class="repeater-heading mb-3">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <h5>Adres Ekle <small class="text-danger">*</small></h5>
                                        </div>
                                        <div class="col-lg-2 text-end">
                                            <a id="new-add" class="btn btn-sm btn-secondary repeater-add-btn" data-repeater-create>+ Yeni Ekle</a>
                                        </div>
                                    </div>
                                </div>

                                <div data-repeater-list="address">
                                    <div data-repeater-item class="item-content row border p-3 mb-4 rounded">
                                        <!-- Adres Başlığı -->
                                        <div class="mb-3 col-md-5">
                                            <input type="text" class="form-control" name="name" required placeholder="Adres Başlığı">
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <input type="text" class="form-control" name="sokak_cadde" required placeholder="Sokak/Cadde">
                                        </div>

                                        <div class="mb-3 col-md-1 text-end">
                                            <a style="font-size: 20px; cursor: pointer" class="text-danger" data-repeater-delete>
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="bina_no" required placeholder="Bina No">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="kat" required placeholder="Kat">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="daire_no" required placeholder="Daire No">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="mahalle" required placeholder="Mahalle">
                                        </div>

                                        <div class="mb-3 col-md-12">
                                            <input type="text" name="adres_tarifi" class="form-control" required placeholder="Adres Tarifi">
                                        </div>

                                        <!-- Harita -->
                                        <div class="col-md-12 mt-4">
                                            <div class="map-container" id="map-{{ uniqid() }}"></div>
                                        </div>

                                        <!-- Enlem/Boylam -->
                                        <div class="col-md-6 mb-3">
                                            <label class="text-dark">Enlem (Latitude)</label>
                                            <input required type="text" name="latitude" class="form-control lat-input">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="text-dark">Boylam (Longitude)</label>
                                            <input required type="text" name="longitude" class="form-control lng-input">
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

    <!-- Leaflet -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        $(document).ready(function () {
            $('.repeater').repeater({
                initEmpty: true, // başlangıçta boş olacak
                defaultValues: {},
                show: function () {
                    $(this).slideDown();

                    // Benzersiz ID oluşturulup harita başlatılıyor
                    const mapContainer = $(this).find('.map-container');
                    const latInput = $(this).find('.lat-input');
                    const lngInput = $(this).find('.lng-input');

                    // Harita konteynerine benzersiz ID ata
                    const uniqueId = 'map-' + Math.random().toString(36).substr(2, 9);
                    mapContainer.attr('id', uniqueId);

                    // Harita başlat
                    initMap(uniqueId, latInput, lngInput);
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });

            // Varsayılan tek adres için (sayfa yüklendiğinde) bir harita başlat
            const firstMap = $('.repeater [data-repeater-item]').first();
            if (firstMap.length) {
                const mapContainer = firstMap.find('.map-container');
                const latInput = firstMap.find('.lat-input');
                const lngInput = firstMap.find('.lng-input');
                const uniqueId = 'map-' + Math.random().toString(36).substr(2, 9);
                mapContainer.attr('id', uniqueId);
                initMap(uniqueId, latInput, lngInput);
            }

            function initMap(mapId, latInput, lngInput) {
                const defaultLat = {{ auth()->user()->latitude ?? '37.15026069044849' }};
                const defaultLng = {{ auth()->user()->longitude ?? '38.77905463205474' }};

                const map = L.map(mapId).setView([defaultLat, defaultLng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                let marker;

                map.on('click', function (e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;

                    if (marker) {
                        map.removeLayer(marker);
                    }

                    marker = L.marker([lat, lng]).addTo(map);

                    latInput.val(lat);
                    lngInput.val(lng);
                });
            }

            document.getElementById('new-add').click();
        });
    </script>
@endsection
