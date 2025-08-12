<div class="col-xl-12 col-xxl-8">
    <h1 id="newOrder" class="text-center mx-auto fw-bold mb-4 text-success" style="display:none;">
        Yeni Sipariş Geldi
    </h1>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example4" class="order-table shadow-hover card-table text-black" style="min-width: 845px">
                    <thead>
                    <tr>
                        <th style="width:15%">Restaurant</th>
                        <th style="width:10%">Sipariş No</th>
                        <th>Tarih</th>
                        <th style="width:10%">Müşteri</th>
                        <th style="width:10%">Kurye</th>
                        <th>İndirim</th>
                        <th>Tutar</th>
                        <th style="width:15%">Paket Mesafesi</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                    </thead>
                    <tbody id="orders-tbody">
                    @foreach ($tumu as $order)
                        @include('admin.partials.orders.orders_row', ['order' => $order])
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
    .blink {
        animation: blinkAnim 0.5s steps(2, start) infinite;
    }

    @keyframes blinkAnim {
        to {
            visibility: hidden;
        }
    }
</style>
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
                    location.reload();
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
                if (action === 'DELIVERED' || action === 'UNSUPPLIED') {
                    updateCourierStatus(orderId);
                }
                Swal.fire({
                    title: 'Sipariş durumu başarıyla değiştirildi.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function() {
                    location.reload();
                });
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

    function updateCourierStatus(orderId) {
        $.ajax({
            type: 'POST',
            url: '/admin/updateCourierStatus',
            data: {
                order_id: orderId,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                console.log('Courier status updated successfully');
            },
            error: function(xhr, status, error) {
                console.log('Failed to update courier status');
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response Text:', xhr.responseText); // Detaylı hata mesajını gösterir
            }
        });
    }

    function printDiv(prId) {
        var printContents = document.getElementById('Printed' + prId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
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
                    location.reload();
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
    var channel = pusher.subscribe('my-channel');

    // Gelen veriyi konsolda görebilmek için
    channel.bind('orders', function(data) {
        console.log('Gelen data:', data);
        if(data.order) {
            refreshOrderTable(data.order);

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
    });

    // Tabloda var olan satırı güncelle veya yeni satır ekle
    function refreshOrderTable(order) {
        var rowId = 'data_' + order.id;
        var existingRow = document.getElementById(rowId);
        if (existingRow) {
            var newRowHtml = generateOrderRowHtml(order);
            $('#'+rowId).replaceWith(newRowHtml);
        } else {
            var newRowHtml = generateOrderRowHtml(order);
            $('#orders-tbody').append(newRowHtml);
        }
    }

    function generateOrderRowHtml(order) {
        console.log('Order detayları:', order); // Data yapısını incele

        const restaurantName = order.restaurant ? order.restaurant.restaurant_name : 'İsim Yok';
        const trackingId = order.tracking_id || '';
        const fullName = order.full_name || '';
        const message = order.message || '';
        const total = (order.total !== null && order.total !== undefined) ? parseFloat(order.total).toFixed(2) : '0.00';
        const amount = (order.amount !== null && order.amount !== undefined) ? parseFloat(order.amount).toFixed(2) : '0.00';
        const platform = order.platform || '';
        const createdAt = order.created_at ? new Date(order.created_at).toLocaleString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '';
        const courierName = order.courier ? order.courier.name : 'Kurye Yok';
        const status = order.status || 1; // varsayılan
        const couriers = order.couriers || []; // Kurye listesi

        // Platform ikonu ve renkleri
        let platformHtml = '';
        if (platform === 'yemeksepeti') {
            platformHtml = `<a class="btn btn-primary btn-rounded" style="padding: 10px;background: #fb0050;border-color: #fb0050; font-size:14px;">
            ${restaurantName ?? 'İsim Yok'} /
            <img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height: 15px">
        </a>`;
        } else if (platform === 'getir') {
            platformHtml = `<a class="btn btn-primary btn-rounded" style="padding: 10px;background: #6244be;border-color: #6244be; font-size:14px;">
            ${restaurantName ?? 'İsim Yok'} /
            <img src="{{ asset('theme/images/getiryemek.png') }}" style="height: 15px">
        </a>`;
        } else if (platform === 'trendyol') {
            platformHtml = `<a style="padding: 10px" class="btn btn-primary btn-rounded">
            ${restaurantName ?? 'İsim Yok'} /
            <img src="{{ asset('theme/images/trendyolyemek.png') }}" style="height: 15px">
        </a>`;
        } else if (platform === 'migros') {
            platformHtml = `<a style="padding: 10px;background: #000080;border-color: #6244be; font-size: 14px" class="btn btn-primary btn-rounded">
            ${restaurantName ?? 'İsim Yok'} /
            <img src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/aff9ed163620751.6556613f80c21.png" style="height: 25px;">
        </a>`;
        } else if (platform === 'adisyo') {
            platformHtml = `<a style="padding: 10px;background: #ff0a0a;border-color: #fff; font-size: 14px" class="btn btn-primary btn-rounded">
            ${restaurantName ?? 'İsim Yok'} /
            <img src="{{ asset('theme/images/adisyoFull.png') }}" style="height: 25px;">
        </a>`;
        } else if (platform === 'telefonsiparis') {
            platformHtml = `<a class="special-ok-button btn-rounded" style="width:100%;font-weight: bold;padding:10px 15px;font-size:14px;">
            ${restaurantName ?? 'İsim Yok'} / POS
        </a>`;
        } else {
            // Varsayılan veya diğer platformlar
            platformHtml = `<span>${restaurantName ?? 'İsim Yok'}</span>`;
        }

        // Kurye bölümü
        let courierSection = '';
        if (order.courier && order.courier.id) {
            courierSection = `
        <div style="display:flex; align-items:center;">
            <span>${order.courier.name}</span>
            <a data-bs-toggle="modal" data-bs-target="#Courier${order.id}" style="color:#fff; background:#f72b50; border-radius:50%; padding:8px; margin-left:8px; cursor:pointer;">
                <i class="fas fa-truck"></i>
            </a>
        </div>`;
        } else {
            courierSection = `
        <a data-bs-toggle="modal" data-bs-target="#Courier${order.id}" class="special-ok-button sharp size-6 px-3 fw-bold">
            <i class="fas fa-truck mr-1"></i> <small>Kurye Ata</small>
        </a>`;
        }

        // Durum seçimi
        const statusOptions = `
        <option value="1" ${status == 1 ? 'selected' : ''}>BEKLİYOR</option>
        <option value="2" ${status == 2 ? 'selected' : ''}>HAZIRLANIYOR</option>
        <option value="3" ${status == 3 ? 'selected' : ''}>KURYEYE VERİLDİ</option>
        <option value="4" ${status == 4 ? 'selected' : ''}>TESLİM EDİLDİ</option>
        <option value="5" ${status == 5 ? 'selected' : ''}>İPTAL EDİLDİ</option>
    `;

        // Satır HTML
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
                                    ${couriers.length > 0 ? couriers.map(c => `<option value="${c.id}">${c.name}</option>`).join('') : '<option disabled>Kurye Listesi Yok</option>'}
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
    <td class="text-ov">${amount} ₺</td>
    <td class="text-ov">
        <input type="text" class="form-control" style="width: 150px;" id="message_${order.id}" value="${message}" placeholder="Mesafe">
    </td>
    <td>
        <input type="hidden" id="tracking_${order.id}" value="${trackingId}">
        <input type="hidden" id="platform_${order.id}" value="${platform}">
        <select class="default-select form-control wide" onchange="StatusOrderChange(event, ${order.id})" ${status == 4 ? 'disabled' : ''}>
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
            <a data-bs-toggle="modal" data-bs-target="#Orders${order.id}" class="btn btn-secondary shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
            <a onclick="printDiv(${order.id})" class="btn btn-warning shadow btn-xs sharp me-1"><i class="fas fa-print"></i></a>
            <a onclick="deleteOrder(${order.id})" class="btn btn-danger shadow btn-xs sharp me-1"><i class="fa fa-times" aria-hidden="true"></i></a>
        </div>
    </td>
</tr>`;
    }
</script>
