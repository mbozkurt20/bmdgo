<script>
    const statusMap = {!! json_encode(\App\Helpers\OrderStatus::statuses()) !!};

    document.addEventListener('DOMContentLoaded', () => {
        fetchOrders();
    });

    function fetchOrders(status) {
        $.ajax({
            url: '/{{$key}}/orders/ajax',
            method: 'GET', // veya 'POST' gerekiyorsa
            success: function (data) {
                Object.keys(statusMap).forEach(status => updateTableForStatus(status));

                if (data) {
                    Object.keys(data).forEach(statusKey => {
                        data[statusKey].forEach(order => {
                            refreshOrderTable(order);
                        });
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('Siparişleri alırken hata:', error);
            }
        });
    }

    // Pusher ayarları
    Pusher.logToConsole = false; // debug için true yapabilirsiniz
    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
    });

    let keyId = "{{ auth($key)->id() }}";

    var channel = pusher.subscribe(`{{$key}}-${keyId}`);

    // Gelen veriyi konsolda görebilmek için
    channel.bind('new-order', function (data) {
        console.log('Gelen data:', data);
        if (data.order) {
            refreshOrderTable(data.order);

            if ('{{\App\Helpers\OrdersHelper::getOrderSystem(1)}}') {
                const audio = new Audio('{{asset('voices/order/beep-warning-6387.mp3')}}');
                audio.play().catch(err => {
                    console.error("Ses çalma başarısız:", err);
                });

                const newOrderEl = document.getElementById('newOrder');
                newOrderEl.style.display = 'block'; // Görünür yap
                newOrderEl.classList.add('blink'); // Yanıp sönme efekti

                // 3 saniye sonra gizle
                setTimeout(() => {
                    newOrderEl.style.display = 'none';
                    newOrderEl.classList.remove('blink');
                }, 3000);
            }
        }
    });

    channel.bind('update-order', function (data) {
        console.log('Gelen data:', data);
        if (data.order) {
            refreshOrderTable(data.order);
        }
    });

    function StatusOrderChange(e, id) {
        var action = e.target.value;
        var tracking_id = $('#tracking_' + id).val();
        var platform = $('#platform_' + id).val();
        var selectEl = e.target;

        // Spinner ekle
        let loadingSpan = document.createElement('span');
        loadingSpan.className = 'ms-2 d-flex align-items-center';
        loadingSpan.innerHTML = `
        <div class="spinner-border spinner-border-sm me-1" role="status"></div>
        <small>Bekleniyor...</small>`;
        selectEl.parentNode.appendChild(loadingSpan);

        if (action === 'UNSUPPLIED') {
            // O siparişe özel modalı aç
            var myModal = new bootstrap.Modal(document.getElementById('cancelModal' + id));
            myModal.show();

            // Spinner'ı modal açıldığı için temizle (işlem modal içinde devam edecek)
            loadingSpan.remove();

            // Select box'ı eski haline getirmek isteyebilirsiniz (isteğe bağlı)
        } else {
            // Diğer durumlar için doğrudan güncelle
            sendOrderStatusUpdate(action, tracking_id, platform, null, null)
                .finally(() => loadingSpan.remove());
        }
    }

    async function handleCancelClick(orderId, platform) {
        const platformsWithReasons = ['getir', 'trendyol', 'migros', 'yemeksepeti'];
        const modalElement = document.getElementById('cancelModal' + orderId);
        // HTML'deki ID ile aynı olmalı
        const reasonArea = document.getElementById('reasonSelectionArea' + orderId);

        if (!reasonArea) return; // Element yoksa hata vermemesi için

        reasonArea.innerHTML = '<div class="spinner-border spinner-border-sm text-danger"></div> Nedenler yükleniyor...';

        var myModal = new bootstrap.Modal(modalElement);
        myModal.show();

        // Platform kontrolü (küçük harf duyarlılığı için)
        const currentPlatform = platform ? platform.toLowerCase() : '';

        if (platformsWithReasons.includes(currentPlatform)) {
            try {
                const response = await fetch(`/get-reasons?platform=${currentPlatform}`);
                const reasons = await response.json();

                let options = reasons.map(r => `<option value="${r.id}">${r.name}</option>`).join('');
                reasonArea.innerHTML = `
            <label class="form-label">Platform İptal Nedeni</label>
            <select class="form-select mb-3" id="platformReasonId${orderId}">
                ${options}
            </select>`;
            } catch (error) {
                reasonArea.innerHTML = '<p class="text-danger small">Platform nedenleri yüklenemedi.</p>';
            }
        } else {
            reasonArea.innerHTML = '';
        }
    }

    function confirmCancel(orderId, trackingId, platform) {
        // Butonu bul ve kilitle
        const confirmBtn = document.querySelector(`#cancelModal${orderId} .btn-danger`);
        const originalText = confirmBtn.innerHTML;

        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> İşleniyor...`;

        const reasonId = document.getElementById('platformReasonId' + orderId)?.value || null;
        const note = document.getElementById('cancelReason' + orderId).value;

        // Parametre sırasına dikkat: orderId'yi sendOrderStatusUpdate'e doğru sırayla gönderiyoruz
        sendOrderStatusUpdate('UNSUPPLIED', trackingId, platform, note, orderId, reasonId)
            .catch(() => {
                // Hata olursa butonu eski haline getir
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalText;
            });
    }

    function updateStatusDirectly(id, nextStatus) {
        const tracking_id = $('#tracking_' + id).val();
        const platform = $('#platform_' + id).val();
        const container = document.getElementById(`action-container-${id}`);

        if (container) {
            container.innerHTML = `
            <div class="text-center">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <div class="small">İşleniyor...</div>
            </div>`;
        }

        sendOrderStatusUpdate(nextStatus, tracking_id, platform, null, null);
    }

    function cancelOrder(id) {
        var tracking_id = $('#tracking_' + id).val();
        var platform = $('#platform_' + id).val();
        var cancelReason = $('#cancelReason' + id).val();
        var action = 'UNSUPPLIED';

        if (!cancelReason || cancelReason.trim() === '') {
            Swal.fire('Lütfen iptal nedenini belirtin.');
            return;
        }

        // Butonu pasif yapalım ki mükerrer tıklanmasın
        const btn = event.target;
        btn.disabled = true;

        sendOrderStatusUpdate(action, tracking_id, platform, cancelReason, null)
            .then(() => {
                // Modalı kapat
                const modalEl = document.getElementById('cancelModal' + id);
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
            })
            .finally(() => {
                btn.disabled = false;
            });
    }

    async function sendOrderStatusUpdate(action, tracking_id, platform, message, orderId, entegraReasonId) {
        // Promise döndürüyoruz ki updateStatusDirectly içindeki .then() çalışsın
        return new Promise((resolve, reject) => {
            var requestData = {
                action: action,
                tracking_id: tracking_id,
                _token: '{{ csrf_token() }}'
            };

            if (message) requestData.message = message;
            if (entegraReasonId) requestData.entegraReasonId = entegraReasonId;

            $.ajax({
                type: 'POST',
                url: '/{{$key}}/' + platform + '/updateOrderStatus',
                data: requestData,
                success: function (data) {
                    Swal.fire({
                        title: 'Başarılı!',
                        text: 'Sipariş durumu güncellendi.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // --- MODAL VE GRİ EKRAN TEMİZLİĞİ ---
                    // Dinamik ID ile modalı bul ve kapat
                    const modalEl = document.getElementById('cancelModal' + orderId);
                    if (modalEl) {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    }

                    // Manuel Backdrop Temizliği (Garanti yöntem)
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
                    // ------------------------------------

                    if (typeof refreshOrderTable === "function") {
                        refreshOrderTable(data.order);
                    }

                    resolve(data);
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: 'Hata oluştu!',
                        text: xhr.responseText || 'Bir hata meydana geldi.',
                        icon: 'error',
                        confirmButtonText: 'Tamam'
                    });
                    reject(error);
                }
            });
        });
    }

    async function refreshOrderTable(order) {
        // ÖNEMLİ: Mantıksal Tab Belirleme
        let targetStatusKey = order.status;

        // Eğer statü PREPARED ama kurye atanmışsa, onu ASSIGNED sekmesine zorla
        if (order.status === 'PREPARED' && order.courier_id && order.courier_id != -1) {
            targetStatusKey = 'ASSIGNED';
        }

        const tabId = statusMap[targetStatusKey]; // Hedef tablo ID'si
        const rowId = 'data_' + order.id;

        const newRowHtml = await generateOrderRowHtml(order);
        const existingRow = $('#' + rowId);

        if (existingRow.length) {
            const parentTable = existingRow.closest("table");
            const currentTabId = parentTable.attr("id"); // Mevcut olduğu tablo ID'si

            // Eğer olması gereken yer ile olduğu yer farklıysa taşı
            if (currentTabId !== tabId) {
                existingRow.remove();
                $('#order-tbody-' + tabId).append(newRowHtml); // Tbody ID'nize göre güncelleyin
            } else {
                existingRow.replaceWith(newRowHtml);
            }
        } else {
            // İlk kez ekleniyorsa
            const targetBody = $('#order-tbody-' + tabId);
            if(targetBody.length) {
                targetBody.append(newRowHtml);
            } else {
                // Eğer tbody ID formatınız farklıysa (tabId doğrudan id ise):
                $(`#${tabId}`).find('tbody').append(newRowHtml);
            }
        }

        // Tablo boş/dolu uyarısını güncelle
        Object.keys(statusMap).forEach(status => updateTableForStatus(status));
        if (targetStatusKey === 'ASSIGNED') updateTableForStatus('ASSIGNED');

        if (order.status === 'HANDOVER') {
            await updateCourierOptions(order.id);
        }
    }

    async function updateCourierOptions(orderId) {
        try {
            const couriers = await fetchCouriers();
            const selectElement = document.querySelector(`#Courier${orderId} select`);

            if (selectElement) {
                while (selectElement.options.length > 1) {
                    selectElement.remove(1);
                }

                couriers.forEach(courier => {
                    const option = document.createElement('option');
                    option.value = courier.id;
                    option.textContent = courier.name;
                    selectElement.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Kurye listesi güncellenirken hata:', error);
        }
    }

    function updateTables(data) {
        // Temizle
        Object.keys(data).forEach(status => {
            const tabId = statusMap[status];
            const tbody = document.querySelector(`#${tabId} tbody`);
            if (tbody) {
                tbody.innerHTML = '';
                data[status].forEach(order => {
                    // Her sipariş için satır ekle
                    const row = document.createElement('tr');
                    // Satıra sütunlar ekle
                    // örnek:
                    row.innerHTML = `<td>${order.id}</td><td>${order.customer_name}</td>`;
                    tbody.appendChild(row);
                });
            }
        });
    }

    function openOrderModal(order) {
        const container = document.getElementById('OrdersModal');
        const modalBody = document.querySelector("#OrdersModal .modal-body");

        // İçeriği doldur
        modalBody.innerHTML = `
      <div class="mb-1 col-md-6">
        <p class="orderTitle">Sipariş Kodu</p>
        <p class="orderProde">${order.tracking_id}</p>
      </div>
      <div class="mb-1 col-md-6">
        <p class="orderTitle">Müşteri Adı</p>
        <p class="orderProde">${order.full_name}</p>
      </div>
      <div class="mb-1 col-md-4">
        <p class="orderTitle">Telefon</p>
        <p class="orderProde">${order.phone}</p>
      </div>
      <div class="mb-1 col-md-4">
        <p class="orderTitle">Tutar</p>
        <p class="orderProde">${order.amount} ₺</p>
      </div>
      <div class="mb-1 col-md-4">
        <p class="orderTitle">Ödeme Yön.</p>
        <p class="orderProde">${order.payment_method}</p>
      </div>
      <div class="mb-2 col-md-12">
        <p class="orderTitle">Adres</p>
        <p class="orderProde">${order.address}</p>
      </div>
      <div class="mb-2 col-md-12">
        <p class="">Müşteri Notu</p>
        <p class="orderProde">${order.notes??'Bulunmuyor.'}</p>
      </div>
    `;

        // Ürün tablosu
        const items = JSON.parse(order.items);
        let tableHTML = `
      <div class="mb-3 mt-4 col-md-12">
        <table class="table table-border table-responsive-sm" style="min-width: 28rem !important;">
          <thead>
            <tr>
              <th style="font-size: 14px;font-weight: 600">Ürün</th>
              <th style="font-size: 14px;font-weight: 600">Adeti</th>
              <th style="font-size: 14px;font-weight: 600">Fiyatı</th>
            </tr>
          </thead>
          <tbody>
    `;
        items.forEach(item => {
            tableHTML += `
          <tr>
            <td class="orderProde text-black">${item.name}</td>
            <td class="orderProde text-black">${item.quantity}</td>
            <td class="orderProde text-black">${item.price} ₺</td>
          </tr>
        `;
        });
        tableHTML += `</tbody></table></div>`;

        modalBody.innerHTML += tableHTML;

        // Modal aç
        let modal = new bootstrap.Modal(container);
        modal.show();

        // Butona yazdır işlevi bağla
        document.getElementById("printOrderBtn").onclick = function () {
            let printContent = modalBody.innerHTML;
            let win = window.open("", "_blank", "width=800,height=600");
            win.document.write(`
          <html>
            <head>
              <title>Sipariş Yazdır</title>
              <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
            </head>
            <h1 class="fw-bold text-black text-center mx-auto">Sipariş Bilgileri </h1>
            <body>${printContent}</body>
          </html>
        `);
            win.document.close();
            win.print();
        };
    }

    function updateTableForStatus(status) {
        const tabId = statusMap[status];
        if (!tabId) return;

        const tableBody = document.querySelector(`#${tabId} tbody`);
        if (tableBody) {
            // Eğer tabloda hiç <tr> yoksa veya sadece bizim "Sipariş bulunmuyor" yazımız varsa
            const rows = tableBody.querySelectorAll('tr:not(.no-order-row)');

            if (rows.length === 0) {
                tableBody.innerHTML = `
                <tr class="no-order-row">
                    <td colspan="12" class="text-center py-4">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-box-open mb-2 text-muted" style="font-size: 2rem;"></i>
                            <span class="fw-bold text-muted">Bu kategoride henüz sipariş bulunmuyor.</span>
                        </div>
                    </td>
                </tr>`;
            } else {
                // Eğer sipariş geldiyse uyarı satırını kaldır
                const noOrderRow = tableBody.querySelector('.no-order-row');
                if (noOrderRow) noOrderRow.remove();
            }
        }
    }

    function printOrder(orderId) {
        fetch('/{{$key}}/printed/' + orderId)
            .then(response => () => {
                console.log({printOrder: response})
                toastr.success("Yeni Sipariş Eklendi ", "Sipariş Başarıyla Eklendi", {
                    positionClass: "toast-top-right",
                    closeButton: true,
                    progressBar: true,
                    timeOut: 1500
                });
            })
            .catch(err => console.error(err));
    }

    function deleteOrder(order) {
        let orderid = order;

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/{{$key}}/orders/delete/' + orderid,
            success: function (data) {
                if (data == "OK") {
                    $('#Courier' + orderid).hide();
                    Swal.fire('Sipariş silindi');
                }
                if (data == "ERR") {
                    Swal.fire('Sipariş silinemedi!');
                }

            },
            error: function () {
                console.log(data);
            }
        });
    }

    function Courier(e, orderId) {
        let courierId = e.target.value;
        const selectEl = e.target;

        if (courierId === "0") return; // Seçim yapılmadıysa işlem yapma

        let loadingSpan = document.createElement('span');
        loadingSpan.className = 'ms-2 d-flex align-items-center';
        loadingSpan.innerHTML = `
        <div class="spinner-border spinner-border-sm me-1 mt-2" role="status"></div>
        <small class="mt-2">Kurye Atanıyor...</small>
    `;
        selectEl.parentNode.appendChild(loadingSpan);

        $.ajax({
            type: 'GET',
            url: '/{{$key}}/orders/sendCourier/' + orderId + '/' + courierId,
            dataType: 'json', // JSON beklediğimizi belirttik
            success: function (data) {
                loadingSpan.remove();

                if (data.success) {
                    const modalElement = document.getElementById('Courier' + orderId);
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);

                    // 1. Önce modalı kapatmayı dene
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // 2. Bootstrap'in temizlik yapması için 150ms bekle, sonra zorla temizle
                    setTimeout(() => {
                        $('.modal-backdrop').remove(); // Kalan gri katmanı sil
                        $('body').removeClass('modal-open').css({
                            'overflow': '',
                            'padding-right': ''
                        });

                        // 3. Ekran temizlendikten SONRA tabloyu yenile
                        fetchOrders();
                    }, 150);

                    Swal.fire({
                        title: data.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function (xhr) {
                loadingSpan.remove();
                let errorMsg = 'İşlem sırasında bir hata oluştu!';

                // Backend'den gelen 400 vb. hataların mesajını oku
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                Swal.fire('Üzgünüz :(', errorMsg, 'error');
            }
        });
    }

    function fetchCouriers() {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: '/{{$key}}/get-couriers',
                success: function (data) {
                    resolve(data);
                },
                error: function (xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    function formatDistance(distanceKm) {
        // İlk olarak, sayıya çevrilmeli
        const km = parseFloat(distanceKm);
        if (isNaN(km)) {
            return 'Geçersiz mesafe'; // veya başka uygun bir çıktı
        }

        if (km >= 1) {
            return `${km.toFixed(2)} km`;
        } else if (km >= 0.001) {
            return `${(km * 1000).toFixed(2)} m`;
        } else {
            return `${(km * 100000).toFixed(2)} cm`;
        }
    }

    async function generateOrderRowHtml(order) {
        const couriers = await fetchCouriers();

        const restaurantName = order.restaurant ? order.restaurant.restaurant_name : 'İsim Yok';
        const trackingId = order.tracking_id || '';
        const fullName = order.full_name || '';
        const message = order.message || '';
        const total = (order.sub_amount !== null && order.sub_amount !== undefined) ? parseFloat(order.sub_amount).toFixed(2) : '0.00';
        const amount = (order.amount !== null && order.amount !== undefined) ? parseFloat(order.amount).toFixed(2) : '0.00';
        const discount = (order.discount !== null && order.discount !== undefined) ? parseFloat(order.discount).toFixed(2) : '0.00';
        const platform = order.platform || '';
        const createdAt = order.created_at ? new Date(order.created_at).toLocaleString('tr-TR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }) : '';

        const courierName = order.courier ? order.courier.name : 'Kurye Bulunmuyor';
        const status = order.status; // varsayılan
        const distanceStr = order.distance ? formatDistance(order.distance) : '';

        // Platform ikonu ve renkleri
        let platformHtml = '';
        if (platform.toLowerCase() === 'yemeksepeti') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height:14px;margin-right:4px;">
            ${restaurantName}
        </span>`;
        } else if (platform.toLowerCase() === 'getir') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="{{ asset('theme/images/platforms/getir.png') }}" style="height:35px;margin-right:4px;">
            ${restaurantName}
        </span>`;
        }
        else if (platform.toLowerCase() === 'gpsyemek') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="{{ asset('theme/images/platforms/gpsyemek.png') }}" style="height:20px;margin-right:4px;">

        </span>`;
        } else if (platform.toLowerCase() === 'trendyol') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="{{ asset('theme/images/platforms/trendyol.png') }}" style="height:16px;margin-right:4px;">
            ${restaurantName}
        </span>`;
        } else if (platform.toLowerCase() === 'migros') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="{{asset('theme/images/platforms/migros.png')}}" style="height:16px;margin-right:4px;">
            ${restaurantName}
        </span>`;
        } else if (platform.toLowerCase() === 'adisyo') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="{{ asset('theme/images/adisyoFull.png') }}" style="height:16px;margin-right:4px;">
            ${restaurantName}
        </span>`;
        } else if (platform.toLowerCase() === 'telefonsiparis') {
            platformHtml = `
        <span class="d-inline-flex justify-content-center border rounded-pill px-2 py-1 small w-100 fw-bold">
            ${restaurantName} / POS
        </span>`;
        } else {
            platformHtml = `<span class="badge bg-light text-dark small">${restaurantName}</span>`;
        }

        // Kurye bölümü
        let courierSection = '';
        let courierStatusBadge = ''; // Yeni durum rozeti

        if (status === 'UNSUPPLIED' || status === 'DELIVERED' || status === 'HANDOVER') {
            courierSection = `
        <a style="cursor:pointer;color: #ec691e">
            <i class="fas fa-truck mr-1"></i> ${order.courier ? order.courier.name.substr(0, 10) : 'Kurye Yok'}
        </a>`;
        } else {
            if (order.courier && order.courier.id) {
                // Eğer kurye atanmışsa statüsüne bakalım
                if (status === 'ASSIGNED') {
                    // Statü ASSIGNED ise dükkandan teslim alınmış demektir
                    courierStatusBadge = '<br><span class="badge bg-success" style="font-size: 10px;"><i class="fas fa-check-double"></i>  Paket Kabul Edildi</span>';
                } else if (status === 'PREPARED') {
                    // Statü hala PREPARED ama kuryesi varsa (Bizim assigned sekmesine zorladığımız durum)
                    courierStatusBadge = '<br><span class="badge bg-primary text-white" style="font-size: 10px;"><i class="fas fa-clock"></i> Teslimat Bekliyor</span>';
                }

                courierSection = `
    <div style="display:flex; flex-direction:column; align-items:start;">
        <a data-bs-toggle="modal" data-bs-target="#Courier${order.id}" style="cursor:pointer;color: #ec691e; font-weight:bold;">
           <i class="fas fa-truck mr-1"></i> ${order.courier.name.substr(0, 15)}
        </a>
        ${courierStatusBadge}
    </div>`;
            } else {
                // Kurye yoksa eski "Kurye Ata" butonu
                courierSection = `
    <a style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#Courier${order.id}" class="sharp text-secondary size-3 px-3 fw-bold">
        <i class="fas fa-truck mr-1"></i> <small>Kurye Ata</small>
    </a>`;
            }
        }
        return `
<tr id="data_${order.id}">
    <td>${platformHtml}
        <input type="hidden" value="${trackingId}" id="tracking_${order.id}">
    </td>
    <td>${trackingId}</td>
    <td>${order.platform_date ?? createdAt}</td>
    <td style="width:200px;overflow: hidden;">${fullName}</td>
    <td>
        ${courierSection}
        <!-- Kurye atama modal -->
        <div class="modal fade" id="Courier${order.id}">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><span class="text-danger">(${trackingId})</span> Siparişe Kurye Ata</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding: 1rem;">
                        <div class="row">
                            <div class="mb-1 col-md-12">
                              <select class="single-select-placeholder js-states form-control" onchange="Courier(event, ${order.id})">
    <option value="0">Kurye Seçiniz</option>

    ${/* Eğer kurye atanmışsa (-1, 0 veya null değilse) Boşa Çıkar seçeneğini göster */
            (order.courier_id && order.courier_id != -1 && order.courier_id != 0)
                ? '<option value="-1" class="text-danger fw-bold">Kurye Boşa Çıkar</option>'
                : ''
        }

    ${couriers.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
</select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td class="text-ov">${total} ₺</td>
    <td class="text-ov">${discount} ₺</td>
    <td class="text-ov">${amount} ₺</td>
    <td class="text-ov">${order.payment_method}</td>
    <td class="text-ov">
        <strong class="text-secondary fw-bold" id="distance${order.id}">
            ${distanceStr}
        </strong>
    </td>
 <td>
    <input type="hidden" id="tracking_${order.id}" value="${trackingId}">
    <input type="hidden" id="platform_${order.id}" value="${platform}">

    <div class="d-grid gap-1" id="action-container-${order.id}">
        ${status === 'PENDING'
            ? `<button class="btn btn-sm btn-primary" onclick="updateStatusDirectly('${order.id}', 'PREPARED')">Hazırlandı</button>`
            : ''
        }

        ${status === 'HANDOVER'
            ? `<button class="btn btn-sm btn-success" onclick="updateStatusDirectly('${order.id}', 'DELIVERED')">Teslim Edildi</button>`
            : ''
        }

        ${status !== 'DELIVERED' && status !== 'UNSUPPLIED'
            ? `<button class="btn btn-sm btn-outline-danger" onclick="handleCancelClick('${order.id}', '${platform}')">İptal Et</button>`
            : `<span class="badge bg-light text-dark">${status === 'DELIVERED' ? 'Tamamlandı' : 'İptal Edildi'}</span>`
        }
    </div>

    <div class="modal fade" id="cancelModal${order.id}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Siparişi İptal Et (#${order.id})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-start">
                    <div id="reasonSelectionArea${order.id}" class="mb-3"></div>

                    <label class="form-label">Notunuz</label>
                    <textarea class="form-control" id="cancelReason${order.id}" rows="3" placeholder="Ek açıklama..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="button" class="btn btn-danger" onclick="confirmCancel('${order.id}','${order.tracking_id}','${order.platform}')">İptali Onayla</button>
                </div>
            </div>
        </div>
    </div>
</td>
    <td>
        <!-- İşlem ikonları -->
        <div class="d-flex">
     <a href="#"
   class="btn btn-secondary shadow btn-xs sharp me-1"
   data-order='${JSON.stringify(order)}'
   onclick="openOrderModal(JSON.parse(this.dataset.order))">
   <i class="fas fa-eye"></i>
</a>

<div class="modal fade" id="OrdersModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sipariş Bilgileri</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding: 1rem;">
        <p>Test Modal İçeriği</p>
      </div>
      <div class="modal-footer">
    <button id="printOrderBtn" class="special-button">Yazdır</button>
        <button type="button" class="special-ok-button" data-bs-dismiss="modal">Kapat</button>

      </div>
    </div>
  </div>
</div>

         <a onclick="printOrder(${order.id})" class="btn btn-danger shadow btn-xs sharp me-1"><i class="fas fa-print"></i></a>
            <!--a onclick="deleteOrder(${order.id})" class="btn btn-danger shadow btn-xs sharp me-1"><i class="fa fa-times" aria-hidden="true"></i></a-->
        </div>
    </td>
</tr>`;
    }
</script>
