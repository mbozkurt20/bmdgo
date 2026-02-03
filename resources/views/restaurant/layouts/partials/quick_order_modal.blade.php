<script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>

<div id="quickOrderModal" class="custom-modal" style="display: none;">
    <div class="custom-modal-content shadow-lg">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <form id="quickOrderForm" class="p-4">
            @csrf
            <div class="modal-header border-0 pb-3">
                <h4 class="modal-title fw-bold text-dark"><i class="fa fa-rocket me-2 text-warning"></i>Hızlı Sipariş</h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Telefon <small class="text-danger">*</small></label>
                        @include('components.phone',['key' => 'phone', 'required' => true, 'value' => null])
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Müşteri Adı</label>
                        <input type="text" name="full_name" id="full_name" class="form-control rounded-pill" placeholder="Ad Soyad" required>
                    </div>

                    <div id="existing-addresses-area" class="col-12 mb-3" style="display: none;">
                        <label class="form-label fw-bold text-success"><i class="fa fa-list-ul me-1"></i> Kayıtlı Adresleri</label>
                        <select id="address_selector" class="form-select rounded-pill border-success">
                            <option value="new">+ Yeni Adres Kullan / Haritadan Seç</option>
                        </select>
                    </div>

                    <div class="col-12"><hr class="my-2"></div>

                    <div class="col-md-7 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-primary mb-0"><i class="fa fa-map-marked-alt me-1"></i> Konum</label>
                            <span id="location-badge" class="badge bg-danger">Konum Seçilmedi</span>
                        </div>
                        <input id="quick-map-search" class="form-control mb-2 rounded-pill shadow-sm" type="text" placeholder="Adres veya bina ara...">
                        <div id="quickOrderMap" style="height: 320px; width: 100%; border-radius: 15px; border: 2px solid #eee;"></div>

                        <div class="row g-2 mt-2">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light small">Lat</span>
                                    <input type="text" name="latitude" id="quick-lat" class="form-control bg-white" readonly required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light small">Lng</span>
                                    <input type="text" name="longitude" id="quick-lng" class="form-control bg-white" readonly required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold">Adres Detayları</label>
                        <div class="mb-2">
                            <input type="text" name="mahalle" id="quick-mahalle" class="form-control form-control-sm border border-dark border-bottom" placeholder="Mahalle" required>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="sokak_cadde" id="quick-sokak" class="form-control form-control-sm border border-dark border-bottom" placeholder="Sokak / Cadde" required>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6"><input type="text" name="bina_no" id="quick-bina" class="form-control form-control-sm border border-dark border-bottom" placeholder="Bina No" required></div>
                            <div class="col-3"><input type="text" name="kat" id="quick-kat" class="form-control form-control-sm border border-dark border-bottom" placeholder="Kat"></div>
                            <div class="col-3"><input type="text" name="daire_no" id="quick-daire" class="form-control form-control-sm border border-dark border-bottom" placeholder="Daire"></div>
                        </div>
                        <div class="mb-2">
                            <textarea name="adress_tarifi" id="quick-tarif" class="form-control form-control-sm border border-dark" rows="3" placeholder="Adres Tarifi..."></textarea>
                        </div>
                        <input type="hidden" name="ilce" id="quick-ilce">
                        <input type="hidden" name="customer_id" id="quick-customer-id">
                    </div>

                    <div class="col-12"><hr class="my-2"></div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ödeme Yöntemi</label>
                        <select name="payment_method" class="form-select border-dark form-control rounded-pill shadow-sm">
                            <option value="Kapıda Nakit İle Ödeme">Kapıda Nakit</option>
                            <option value="Kapıda Kredi Kartı İle Ödeme">Kapıda Kredi Kartı</option>
                            <option value="Kapıda Ticket İle Ödeme">Kapıda Ticket</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tutar (₺)</label>
                        <input type="number" step="0.01" name="amount" class="border-dark form-control rounded-pill shadow-sm fw-bold text-dark" placeholder="0.00" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0">
                <button id="submitOrderBtn" type="submit" class="special-button w-100 rounded-pill py-3 fw-bold shadow" disabled>
                    KONUM SEÇİNİZ
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); justify-content: center; align-items: center; }
    .custom-modal-content { background: #fff; border-radius: 25px; width: 95%; max-width: 850px; position: relative; animation: modalFadeIn 0.3s ease; max-height: 95vh; overflow-y: auto; }
    @keyframes modalFadeIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .close-btn { position: absolute; top: 15px; right: 25px; font-size: 32px; cursor: pointer; color: #999; z-index: 10; }
    .special-ok-button { background-color: #28a745 !important; color: white !important; border: none; cursor: not-allowed; opacity: 0.8; }
</style>

<script>
    let quickMap, quickMarker, quickGeocoder, quickAutocomplete;
    let isMapInitialized = false;

    // 1. BUTON DURUMU
    function updateLocationStatus() {
        const lat = $('#quick-lat').val();
        const btn = $('#submitOrderBtn');
        const badge = $('#location-badge');
        if (lat && lat !== "") {
            btn.prop('disabled', false).text('SİPARİŞİ ONAYLA VE GÖNDER');
            badge.removeClass('bg-danger').addClass('bg-success').text('Konum Alındı ✓');
        } else {
            btn.prop('disabled', true).text('KONUM SEÇİNİZ');
            badge.removeClass('bg-success').addClass('bg-danger').text('Konum Seçilmedi');
        }
    }

    // 2. HARİTA & ADRES ÇÖZÜMLEME
    function initQuickOrderMap() {
        if (typeof google === 'undefined') return;
        const initialPos = { lat: parseFloat("{{ auth()->user()->admin->latitude }}") || 37.1502, lng: parseFloat("{{ auth()->user()->admin->longitude }}") || 38.7790 };

        quickMap = new google.maps.Map(document.getElementById("quickOrderMap"), { center: initialPos, zoom: 15, mapTypeControl: false, streetViewControl: false });
        quickMarker = new google.maps.Marker({ position: initialPos, map: quickMap, draggable: true });
        quickGeocoder = new google.maps.Geocoder();

        quickAutocomplete = new google.maps.places.Autocomplete(document.getElementById("quick-map-search"));
        quickAutocomplete.addListener("place_changed", () => {
            const place = quickAutocomplete.getPlace();
            if (!place.geometry) return;
            quickMap.setCenter(place.geometry.location);
            quickMarker.setPosition(place.geometry.location);
            fillAddressFromPos(place.geometry.location);
        });

        quickMap.addListener("click", (e) => { quickMarker.setPosition(e.latLng); fillAddressFromPos(e.latLng); });
        quickMarker.addListener("dragend", (e) => { fillAddressFromPos(e.latLng); });
        isMapInitialized = true;
    }

    function fillAddressFromPos(pos) {
        const lat = pos.lat();
        const lng = pos.lng();

        $('#quick-lat').val(lat.toFixed(6));
        $('#quick-lng').val(lng.toFixed(6));
        updateLocationStatus();

        quickGeocoder.geocode({ location: pos }, (results, status) => {
            if (status === "OK" && results[0]) {
                const components = results[0].address_components;
                const fullAddr = results[0].formatted_address;

                let mahalle = '', ilce = '', sokak = '', bNo = '';

                // 1. ADIM: Tüm bileşenleri tara (Genişletilmiş Filtre)
                components.forEach(c => {
                    const types = c.types;

                    // Mahalle için tüm ihtimaller
                    if (types.includes("neighborhood") ||
                        types.includes("sublocality_level_1") ||
                        types.includes("sublocality")) {
                        mahalle = c.long_name;
                    }

                    // İlçe (Türkiye için genelde level_2)
                    if (types.includes("administrative_area_level_2") ||
                        types.includes("district")) {
                        ilce = c.long_name;
                    }

                    if (types.includes("route")) sokak = c.long_name;
                    if (types.includes("street_number")) bNo = c.long_name;
                });

                // 2. ADIM: Yedek Mekanizma (Eğer hala boşsa)
                // Google bazen mahalleyi virgülle ayrılmış tam adresin ilk başına koyar
                if (!mahalle && fullAddr) {
                    const parts = fullAddr.split(',');
                    // Genelde ilk parça Mahalle veya Site adıdır
                    mahalle = parts[0].trim().replace(' Mahallesi', '').replace(' Mah.', '');
                }

                // Formu Doldur
                $('#quick-mahalle').val(mahalle);
                $('#quick-ilce').val(ilce);
                $('#quick-sokak').val(sokak);
                $('#quick-bina').val(bNo);
                $('#quick-tarif').val(fullAddr); // Tam adresi buraya yaz ki operatör görsün
            }
        });
    }

    // 3. FORM GÖNDERME (AJAX)
    document.getElementById("quickOrderForm").addEventListener("submit", async function(e) {
        e.preventDefault();
        const form = e.target;
        const submitBtn = document.getElementById("submitOrderBtn");

        submitBtn.disabled = true;
        submitBtn.textContent = "Sipariş Alınıyor...";
        const originalClasses = submitBtn.className;
        submitBtn.className = "special-ok-button w-100 rounded-pill py-3 fw-bold";

        const data = {
            restaurant_id: {{ auth()->user()->id }},
            full_name: form.full_name.value,
            phone: form.phone.value,
            ilce: form.ilce.value,
            mahalle: form.mahalle.value,
            sokak_cadde: form.sokak_cadde.value,
            bina_no: form.bina_no.value,
            kat: form.kat.value,
            daire_no: form.daire_no.value,
            adress_tarifi: form.adress_tarifi.value,
            latitude: form.latitude.value,
            longitude: form.longitude.value,
            verify_code: Math.floor(100000 + Math.random() * 900000),
            payment_method: form.payment_method.value,
            amount: form.amount.value,
            items: JSON.stringify([{ price: 0.00, unitSellingPrice: 0.00, quantity: 1, productId: 0, name: "Hızlı Sipariş" }])
        };

        try {
            const response = await fetch("{{ route('quick.order.store') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                // Formu temizle
                form.reset();

                // Koordinatları ve badge durumunu sıfırla
                $('#quick-lat').val('');
                $('#quick-lng').val('');
                updateLocationStatus();

                // Modalı kapat
                $('#quickOrderModal').fadeOut(300);

                showToast("Siparişiniz Başarıyla Eklendi", "success");

                // --- ÖNEMLİ: Eğer sayfada DataTable varsa tabloyu yeniler ---
                if ($.fn.DataTable.isDataTable('#example')) { // '#example' tablo id'niz olmalı
                    $('#example').DataTable().ajax.reload(null, false);
                }

                // location.reload(); <-- BU SATIRI SİLDİK, ARTIK YENİLENMEZ

            } else {
                showToast(response.status === 400 ? "Lütfen Adres Bilgilerini Kontrol Edin." : "Bir hata oluştu", "error");
            }
        } catch (error) {
            showToast("Sunucu hatası!", "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = "Siparişi Gönder";
            submitBtn.className = originalClasses;
        }
    });

    // 4. DİĞER FONKSİYONLAR (Modal, Telefon, Toast)
    $(document).on('click', '#openModalBtn2', function() {
        $('#quickOrderModal').css('display', 'flex');
        setTimeout(() => { if (!isMapInitialized) initQuickOrderMap(); else google.maps.event.trigger(quickMap, "resize"); }, 400);
    });

    $(document).on('click', '#closeModalBtn', function() { $('#quickOrderModal').hide(); });

    /*
    $(document).on('input', 'input[name="phone"]', function() {
        let phone = $(this).val();
        if (phone.length >= 10) {
            $.get(`/restaurant/customers/get-by-phone/${phone}`, function(data) {
                if(data.success) {
                    $('#full_name').val(data.customer.name);
                    $('#quick-customer-id').val(data.customer.id);
                    let selector = $('#address_selector');
                    selector.find('option:not([value="new"])').remove();
                    data.addresses.forEach(addr => selector.append(`<option value="${addr.id}" data-all='${JSON.stringify(addr)}'>${addr.mahalle}</option>`));
                    $('#existing-addresses-area').slideDown();
                }
            });
        }
    });
    */

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast ${type} show`;
        toast.style = "position:fixed; top:20px; right:20px; z-index:10000; padding:15px; border-radius:8px; color:white; background:" + (type === 'success' ? '#28a745' : '#dc3545');
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.remove(); }, 3000);
    }
</script>
