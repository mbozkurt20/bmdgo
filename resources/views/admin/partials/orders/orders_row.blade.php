<tr id="data_{{ $order->id }}" class="msg">
    <td>
        @if ($order->platform == 'yemeksepeti' || $order->platform == 'Yemeksepeti')
            <a class="btn btn-primary btn-rounded" style="padding: 10px;background: #fb0050;border-color: #fb0050; font-size:14px;">
                {{ $order->restaurant->restaurant_name }} /
                <img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height: 15px">
            </a>
        @elseif ($order->platform == 'getir' || $order->platform == 'Getir')
            <a class="btn btn-primary btn-rounded" style="padding: 10px;background: #6244be;border-color: #6244be; font-size:14px;">
                {{ $order->restaurant->restaurant_name }} /
                <img src="{{ asset('theme/images/getiryemek.png') }}" style="height: 15px">
            </a>
        @elseif ($order->platform == 'trendyol' || $order->platform == 'Trendyol')
            <a style="padding: 10px" class="btn btn-primary btn-rounded">
                {{ $order->restaurant->restaurant_name }} /
                <img src="{{ asset('theme/images/trendyolyemek.png') }}" style="height: 15px">
            </a>
        @elseif ($order->platform == 'migros' || $order->platform == 'Migros')
            <a style="padding: 10px;background: #000080;border-color: #6244be; font-size: 14px" class="btn btn-primary btn-rounded">
                {{ $order->restaurant->restaurant_name }} /
                <img src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/aff9ed163620751.6556613f80c21.png" style="height: 25px;">
            </a>
        @elseif ($order->platform == 'adisyo' || $order->platform == 'Adisyo')
            <a style="padding: 10px;background: #ff0a0a;border-color: #fff; font-size: 14px" class="btn btn-primary btn-rounded">
                {{ $order->restaurant->restaurant_name }} /
                <img src="{{ asset('theme/images/adisyoFull.png') }}" style="height: 25px;">
            </a>
        @elseif ($order->platform == 'telefonsiparis')
            <a class="special-ok-button btn-rounded" style="width:100%;font-weight: bold;padding:10px 15px;font-size:14px;">
                {{ $order->restaurant->restaurant_name }} / POS
            </a>
        @endif
        <input type="hidden" value="{{ $order->tracking_id }}" id="tracking_{{ $order->tracking_id }}">
    </td>
    <td>{{ $order->tracking_id }}</td>
    <td>{{ date('d-m-Y H:i:s',strtotime($order->created_at)) }}</td>
    <td>{{ $order->full_name }}</td>
    <td>
        @php
            $ordersor = \App\Models\CourierOrder::where('order_id', $order->id)->first();
        @endphp
        @if ($ordersor)
            @php
                $courier = \App\Models\Courier::where('id', $ordersor->courier_id)->first();
            @endphp
            <div style="display:flex; align-items:center;">
                <span>
                    {{ $courier ? $courier->name : 'Silinmiş Kurye' }}
                </span>
                <a data-bs-toggle="modal" data-bs-target="#Courier{{ $order->id }}" style="color:#fff; background:#f72b50; border-radius:50%; padding:8px; margin-left:8px; cursor:pointer;">
                    <i class="fas fa-truck"></i>
                </a>
            </div>
        @else
            <a data-bs-toggle="modal" data-bs-target="#Courier{{ $order->id }}" class="special-ok-button sharp size-6 px-3 fw-bold">
                <i class="fas fa-truck mr-1"></i> <small>Kurye Ata</small>
            </a>
        @endif

        <!-- Kurye atama modal -->
        <div class="modal fade" id="Courier{{ $order->id }}">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">({{ $order->tracking_id }}) Siparişe Kurye Ata</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding: 1rem;">
                        <div class="row">
                            <div class="mb-1 col-md-12">
                                <select class="single-select-placeholder js-states" onchange="Courier(event, {{ $order->id }})">
                                    <option value="0">Kurye Seçiniz</option>
                                    @foreach ($couriers as $courier)
                                        <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                                    @endforeach
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

    @php
        $total = 0;

        if ($order->platform == 'trendyol' || $order->platform == 'Trendyol') {
            if ($order->promotions) {
                $promotions = json_decode($order->promotions);
                foreach ($promotions as $promotion) {
                    $total += $promotion->totalSellerAmount ?? 0;
                }
            }

              if ($order->coupon) {
                $coupon = json_decode($order->coupon);
                $total += $coupon->totalSellerAmount ?? 0;
            }
        }
    @endphp

    <td class="text-ov">{{ number_format($total, 2) }} ₺</td>
    <td class="text-ov">{{ number_format($order->amount, 2) }} ₺</td>
    <td class="text-ov">
        <input type="text" class="form-control" style="width: 150px;" id="message_{{ $order->id }}" value="{{ $order->message }}" placeholder="Mesafe">
    </td>
    <td>
        <input type="hidden" id="tracking_{{ $order->id }}" value="{{ $order->tracking_id }}">
        <input type="hidden" id="platform_{{ $order->id }}" value="{{ $order->platform }}">

        <select class="default-select form-control wide" onchange="StatusOrderChange(event, {{ $order->id }})" @if ($order->status == App\Helpers\OrderStatus::DELIVERED) disabled @endif>
            <option value="{{ App\Helpers\OrderStatus::PENDING }}" @if ($order->status == App\Helpers\OrderStatus::PENDING) selected @endif>BEKLİYOR</option>
            <option value="{{ App\Helpers\OrderStatus::PREPARED }}" @if ($order->status == App\Helpers\OrderStatus::PREPARED) selected @endif>HAZIRLANIYOR</option>
            <option value="{{ App\Helpers\OrderStatus::HANDOVER }}" @if ($order->status == App\Helpers\OrderStatus::HANDOVER) selected @endif>KURYEYE VERİLDİ</option>
            <option value="{{ App\Helpers\OrderStatus::DELIVERED }}" @if ($order->status == App\Helpers\OrderStatus::DELIVERED) selected style="background-color: green; color: white;" @endif>TESLİM EDİLDİ</option>
            <option value="{{ App\Helpers\OrderStatus::UNSUPPLIED }}" @if ($order->status == App\Helpers\OrderStatus::UNSUPPLIED) selected @endif>İPTAL EDİLDİ</option>
        </select>

        <!-- Sipariş iptal modal -->

        <!-- cancelModal Modal -->
        <div class="modal fade" id="cancelModal" tabindex="-1"
             aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">Siparişi
                            İptal
                            Et</h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="cancelReason" class="form-label">Siparişi
                            neden
                            iptal etmek istiyorsunuz?</label>
                        <textarea class="form-control" id="message" rows="4" placeholder="Lütfen iptal nedeninizi yazın..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Geri Dön</button>
                        <button type="button" class="btn btn-danger"
                                id="confirmCancel">İptal Et</button>
                    </div>
                </div>
            </div>
        </div>
    </td>
    <td>
        <div class="d-flex">
            <a data-bs-toggle="modal" data-bs-target="#Orders{{ $order->id }}"
               class="btn btn-secondary shadow btn-xs sharp me-1">
                <i class="fas fa-eye"></i>
            </a>
            @if ($order->platform == 'getir')
                <div class="modal fade" id="Orders{{ $order->id }}">
                    <div class="modal-dialog modal-dialog-centered"
                         role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Sipariş Bilgileri</h5>
                                <button type="button" class="btn-close"
                                        data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body" style="padding: 1rem;">
                                <div class="row">
                                    <div class="mb-1 col-md-6">
                                        <p class="orderTitle">Sipariş Kodu</p>
                                        <p class="orderProde">
                                            {{ $order->tracking_id }}</p>
                                    </div>
                                    <div class="mb-1 col-md-6">
                                        <p class="orderTitle">Müşteri Adı</p>
                                        <p class="orderProde">
                                            {{ $order->full_name }}</p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Telefon</p>
                                        <p class="orderProde">{{ $order->phone }}
                                        </p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Tutar</p>
                                        <p class="orderProde">{{ $order->amount }}
                                            ₺</p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Ödeme Yön.</p>
                                        <p class="orderProde">

                                            {{ $order->payment_method }}
                                        </p>
                                    </div>
                                    <div class="mb-2 col-md-12">
                                        <p class="orderTitle">Adres</p>
                                        <p class="orderProde">
                                            {{ \App\Helpers\OrdersHelper::addressReplace($order->address) }}</p>
                                    </div>
                                    <div class="mb-2 col-md-12">
                                        <p class="orderTitle">Müşteri Notu</p>
                                        <p class="orderProde">
                                            {{ $order->notes }}</p>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <table class="table table-responsive-sm"
                                               style="min-width: 28rem !important;">
                                            <thead>
                                            <tr>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Ürün</th>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Adeti</th>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Fiyatı</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach (json_decode($order->items) as $item)
                                                <tr>
                                                    <th class="orderProde">
                                                        {{ $item->name->tr }}
                                                    </th>
                                                    <th class="orderProde">
                                                        {{ $item->count }}
                                                    </th>
                                                    <th class="orderProde">
                                                        {{ $item->price }} ₺
                                                    </th>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-primary light"
                                        onclick="printDiv({{ $order->id }})"><i
                                        class="fa fa-print"></i>Yazdır</button>
                                <button type="button"
                                        class="btn btn-danger light"
                                        data-bs-dismiss="modal">Kapat</button>
                            </div>

                        </div>
                    </div>
                </div>
            @elseif($order->platform == 'yemeksepeti')
                <div class="modal fade" id="Orders{{ $order->id }}">
                    <div class="modal-dialog modal-dialog-centered"
                         role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Sipariş Bilgileri</h5>
                                <button type="button" class="btn-close"
                                        data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body" style="padding: 1rem;">
                                <div class="row">
                                    <div class="mb-1 col-md-6">
                                        <p class="orderTitle">Sipariş Kodu</p>
                                        <p class="orderProde">
                                            {{ $order->tracking_id }}</p>
                                    </div>
                                    <div class="mb-1 col-md-6">
                                        <p class="orderTitle">Müşteri Adı</p>
                                        <p class="orderProde">
                                            {{ $order->full_name }}</p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Telefon</p>
                                        <p class="orderProde">{{ $order->phone }}
                                        </p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Tutar</p>
                                        <p class="orderProde">{{ $order->amount }}
                                            ₺</p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Ödeme Yön.</p>
                                        <p class="orderProde">

                                            {{ $order->payment_method }}
                                        </p>
                                    </div>
                                    <div class="mb-2 col-md-12">
                                        <p class="orderTitle">Adres</p>
                                        <p class="orderProde">
                                            {{ \App\Helpers\OrdersHelper::addressReplace($order->address) }}</p>
                                    </div>
                                    <div class="mb-2 col-md-12">
                                        <p class="orderTitle">Müşteri Notu</p>
                                        <p class="orderProde">
                                            {{ $order->notes }}</p>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        @php $items = json_decode($order->items); @endphp
                                        <table class="table table-responsive-sm"
                                               style="min-width: 28rem !important;">
                                            <thead>
                                            <tr>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Ürün</th>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Adeti</th>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Fiyat</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($items as $item)
                                                <tr>
                                                    <th class="orderProde">
                                                        {{ $item->name }}
                                                    </th>
                                                    <th class="orderProde">
                                                        {{ $item->count }}
                                                    </th>
                                                    <th class="orderProde">
                                                        {{ $item->price }}
                                                    </th>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-primary light"
                                        onclick="printDiv({{ $order->id }})"><i
                                        class="fa fa-print"></i>Yazdır</button>
                                <button type="button"
                                        class="btn btn-danger light"
                                        data-bs-dismiss="modal">Kapat</button>
                            </div>

                        </div>
                    </div>
                </div>
            @elseif($order->platform == 'adisyo')
                <div class="modal fade" id="Orders{{ $order->id }}">
                    <div class="modal-dialog modal-dialog-centered"
                         role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Sipariş Bilgileri</h5>
                                <button type="button" class="btn-close"
                                        data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body" style="padding: 1rem;">
                                <div class="row">
                                    <div class="mb-1 col-md-6">
                                        <p class="orderTitle">Sipariş Kodu</p>
                                        <p class="orderProde">
                                            {{ $order->tracking_id }}</p>
                                    </div>
                                    <div class="mb-1 col-md-6">
                                        <p class="orderTitle">Müşteri Adı</p>
                                        <p class="orderProde">
                                            {{ $order->full_name }}</p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Telefon</p>
                                        <p class="orderProde">{{ $order->phone }}
                                        </p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Tutar</p>
                                        <p class="orderProde">{{ $order->amount }}
                                            ₺</p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Ödeme Yön.</p>
                                        <p class="orderProde">

                                            {{ $order->payment_method }}
                                        </p>
                                    </div>
                                    <div class="mb-2 col-md-12">
                                        <p class="orderTitle">Adres</p>
                                        <p class="orderProde">
                                            {{ \App\Helpers\OrdersHelper::addressReplace($order->address)  }}</p>
                                    </div>
                                    <div class="mb-2 col-md-12">
                                        <p class="orderTitle">Müşteri Notu</p>
                                        <p class="orderProde">
                                            {{ $order->notes }}</p>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        @php $items = json_decode($order->items); @endphp
                                        <table class="table table-responsive-sm"
                                               style="min-width: 28rem !important;">
                                            <thead>
                                            <tr>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Ürün</th>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Adeti</th>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Fiyat</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($items as $item)
                                                <tr>
                                                    <th class="orderProde">
                                                        {{ $item->name }}
                                                    </th>
                                                    <th class="orderProde">
                                                        {{ $item->count }}
                                                    </th>
                                                    <th class="orderProde">
                                                        {{ $item->price }}
                                                    </th>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-primary light"
                                        onclick="printDiv({{ $order->id }})"><i
                                        class="fa fa-print"></i>Yazdır</button>
                                <button type="button"
                                        class="btn btn-danger light"
                                        data-bs-dismiss="modal">Kapat</button>
                            </div>

                        </div>
                    </div>
                </div>
            @else
                <div class="modal fade" id="Orders{{ $order->id }}">
                    <div class="modal-dialog modal-dialog-centered"
                         role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Sipariş Bilgileri</h5>
                                <button type="button" class="btn-close"
                                        data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body" style="padding: 1rem;">
                                <div class="row">
                                    <div class="mb-1 col-md-6">
                                        <p class="orderTitle">Sipariş Kodu</p>
                                        <p class="orderProde">
                                            {{ $order->tracking_id }}</p>
                                    </div>
                                    <div class="mb-1 col-md-6">
                                        <p class="orderTitle">Müteri Adı</p>
                                        <p class="orderProde">
                                            {{ $order->full_name }}</p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Telefon</p>
                                        <p class="orderProde">{{ $order->phone }}
                                        </p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Tutar</p>
                                        <p class="orderProde">{{ $order->amount }}
                                            ₺</p>
                                    </div>
                                    <div class="mb-1 col-md-4">
                                        <p class="orderTitle">Ödeme Yön.</p>
                                        <p class="orderProde">
                                            {{ $order->payment_method }}

                                        </p>
                                    </div>
                                    <div class="mb-2 col-md-12">
                                        <p class="orderTitle">Adres</p>
                                        <p class="orderProde">
                                            {{ \App\Helpers\OrdersHelper::addressReplace($order->address) }}</p>
                                    </div>
                                    <div class="mb-2 col-md-12">
                                        <p class="orderTitle">Müşteri Notu</p>
                                        <p class="orderProde">
                                            {{ $order->notes }}</p>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        @php $items = json_decode($order->items); @endphp
                                        <table class="table table-responsive-sm"
                                               style="min-width: 28rem !important;">
                                            <thead>
                                            <tr>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Ürün</th>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Adeti</th>
                                                <th
                                                    style="font-size: 14px;font-weight: 600">
                                                    Fiyatı</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($items as $item)
                                                <tr>
                                                    <th class="orderProde">
                                                        {{ $item->name }}
                                                    </th>
                                                    <th class="orderProde">
                                                        {{ count($item->items) }}
                                                    </th>
                                                    <th class="orderProde">
                                                        {{ $item->price }} ₺
                                                    </th>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-primary light"
                                        onclick="printDiv({{ $order->id }})"><i
                                        class="fa fa-print"></i>Yazdır</button>
                                <button type="button"
                                        class="btn btn-danger light"
                                        data-bs-dismiss="modal">Kapat</button>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

            <a onclick="printDiv({{ $order->id }})"
               class="btn btn-warning shadow btn-xs sharp me-1">
                <i class="fas fa-print"></i>
            </a>
            <a onclick="deleteOrder({{ $order->id }})"
               class="btn btn-danger shadow btn-xs sharp me-1">
                <i class="fa fa-times" aria-hidden="true"></i>

            </a>
        </div>
    </td>
</tr>
