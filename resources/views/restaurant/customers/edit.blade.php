@extends('restaurant.layouts.app')
@section('content')
    <style>
        #modalMap { height: 450px; width: 100%; border-radius: 10px; background-color: #eee; }
        .pac-container { z-index: 10000 !important; }
        .item-content {
            background: #ffffff;
            border: 1px solid #e0e0e0 !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            position: relative;
        }
        .address-header {
            background: #f8f9fa;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            padding: 12px 20px;
            border-bottom: 1px solid #eee;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .coord-badge { font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; }
        .coord-missing { background: #ffebee; color: #c62828; }
        .coord-ok { background: #e8f5e9; color: #2e7d32; }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Müşteri Düzenle: {{ $customer->name }}</h4></div>
                    <div class="card-body">
                        <form method="post" class="repeater" id="customerForm" action="{{ route('restaurant.customers.update') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{ $customer->id }}">

                            <div class="row border-bottom pb-4 mb-4">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label fw-bold">Müşteri Adı <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $customer->name) }}" required>
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label fw-bold">Telefon <small class="text-danger">*</small></label>
                                    @include('components.phone',['key' => 'phone', 'required' => true, 'value' => old('phone', $customer->phone)])
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label fw-bold">Telefon 2</label>
                                    @include('components.phone',['key' => 'mobile', 'required' => false, 'value' => old('mobile', $customer->mobile)])
                                </div>
                            </div>

                            <div class="repeater-heading d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5>Adres Bilgileri</h5>
                                    <p><strong>Konumu Düzenle</strong> seçerek konumunuzu onaylayınız</p>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" data-repeater-create>
                                    <i class="fa fa-plus me-1"></i> Yeni Adres Ekle
                                </button>
                            </div>

                            <div data-repeater-list="address">
                                @php
                                    $addresses = old('address') ? old('address') : \App\Models\CustomerAddress::where('customer_id', $customer->id)->get();
                                @endphp

                                @foreach ($addresses as $index => $address)
                                    <div data-repeater-item class="item-content p-4 rounded-3">
                                        {{-- Mevcut adresin ID'sini sakla (Yeni eklenenlerde null gider) --}}
                                        <input type="hidden" name="id" value="{{ is_array($address) ? ($address['id'] ?? '') : $address->id }}">

                                        <div class="address-header">
                                            <span class="fw-bold text-dark"><i class="fa fa-map-pin me-2 text-danger"></i>Adres Kaydı</span>
                                            @php
                                                $lat = is_array($address) ? ($address['latitude'] ?? '') : $address->latitude;
                                                $lng = is_array($address) ? ($address['longitude'] ?? '') : $address->longitude;
                                            @endphp
                                            <span class="coord-badge {{ $lat ? 'coord-ok' : 'coord-missing' }} status-badge">
                                                {{ $lat ? "Onaylandı (".round($lat, 4).", ".round($lng, 4).")" : 'Konum Seçilmedi' }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="mb-3 col-md-4">
                                                        <label class="small fw-bold">Başlık (Ev/İş)</label>
                                                        <input type="text" class="form-control border border-dark" name="name" value="{{ is_array($address) ? $address['name'] : $address->name }}" required>
                                                    </div>
                                                    <div class="mb-3 col-md-4">
                                                        <label class="small fw-bold">İlçe {{ $address->district_id}}</label>
                                                        <select class="form-control border border-dark addr-ilce-select" name="district_id" required>
                                                            <option value="">İlçe Seçiniz</option>
                                                            @foreach($districts as $dist)
                                                                <option value="{{ $dist->id }}"
                                                                    {{ (is_array($address) ? ($address['district_id'] ?? '') : $address->district_id) == $dist->id ? 'selected' : '' }}>
                                                                    {{ $dist->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3 col-md-4">
                                                        <label class="small fw-bold">Mahalle</label>
                                                        <input type="text" class="form-control border border-dark addr-mahalle" name="mahalle" value="{{ is_array($address) ? $address['mahalle'] : $address->mahalle }}" required>
                                                    </div>
                                                    <div class="mb-3 col-md-4">
                                                        <label class="small fw-bold">Sokak/Cadde</label>
                                                        <input type="text" class="form-control border border-dark addr-sokak" name="sokak_cadde" value="{{ is_array($address) ? $address['sokak_cadde'] : $address->sokak_cadde }}" required>
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <label class="small fw-bold">Bina/No</label>
                                                        <input type="text" class="form-control border border-dark addr-bina" name="bina_no" value="{{ is_array($address) ? $address['bina_no'] : $address->bina_no }}" required>
                                                    </div>
                                                    <div class="mb-3 col-md-2">
                                                        <label class="small fw-bold">Kat</label>
                                                        <input type="text" class="form-control border border-dark" name="kat" value="{{ is_array($address) ? ($address['kat'] ?? '') : $address->kat }}">
                                                    </div>
                                                    <div class="mb-3 col-md-2">
                                                        <label class="small fw-bold">Daire</label>
                                                        <input type="text" class="form-control border border-dark" name="daire_no" value="{{ is_array($address) ? ($address['daire_no'] ?? '') : $address->daire_no }}">
                                                    </div>
                                                    <div class="mb-3 col-md-5">
                                                        <label class="small fw-bold">Adres Tarifi</label>
                                                        <input type="text" class="form-control border border-dark addr-tarif" name="adres_tarifi" value="{{ is_array($address) ? ($address['adres_tarifi'] ?? '') : $address->adres_tarifi }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 border-start d-flex flex-column justify-content-center align-items-center">
                                                <button type="button" class="btn btn-outline-success btn-sm w-100 mb-3 open-map-modal">
                                                    <i class="fa fa-map-marker-alt me-2"></i>Konumu Düzenle
                                                </button>

                                                <input type="hidden" class="input-lat" name="latitude" value="{{ $lat }}">
                                                <input type="hidden" class="input-lng" name="longitude" value="{{ $lng }}">

                                                <button type="button" class="btn btn-link text-danger btn-sm p-0" data-repeater-delete>
                                                    <i class="fa fa-trash-alt me-1"></i> Adresi Kaldır
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-success float-end mt-4 px-5">Güncellemeyi Tamamla</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konum Seçiniz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input id="modal-search" class="form-control mb-3" type="text" placeholder="Arama yapın...">
                    <div id="modalMap"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-success" id="confirmLocation">Konumu Onayla</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>

    <script>
        let map, marker, autocomplete, currentRow, geocoder;

        $(document).ready(function () {
            geocoder = new google.maps.Geocoder();

            $('.repeater').repeater({
                initEmpty: false,
                show: function () {
                    $(this).slideDown();
                    $(this).find('.status-badge').removeClass('coord-ok').addClass('coord-missing').text('Konum Seçilmedi');
                    $(this).find('input[type="hidden"]').val('');
                    $(this).find('input[type="text"]').val('');
                },
                hide: function (deleteElement) {
                    if(confirm('Bu adresi silmek istediğinize emin misiniz?')) { $(this).slideUp(deleteElement); }
                }
            });

            function initMap() {
                const initialPos = { lat: 37.1502, lng: 38.7790 };
                map = new google.maps.Map(document.getElementById("modalMap"), { center: initialPos, zoom: 13 });
                marker = new google.maps.Marker({ position: initialPos, map: map, draggable: true });

                autocomplete = new google.maps.places.Autocomplete(document.getElementById("modal-search"));
                autocomplete.addListener("place_changed", () => {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) return;
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                    marker.setPosition(place.geometry.location);
                });
                map.addListener("click", (e) => { marker.setPosition(e.latLng); });
            }
            initMap();

            $(document).on('click', '.open-map-modal', function() {
                currentRow = $(this).closest('[data-repeater-item]');
                $('#mapModal').modal('show');
                setTimeout(() => {
                    google.maps.event.trigger(map, 'resize');
                    let lat = currentRow.find('.input-lat').val();
                    if (lat) {
                        let pos = { lat: parseFloat(lat), lng: parseFloat(currentRow.find('.input-lng').val()) };
                        map.setCenter(pos);
                        marker.setPosition(pos);
                    }
                }, 300);
            });

            $('#confirmLocation').click(function() {
                const pos = marker.getPosition();
                const lat = pos.lat().toFixed(6);
                const lng = pos.lng().toFixed(6);

                currentRow.find('.input-lat').val(lat);
                currentRow.find('.input-lng').val(lng);
                currentRow.find('.status-badge')
                    .removeClass('coord-missing').addClass('coord-ok')
                    .html(`<i class="fa fa-check"></i> Onaylandı (${lat}, ${lng})`);

                geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        const components = results[0].address_components;
                        const fullAddress = results[0].formatted_address;

                        // Değişkenleri en başta güvenli tanımlıyoruz
                        let neighborhood = '';
                        let street = '';
                        let bNo = '';
                        let districtName = '';

                        components.forEach(c => {
                            const types = c.types;

                            // MAHALLE: Google'ın dönebileceği tüm mahalle varyasyonlarını tara
                            if (types.includes("neighborhood") ||
                                types.includes("sublocality_level_1") ||
                                types.includes("administrative_area_level_4")) {
                                neighborhood = c.long_name;
                            }

                            // İLÇE
                            if (types.includes("administrative_area_level_2")) {
                                districtName = c.long_name;
                            }

                            // SOKAK & NO
                            if (types.includes("route")) street = c.long_name;
                            if (types.includes("street_number")) bNo = c.long_name;
                        });

                        // 1. GARANTİ: İlçe (Karaköprü vb.) bulunamazsa tam adresten çek
                        if (!districtName) {
                            const districtMatch = fullAddress.match(/(\w+)\/Şanlıurfa/);
                            if (districtMatch) districtName = districtMatch[1];
                        }

                        // 2. GARANTİ: Mahalle bulunamazsa (Bazen Google vermez)
                        // Genelde tam adresin ilk parçası mahalledir (Örn: "Batıkent, 63320 Karaköprü...")
                        if (!neighborhood) {
                            let addrParts = fullAddress.split(',');
                            if (addrParts.length > 0) neighborhood = addrParts[0].trim();
                        }

                        // İLÇE SELECT BOX EŞLEŞTİRME
                        if (districtName) {
                            let districtSelect = currentRow.find('.addr-ilce-select');
                            districtSelect.find('option').each(function() {
                                let optionText = $(this).text().trim().toLocaleLowerCase('tr');
                                let foundText = districtName.toLocaleLowerCase('tr');
                                if (optionText === foundText) {
                                    districtSelect.val($(this).val()).trigger('change');
                                }
                            });
                        }

                        // FORM ALANLARINA BAS
                        currentRow.find('.addr-mahalle').val(neighborhood);
                        currentRow.find('.addr-sokak').val(street);
                        currentRow.find('.addr-bina').val(bNo);
                        currentRow.find('.addr-tarif').val(fullAddress);
                    }
                });

                $('#mapModal').modal('hide');
            });
        });
    </script>
@endsection
