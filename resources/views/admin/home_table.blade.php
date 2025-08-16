<div class="col-xl-12 col-xxl-8 mt-4">
    <ul class="nav nav-tabs" id="orderStatusTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">Bekliyor</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prepared-tab" data-bs-toggle="tab" data-bs-target="#prepared" type="button" role="tab">Hazırlanıyor</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="handover-tab" data-bs-toggle="tab" data-bs-target="#handover" type="button" role="tab">Kuryeye Verildi</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button" role="tab">Teslim Edildi</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="unsupplied-tab" data-bs-toggle="tab" data-bs-target="#unsupplied" type="button" role="tab">İptal Edildi</button>
        </li>
    </ul>

    @php
        $statuses = [
            'PENDING' => 'pending',
            'PREPARED' => 'prepared',
            'HANDOVER' => 'handover',
            'DELIVERED' => 'delivered',
            'UNSUPPLIED' => 'unsupplied'
        ];
    @endphp

    <h2 id="newOrder" class="mt-4 bg-white py-2 text-center mx-auto fw-bold mb-4" style="display:none;color: rgba(231,0,77,0.82)" >
        Yeni Sipariş Geldi
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="orderStatusTabsContent">
                @foreach ($statuses as $statusKey => $statusId)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $statusId }}" role="tabpanel" aria-labelledby="{{ $statusId }}-tab">
                        <div class="table-responsive">
                            <table class="order-table shadow-hover card-table table table-bordered table-hover shadow-hover card-table text-black" style="min-width: 845px">
                                <thead>
                                <tr>
                                    <th style="color:#0d2646;width:12%;font-size: 14px;font-weight: bold">Restaurant</th>
                                    <th style="color:#0d2646;width:8%;font-size: 14px;font-weight: bold">Sipariş No</th>
                                    <th style="color:#0d2646;font-size: 14px;font-weight: bold">Saati</th>
                                    <th style="color:#0d2646;width:8%;font-size: 14px;font-weight: bold">Müşteri</th>
                                    <th style="color:#0d2646;width:10%;font-size: 14px;font-weight: bold">Kurye</th>
                                    <th style="color:#0d2646;font-size: 14px;font-weight: bold">Ara Tutar</th>
                                    <th style="color:#0d2646;font-size: 14px;font-weight: bold">İndirim</th>
                                    <th style="color:#0d2646;font-size: 14px;font-weight: bold">Tutar</th>
                                    <th style="color:#0d2646;font-size: 14px;font-weight: bold">Ödeme Türü</th>
                                    <th style="color:#0d2646;width:8%;font-size: 14px;font-weight: bold">Paket Mesafesi</th>
                                    <th style="color:#0d2646;font-size: 14px;font-weight: bold">Durum</th>
                                    <th style="color:#0d2646;font-size: 14px;font-weight: bold">İşlem</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tumu->where('status', $statusKey) as $order)
                                    @include('admin.partials.orders.orders_row', ['order' => $order])
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function printOrder(orderId) {
        fetch('/admin/printed/' + orderId) // veya route() ile endpoint
            .then(response => response.json())
            .then(data => {
                var printWindow = window.open('', '', 'height=600,width=400');
                printWindow.document.write(data.printed);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            })
            .catch(err => console.error(err));
    }
