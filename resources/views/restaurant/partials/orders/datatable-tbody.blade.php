@foreach ($tumu as $order)
    <tr id="data_{{ $order->id }}">
        <td>
            @if ($order->platform == 'yemeksepeti' || $order->platform == 'Yemeksepeti')
                <a class="btn btn-primary btn-rounded"
                   style="padding: 10px;background: #fb0050;border-color: #fb0050; font-size:14px;"><img alt="" src="{{ asset('theme/images/yemeksepeti.png') }}" style="height: 15px">
                </a>
            @endif
            @if ($order->platform == 'getir' || $order->platform == 'Getir')
                <a class="btn btn-primary btn-rounded"
                   style="padding: 10px;background: #6244be;border-color: #6244be; font-size:14px;">
                    <img src="{{ asset('theme/images/getiryemek.png') }}"
                         style="height: 15px"> </a>
            @endif
            @if ($order->platform == 'trendyol' || $order->platform == 'Trendyol')
                <a style="padding: 10px" class="btn btn-primary btn-rounded"><img
                        src="{{ asset('theme/images/trendyolyemek.png') }}"
                        style="height: 15px"> </a>
            @endif
            @if ($order->platform == 'migros' || $order->platform == 'Migros')
                <a style="padding: 10px;background: #000080;border-color: #6244be; font-size: 14px"
                   class="btn btn-primary btn-rounded"><img
                        src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/aff9ed163620751.6556613f80c21.png"
                        style="height: 25px;"> </a>
            @endif

            @if ($order->platform == 'adisyo' || $order->platform == 'Adisyo')
                <a style="padding: 10px;background: #ff0a0a;border-color: #fff; font-size: 14px"
                   class="btn btn-primary btn-rounded"><img
                        src="{{ asset('theme/images/adisyoFull.png') }}"
                        style="height: 25px;"> </a>
            @endif
            @if ($order->platform == 'telefonsiparis')
                <a class="special-ok-button btn-rounded"
                   style="width:100%;font-weight: bold;padding:10px 15px;font-size:14px;">
                    POS</a>
            @endif


            <input type="hidden" value="{{ $order->tracking_id }}"
                   id="tracking_{{ $order->tracking_id }}">

        </td>
        <td style="text-align: center">{{ $order->tracking_id }}</td>
        <td>{{ $order->full_name }}</td>
        <td>{{ $order->phone }}</td>
        <td>
            @if ($order->courier_id == -1)
                @php
                    $courierOrder = \App\Models\CourierOrder::where('order_id', $order->id)->first();
                    $courierName = 'Kurye Bekleniyor';
                    if ($courierOrder) {
                        $courier = \App\Models\Courier::find(
                            $courierOrder->courier_id,
                        );
                        if ($courier) {
                            $courierName = $courier->name;
                        }
                    }
                @endphp
                {{ $courierName }}
                <a href="#""
                style="    color: #ffffff;
                background: #f72b50;
                border-radius: 50%;
                padding: 8px;
                cursor: pointer;">
                <i class="fas fa-truck"></i>
                </a>
            @endif

            @if ($order->courier_id >= 1)
                @php
                    $courier = \App\Models\Courier::where(
                        'id',
                        $order->courier_id,
                    )->first();
                @endphp
                @if ($courier)
                    {{ $courier->name }}
                @else
                    <span>Kurye Bekleniyor</span> <!-- Optional fallback message -->
                @endif
            @endif

            @if ($order->courier_id == 0)
                <a data-bs-toggle="modal"
                   data-bs-target="#Courier{{ $order->id }}"
                   style="padding:10px 15px;height: 50px;"
                   class="special-ok-button sharp me-1">
                    <i class="fas fa-truck"></i>
                </a>

                <div class="modal fade" id="Courier{{ $order->id }}">
                    <div class="modal-dialog modal-dialog-centered"
                         role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    ({{ $order->tracking_id }})
                                    Siparişe Kurye Ata</h5>
                                <button type="button" class="btn-close"
                                        data-bs-dismiss="modal">
                                </button>
                            </div>
                            <div class="modal-body" style="padding: 1rem;">
                                <div class="row">
                                    <div class="mb-1 col-md-12">
                                        <select
                                            class="single-select-placeholder js-states"
                                            onchange="Courier(event,{{ $order->id }})">
                                            <option value="0">Kurye Seç
                                            </option>
                                            <option value="-1">{{env('APP_NAME')}}
                                            </option>
                                            @foreach ($couriers as $courier)
                                                <option
                                                    value="{{ $courier->id }}">
                                                    {{ $courier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-danger light"
                                        data-bs-dismiss="modal">Kapat
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
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
        <td class="text-ov">{{ number_format($order->amount - $total, 2) }} ₺</td>
        <td class="text-ov">
            <input type="text"
                   class="form-control" style="width: 150px;"
                   id="message_{{ $order->id }}"
                   value="{{ $order->message }}"
                   placeholder="Mesafe">
        </td>
        <td>
            {{ $order->payment_method }}
        </td>
        <td>
            <input type="hidden" id="tracking_{{ $order->id }}"
                   value="{{ $order->tracking_id }}">
            <input type="hidden" id="platform_{{ $order->id }}"
                   value="{{ $order->platform }}">

            <select class="default-select  form-control wide"
                    onchange="StatusOrderChange(event, {{ $order->id }})"
                    @if ($order->status == 'DELIVERED') disabled @endif>

                <option value="PENDING"
                        @if ($order->status == 'PENDING') selected @endif>
                    BEKLİYOR
                </option>
                <option value="PREPARED"
                        @if ($order->status == 'PREPARED') selected @endif>
                    HAZIRLANIYOR
                </option>
                <option value="HANDOVER"
                        @if ($order->status == 'HANDOVER') selected @endif>
                    KURYEYE VERİLDİ
                </option>
                <option value="DELIVERED"
                        @if ($order->status == 'DELIVERED') selected @endif>
                    TESLİM EDİLDİ
                </option>
                <option value="UNSUPPLIED"
                        @if ($order->status == 'UNSUPPLIED') selected @endif>
                    İPTAL EDİLDİ
                </option>
            </select>
        </td>
        <!-- cancelModal Modal -->
        <div class="modal fade" id="cancelModal" tabindex="-1"
             aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">Siparişi İptal Et</h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="cancelReason" class="form-label">
                            Siparişi neden iptal etmek istiyorsunuz?
                        </label>
                        <textarea class="form-control" id="message" rows="4"
                                  placeholder="Lütfen iptal nedeninizi yazınız..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Geri Dön
                        </button>
                        <button type="button" class="btn btn-danger"
                                id="confirmCancel">İptal Et
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</td>
        <td>
            <div class="d-flex">
                <a data-bs-toggle="modal"
                   data-bs-target="#Orders{{ $order->id }}"
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
                                            <p class="orderProde">
                                                {{ $order->phone }}
                                            </p>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <p class="orderTitle">Tutar</p>
                                            <p class="orderProde">
                                                {{ $order->amount }}₺</p>
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
                                                {{ $order->address }}</p>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <table
                                                class="table table-responsive-sm"
                                                style="min-width: 28rem !important;">
                                                <thead>
                                                <tr>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Ürün
                                                    </th>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Adeti
                                                    </th>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Fiyatı
                                                    </th>
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
                                                            {{ $item->price }}
                                                            ₺
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
                                            class="fa fa-print"></i>Yazdır
                                    </button>
                                    <button type="button"
                                            class="btn btn-danger light"
                                            data-bs-dismiss="modal">Kapat
                                    </button>
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
                                            <p class="orderProde">
                                                {{ $order->phone }}
                                            </p>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <p class="orderTitle">Tutar</p>
                                            <p class="orderProde">
                                                {{ $order->amount }}
                                            </p>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <p class="orderTitle">Ödeme Yn.</p>
                                            <p class="orderProde">

                                                {{ $order->payment_method }}
                                            </p>
                                        </div>
                                        <div class="mb-2 col-md-12">
                                            <p class="orderTitle">Adres</p>
                                            <p class="orderProde">
                                                {{ $order->address }}</p>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            @php $items = json_decode($order->items); @endphp
                                            <table
                                                class="table table-responsive-sm"
                                                style="min-width: 28rem !important;">
                                                <thead>
                                                <tr>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Ürün
                                                    </th>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Adeti
                                                    </th>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Fiyatı
                                                    </th>
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
                                                            ₺
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
                                            class="fa fa-print"></i>Yazdır
                                    </button>
                                    <button type="button"
                                            class="btn btn-danger light"
                                            data-bs-dismiss="modal">Kapat
                                    </button>
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
                                            <p class="orderProde">
                                                {{ $order->phone }}
                                            </p>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <p class="orderTitle">Tutar</p>
                                            <p class="orderProde">
                                                {{ $order->amount }}
                                            </p>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <p class="orderTitle">Ödeme Yn.</p>
                                            <p class="orderProde">

                                                {{ $order->payment_method }}
                                            </p>
                                        </div>
                                        <div class="mb-2 col-md-12">
                                            <p class="orderTitle">Adres</p>
                                            <p class="orderProde">
                                                {{ $order->address }}</p>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            @php $items = json_decode($order->items); @endphp
                                            <table
                                                class="table table-responsive-sm"
                                                style="min-width: 28rem !important;">
                                                <thead>
                                                <tr>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Ürün
                                                    </th>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Adeti
                                                    </th>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Fiyatı
                                                    </th>
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
                                                            ₺
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
                                            class="fa fa-print"></i>Yazdır
                                    </button>
                                    <button type="button"
                                            class="btn btn-danger light"
                                            data-bs-dismiss="modal">Kapat
                                    </button>
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
                                            <p class="orderTitle">Müşteri Ad</p>
                                            <p class="orderProde">
                                                {{ $order->full_name }}</p>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <p class="orderTitle">Telefon</p>
                                            <p class="orderProde">
                                                {{ $order->phone }}
                                            </p>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <p class="orderTitle">Tutar</p>
                                            <p class="orderProde">
                                                {{ $order->amount }}
                                            </p>
                                        </div>
                                        <div class="mb-1 col-md-4">
                                            <p class="orderTitle">Ödeme Yn.</p>
                                            <p class="orderProde">

                                                {{ $order->payment_method }}

                                            </p>
                                        </div>
                                        <div class="mb-2 col-md-12">
                                            <p class="orderTitle">Adres</p>
                                            <p class="orderProde">
                                                {{ $order->address }}</p>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            @php $items = json_decode($order->items); @endphp
                                            <table
                                                class="table table-responsive-sm"
                                                style="min-width: 28rem !important;">
                                                <thead>
                                                <tr>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Ürn
                                                    </th>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Adeti
                                                    </th>
                                                    <th
                                                        style="font-size: 14px;font-weight: 600">
                                                        Fiyatı
                                                    </th>
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
                                                            {{ $item->price }}
                                                            ₺
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
                                            class="fa fa-print"></i>Yazdr
                                    </button>
                                    <button type="button"
                                            class="btn btn-danger light"
                                            data-bs-dismiss="modal">Kapat
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                <a onclick="printDiv({{ $order->id }})"
                   class="btn btn-danger shadow btn-xs sharp me-1">
                    <i class="fas fa-print"></i>
                </a>
            </div>


        </td>
    </tr>
@endforeach
