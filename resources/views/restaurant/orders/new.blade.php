<!DOCTYPE HTML>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="{{env('APP_NAME')}}">
    <title>Sipariş Ekranı - {{env('APP_NAME')}}</title>

    <link href="{{asset('pos/assets/css/ui.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
          integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link href="{{asset('pos/assets/css/OverlayScrollbars.css')}}" type="text/css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>

        .coupon-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-start;
            padding-bottom: 12px;
        }

        .coupon-item {
            flex: 1 1 calc(50% - 12px);
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
            box-sizing: border-box;
            background-color: #fce9ef;
            color: #e7004d;,
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            user-select: none;
        }

        .coupon-item:hover {
            background-color: #fdf7f9;
            border-color: #b3003a;
        }

        .coupon-item.selected {
            background-color: #e7004d;
            color: white;
            border-color: #a3003b;
        }

        @media (max-width: 576px) {
            .coupon-item {
                flex: 1 1 100%;
            }
        }
    </style>
    <style>
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: #e7004d;
        }

        .special-button {
            background-color: #0d2646; /* Indigo-600 */
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 2.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .special-button:hover {
            background-color: #132945; /* Indigo-700 */
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }


        .special-ok-button {
            background-color: #e7004d; /* Indigo-600 */
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 2.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .special-ok-button:hover {
            background-color: #dc205f; /* Indigo-700 */
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .special-ok-button-small {
            background-color: #e7004d; /* Indigo-600 */
            color: white;
            padding: 0.35rem 1rem;
            font-size: 0.76rem;
            font-weight: 600;
            border: none;
            border-radius: 2.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .special-ok-button-small:hover {
            background-color: #dc205f; /* Indigo-700 */
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

    </style>

    <style>
        #map {
            border: #0d2646 solid 2px;
            height: 300px; /* ya da istediğin başka bir yükseklik */
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
        }
    </style>
    <style>
        .select2-results__option[aria-selected] {
            height: 40px;
            font-weight: bold;
        }

        .select2-container--default .select2-selection--single {
            height: 40px;
        }

        .avatar {
            vertical-align: middle;
            width: 35px;
            height: 35px;
            border-radius: 0%;
        }

        .bg-default, .btn-default {
            background-color: #f2f3f8;
        }

        .tabs {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .tabs input[type="radio"] {
            display: none;
        }

        .tabs label {
            padding: 12px 20px;
            cursor: pointer;
            background: #f1f1f1;
            margin-right: 5px;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
            font-weight: 600;
            font-size: 20px;
        }

        .tabs input[type="radio"]:checked + label {
            background: #0d2646;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .tabs .tab {
            width: 100%;
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }

        .tabs input[type="radio"]:checked + label + .tab {
            display: block;
        }

        .card-product {
            border: none;
            border-radius: 10px;
            transition: transform 0.2s ease-in-out, box-shadow 0.3s;
        }

        .card-product:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .card-product .title {
            font-size: 20px;
            font-weight: bold;
            color: #fff;
        }

        .card-product .price-new {
            font-size: 22px;
            font-weight: bold;
            color: #fff;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .toplusil a i:hover {
            color: red;
        }

        .paymentRol {
            padding: 12px;
            font-size: 18px;
            color: #fff;
            text-align: center;
            font-weight: bold;
            height: 80px;
            border-radius: 10px;
            margin: 0px 2px;
            cursor: pointer;
        }

        .nakit {
            background: #243d7a;
        }

        .kkkarti {
            background: #183785;
        }

        .kkarti {
            background: #0077b8;
        }

        .kayit {
            background: #1fde74;
            padding: 25px;
            font-size: 22px;
        }

        .customer {
            padding: 5px 10px;
            border: 1px solid white;
            width: 100%;
            border-radius: 10px;
            text-align: left;
        }

        .rightbtn {
            padding: 0 !important;
        }

        .rightbtn a {
            padding: 10px 15px;
            height: 50px;
            font-size: 18px;
        }

        .selectiki {
            height: 50px !important;
            padding: 10px;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            line-height: 40px;
        }

        .select2-container--default .select2-results__option--selected {
            background-color: #e7e7e7;
            line-height: 40px;
        }

        .select2-results__option--selectable {
            cursor: pointer;
            line-height: 40px;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #aaa;
            height: 45px;
        }

        #loader {
            position: absolute;
            padding: 0;
            width: 100%;
            height: 100vh;
            text-align: center;
            background: #fff;
            z-index: 999;
        }

        #loader img {
            position: relative;
            top: 50%;
        }

        .in::placeholder {
            color: #e7004d;
        }

        .drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: 86.6666%; /* Ekranın 2/3'ü */
            height: 100vh;
            background-color: #ffffff;
            box-shadow: -2px 0 12px rgba(0, 0, 0, 0.3);
            z-index: 50;
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
        }

        /* Drawer aktif olduğunda */
        .drawer.open {
            transform: translateX(0%);
        }

        /* Drawer Başlık */
        .drawer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            background-color: #f8f8f8;
        }

        /* Kapatma Butonu */
        .drawer-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #e7004d;
        }

        /* Drawer İçeriği */
        .drawer-body {
            padding: 20px;
        }


    </style>
