<div class="col-xl-12 col-xxl-8 mt-4">
    <ul class="nav nav-tabs" id="orderStatusTabs" role="tablist">
        @foreach(\App\Helpers\OrderStatus::statuses() as $value => $key)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{$key}}-tab" data-bs-toggle="tab"
                        data-bs-target="#{{$key}}"
                        type="button" role="tab">
                    {{__('statuses.'.$key)}}
                </button>
            </li>
        @endforeach
        <li class="nav-item" role="presentation">
            <button class="nav-link bg-white border border-primary text-primary" onclick="fetchOrders()">
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
        $statuses = \App\Helpers\OrderStatus::statuses();
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
                                    <th style="color:#259a38;width:12%;font-size: 14px;font-weight: bold">Restaurant</th>
                                    <th style="color:#259a38;width:8%;font-size: 14px;font-weight: bold">Sipariş No</th>
                                    <th style="color:#259a38;font-size: 14px;font-weight: bold">Saati</th>
                                    <th style="color:#259a38;width:8%;font-size: 14px;font-weight: bold">Müşteri</th>
                                    <th style="color:#259a38;width:10%;font-size: 14px;font-weight: bold">Kurye</th>
                                    <th style="color:#259a38;font-size: 14px;font-weight: bold">Ara Tutar</th>
                                    <th style="color:#259a38;font-size: 14px;font-weight: bold">İndirim</th>
                                    <th style="color:#259a38;font-size: 14px;font-weight: bold">Tutar</th>
                                    <th style="color:#259a38;font-size: 14px;font-weight: bold">Ödeme Türü</th>
                                    <th style="color:#259a38;width:8%;font-size: 14px;font-weight: bold">Paket Mesafesi</th>
                                    <th style="color:#259a38;font-size: 14px;font-weight: bold">İşlem</th>
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

@include('partials.home_scripts',['key' => 'dealer'])
