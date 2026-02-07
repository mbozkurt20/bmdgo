@extends('restaurant.layouts.app')
@section('content')
    <style>
        #modalMap { height: 450px; width: 100%; border-radius: 10px; background-color: #eee; }
        .pac-container { z-index: 10000 !important; }

        /* Kart Tasarımı */
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
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Yeni Müşteri Formu</h4></div>
                    <div class="card-body">
                        <form method="post" class="repeater" id="customerForm" action="{{ route('restaurant.customers.create') }}">
                            @csrf
                            <div class="row border-bottom pb-4 mb-4">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label fw-bold">Müşteri Adı <small class="text-danger">*</small></label>
                                    <input value="{{old('name')}}" type="text" class="form-control" placeholder="Ali Yılmaz" name="name" required>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label fw-bold">Telefon <small class="text-danger">*</small></label>
                                    @include('components.phone',['key' => 'phone', 'required' => true,'value' => null])
                                </div>
                                <div class="mb-3 col-md-4">
                                    <label class="form-label fw-bold">Telefon 2</label>
                                    @include('components.phone',['key' => 'mobile', 'required' => false,'value' => null])
                                </div>
                            </div>

                            <div class="repeater-heading d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5>Adres Bilgileri</h5>
                                    <p class="fw-bold">Konum Seç diyerek konum bilgileriniz giriniz</p>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" data-repeater-create>
                                    <i class="fa fa-plus me-1"></i> Yeni Adres Ekle
                                </button>
                            </div>

                            <div data-repeater-list="address">
                                @if(old('address'))
                                    @foreach(old('address') as $index => $oldAddress)
                                        <div data-repeater-item class="item-content p-4 rounded-3">
                                            <div class="address-header">
                                                <span class="fw-bold text-dark"><i class="fa fa-map-pin me-2 text-danger"></i>Adres Kaydı</span>
                                                <span class="coord-badge {{ $oldAddress['latitude'] ? 'coord-ok' : 'coord-missing' }} status-badge">
                                                    {{ $oldAddress['latitude'] ? 'Onaylandı ('.round($oldAddress['latitude'], 4).')' : 'Konum Seçilmedi' }}
                                                </span>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <div class="mb-3 col-md-4">
                                                            <label class="small fw-bold">Başlık (Ev/İş)</label>
                                                            <input type="text" class="form-control border border-dark" name="name" value="{{ $oldAddress['name'] }}" required>
                                                        </div>
                                                        <div class="mb-3 col-md-4">
                                                            <label class="small fw-bold">İlçe</label>
                                                            <input type="text" class="form-control border border-dark addr-ilce" name="ilce" value="{{ $oldAddress['ilce'] ?? '' }}">
                                                        </div>
                                                        <div class="mb-3 col-md-4">
                                                            <label class="small fw-bold">Mahalle</label>
                                                            <input type="text" class="form-control border border-dark addr-mahalle" name="mahalle" value="{{ $oldAddress['mahalle'] }}" required>
                                                        </div>
                                                        <div class="mb-3 col-md-4">
                                                            <label class="small fw-bold">Sokak/Cadde</label>
                                                            <input type="text" class="form-control border border-dark addr-sokak" name="sokak_cadde" value="{{ $oldAddress['sokak_cadde'] }}" required>
                                                        </div>
                                                        <div class="mb-3 col-md-3">
                                                            <label class="small fw-bold">Bina/No</label>
                                                            <input type="text" class="form-control border border-dark addr-bina" name="bina_no" value="{{ $oldAddress['bina_no'] }}">
                                                        </div>
                                                        <div class="mb-3 col-md-2">
                                                            <label class="small fw-bold">Kat</label>
                                                            <input type="text" class="form-control border border-dark" name="kat" value="{{ $oldAddress['kat'] }}">
                                                        </div>
                                                        <div class="mb-3 col-md-2">
                                                            <label class="small fw-bold">Daire</label>
                                                            <input type="text" class="form-control border border-dark" name="daire_no" value="{{ $oldAddress['daire_no'] }}">
                                                        </div>
                                                        <div class="mb-3 col-md-5">
                                                            <label class="small fw-bold">Adres Tarifi</label>
                                                            <input type="text" class="form-control border border-dark addr-tarif" name="adres_tarifi" value="{{ $oldAddress['adres_tarifi'] }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 border-start d-flex flex-column justify-content-center align-items-center">
                                                    <button type="button" class="btn btn-outline-success btn-sm w-100 mb-3 open-map-modal">
                                                        <i class="fa fa-map-marker-alt me-2"></i>Konum Seç
                                                    </button>
                                                    <input type="hidden" class="input-lat" name="latitude" value="{{ $oldAddress['latitude'] }}">
                                                    <input type="hidden" class="input-lng" name="longitude" value="{{ $oldAddress['longitude'] }}">
                                                    <button type="button" class="btn btn-link text-danger btn-sm p-0" data-repeater-delete>
                                                        <i class="fa fa-trash-alt me-1"></i> Adresi Kaldır
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Eğer hata yoksa ve sayfa ilk defa açılıyorsa boş bir tane göster --}}
                                    <div data-repeater-item class="item-content p-4 rounded-3">
                                        <div class="address-header">
                                            <span class="fw-bold text-dark"><i class="fa fa-map-pin me-2 text-danger"></i>Adres Kaydı</span>
                                            <span class="coord-badge coord-missing status-badge">Konum Seçilmedi</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="mb-3 col-md-4">
                                                        <label class="small fw-bold">Başlık (Ev/İş)</label>
                                                        <input type="text" class="form-control border border-dark" name="name" required>
                                                    </div>
                                                    <div class="mb-3 col-md-4">
                                                        <label class="small fw-bold">İlçe</label>
                                                        <input type="text" class="form-control border border-dark addr-ilce" name="ilce">
                                                    </div>
                                                    <div class="mb-3 col-md-4">
                                                        <label class="small fw-bold">Mahalle</label>
                                                        <input type="text" class="form-control border border-dark addr-mahalle" name="mahalle" required>
                                                    </div>
                                                    <div class="mb-3 col-md-4">
                                                        <label class="small fw-bold">Sokak/Cadde</label>
                                                        <input type="text" class="form-control border border-dark addr-sokak" name="sokak_cadde" required>
                                                    </div>
                                                    <div class="mb-3 col-md-3">
                                                        <label class="small fw-bold">Bina/No</label>
                                                        <input type="text" class="form-control border border-dark addr-bina" name="bina_no" required>
                                                    </div>
                                                    <div class="mb-3 col-md-2">
                                                        <label class="small fw-bold">Kat</label>
                                                        <input type="text" class="form-control border border-dark" name="kat">
                                                    </div>
                                                    <div class="mb-3 col-md-2">
                                                        <label class="small fw-bold">Daire</label>
                                                        <input type="text" class="form-control border border-dark" name="daire_no">
                                                    </div>
                                                    <div class="mb-3 col-md-5">
                                                        <label class="small fw-bold">Adres Tarifi</label>
                                                        <input type="text" class="form-control border border-dark addr-tarif" name="adres_tarifi">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 border-start d-flex flex-column justify-content-center align-items-center">
                                                <button type="button" class="btn btn-outline-success btn-sm w-100 mb-3 open-map-modal">
                                                    <i class="fa fa-map-marker-alt me-2"></i>Konum Seç
                                                </button>
                                                <input type="hidden" class="input-lat" name="latitude">
                                                <input type="hidden" class="input-lng" name="longitude">
                                                <button type="button" class="btn btn-link text-danger btn-sm p-0" data-repeater-delete>
                                                    <i class="fa fa-trash-alt me-1"></i> Adresi Kaldır
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-success float-end mt-4 px-5">Kaydı Tamamla</button>
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

            // REPEATER ÇİFTLEME SORUNUNU ÇÖZEN BAŞLATMA
            var $repeater = $('.repeater').repeater({
                initEmpty: false, // Sayfa açıldığında 1 tane boş gelsin mi?
                show: function () {
                    $(this).slideDown();
                    // Yeni eklenen öğe için badge sıfırla
                    $(this).find('.status-badge').removeClass('coord-ok').addClass('coord-missing').text('Konum Seçilmedi');
                    $(this).find('input[type="hidden"]').val('');
                },
                hide: function (deleteElement) {
                    if(confirm('Emin misiniz?')) { $(this).slideUp(deleteElement); }
                }
            });

            // Form Gönderme Kontrolü (Lat/Lng Zorunluluğu)
            $('#customerForm').on('submit', function(e) {
                let allSet = true;
                $('.input-lat').each(function() {
                    if (!$(this).val()) { allSet = false; }
                });
                if (!allSet) {
                    e.preventDefault();
                    alert("Lütfen tüm adresler için haritadan konum seçiniz!");
                }
            });

            // Harita Fonksiyonları
            function initMap() {
                const initialPos = {!! $location !!};
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
                    .removeClass('coord-missing')
                    .addClass('coord-ok')
                    .html(`<i class="fa fa-check"></i> Onaylandı (${lat}, ${lng})`);

                geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        const components = results[0].address_components;
                        const fullAddress = results[0].formatted_address;

                        // Değişkenleri tanımlıyoruz (neighborhood hatasını önlemek için let kullanıyoruz)
                        let neighborhood = '';
                        let street = '';
                        let bNo = '';
                        let district = '';

                        // 1. Standart Etiket Taraması
                        components.forEach(c => {
                            const types = c.types;

                            // MAHALLE (Varyasyonları ile birlikte)
                            if (types.includes("neighborhood") ||
                                types.includes("sublocality_level_1") ||
                                types.includes("administrative_area_level_4")) {
                                neighborhood = c.long_name;
                            }

                            // İLÇE
                            if (types.includes("administrative_area_level_2") || types.includes("district")) {
                                district = c.long_name;
                            }

                            // SOKAK / CADDE
                            if (types.includes("route")) {
                                street = c.long_name;
                            }

                            // BİNA NO
                            if (types.includes("street_number")) {
                                bNo = c.long_name;
                            }
                        });

                        // 2. GARANTİ ADIMLARI (Etiketlerden gelmezse adresten parçala)

                        // İlçe Garantisi (Karaköprü/Şanlıurfa kalıbı)
                        if (!district) {
                            const districtMatch = fullAddress.match(/(\w+)\/Şanlıurfa/);
                            if (districtMatch) district = districtMatch[1];
                        }

                        // Mahalle Garantisi (Adresin ilk parçası genelde mahalledir)
                        if (!neighborhood) {
                            let parts = fullAddress.split(',');
                            if (parts.length > 0) neighborhood = parts[0].trim();
                        }

                        // Sokak Garantisi
                        if (!street && fullAddress.includes('Sokak')) {
                            let streetMatch = fullAddress.match(/(\d+\.?\s?Sokak)/);
                            if (streetMatch) street = streetMatch[0];
                        }

                        // Form alanlarına doldur
                        currentRow.find('.addr-mahalle').val(neighborhood);
                        currentRow.find('.addr-ilce').val(district);
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
