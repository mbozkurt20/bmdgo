<div class="col-xl-12 col-xxl-8 mt-4">
    <ul class="nav nav-tabs" id="orderStatusTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                    type="button" role="tab">
                <i class="
                fa fa-clock" aria-hidden="true"></i> Bekliyor
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prepared-tab" data-bs-toggle="tab" data-bs-target="#prepared" type="button"
                    role="tab">
                <i class="
                fa fa-cutlery" aria-hidden="true"></i> Hazırlanıyor
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned" type="button"
                    role="tab">
                <i class="
                fa fa-battery-empty" aria-hidden="true"></i> Kuryeye Atananlar
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="handover-tab" data-bs-toggle="tab" data-bs-target="#handover" type="button"
                    role="tab">
                <i class="
                fa fa-motorcycle" aria-hidden="true"></i> Kuryeye Verildi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button"
                    role="tab">
                <i class="
                fa fa-check-circle" aria-hidden="true"></i> Teslim Edildi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="unsupplied-tab" data-bs-toggle="tab" data-bs-target="#unsupplied" type="button"
                    role="tab">
                <i class="
                fa fa-times-circle" aria-hidden="true"></i> İptal Edildi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" onclick="fetchOrders()">
                <i class="
                fa fa-refresh" aria-hidden="true"></i> Yenile
            </button>
        </li>
    </ul>



    <h2 id="newOrder" class="mt-4 bg-white py-2 text-center mx-auto fw-bold mb-4"
        style="display:none;color: rgba(231,0,77,0.82)">
        Yeni Sipariş Geldi
    </h2>

    @php
        $statuses = [
            'PENDING' => 'pending',
            'PREPARED' => 'prepared',
            'ASSIGNED' => 'assigned',
            'HANDOVER' => 'handover',
            'DELIVERED' => 'delivered',
            'UNSUPPLIED' => 'unsupplied'
        ];
    @endphp

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
                                <tbody id="order-tbody-{{ $statusId }}"></tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@include('partials.home_scripts',['key' => 'restaurant'])
