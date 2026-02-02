@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">{{$coupon->name}}</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/restaurant/categories">{{$coupon->name}}</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Düzenle</a></li>
                </ol>
            </div>
        </div>
        @if(session()->has('message'))
            <div class="custom-alert success">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('message') }}</span>
            </div>
        @endif

        @if(session()->has('test') )
            <div class="custom-alert error">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('test') }}</span>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Kupon Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('restaurant.coupons.update')}}">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="id" value="{{$coupon->id}}">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Kupon Adı</label>
                                        <input value="{{$coupon->name}}" required type="text" class="form-control" name="name" placeholder="Kupon Adı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Kupon Açıklması (opsiyonel)</label>
                                        <input value="{{$coupon->description}}" type="text" class="form-control" name="description" placeholder="Kupon Açıklaması">
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Kupon Tutarı</label>
                                        <input value="{{$coupon->total_seller_amount}}" required type="number" class="form-control" name="total_seller_amount" placeholder="Kupon Tutarı">
                                    </div>
                                </div>

                                <button type="submit" class="special-button float-end mt-4">Kaydı Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


