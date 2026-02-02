@extends('dealer.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Profil Düzenle</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/couriers">Profil</a></li>
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
                        <h4 class="card-title">Profil Düzenle </h4>
                    </div>
                    <div class="card-body">


                        <div class="basic-form">
                            <form action="{{ route('dealer.profile.update') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark" for="name">İsim</label>
                                        <input type="text" name="name" class="form-control"
                                               value="{{ old('name',$auth->name) }}">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark" for="email">Eposta</label>
                                        <input type="text" value="{{ old('email',$auth->email) }}" name="email" class="form-control">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="text-dark" for="password">Yeni Şifre</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>

                                    <button type="submit" class="special-button mt-3">Güncelle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
