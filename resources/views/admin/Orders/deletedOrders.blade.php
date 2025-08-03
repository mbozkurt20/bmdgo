@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Siparişler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Siparişler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Liste</a></li>
                </ol>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter" placeholder="Sipariş ara..">
                    <span class="input-group-text"><a href="javascript:void(0)"><i
                                class="flaticon-381-search-2"></i></a></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="javascript:void(0);" class="btn bg-white btn-rounded me-2 mb-2 text-black shadow-sm"><i
                        class="fas fa-calendar-times me-3 scale3 text-primary"></i>Filtrele<i
                        class="fas fa-chevron-down ms-3 text-primary"></i></a>
                <a href="javascript:void(0);" class="btn btn-secondary btn-rounded mb-2"><i class="fas fa-sync"></i></a>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="table-responsive  card card-body">
                    <table id="example3" class="order-table shadow-hover card-table text-black" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>Platform</th>
                                <th>Sipariş No</th>
                                <th>Müşteri</th>
                                <th>Telefon</th>
                                <th>Tutar</th>
                                <th>Ödeme Yön.</th>
                                <th>Iptal Sebebi</th>
                                <th>Durum</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr id="data_{{ $order->id }}">
                                    <td>
                                        @if ($order->platform == 'yemeksepeti')
                                            <a class="btn btn-primary btn-rounded"
                                                style="padding: 10px;background: #fb0050;border-color: #fb0050;"><img
                                                    src="{{ asset('theme/images/yemeksepeti.png') }}" style="height: 15px">
                                            </a>
                                        @endif
                                        @if ($order->platform == 'getir')
                                            <a class="btn btn-primary btn-rounded"
                                                style="padding: 10px;background: #6244be;border-color: #6244be;"> <img
                                                    src="{{ asset('theme/images/getiryemek.png') }}" style="height: 15px">
                                            </a>
                                        @endif
                                        @if ($order->platform == 'trendyol')
                                            <a style="padding: 10px" class="btn btn-primary btn-rounded"><img
                                                    src="{{ asset('theme/images/trendyolyemek.png') }}"
                                                    style="height: 15px"> </a>
                                        @endif
                                        @if ($order->platform == 'adisyo')
                                            <a style="padding: 10px;background: #ff0a0a;border-color: #fff; font-size: 14px"
                                                class="btn btn-primary btn-rounded"><img
                                                    src="{{ asset('theme/images/adisyoFull.png') }}" style="height: 25px;">
                                            </a>
                                        @endif
                                        @if ($order->platform == 'telefonsiparis')
                                            <a class="btn btn-warning btn-rounded"
                                                style="wdith:100%;font-weight: bold;padding:10px 15px;font-size:14px;">
                                                POS</a>
                                        @endif

                                    </td>
                                    <td>{{ $order->tracking_id }}</td>
                                    <td>{{ $order->full_name }}</td>
                                    <td>{{ $order->phone }}</td>
                                    <td class="text-ov">{{ $order->amount }} TL</td>
                                    <td>
                                        @if ($order->payment_method == 'PAY_WITH_CARD')
                                            Kredi Kartı
                                        @else
                                            {{ $order->payment_method }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $order->message }}
                                    </td>
                                    <td>
                                        <select class="default-select  form-control wide"
                                            onchange="Status(event,{{ $order->id }})">
                                            <option value="0">{{ $order->status }}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a data-bs-toggle="modal" data-bs-target="#Orders{{ $order->id }}"
                                                class="btn btn-secondary shadow btn-xs sharp me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <div class="modal fade" id="Orders{{ $order->id }}">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
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
                                                                    <p class="orderProde">{{ $order->tracking_id }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-6">
                                                                    <p class="orderTitle">Müşteri Adı</p>
                                                                    <p class="orderProde">{{ $order->full_name }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Telefon</p>
                                                                    <p class="orderProde">{{ $order->phone }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Tutar</p>
                                                                    <p class="orderProde">{{ $order->amount }}</p>
                                                                </div>
                                                                <div class="mb-1 col-md-4">
                                                                    <p class="orderTitle">Ödeme Yön.</p>
                                                                    <p class="orderProde">
                                                                        @if ($order->payment_method == 'PAY_WITH_CARD')
                                                                            Kredi Kartı
                                                                        @else
                                                                            {{ $order->payment_method }}
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                                <div class="mb-2 col-md-12">
                                                                    <p class="orderTitle">Adres</p>
                                                                    <p class="orderProde">{{ $order->address }}</p>
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
                                                                                        {{ $item->name }}</th>
                                                                                    <th class="orderProde">1</th>
                                                                                    <th class="orderProde">
                                                                                        {{ $item->price }} TL</th>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-primary light"
                                                                onclick="printDiv({{ $order->id }})"><i
                                                                    class="fa fa-print"></i>Yazdır</button>
                                                            <button type="button" class="btn btn-danger light"
                                                                data-bs-dismiss="modal">Kapat</button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <a onclick="printDiv({{ $order->id }})"
                                                class="btn btn-danger shadow btn-xs sharp me-1">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>

                                    </td>


                                    <script>
                                        function Courier(e, order) {
                                            let courier = e.target.value;
                                            let orderid = order;

                                            $.ajax({
                                                type: 'GET', //THIS NEEDS TO BE GET
                                                url: '/restaurant/orders/sendCourier/' + orderid + '/' + courier,
                                                success: function(data) {
                                                    if (data == "OK") {
                                                        alert("Kurye atama başarılı.");
                                                    }
                                                    if (data == "NO") {
                                                        alert("Kurye müsait değil.");
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
                            <!-- Modal -->


                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#example3').DataTable();
            //DataTable custom search field
            $('#custom-filter').keyup(function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endsection
