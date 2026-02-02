@extends('restaurant.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Yazıcı Tanımla</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/restaurant/categories">Yazıcı Tanımla</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
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
                        <h4 class="card-title">Yeni Yazıcı Tanımla</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('restaurant.prints.create')}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Yazıcı Adı</label>
                                        <input required type="text" class="form-control" name="name" placeholder="Yazıcı Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Yazıcı Açıklması (opsiyonel)</label>
                                        <input type="text" class="form-control" name="description" placeholder="Yazıcı Açıklaması">
                                    </div>
                                </div>

                                <button type="submit" class="special-button float-end mt-4">Kaydet</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


