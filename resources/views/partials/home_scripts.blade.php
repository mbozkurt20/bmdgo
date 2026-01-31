<script>
    const statusMap = {
        'PENDING': 'pending',
        'PREPARED': 'prepared',
        'ASSIGNED': 'assigned',
        'HANDOVER': 'handover',
        'DELIVERED': 'delivered',
        'UNSUPPLIED': 'unsupplied'
    };

    document.addEventListener('DOMContentLoaded', () => {
        fetchOrders();
    });

    function fetchOrders() {
        $.ajax({
            url: '/{{$key}}/orders/ajax',
            method: 'GET', // veya 'POST' gerekiyorsa
            success: function (data) {
                console.log({gird: data})
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

        // Spinner + bekleniyor yazısı
        let loadingSpan = document.createElement('span');
        loadingSpan.className = 'ms-2 d-flex align-items-center';
        loadingSpan.innerHTML = `
        <div class="spinner-border spinner-border-sm me-1" role="status"></div>
        <small>Bekleniyor...</small>
    `;

        // Select elementinin yanına ekle
        selectEl.parentNode.appendChild(loadingSpan);

        // İptal işlemi
        if (action === 'UNSUPPLIED') {
            $('#cancelModal').modal('show');

            $('#confirmCancel').off('click').on('click', function () {
                var cancelReason = $('#cancelReason' + id).val();

                if (cancelReason.trim() === '') {
                    Swal.fire('Lütfen iptal nedenini belirtin.');
                    loadingSpan.remove(); // iptal durumunda spinner kaldır
                    return;
                }
                // Güncelle
                sendOrderStatusUpdate(action, tracking_id, platform, cancelReason, id)
                    .finally(() => loadingSpan.remove());
            });
        } else {
            // Diğer durumlar
            sendOrderStatusUpdate(action, tracking_id, platform, null, id)
                .finally(() => loadingSpan.remove());
        }
    }

    async function sendOrderStatusUpdate(action, tracking_id, platform, message, orderId) {
        var requestData = {
            action: action,
            tracking_id: tracking_id,
            _token: '{{ csrf_token() }}'
        };

        if (message) {
            requestData.message = message;
        }

        console.log({platform:platform})
        $.ajax({
            type: 'POST',
            url: '/{{$key}}/' + platform + '/updateOrderStatus',
            data: requestData,
            success: function (data) {
                Swal.fire({
                    title: 'Sipariş durumu başarıyla değiştirildi.',
                    icon: 'success',
                    confirmButtonText: 'Tamam',
                    confirmButtonColor: '#259a38',
                    cancelButtonColor: '#ec691e',
                })
                $('#cancelModal').modal('hide');

                refreshOrderTable(data.order);
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: 'Hata oluştu!',
                    text: xhr.responseText, // Sunucudan gelen hata mesajını göstermek için
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    }

    async function refreshOrderTable(order) {
        const tabId = statusMap[order.status];
        const rowId = 'data_' + order.id;

        const newRowHtml = await generateOrderRowHtml(order);
        const existingRow = $('#' + rowId);

        if (existingRow.length) {
            // Aynı satır zaten var
            const parentTableId = existingRow.closest("table").attr("id");
            if (parentTableId !== tabId) {
                // Eski tablodan kaldır ve yeni tab’a ekle
                existingRow.remove();
                $('#' + tabId).find('tbody').append(newRowHtml);
            } else {
                // Aynı tabloda ise güncelle
                existingRow.replaceWith(newRowHtml);
            }
        } else {
            // Satır yoksa ekle
            $('#' + tabId).find('tbody').append(newRowHtml);
        }

        // Tab boşsa mesaj göster
        updateTableForStatus(order.status);

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
        const tableBody = document.querySelector(`#${statusMap[status]} tbody`);
        if (!tableBody || tableBody.children.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="10" class="text-center">Sipariş bulunmuyor</td></tr>`;
        }
    }

    function printOrder(orderId) {
        fetch('/{{$key}}/printed/' + orderId)
            .then(response => () => {
                console.log({printOrder: response})
                toastr.success("1 Yeni Sipariş Eklendi ", "Sipariş Başarıyla Eklendi", {
                    positionClass: "toast-top-right",
                    closeButton: true,
                    progressBar: true,
                    timeOut: 1500
                });
            })
            .catch(err => console.error(err));
    }

    const selectBox = document.getElementById("selectedCourier");
    const optionsContainer = document.getElementById("courierOptions");
    const options = document.querySelectorAll(".option");
    const courierIdInput = document.getElementById("courierId");

    selectBox.addEventListener("click", () => {
        optionsContainer.style.display = optionsContainer.style.display === "block" ? "none" : "block";
    });

    options.forEach(option => {
        option.addEventListener("click", (e) => {
            const selectedText = option.innerText;
            const selectedId = option.getAttribute("data-id");

            selectBox.innerHTML = selectedText;
            courierIdInput.value = selectedId;
            optionsContainer.style.display = "none";
        });
    });

    document.addEventListener("click", function (event) {
        if (!selectBox.contains(event.target) && !optionsContainer.contains(event.target)) {
            optionsContainer.style.display = "none";
        }
    });

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
                    // Modalı güvenli kapat
                    const modalElement = document.getElementById('Courier' + orderId);
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Ekran kararmasını önle
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('overflow', '');

                    Swal.fire({
                        title: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    fetchOrders(); // Tabloyu tazele
                } else {
                    Swal.fire('Uyarı', data.message || 'Kurye Atama Başarısız', 'error');
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
            <img src="{{ asset('theme/images/getiryemek.png') }}" style="height:14px;margin-right:4px;">
            ${restaurantName}
        </span>`;
        }
        else if (platform.toLowerCase() === 'gpsyemek') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="{{ asset('theme/images/gpsyemek.png') }}" style="height:20px;margin-right:4px;">

        </span>`;
        } else if (platform.toLowerCase() === 'trendyol') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="{{ asset('theme/images/trendyolyemek.png') }}" style="height:14px;margin-right:4px;">
            ${restaurantName}
        </span>`;
        } else if (platform.toLowerCase() === 'migros') {
            platformHtml = `
        <span class="d-inline-flex align-items-center border rounded-pill px-2 py-1 small">
            <img src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/aff9ed163620751.6556613f80c21.png" style="height:16px;margin-right:4px;">
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
        if (status === 'UNSUPPLIED' || status === 'DELIVERED' || status === 'HANDOVER') {
            courierSection = `
        <a style="cursor:pointer;color: #ec691e">
            <i class="fas fa-truck mr-1"></i> ${order.courier ? order.courier.name.substr(0, 10) : 'Kurye Yok'}
        </a>`;
        } else {
            if (order.courier && order.courier.id) {
                courierSection = `
            <div style="display:flex; align-items:center;">
                <a data-bs-toggle="modal" data-bs-target="#Courier${order.id}" style="cursor:pointer;color: #ec691e">
                   <i class="fas fa-truck mr-1"></i> ${order.courier.name.substr(0, 10)}
                </a>
            </div>`;
            } else {
                courierSection = `
            <a style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#Courier${order.id}" class="sharp text-secondary size-3 px-3 fw-bold">
                <i class="fas fa-truck mr-1"></i> <small>Kurye Ata</small>
            </a>`;
            }
        }


        // Durum seçenekleri
        const statusOptions = `
        <option value="PENDING" ${status == 'PENDING' ? 'selected' : ''}>BEKLİYOR</option>
        <option value="PREPARED" ${status == 'PREPARED' ? 'selected' : ''}>HAZIRLANIYOR</option>
        <option value="DELIVERED" ${status == 'DELIVERED' ? 'selected' : ''}>TESLİM EDİLDİ</option>
        <option value="UNSUPPLIED" ${status == 'UNSUPPLIED' ? 'selected' : ''}>İPTAL EDİLDİ</option>
    `;

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
    <td class="text-ov">${order.payment_method} ₺</td>
    <td class="text-ov">
        <strong class="text-secondary fw-bold" id="distance${order.id}">
            ${distanceStr}
        </strong>
    </td>
    <td>
        <input type="hidden" id="tracking_${order.id}" value="${trackingId}">
        <input type="hidden" id="platform_${order.id}" value="${platform}">
        <select class="inline-order-select form-control" onchange="StatusOrderChange(event, ${order.id})" ${status == 'DELIVERED' ? 'disabled' : ''}>
            ${statusOptions}
        </select>
        <!-- İptal modal -->
        <div class="modal fade" id="cancelModal${order.id}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Siparişi İptal Et</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="cancelReason${order.id}" class="form-label">İptal nedeniniz?</label>
                        <textarea class="form-control" id="cancelReason${order.id}" rows="4" placeholder="İptal nedeninizi yazın..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Geri Dön</button>
                        <button type="button" class="btn btn-danger" onclick="cancelOrder(${order.id})">İptal Et</button>
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
