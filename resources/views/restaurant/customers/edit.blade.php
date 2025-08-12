@extends('restaurant.layouts.app')
@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .map-container {
            border: #0d2646 solid 2px;
            height: 300px;
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
                                        <div class="repeater-heading">
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    <h5 class="pull-left">Adres Ekle</h5>
                                                </div>
                                                <div class="col-lg-2" style="text-align: right">
                                                    <a class="special-ok-button-small btn-xs repeater-add-btn" data-repeater-create>+ Yeni Ekle
                                                    </a>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="clearfix"></div>
                                        <!-- Repeater Items -->
                                        <div data-repeater-list="address">
                                            @foreach (\App\Models\CustomerAddress::where('customer_id', $customer->id)->get() as $adres)
                                                <!-- Repeater Content -->
                                                <div data-repeater-item class="item-content row"
                                                    style="background: #f4f4f4;margin: 15px 0px  10px;padding:10px 0px;border-radius: 10px">
                                                    <input req type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                    <input type="hidden" name="id" value="{{ $adres->id }}">
                                                    <input type="hidden" name="type" value="up">
                                                    <div class="mb-3 col-md-5">
                                                        <input type="text" class="form-control" name="name" required
                                                            placeholder="Adres Başlığı" value="{{ $adres->name }}">
                                                    </div>
                                                    <div class="mb-3 col-md-6">
                                                        <input type="text" class="form-control" name="sokak_cadde" required
                                                            placeholder="Sokak/Cadde" value="{{ $adres->sokak_cadde }}">
                                                    </div>
                                                    <div class="mb-3 col-md-1">
                                                        <div class="pull-right repeater-remove-btn">
                                                            <a id="remove-btn" style="font-size: 20px;cursor: pointer"
                                                                class="text-danger" data-repeater-delete>
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <input type="text" class="form-control" name="bina_no" required
                                                            value="{{ $adres->bina_no }}" placeholder="Bina No">
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <input type="text" class="form-control" name="kat" required
                                                            value="{{ $adres->kat }}" placeholder="Kat">
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <input type="text" class="form-control" name="daire_no" required
                                                            value="{{ $adres->daire_no }}" placeholder="Daire No">
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <input type="text" class="form-control" name="mahalle" required
                                                            value="{{ $adres->mahalle }}" placeholder="Mahalle">
                                                    </div>
                                                    <div class="mb-3 col-md-12">
                                                        <input type="text" name="adres_tarifi" class="form-control"
                                                            value="{{ $adres->adres_tarifi }}"
                                                            placeholder="Adres Tarifi">
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="map-container" id="map-{{ uniqid() }}"></div>
                                                    </div>

                                                    <!-- Enlem/Boylam -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="text-dark">Enlem (Latitude)</label>
                                                        <input required type="text" value="{{ $adres->latitude }}" name="latitude" class="form-control lat-input">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="text-dark">Boylam (Longitude)</label>
                                                        <input required type="text"   value="{{ $adres->longitude }}" name="longitude" class="form-control lng-input">
                                                    </div>

                                                </div>
                                                <!-- Repeater Remove Btn -->

                                                <div class="clearfix"></div>
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

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        $(document).ready(function () {
            const initializedMaps = new Set();

            function initMap(mapId, latInput, lngInput) {
                if (initializedMaps.has(mapId)) return; // Eğer zaten başlatıldıysa atla
                initializedMaps.add(mapId);

                const defaultLat = parseFloat(latInput.val()) || 37.15026069044849;  // Varsayılan koordinat
                const defaultLng = parseFloat(lngInput.val()) || 38.77905463205474;

                const map = L.map(mapId).setView([defaultLat, defaultLng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                let marker;

                if (latInput.val() && lngInput.val()) {
                    const lat = parseFloat(latInput.val());
                    const lng = parseFloat(lngInput.val());
                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 15);
                }

                map.on('click', function (e) {
                    const lat = e.latlng.lat.toFixed(6);
                    const lng = e.latlng.lng.toFixed(6);

                    if (marker) {
                        map.removeLayer(marker);
                    }

                    marker = L.marker([lat, lng]).addTo(map);

                    latInput.val(lat);
                    lngInput.val(lng);
                });
            }

            // Sayfa açıldığında mevcut map-container'ları başlat
            $('.map-container').each(function () {
                const mapId = $(this).attr('id');
                if (!mapId) return;

                const container = $(this).closest('[data-repeater-item]');
                const latInput = container.find('input.lat-input');
                const lngInput = container.find('input.lng-input');

                initMap(mapId, latInput, lngInput);
            });

            // Repeater plugin için
            $('.repeater').repeater({
                show: function () {
                    const $this = $(this);
                    $this.slideDown(400, function () {
                        const mapContainer = $this.find('.map-container');

                        if (!mapContainer.attr('id')) {
                            const newId = 'map-' + Math.random().toString(36).substr(2, 9);
                            mapContainer.attr('id', newId);
                        }

                        const latInput = $this.find('input.lat-input');
                        const lngInput = $this.find('input.lng-input');

                        initMap(mapContainer.attr('id'), latInput, lngInput);
                    });
                },
                hide: function (deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });
        });
    </script>

@endsection
