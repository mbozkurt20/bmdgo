@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kuryeler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Kuryeler</a></li>
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
                        <h4 class="card-title">Kurye Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{ route('admin.couriers.update') }}">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="id" value="{{ $courier->id }}">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Kurye Adı</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $courier->name }}" placeholder="Kurye Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon Numarası</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ $courier->phone }}" placeholder="Telefon Numarası">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Şifresi</label>
                                        <input type="text" class="form-control" name="password"
                                            value="{{ $courier->password }}" placeholder="Kurye Apk Şifresi">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Paket Başı Fiyatı</label>
                                        <input type="text" class="form-control" name="price"
                                            value="{{ $courier->price }}" placeholder="Paket Başı Fiyatı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Kurye Durumu</label>
                                        <select class="form-control" name="situation">
                                            <option value="Aktif" {{ $courier->situation == 'Aktif' ? 'selected' : '' }}>
                                                Aktif</option>
                                            <option value="Molada" {{ $courier->situation == 'Molada' ? 'selected' : '' }}>
                                                Molada</option>
                                            <option value="Serviste"
                                                {{ $courier->situation == 'Serviste' ? 'selected' : '' }}>Serviste</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Kaydı Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
