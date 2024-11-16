@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kuryeler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Kuryeler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{$courier->name}}</a></li>
                </ol>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                {{$courier->name}}
            </div>
            <div class="d-flex align-items-center flex-wrap">
                {{$courier->phone}}
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="table-responsive">
                    <table class="table table-responsive-sm">
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

                                if($order->payment_method == "PAY_WITH_CARD"){
                                    $kkarti = $kkarti + $order->amount;
                                    $top_kkarti += $top_kkarti++;
                                }
                            @endphp
                            <tr >
                                <td>{{$order->platform}}</td>
                                <td>{{$order->tracking_id}}</td>
                                <td>{{$order->full_name}}</td>
                                <td>{{$order->phone}}</td>
                                <td class="text-ov">{{$order->amount}} ₺</td>
                                <td>
                                    @if($order->payment_method == "PAY_WITH_CARD")
                                        Kredi Kartı
                                    @else
                                        {{$order->payment_method}}
                                    @endif
                                </td>
                                <td>{{$order->status}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot style="background: #f5f5f5">
                        <tr>
                            <th scope="row">Siparişler ({{count($orders)}})</th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="font-size: 15px;font-weight: bold">({{$top_nakit}}) Adet Nakit</td>
                            <td style="font-size: 15px;font-weight: bold">({{$top_kkarti}}) Adet K.Kartı Adeti</td>
                            <td style="font-size: 15px;font-weight: bold">({{$top_kkkarti}}) Adet Kap K.Kartı</td>
                        </tr>
                        <tr>
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

@endsection
