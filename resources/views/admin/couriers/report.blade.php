@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kurye Rapor</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Kuryeler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{$courier->name}}</a></li>
                </ol>
            </div>
        </div>
        <div class="card card-body">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div class="customer-search text-dark  size-1 mb-sm-0 mb-3">
                    {{$courier->name}}
                </div>
                <div class="d-flex size-1 text-dark align-items-center flex-wrap">
                    {{$courier->phone}}
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="table-responsive">
                        <table class="table table-responsive-sm text-dark">
                            <thead>
                            <tr>
                                <th>Platform</th>
                                <th>Sipariş No</th>
                                <th>Müşteri</th>
                                <th>Telefon</th>
                                <th>Tutar</th>
                                <th>Ödeme Yön.</th>
                                <th>Durum</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $nakit = 0;
                                $kkarti = 0;
                                $kkkarti = 0;
                                $top_siparis = 0;
                                $top_nakit = 0;
                                $top_kkarti = 0;
                                $top_kkkarti = 0;
                            @endphp

                            @foreach($orders as $order)
                                @php
                                    // Ödeme yöntemine göre toplamları ayarlıyoruz
                                    if($order->payment_method == "PAY_WITH_CARD"){
                                        $kkarti += $order->amount;
                                        $top_kkarti++;
                                    } elseif($order->payment_method == "CASH") {
                                        $nakit += $order->amount;
                                        $top_nakit++;
                                    } elseif($order->payment_method == "CASH_ON_DELIVERY_CARD") {
                                        $kkkarti += $order->amount;
                                        $top_kkkarti++;
                                    }
                                @endphp

                                <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#ffffff' : '#0d2646' }}!important; color: {{ $loop->iteration % 2 == 0 ? '#000000' : '#ffffff' }};">
                                    <td>{{$order->platform}}</td>
                                    <td>{{$order->tracking_id}}</td>
                                    <td>{{$order->full_name}}</td>
                                    <td>{{$order->phone}}</td>
                                    <td class="text-ov">{{$order->amount}} ₺</td>
                                    <td>
                                        @if($order->payment_method == "PAY_WITH_CARD")
                                            Kredi Kartı
                                        @elseif($order->payment_method == "CASH")
                                            Nakit
                                        @elseif($order->payment_method == "CASH_ON_DELIVERY_CARD")
                                            Kapıda Kredi Kartı
                                        @else
                                            {{$order->payment_method}}
                                        @endif
                                    </td>
                                    <td>{{$order->status}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot >
                            <tr>
                                <th scope="row">Siparişler ({{count($orders)}})</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="font-size: 15px;font-weight: bold">({{$top_nakit}}) Adet Nakit</td>
                                <td style="font-size: 15px;font-weight: bold">({{$top_kkarti}}) Adet K.Kartı Adeti</td>
                                <td style="font-size: 15px;font-weight: bold">({{$top_kkkarti}}) Adet Kap K.Kartı</td>
                            </tr>
                            <tr style="background: #0d2646;color: white">
                                <th scope="row">Ödemeler</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="font-size: 15px;font-weight: bold">Nakit :  {{$nakit}} ₺</td>
                                <td style="font-size: 15px;font-weight: bold">K.Kartı : {{$kkarti}} ₺</td>
                                <td style="font-size: 15px;font-weight: bold">Kap K.Kartı : {{$kkkarti}} ₺</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