</head>
<body>

<div id="loader" style=" display: none;">
    <div>
        <img src="https://wpamelia.com/wp-content/uploads/2018/11/ezgif-2-6d0b072c3d3f.gif" style="height:100px">
    </div>
</div>

<div id="drawer" class="drawer" style="z-index: 1050">
    <div class="drawer-header">
        <h4 class="m-0">Sepet</h4>
        <button onclick="toggleDrawer()" class="drawer-close"><i class="fas fa-times"></i></button>
    </div>
    <div class="drawer-body">
        <section class="header-main" style="background:#0d2646">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <!-- Ortalanmış başlık -->
                    <div class="col-md-4 col-sm-12 text-center">
                        <h2 class="logo-text text-white m-0">Sipariş Ekranı</h2>
                        <small class="text-white">{{ Auth::user()->restaurant_name }}</small>
                    </div>

                    <!-- Butonlar sağa hizalı -->
                    <div class="col-md-5 col-sm-12 text-right mt-2">
                        <a class="special-ok-button" href="{{ url('/restaurant') }}">
                            <i class="fas fa-home"></i> Anasayfa
                        </a>
                        <span class="px-1"></span>


                        <a class="special-ok-button text-white" data-toggle="modal" data-target="#musteriAta">
                            <i class="fas fa-user-plus"></i> Müşteri Seçiniz
                        </a>
                    </div>

                    <!-- Müşteri bilgisi sağa hizalı -->
                    <div class="col-md-3 col-sm-12 text-right">
                        <div class="brand-wrap customer text-white">
                            <div style="padding: 10px">Seçili Müşteri Bulunmuyor...</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-content padding-y-sm">
            <form method="POST" action="javascript:void(0);" name="formPos">
                <div class="container-fluid">
                    <div class="row">
                        <input type="hidden" name="payment_control" id="payment_control" value="0">
                        <input type="hidden" name="user_id" id="customer_id" value="0">
                        <input type="hidden" name="courier_id" id="courier_id" value="">
                        <input type="hidden" name="total" id="totalPrice" value="">
                        <div class="col-md-9 card padding-y-sm card"
                             style="border-radius: 10px; background-color: #fdfdfd; padding: 20px;min-height: 70vh">

                            @php $checked = 0; @endphp

                            {{-- Kategori Sekmeleri --}}
                            <div class="nav nav-tabs mb-4 b" id="categoryTabs">
                                @foreach($categories as $cat)
                                    @php $checked++; @endphp
                                    <button
                                        style="background: #0d2646"
                                        class="size-3 text-white nav-link {{ $checked == 1 ? 'active  text-dark' : '' }}"
                                        id="tabProduct_{{$cat->id}}_tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#tabProduct_{{$cat->id}}"
                                        type="button"
                                        role="tab"
                                    >
                                        {{ $cat->name }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Ürün Kartları --}}
                            <div class="tab-content mt-4">
                                @forelse($categories as $cat)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                         id="tabProduct_{{$cat->id}}"
                                         role="tabpanel">

                                        <div class="row">
                                            @foreach(\App\Models\Product::where('category_id', $cat->id)
                                                    ->where('status','active')
                                                    ->where('restaurant_id',auth()->id())
                                                    ->get() as $pro)

                                                <div class="col-md-3 mb-4" onclick="productAdd({{$pro->id}})"
                                                     style="cursor: pointer">
                                                    <div class="card text-white" style="background:#e7004d">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title text-white"
                                                                style="font-size: 24px">{{$pro->name}}</h5>
                                                            <p class="card-text"
                                                               style="font-size: 20px;font-weight: bold">
                                                                {{ number_format($pro->price, 2, ',', '.') }} ₺
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <h3>Kategori Bulunmuyor...</h3>
                                @endforelse
                            </div>
                        </div>

                        <!-- SAĞ SEPET -->
                        <div class="col-md-3 ">
                            <div class="card shadow-lg" style="border-radius: 10px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="size-3 px-3 mt-2">Sepetim </h4>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="special-ok-button-small text-white mt-2 float-end float-right"
                                           onclick="removePos(1)"><i
                                                class="fa fa-trash-alt"></i> Sepeti Temizle </a>

                                        <!-- Kupon Seçimi Butonu -->
                                        @if(count($coupons))
                                            <button type="button" class="px-2 special-ok-button-small text-white mt-2 float-end float-right" data-bs-toggle="modal" data-bs-target="#couponModal">
                                               + Kuponlar
                                            </button>

                                            <!-- Kupon Modal -->
                                            <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content" style="border-radius: 10px;">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="couponModalLabel">Kuponlar</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                                        </div>
                                                        <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                                                            <div class="coupon-list p-3">
                                                                @foreach($coupons as $coupon)
                                                                    <div class="coupon-item"
                                                                         data-coupon-id="{{ $coupon->id }}"
                                                                         data-coupon-name="{{ $coupon->name }}"
                                                                         data-coupon-amount="{{ $coupon->total_seller_amount }}"
                                                                         onclick="selectCoupon(this); $('#couponModal').modal('hide');"
                                                                         style="cursor: pointer; padding: 10px; border-bottom: 1px solid #eee;">
                                                                        <strong>{{ $coupon->name }}</strong><br>
                                                                        Toplam Tutar: {{ number_format($coupon->total_seller_amount, 2, ',', '.') }} ₺
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                    <hr>

                                    <div class="productItems row" style="min-height: 500px;">
                                        <div class="col-lg-12" id="productItemListp"
                                             style="padding: 20px;height: 460px;overflow-y: scroll">
                                            @foreach(\Cart::session(\Illuminate\Support\Facades\Auth::user()->id)->getContent() as $basket)
                                                <div id="posItem_{{$basket->id}}"
                                                     style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
                background-color: #f1f1f1; border-radius: 10px; padding: 12px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

                                                    <!-- Ürün Görseli -->
                                                    <div style="flex: 0 0 auto; margin-right: 12px;">
                                                        <img src="{{$basket->associatedModel->image}}"
                                                             alt="Ürün Görseli"
                                                             style="height: 60px; width: 60px; object-fit: cover; border-radius: 6px;">
                                                        <input type="hidden" name="product_id[]"
                                                               value="{{$basket->id}}">
                                                    </div>

                                                    <!-- Ürün Bilgileri -->
                                                    <div style="flex: 1 1 auto; min-width: 150px;">
                                                        <div
                                                            style="font-weight: bold; font-size: 14px; color: #333;">{{$basket->name}}</div>
                                                        <div
                                                            style="color: #555; font-size: 13px;">{{number_format($basket->price, 2, ',', '.')}}
                                                            ₺
                                                        </div>
                                                    </div>

                                                    <!-- Adet Butonları -->
                                                    <div
                                                        style="flex: 0 0 auto; display: flex; align-items: center; gap: 6px; margin-top: 8px;">
                                                        <button type="button" onclick="updateMinus({{$basket->id}})"
                                                                style="background-color: #dc3545; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                                                            <i class="fa fa-minus"></i>
                                                        </button>

                                                        <input type="text" name="quantity[]"
                                                               id="quantity_{{$basket->id}}"
                                                               value="{{$basket->quantity}}" disabled
                                                               style="width: 40px; height: 30px; text-align: center; font-weight: bold; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; background-color: white;">

                                                        <button type="button" onclick="updatePlus({{$basket->id}})"
                                                                style="background-color: #0d2646; border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer;">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-12" id="productItemLista"
                                             style="padding: 20px;height: 460px;overflow-y: scroll;display: none">

                                        </div>
                                    </div>
                                    <div style="padding: 1rem 1.2rem;" class="">

                                        <dl class="dlist-align">
                                            <dt>Toplam:</dt>
                                            <dd class="text-right h4 b"
                                                id="posTotal"> {{number_format(\Cart::session(\Illuminate\Support\Facades\Auth::user()->id)->getTotal(), 2, ',', '.')}}
                                                TL
                                            </dd>

                                            <dt>Kupon:</dt>
                                            <dd class="fw-bold size-4 text-danger" id="selectedCoupon">
                                                <span id="selectedCouponName">Bulunmuyor</span>
                                                <input type="hidden" id="coupon_id" name="coupon_id" value="">
                                            </dd>
                                        </dl>

                                        <div class="text-danger" id="selectedCoupon"
                                             style="margin-top:10px; font-weight: normal;">

                                        </div>
                                        <div class="row" style="margin:0px;">
                                            <div class="col-md-4" style="padding: 10px 0px">
                                                <div class="paymentRol nakit"
                                                     onclick="PaymentMethodSave('Kapıda Nakit ile Ödeme')"
                                                     style="font-size: 14px;">
                                                    <i class="fas fa-lira-sign"></i><br>
                                                    Nakit
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="padding: 10px 0px">
                                                <div class="paymentRol kkarti"
                                                     onclick="PaymentMethodSave('Kapıda Ticket ile Ödeme')"
                                                     style="font-size: 14px;">
                                                    <i class="fas fa-credit-card"></i><br>
                                                    Ticket
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="padding: 10px 0px">
                                                <div class="paymentRol kkkarti"
                                                     onclick="PaymentMethodSave('Kapıda Kredi Kartı ile Ödeme')"
                                                     style="font-size: 14px;">
                                                    <i class="fas fa-credit-card"></i><br>
                                                    Kredi Kartı
                                                </div>
                                            </div>

                                            <div class="col-md-12" style="padding: 10px 0px">
                                                <div class="paymentRol kayit" onclick="CreateOrder()">
                                                    <i class="fas fa-check"></i>
                                                    Kaydet
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <div class="modal fade" id="kuryeAta" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Kurye Seçiniz</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <select class="js-example-basic-single" onchange="CourierSet(event)"
                                        style="width: 100%;">
                                    <option value="0">Kurye Ata</option>
                                    <option value="-1">{{env('APP_NAME')}} Kuryesi</option>
                                    @foreach($courierses as $courier)
                                        <option value="{{$courier->id}}">{{$courier->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tamam</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="musteriAta" tabindex="-1" role="dialog" aria-labelledby="musteriAtaLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="musteriAtaLabel">Müşteri Seçiniz</h5>
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Kapat"
                            style="font-size: 1.2rem; padding: 0.25rem 0.5rem; background: white; border: none; line-height: 1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="customerSelect" class="font-weight-medium mb-2" style="font-size: 0.95rem;">
                                📋 Müşteri Seçimi
                            </label>
                            <select id="customerSelect"
                                    class="form-control js-example-basic-single"
                                    onchange="customerSelect(event)">
                                <option value="0">🔍 Müşteri Seçiniz...</option>
                                <!-- Müşteri listesi JavaScript ile doldurulacak -->
                            </select>
                            <small class="form-text text-muted mt-2">
                                Aramak için yazmaya başlayabilirsiniz. Seçim yapıldıktan sonra "Tamam" tuşuna basınız.
                            </small>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="special-ok-button" data-toggle="modal" data-target="#yeniMusteri">
                            <i class="fas fa-plus"></i> Müşteri Ekle
                        </button>
                        <button type="button" class="special-button" data-dismiss="modal">Tamam</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yeni müşteri ekle -->
        <div class="modal fade" id="yeniMusteri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Müşteri Ekle</h5>
                        <button style="    font-size: 1.2rem; /* or 1rem for even smaller */
    padding: 0.25rem 0.5rem;background: white;border: none;
    line-height: 1;" type="button" class="close small-close-btn" data-dismiss="modal" aria-label="Kapat">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="basic-form">
                                <form method="post" id="customerForm">
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Müşteri Adı</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                   placeholder="Müşteri Adı"
                                                   required>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Telefon Numarası</label>
                                            <input type="text" class="form-control" name="phone"
                                                   placeholder="Telefon Numarası" id="phoneNumber" required>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">İlçe Seçiniz</label>
                                            <select class="form-control" required name="ilce" id="">
                                                @foreach(App\Models\District::where('city_id',\App\Models\Admin::find(auth()->user()->admin_id)->city_id)->get() as $d)
                                                    <option value="{{$d->id}}">{{$d->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="card-body" style="border-top:1px solid #ddd;padding: 0px 0px">
                                        <div class="clearfix"></div>
                                        <div class="mb-3">
                                            <h5 class="fw-semibold text-black mb-3">Adres Bilgileri</h5>

                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2 text-black">Mah.</span>
                                                <input type="text" name="mahalle" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: Ankara" required>
                                            </div>

                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2 text-black">Sok.</span>
                                                <input type="text" name="sokak_cadde" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: 5021" required>
                                            </div>

                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2 text-black">Apt Adı.</span>
                                                <input type="text" name="bina_no" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: Deniz Apt." required>
                                            </div>

                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2 text-black">Kat:</span>
                                                <input type="text" name="kat" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: 3" required>
                                            </div>

                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2 text-black">Daire:</span>
                                                <input type="text" name="daire_no" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: 5" required>
                                            </div>

                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2 text-black">Adres Tarifi (opsiyonel):</span>
                                                <input type="text" name="adress_tarifi" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: 5" required>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="special-button" onclick="CreateCustomer()">Kaydet</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Yeni müşteri ekle -->

        <input type="hidden" value="{{Auth::user()->id}}" id="restaurant">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{asset('pos/assets/js/jquery-2.0.0.min.js')}}" type="text/javascript"></script>
<script src="{{asset('pos/assets/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('pos/assets/js/OverlayScrollbars.js')}}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

    // Modal açıldığında input alanına odaklanma
    $('#yeniMusteri').on('shown.bs.modal', function () {
        $('#name').focus();
    });

    // Modal kapatıldığında formu temizleme
    $('#yeniMusteri').on('hidden.bs.modal', function () {
        document.getElementById("customerForm").reset();
    });

    function toggleDrawer() {
        const drawer = document.getElementById('drawer');
        const isOpen = drawer.classList.contains('open');

        if (isOpen) {
            drawer.classList.remove('open');
            localStorage.setItem('drawerState', 'closed');
        } else {
            drawer.classList.add('open');
            localStorage.setItem('drawerState', 'open');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const drawer = document.getElementById('drawer');
        drawer.classList.remove('open'); // her zaman kapalı başlat
    });
</script>

<script type="text/javascript">
    function selectCoupon(element) {
        var couponId = $(element).data('coupon-id');
        var couponName = $(element).data('coupon-name');
        var couponAmount = $(element).data('coupon-amount');

        // Önce tüm kuponlardan seçili stilini kaldır
        $('.coupon-item').removeClass('selected');

        // Tıklanan kupona seçili stilini ekle
        $(element).addClass('selected');

        // Seçilen kuponu göster
        $('#selectedCouponName').text(couponName);
        $('#coupon_id').val(couponId);
    }

    $('form[name="formPos"]').on('submit', function (e) {
        e.preventDefault(); // Sayfa yenilenmesin
        CreateOrder(); // Siparişi oluştur
    });

    $(document).ready(function () {
        loadCustomers();

        $('#customerSelect').select2({
            dropdownParent: $('#musteriAta'),
            width: '100%',
            placeholder: 'Müşteri Seçiniz'
        });

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/removePOS',
            success: function (data) {
                $('#productItemListp').html("");
                $('#productItemLista').html("");
                $('.customer').html('<div style="text-align: center;padding: 15px">Müşteri Seçin</div>');
                $('#posTotalItem').html("0");
                $('#posTotal').html("0,00 TL");
            },
            error: function () {
                console.log(data);
            }
        });

        const drawer = document.getElementById('drawer');
        const savedState = localStorage.getItem('drawerState');

        if (savedState === 'open') {
            drawer.classList.add('open');
        } else {
            drawer.classList.remove('open');
        }

        $('.js-example-basic-single').select2({
            selectionCssClass: 'selectiki',
            placeholder: 'Müşteri Arayınız..',
            allowClear: true // placeholder için önerilir
        });

        $.ajax({
            type: 'GET',
            url: '/restaurant/get-pos-items',
            success: function (data) {
                console.log({new: data})
                $('#productItemLista').append(data.items);

                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                $('#totalPrice').val(data.total);
            },
            error: function () {
                console.log("Sepet yüklenirken hata oluştu.");
            }
        });
    });

    function productAdd(e) {
        let quant = $('#quantity_' + e).val();

        $('#loader').css('display', 'block');

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/addPOS/' + e,
            success: function (data) {
                let src = '{{url('pos/audio/dot.mp3')}}';
                let audio = new Audio(src);
                audio.play();

                $('#productItemListp').css('display', 'none');
                $('#productItemLista').css('display', 'block');

                if (data.durum === "yok") {
                    console.log({data: data})
                    $('#productItemLista').append(data.items);
                } else {
                    let newquant = parseInt(quant) + 1;
                    $('#quantity_' + e).val(newquant);
                }

                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                $('#totalPrice').val(data.total);

                $('#loader').css('display', 'none');
            },
            error: function () {
                console.log(data);
            }
        });
    }

    function updatePlus(id) {
        $('#loader').css('display', 'block');

        let quant = $('#quantity_' + id).val();

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/updatePlusPOS/' + id,
            success: function (data) {
                let src = '{{url('pos/audio/dot.mp3')}}';
                let audio = new Audio(src);
                audio.play();

                let newquant = parseInt(quant) + 1;
                $('#quantity_' + id).val(newquant);

                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                $('#totalPrice').val(data.total);

                $('#loader').css('display', 'none');
            },
            error: function () {
                console.log(data);
            }
        });
    }

    function updateMinus(id) {
        $('#loader').css('display', 'block');

        let qty = document.getElementById("quantity_" + id).value;

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/updateMinusPOS/' + id + '/' + qty,
            success: function (data) {
                let src = '{{url('pos/audio/dot.mp3')}}';
                let audio = new Audio(src);
                audio.play();

                if (qty <= 1) {
                    $("#posItem_" + id).remove();
                    $('#posTotalItem').html(data.posTotalItem);
                    $('#posTotal').html(data.posTotal);
                    $('#totalPrice').val(data.total);
                } else {
                    let newquant = parseInt(qty) - 1;
                    $('#quantity_' + id).val(newquant);
                    $('#posTotalItem').html(data.posTotalItem);
                    $('#posTotal').html(data.posTotal);
                    $('#totalPrice').val(data.total);
                }

                $('#loader').css('display', 'none');
            },
            error: function () {
                console.log(data);
            }
        });
    }

    function removePos(e) {
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/removePOS',
            success: function (data) {
                let src = '{{url('pos/audio/trash.mp3')}}';
                let audio = new Audio(src);
                audio.play();
                $('#productItemListp').html("");
                $('#productItemLista').html("");
                $('.customer').html('<div style="text-align: center;padding: 15px">Müşteri Seçin</div>');
                $('#posTotalItem').html("0");
                $('#posTotal').html("0,00 TL");
            },
            error: function () {
                console.log(data);
            }
        });
    }

    function PaymentMethodSave(e) {
        $('#payment_control').val(e);

        if (e === "Kapıda Nakit ile Ödeme") {
            let src = '{{url('pos/audio/beep.mp3')}}';
            let audio = new Audio(src);
            audio.play();
            $('.nakit').css('background', '#e7004d');
            $('.kkarti').css('background', '#0077b8');
            $('.kkkarti').css('background', '#183785');
        }
        if (e === "Kapıda Ticket ile Ödeme") {
            let src = '{{url('pos/audio/beep.mp3')}}';
            let audio = new Audio(src);
            audio.play();
            $('.nakit').css('background', '#1f49d3');
            $('.kkarti').css('background', '#e7004d');
            $('.kkkarti').css('background', '#183785');
        }
        if (e === "Kapıda Kredi Kartı ile Ödeme") {
            let src = '{{url('pos/audio/beep.mp3')}}';
            let audio = new Audio(src);
            audio.play();
            $('.nakit').css('background', '#1f49d3');
            $('.kkarti').css('background', '#0077b8');
            $('.kkkarti').css('background', '#e7004d');
        }
    }

    function CourierSet(e) {
        $('#courier_id').val(e.target.value);
        $('#kuryeAta').modal('hide');
    }

    function customerSelect(e) {
        let customerId = e.target.value;
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/customerpos/' + customerId,
            success: function (data) {
                $('#musteriAta').modal('hide');
                $('.customer').html(data.customer);
                $('#customer_id').val(e.target.value);

            },
            error: function () {
                console.log(data);
            }
        });
    }

    function CreateCustomer() {
        const form = document.getElementById('customerForm');

        // Form validation kontrolü
        if (!form.checkValidity()) {
            form.reportValidity(); // Eksik alanları gösterir
            return; // AJAX çalışmaz
        }


        // AJAX işlemi
        $.ajax({
            type: 'POST',
            url: '/restaurant/orders/customeradd' + '?_token=' + '{{ csrf_token() }}',
            data: $('#customerForm').serialize(),
            success: function (response) {
                $('.customer').html(response.customer);
                $('#customer_id').val(response.customerid);

                if(response.message){
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }

                // Modal kapat ve formu sıfırla
                $('#yeniMusteri').modal('hide');
                form.reset();

                $('#customerSelect').select2({
                    dropdownParent: $('#musteriAta'),
                    width: '100%',
                    placeholder: 'Müşteri Seçiniz'
                });

                loadCustomers();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Hata',
                    text: 'Müşteri eklenirken bir hata oluştu!',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function CreateOrder() {
        this.disabled = true;

        var payment_control = $('#payment_control').val();
        var customer_id = $('#customer_id').val();
        var courier_id = $('#courier_id').val();
        var coupon_id = $('#coupon_id').val();
        var total = $('#totalPrice').val();

        console.log({coupon_id: coupon_id})
        let products = [];

        $('.item').each(function () {
            products.push({
                product_id: $(this).find('input[name="product_id"]').val(),
                quantity: $(this).find('input[name="quantity"]').val()
            })
        })

        console.log({payment_control: payment_control})
        console.log({products: products})

        if (payment_control != 0) {
            if (customer_id > 0) {
                if (products.length > 0) {

                    //İşlemleriburada yapacağız
                    $.ajax({
                        type: 'POST',
                        url: '/restaurant/orders/addOrder' + '?_token=' + '{{ csrf_token() }}',
                        data: {
                            customer_id: customer_id,
                            payment_method: payment_control,
                            courier_id: courier_id,
                            coupon_id: coupon_id,
                            products: products,
                            amount: total
                        },
                        success: function (response) {
                            console.log({sgf: response})
                            if (response.status === "BalanceError") {
                                console.log('balance girdi')
                                Swal.fire({
                                    title: response.message,
                                    text: 'Üzgünüz, Kontör Bakiyeniz Yetersiz Olduğundan Ürün Eklenemiyor !',
                                    icon: 'warning',
                                    confirmButtonText: 'Tamam',
                                    background: '#ffffff', // senin rengin
                                    color: '#fff',
                                    iconColor: '#e7004d', // modern yeşil
                                    confirmButtonColor: '#e7004d', // modern yeşil düğme
                                    customClass: {
                                        popup: 'rounded-xl shadow-2xl',
                                        confirmButton: 'px-6 py-3 text-lg font-semibold',
                                    },
                                    showClass: {
                                        popup: 'animate__animated animate__fadeInDown',
                                    },
                                    hideClass: {
                                        popup: 'animate__animated animate__fadeOutUp',
                                    }
                                })
                            }

                            if (response.status === 'ERR') {
                                Swal.fire({
                                    title: response.message,
                                    text: '',
                                    icon: 'warning',
                                    confirmButtonText: 'Tamam',
                                    background: '#ffffff', // senin rengin
                                    color: '#fff',
                                    iconColor: '#e7004d', // modern yeşil
                                    confirmButtonColor: '#e7004d', // modern yeşil düğme
                                    customClass: {
                                        popup: 'rounded-xl shadow-2xl',
                                        confirmButton: 'px-6 py-3 text-lg font-semibold',
                                    },
                                    showClass: {
                                        popup: 'animate__animated animate__fadeInDown',
                                    },
                                    hideClass: {
                                        popup: 'animate__animated animate__fadeOutUp',
                                    }
                                })
                            }

                            if (response.status === "OK") {
                                var divToPrint = response.printed;
                                var mywindow = window.open('', 'PRINT', 'height=600,width=800');
                                mywindow.document.write('<html><head><title>' + document.title + '</title>');
                                mywindow.document.write('</head><body >');
                                mywindow.document.write(divToPrint);
                                mywindow.document.write('</body></html>');
                                mywindow.document.close(); // necessary for IE >= 10
                                mywindow.focus(); // necessary for IE >= 10*/
                                mywindow.print();

                                // Seçili müşteriyi sıfırla
                                $('#customerSelect').val('0').trigger('change');

                                // Müşteri bilgilerini temizle
                                $('.customer').html('');
                                $('#customer_id').val('');

                                $.ajax({
                                    type: 'GET', //THIS NEEDS TO BE GET
                                    url: '/restaurant/orders/removePOS',
                                    success: function (data) {
                                        let src = '{{url('pos/audio/trash.mp3')}}';
                                        let audio = new Audio(src);
                                        audio.play();
                                        $('#productItemListp').html("");
                                        $('#productItemLista').html("");
                                        $('#posTotalItem').html("0");
                                        $('.customer').html('<div style="text-align: center;padding: 15px">Müşteri Seçin</div>');
                                        $('#posTotal').html("0,00 TL");

                                        $('.nakit').css('background', '#1f49d3');
                                        $('.kkarti').css('background', '#0077b8');
                                        $('.kkkarti').css('background', '#183785');

                                        Swal.fire({
                                            title: 'Sipariş Tamamlandı',
                                            text: 'Siparişiniz başarıyla alındı!',
                                            icon: 'success',
                                            confirmButtonText: 'Tamam',
                                            background: '#ffffff', // senin rengin
                                            color: '#fff',
                                            iconColor: '#30d760', // modern yeşil
                                            confirmButtonColor: '#1fde74', // modern yeşil düğme
                                            customClass: {
                                                popup: 'rounded-xl shadow-2xl',
                                                confirmButton: 'px-6 py-3 text-lg font-semibold',
                                            },
                                            showClass: {
                                                popup: 'animate__animated animate__fadeInDown',
                                            },
                                            hideClass: {
                                                popup: 'animate__animated animate__fadeOutUp',
                                            }
                                        })
                                        this.disabled = false;
                                    },
                                    error: function () {
                                        console.log(data);
                                    }
                                });
                            }
                        },
                        error: function (response) {
                            console.log({response: response})
                        }
                    });

                } else {
                    Swal.fire({
                        title: 'Sepetinizde ürün Bulunmuyor!!',
                        text: '',
                        icon: 'warning',
                        confirmButtonText: 'Tamam',
                        background: '#ffffff', // senin rengin
                        color: '#fff',
                        iconColor: '#e7004d', // modern yeşil
                        confirmButtonColor: '#e7004d', // modern yeşil düğme
                        customClass: {
                            popup: 'rounded-xl shadow-2xl',
                            confirmButton: 'px-6 py-3 text-lg font-semibold',
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown',
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp',
                        }
                    })
                }
            } else {
                Swal.fire({
                    title: 'Lütfen Bir Müşteri Seçiniz',
                    text: '',
                    icon: 'warning',
                    confirmButtonText: 'Tamam',
                    background: '#ffffff', // senin rengin
                    color: '#fff',
                    iconColor: '#e7004d', // modern yeşil
                    confirmButtonColor: '#e7004d', // modern yeşil düğme
                    customClass: {
                        popup: 'rounded-xl shadow-2xl',
                        confirmButton: 'px-6 py-3 text-lg font-semibold',
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown',
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp',
                    }
                })
                $('#musteriAta').modal('show');
            }
        } else {
            Swal.fire({
                title: 'Lütfen Bir Ödeme Methodu Seçiniz',
                text: '',
                icon: 'warning',
                confirmButtonText: 'Tamam',
                background: '#ffffff', // senin rengin
                color: '#fff',
                iconColor: '#e7004d', // modern yeşil
                confirmButtonColor: '#e7004d', // modern yeşil düğme
                customClass: {
                    popup: 'rounded-xl shadow-2xl',
                    confirmButton: 'px-6 py-3 text-lg font-semibold',
                },
                showClass: {
                    popup: 'animate__animated animate__fadeInDown',
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp',
                }
            })
        }
    }

    function loadCustomers() {
        $.ajax({
            type: 'GET',
            url: '/restaurant/get-customers', // Bu endpoint'i oluşturmanız gerekecek
            success: function(data) {
                console.log({data:data})
                $('#customerSelect').empty().append('<option value="0">🔍 Müşteri Seçiniz...</option>');

                if (data.customers && data.customers.length > 0) {
                    $.each(data.customers, function(index, customer) {
                        $('#customerSelect').append(
                            $('<option>', {
                                value: customer.id,
                                text: customer.name + ' - ' + customer.phone
                            })
                        );
                    });
                } else {
                    $('#customerSelect').append(
                        $('<option>', {
                            value: '',
                            text: 'Müşteri bulunamadı',
                            disabled: true
                        })
                    );
                }

                // Select2'yi yeniden başlat
                $('#customerSelect').select2({
                    dropdownParent: $('#musteriAta'),
                    width: '100%',
                    placeholder: 'Müşteri Seçiniz'
                });
            },
            error: function() {
                console.log("Müşteri listesi yüklenirken hata oluştu.");
                $('#customerSelect').empty().append('<option value="0">🔍 Müşteri Seçiniz...</option>');

                // Select2'yi yeniden başlat
                $('#customerSelect').select2({
                    dropdownParent: $('#musteriAta'),
                    width: '100%',
                    placeholder: 'Müşteri Seçiniz'
                });
            }
        });
    }
</script>
</body>
</html>
