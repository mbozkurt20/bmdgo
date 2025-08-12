@extends('admin.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/admin/home/index.css')}}">
    <div class="container-fluid">
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div class="w-100 d-flex align-items-center justify-content-between">
                    <form method="GET" action="{{ route('admin.filterByDate') }}"
                          class="d-flex  gap-3 align-items-center">
                        <div>
                            <input type="date" class="form-control custom-input" id="start_date" name="start_date"
                                   required>
                        </div>
                        <div>
                            <input type="date" class="form-control custom-input" id="end_date" name="end_date" required>
                        </div>
                        <div class="d-flex align-items-end">
                            <button style="background: #e7004d;color:#fff;font-size: 0.8rem" type="submit"
                                    class="btn custom-btn">
                                <i class="fas fa-calendar-day" style="padding-right:5px"></i>
                                Filtrele</button>
                        </div>
                    </form>
                    <div class="date-filters d-flex align-items-center gap-1 ">
                        <a style="font-size:0.8rem;font-weight: 300" href="{{ route('admin.filter', ['date' => 'today']) }}"
                           class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Bugün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'yesterday']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-day text-danger"></i>
                            <span>Dün</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'this_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Bu Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'last_week']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Geçen Hafta</span>
                        </a>
                        <a style="font-size:0.8rem;font-weight: 300"
                           href="{{ route('admin.filter', ['date' => 'last_month']) }}" class="date-filter custom-link">
                            <i class="fas fa-calendar-week text-danger"></i>
                            <span>Geçen Ay</span>
                        </a>
                    </div>
                    <div class="customer-search mb-sm-0 ">
                        <div class="input-group search-area">
                            <input style="font-size: 0.8rem;width:200px" type="text" class="form-control"
                                   id="custom-filter" placeholder="Sipariş ara..">
                            <span class="input-group-text"><a href="javascript:void(0)"><i
                                        class="flaticon-381-search-2"></i></a></span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-6">
                <div class="orders-section" style="margin-bottom: 10px">
                    <h4>Siparişler</h4>
                    <div class="row g-3">
                        <!-- All Orders Button -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100" style="background: #e7004d">

                                <span class="fw-bold">
                                    <i class="fa-solid fa-box" style="color: #fffdfd;font-size:18px;padding-right:10px"></i>
                                    Tüm Siparişler</span>
                                <span class="badge bg-white text-dark order-number">{{ count($tumu) }}</span>
                            </button>
                        </div>
                        <!-- Getir Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100" style="background: #4927b3">
                                <img src="{{ asset('theme/images/GetirYemek_Logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="77px"
                                     height="14px" alt="">
                                <span class="fw-bold">Getir Siparişleri</span>
                                <span class="badge bg-white text-dark order-number">{{ count($getiryemek) }}</span>
                            </button>
                        </div>
                        <!-- Trendyol Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100" style="background: orangered">
                                <img src="{{ asset('theme/images/trendyolyemek.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="71px"
                                     height="14px" alt="">
                                <span class="fw-bold">Trendyol Siparişleri</span>
                                <span class="badge bg-white text-dark order-number">{{ count($trendyol) }}</span>
                            </button>
                        </div>

                        <!-- Yemeksepeti Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #F90050">
                                <img src="{{ asset('theme/images/Yemeksepeti_Logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="79px"
                                     height="15px" alt="">
                                <span class="fw-bold">Yemeksepeti Siparişleri</span>
                                <span class="badge bg-white text-dark order-number">{{ count($yemeksepeti) }}</span>
                            </button>
                        </div>
                        <!-- Migros Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #363A86">
                                <img src="{{ asset('theme/images/MigrosYemek_logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="69px"
                                     height="28px" alt="">
                                <span class="fw-bold">Migros Yemek Siparişleri</span>
                                <span class="badge bg-white text-dark order-number">{{ $migros }}</span>
                            </button>
                        </div>
                        <!-- Phone Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #0d2646">
                                <i class="fa-solid fa-phone" style="color: #fff;font-size:18px;padding-left:10px"></i>
                                <span class="fw-bold">Telefonla Gelen Sipariş</span>
                                <span class="badge bg-white text-dark order-number">{{ count($telefonsiparis) }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Section -->
            <div class="col-lg-6">
                <div class="performance-section mb-2">
                    <h4>Satış Performansı</h4>
                    <div class="row g-3">
                        <!-- Revenue Card -->
                        <div class="col-4">
                            <div class="order-card text-white" style="background: #0d2646">
                                <p class="fw-bold">Ciro</p>
                                <span class="order-number">{{ $formattedExpense }} TL</span>
                            </div>
                        </div>
                        <!-- Orders Count Card -->
                        <div class="col-4">
                            <div class="order-card text-white" style="background: #e7004d">
                                <p class="fw-bold">Sipariş Sayısı</p>
                                <span class="order-number">{{ count($tumu) }} Adet</span>
                            </div>
                        </div>
                        <!-- Average Order Amount Card -->
                        <div class="col-4">
                            <div class="order-card text-white" style="background: #0d2646">
                                <p class="fw-bold">Ortalama Sipariş Tutarı</p>
                                <span class="order-number">{{ $formattedAverageExpense }} TL</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="performance-section">
                    <h4>Kurye Performansı</h4>
                    <div class="row g-3">
                        <!-- Revenue Card -->
                        <div class="col-4">
                            <div class="order-card text-white" style="background: #0d2646">
                                <p class="fw-bold">Kurye Sayısı</p>
                                <span class="order-number">{{ $totalCouriers }} Kişi</span>
                            </div>
                        </div>
                        <!-- Orders Count Card -->
                        <div class="col-4">
                            <div class="order-card text-white" style="background: #e7004d">
                                <p class="fw-bold">Boş Kurye</p>
                                <span class="order-number">{{ $idleCouriers }} Kişi</span>
                            </div>
                        </div>
                        <!-- Average Order Amount Card -->
                        <div class="col-4">
                            <div class="order-card text-white" style="background: #0d2646">
                                <p class="fw-bold">Molada Kurye</p>
                                <span class="order-number">{{ $breakCouriers }} Kişi</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12 col-xxl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example4" class="order-table shadow-hover card-table text-black"
                                   style="min-width: 845px">
                                <thead>
                                <tr>
                                    <th style="width:15%">Restaurant</th>
                                    <th style="width: 10%">Sipariş No</th>
                                    <th>Saati</th>
                                    <th style="width: 10%">Müşteri</th>
                                    <th style="width: 15%">Kurye</th>
                                    <th>İndirim</th>
                                    <th>Tutar</th>
                                    <th style="width: 15%">Paket Mesafesi</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($tumu as $order)
                                    <tr id="data_{{ $order->id }}" class="msg">
                                        <td>
                                            @php $restaurant = \App\Models\Restaurant::where('id',$order->restaurant_id)->first(); @endphp

                                            @if ($order->platform == 'yemeksepeti' || $order->platform == 'Yemeksepeti')
                                                <a class="btn btn-primary btn-rounded"
                                                   style="padding: 10px;background: #fb0050;border-color: #fb0050; font-size:14px;">
                                                    {{ $restaurant->restaurant_name }} /
                                                    <img src="{{ asset('theme/images/yemeksepeti.png') }}"
                                                         style="height: 15px">
                                                </a>
                                            @endif
                                            @if ($order->platform == 'getir' || $order->platform == 'Getir')
                                                <a class="btn btn-primary btn-rounded"
                                                   style="padding: 10px;background: #6244be;border-color: #6244be; font-size:14px;">
                                                    {{ $restaurant->restaurant_name }} /
                                                    <img src="{{ asset('theme/images/getiryemek.png') }}"
                                                         style="height: 15px"> </a>
                                            @endif
                                            @if ($order->platform == 'trendyol' || $order->platform == 'Trendyol')
                                                <a style="padding: 10px" class="btn btn-primary btn-rounded">
                                                    {{ $restaurant->restaurant_name }} /
                                                    <img src="{{ asset('theme/images/trendyolyemek.png') }}"
                                                         style="height: 15px"> </a>
                                            @endif
                                            @if ($order->platform == 'migros' || $order->platform == 'Migros')
                                                <a style="padding: 10px;background: #000080;border-color: #6244be; font-size: 14px"
                                                   class="btn btn-primary btn-rounded">
                                                    {{ $restaurant->restaurant_name }} /
                                                    <img src="https://mir-s3-cdn-cf.behance.net/project_modules/max_1200/aff9ed163620751.6556613f80c21.png"
                                                         style="height: 25px;"> </a>
                                                <!-- TODO : Adisyo Platform Order-->
                                            @endif

                                            @if ($order->platform == 'adisyo' || $order->platform == 'Adisyo')
                                                <a style="padding: 10px;background: #ff0a0a;border-color: #fff; font-size: 14px"
                                                   class="btn btn-primary btn-rounded">
                                                    {{ $restaurant->restaurant_name }} /
                                                    <img src="{{ asset('theme/images/adisyoFull.png') }}"
                                                         style="height: 25px;"> </a>
                                            @endif
                                            @if ($order->platform == 'telefonsiparis')
                                                <a class="special-ok-button btn-rounded"
                                                   style="width:100%;font-weight: bold;padding:10px 15px;font-size:14px;">
                                                    {{ $restaurant->restaurant_name }} /
                                                    POS</a>
                                            @endif

                                            <input type="hidden" value="{{ $order->tracking_id }}"
                                                   id="tracking_{{ $order->tracking_id }}">


                                        </td>
                                        <td>{{ $order->tracking_id }}</td>
                                        <td>{{ $order->created_at }}</td>
                                        <td style="widt:200px;overflow: hidden;">{{ $order->full_name }}</td>
                                        <td>


                                            @php $ordersor = \App\Models\CourierOrder::where('order_id',$order->id)->first(); @endphp



                                            @if ($ordersor)
                                                @php
                                                    $courier = \App\Models\Courier::where(
                                                        'id',
                                                        $ordersor->courier_id,
                                                    )->first();
                                                    if ($courier) {
                                                        echo $courier->name;
                                                    } else {
                                                        echo 'Silinmiş Kurye';
                                                    }

                                                @endphp
                                                <a data-bs-toggle="modal"
                                                   data-bs-target="#Courier{{ $order->id }}"
                                                   style=" color: #ffffff;
                                                                background: #f72b50;
                                                                border-radius: 50%;
                                                                padding: 8px;
                                                                cursor: pointer;">
                                                    <i class="fas fa-truck"></i>
                                                </a>
                                            @else
                                                <a data-bs-toggle="modal"
                                                   data-bs-target="#Courier{{ $order->id }}"
                                                   class="special-ok-button sharp size-6 px-3 fw-bold">
                                                    <i class="fas fa-truck mr-1"></i> <small>Kurye Ata</small>
                                                </a>
                                            @endif
                                            <div class="modal fade" id="Courier{{ $order->id }}">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">({{ $order->tracking_id }})
                                                                Siparişe Kurye Ata</h5>
                                                            <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal">
                                                            </button>
                                                        </div>
                                                        <div class="modal-body" style="padding: 1rem;">
                                                            <div class="row">
                                                                <div class="mb-1 col-md-12">
                                                                    <select class="single-select-placeholder js-states"
                                                                            onchange="Courier(event,{{ $order->id }})">
                                                                        <option value="0">Kurye Seçiniz</option>
                                                                        @foreach ($couriers as $courier)
                                                                            <option value="{{ $courier->id }}">
                                                                                {{ $courier->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger light"
                                                                    data-bs-dismiss="modal">Kapat</button>
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
                                            <input type="text" class="form-control" style="width: 150px;"
                                                   id="message_{{ $order->id }}" value="{{ $order->message }}"
                                                   placeholder="Mesafe">
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
                                                        @if ($order->status == 'DELIVERED') selected style="background-color: green; color: white;" @endif>
                                                    TESLİM EDiLDİ
                                                </option>
                                                <option value="UNSUPPLIED"
                                                        @if ($order->status == 'UNSUPPLIED') selected @endif>
                                                    iPTAL EDİLDİ
                                                </option>

                                            </select>

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
                                        <script>
                                            $(document).ready(function() {

                                                $("#message_" + {{ $order->id }}).change(function() {
                                                    var data = $("#message_" + {{ $order->id }}).val();
                                                    var tracking_id = $("#tracking_" + {{ $order->id }}).val();
                                                    $.ajax({
                                                        type: 'POST',
                                                        url: 'admin/orders/message' + '?_token=' + '{{ csrf_token() }}',
                                                        data: {
                                                            message: data,
                                                            tracking_id: tracking_id
                                                        },
                                                        success: function(data) {
                                                            Swal.fire('Sipariş notu iletildi!');
                                                        },
                                                        error: function() {
                                                            console.log(data);
                                                        }
                                                    });


                                                });
                                            });

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

                                            function printDiv(prId) {
                                                var printContents = document.getElementById('Printed' + prId).innerHTML;
                                                var originalContents = document.body.innerHTML;

                                                document.body.innerHTML = printContents;

                                                window.print();

                                                document.body.innerHTML = originalContents;
                                            }
                                        </script>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