</script>
<script>
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
</script>
<script>
    function deleteOrder(order) {
        let orderid = order;

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/admin/orders/delete/' + orderid,
            success: function(data) {
                if (data == "OK") {
                    $('#Courier' + orderid).hide();
                    Swal.fire('Sipariş silindi');
                }
                if (data == "ERR") {
                    Swal.fire('Sipariş silinemedi!');
                }

            },
            error: function() {
                console.log(data);
            }
        });
    }

    function sendOrderStatusUpdate(action, tracking_id, platform, message, orderId) {
        var requestData = {
            action: action,
            tracking_id: tracking_id,
            _token: '{{ csrf_token() }}'
        };

        if (message) {
            requestData.message = message;
        }

        $.ajax({
            type: 'POST',
            url: '/admin/' + platform + '/updateOrderStatus',
            data: requestData,
            success: function(data) {
                Swal.fire({
                    title: 'Sipariş durumu başarıyla değiştirildi.',
                    icon: 'success',
                    confirmButtonText: 'Tamam',
                    confirmButtonColor: '#0d2646',
                    cancelButtonColor: '#e7004d',
                })
                $('#cancelModal').modal('hide');
            },
            error: function(xhr, status, error) {
                console.log('Failed to update order status');
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response Text:', xhr.responseText); // Daha detaylı hata mesajını gösterir
                Swal.fire({
                    title: 'Hata oluştu!',
                    text: xhr.responseText, // Sunucudan gelen hata mesajını göstermek için
                    icon: 'error',
                    confirmButtonText: 'Tamam'
                });
            }
        });
    }

    function StatusOrderChange(e, id) {
        var action = e.target.value;
        var tracking_id = $('#tracking_' + id).val();
        var platform = $('#platform_' + id).val();

        // İptal işlemi için modal açılır
        if (action === 'UNSUPPLIED') {
            $('#cancelModal').modal('show');

            $('#confirmCancel').off('click').on('click', function() {
                var cancelReason = $('#message').val();

                if (cancelReason.trim() === '') {
                    Swal.fire('Lütfen iptal nedenini belirtin.');
                    return;
                }

                // Sipariş durumu ve kurye durumu güncellemesi
                sendOrderStatusUpdate(action, tracking_id, platform, cancelReason, id);
            });
        } else {
            // Diğer durumlar için doğrudan sipariş durumu ve kurye durumu güncellemesi
            sendOrderStatusUpdate(action, tracking_id, platform, null, id);
        }
    }

    function Courier(e, order) {
        let courier = e.target.value;
        let orderid = order;

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/admin/orders/sendCourier/' + orderid + '/' + courier,
            success: function(data) {
                if (data == "OK") {
                    $('#Courier' + orderid).hide();
                    Swal.fire('Kurye başarılı bir şekilde atand!!');
                }
                if (data == "ERR") {
                    Swal.fire('Kurye molada veya müsait deil!!');
                }

            },
            error: function() {
                console.log(data);
            }
        });

    }

    // Pusher ayarları
    Pusher.logToConsole = false; // debug için true yapabilirsiniz
    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
        cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
    });

    var channel = pusher.subscribe('orders');

    // Gelen veriyi konsolda görebilmek için
    channel.bind('new-order', function(data) {
        console.log('Gelen data:', data);
        if(data.order) {
            refreshOrderTable(data.order);

            if ({{\App\Helpers\OrdersHelper::getOrderSystem(1)}}){
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

    channel.bind('update-order', function(data) {
        console.log('Gelen data:', data);
        if(data.order) {
            refreshOrderTable(data.order);
        }
    });

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

    // Tabloda var olan satırı güncelle veya yeni satır ekle
    function refreshOrderTable(order) {
        // Duruma göre uygun sekme ID'sini belirle
        const statusMap = {
            'PENDING': 'pending',
            'PREPARED': 'prepared',
            'HANDOVER': 'handover',
            'DELIVERED': 'delivered',
            'UNSUPPLIED': 'unsupplied'
        };
        const tabId = statusMap[order.status]; // Duruma göre tab id'si

        // Satırı güncelle veya yeni satır ekle
        const rowId = 'data_' + order.id;
        const existingRow = document.getElementById(rowId);
        const newRowHtml = generateOrderRowHtml(order);

        if (existingRow) {
            // Satır güncelle
            $('#'+rowId).replaceWith(newRowHtml);
        } else {
            // Yeni satır ekle
            $('#'+tabId).find('tbody').append(newRowHtml);
        }
    }

    function getCouriers(){
        let couriers = [];

        $.ajax({
            type: 'GET',
            url: '/admin/get-couriers',
            success: function(data) {
                console.log({hele: data})
                couriers = data
            },
            error: function(xhr, status, error) {
            }
        });

        return couriers;
    }

    function generateOrderRowHtml(order) {
        console.log('Order detayları:', order); // Data yapısını incele

        const restaurantName = order.restaurant ? order.restaurant.restaurant_name : 'İsim Yok';
        const trackingId = order.tracking_id || '';
        const fullName = order.full_name || '';
        const message = order.message || '';
        const total = (order.sub_amount !== null && order.sub_amount !== undefined) ? parseFloat(order.sub_amount).toFixed(2) : '0.00';
        const amount = (order.amount !== null && order.amount !== undefined) ? parseFloat(order.amount).toFixed(2) : '0.00';
        const discount = (order.discount !== null && order.discount !== undefined) ? parseFloat(order.discount).toFixed(2) : '0.00';
        const platform = order.platform || '';
        const createdAt = order.created_at ? new Date(order.created_at).toLocaleString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '';
        const courierName = order.courier ? order.courier.name : 'Kurye Yok';
        const status = order.status; // varsayılan
        const couriers =  getCouriers() || []; // Kurye listesi
        const distanceStr = order.distance ? formatDistance(order.distance) : '';

        // Platform ikonu ve renkleri
        let platformHtml = '';
        if (platform.toLowerCase() === 'yemeksepeti') {
            platformHtml = `<a class="btn btn-primary btn-rounded" style="padding: 5px;background: #fb0050;border-color: #fb0050; font-size:12px;">
            ${restaurantName} /
            <img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height: 15px">
        </a>`;
        } else if (platform.toLowerCase() === 'getir') {
            platformHtml = `<a class="btn btn-primary btn-rounded" style="padding: 5px;background: #6244be;border-color: #6244be; font-size:12px;">
            ${restaurantName} /
            <img src="{{ asset('theme/images/getiryemek.png') }}" style="height: 15px">
        </a>`;
        } else if (platform.toLowerCase() === 'trendyol') {
            platformHtml = `<a style="padding: 5px;background: #6244be;border-color: #6244be; font-size:12px;" class="btn btn-primary btn-rounded">
            ${restaurantName} /
            <img src="{{ asset('theme/images/trendyolyemek.png') }}" style="height: 15px">
        </a>`;
        } else if (platform.toLowerCase() === 'migros') {
            platformHtml = `<a style="padding: 5px;background: #000080;border-color: #6244be; font-size: 12px" class="btn btn-primary btn-rounded">
            ${restaurantName} /
            <img src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/aff9ed163620751.6556613f80c21.png" style="height: 25px;">
        </a>`;
        } else if (platform.toLowerCase() === 'adisyo') {
            platformHtml = `<a style="padding: 5px;background: #ff0a0a;border-color: #fff; font-size: 14px" class="btn btn-primary btn-rounded">
            ${restaurantName} /
            <img src="{{ asset('theme/images/adisyoFull.png') }}" style="height: 25px;">
        </a>`;
        } else if (platform.toLowerCase() === 'telefonsiparis') {
            platformHtml = `<a class="special-ok-button btn-rounded" style="width:100%;font-weight: bold;padding:10px 15px;font-size:12px;">
            ${restaurantName} / POS
        </a>`;
        } else {
            // Varsayılan
            platformHtml = `<span>${restaurantName}</span>`;
        }

        // Kurye bölümü
        let courierSection = '';
        if (order.courier && order.courier.id) {
            courierSection = `
        <div style="display:flex; align-items:center;">

            <a data-bs-toggle="modal" data-bs-target="#Courier${order.id}" style="cursor:pointer;color: #e7004d">
               <i class="fas fa-truck mr-1"></i> ${order.courier.name.substr(0, 10)}
            </a>
        </div>`;
        } else {
            courierSection = `
        <a style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#Courier${order.id}" class="sharp text-secondary size-3 px-3 fw-bold">
            <i class="fas fa-truck mr-1"></i> <small>Kurye Ata</small>
        </a>`;
        }

        // Durum seçenekleri
        const statusOptions = `
        <option value="1" ${status == 'PENDING' ? 'selected' : ''}>BEKLİYOR</option>
        <option value="2" ${status == 'PREPARED' ? 'selected' : ''}>HAZIRLANIYOR</option>
        <option value="3" ${status == 'HANDOVER' ? 'selected' : ''}>KURYEYE VERİLDİ</option>
        <option value="4" ${status == 'DELIVERED' ? 'selected' : ''}>TESLİM EDİLDİ</option>
        <option value="5" ${status == 'UNSUPPLIED' ? 'selected' : ''}>İPTAL EDİLDİ</option>
    `;

        return `
<tr id="data_${order.id}">
    <td>${platformHtml}
        <input type="hidden" value="${trackingId}" id="tracking_${order.id}">
    </td>
    <td>${trackingId}</td>
    <td>${createdAt}</td>
    <td style="width:200px;overflow: hidden;">${fullName}</td>
    <td>
        ${courierSection}
        <!-- Kurye atama modal -->
        <div class="modal fade" id="Courier${order.id}">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">(${trackingId}) Siparişe Kurye Ata</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding: 1rem;">
                        <div class="row">
                            <div class="mb-1 col-md-12">
                                <select class="single-select-placeholder js-states" onchange="Courier(event, ${order.id})">
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
        <select class="inline-order-select" onchange="StatusOrderChange(event, ${order.id})" ${status == 4 ? 'disabled' : ''}>
            ${statusOptions} s
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
        <a href="#" class="btn btn-secondary shadow btn-xs sharp me-1" onclick='openOrderModal(${JSON.stringify(order).replace(/'/g, "\\'")})'>
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
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Kapat</button>
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
<script>
    function openOrderModal(order) {
        const container = document.getElementById('orderDetailsContainer');
        container.innerHTML = '';

        container.innerHTML += `
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
        <p class="orderTitle">Müşteri Notu</p>
        <p class="orderProde">${order.notes}</p>
      </div>
    `;

        const items = JSON.parse(order.items);
        let tableHTML = `
      <div class="mb-3 col-md-12">
        <table class="table table-responsive-sm" style="min-width: 28rem !important;">
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
            <th class="orderProde">${item.name}</th>
            <th class="orderProde">${item.items.length}</th>
            <th class="orderProde">${item.price} ₺</th>
          </tr>
        `;
        });
        tableHTML += `</tbody></table></div>`;
        container.innerHTML += tableHTML;

        // Yazdır butonu güvenli bağlama
        document.getElementById('printOrderBtn').onclick = function() {
            printOrder(order.id);
        };

        // Modalı aç
        const modalEl = document.getElementById('OrdersModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
</script>
