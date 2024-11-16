@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restaurantlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Restaurantlar</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $restaurant->name }}</a></li>
                </ol>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                </button> <a href="#"> {{ session()->get('message') }}</a>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">İşyeri Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{ route('admin.restaurants.update') }}">
                                @csrf
                                <div class="row">
                                    <input type="hidden" value="{{ $restaurant->id }}" name="id">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">İşyeri Adı</label>
                                        <input type="text" class="form-control" name="restaurant_name"
                                            value="{{ $restaurant->restaurant_name }}" placeholder="İşyeri Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Yetkili Adı</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $restaurant->name }}" placeholder="Yetkili Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">E-posta Adresi</label>
                                        <input type="email" class="form-control" name="email"
                                            value="{{ $restaurant->email }}" placeholder="E-posta Adresi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ $restaurant->phone }}" placeholder="Telefon">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Şifre</label>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Değiştirmek istemiyorsanız boş bırakın">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Vergi Dairesi</label>
                                        <input type="text" class="form-control" name="tax_name"
                                            value="{{ $restaurant->tax_name }}" placeholder="Vergi Dairesi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Vergi Numarası</label>
                                        <input type="text" class="form-control" name="tax_number"
                                            value="{{ $restaurant->tax_number }}" placeholder="Vergi Numarası">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Entegra ID</label>
                                        <input type="text" class="form-control" name="entegra_id"
                                            value="{{ $restaurant->entegra_id }}" placeholder="Entegra Id">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Entegra Token</label>
                                        <input type="text" class="form-control" name="entegra_token"
                                            value="{{ $restaurant->entegra_token }}" placeholder="Entegra Token">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Paket Fiyatı</label>
                                        <input type="text" class="form-control" name="package_price"
                                            value="{{ $restaurant->package_price }}" placeholder="Paket Fiyatı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" name="status">
                                            <option value="active"
                                                {{ $restaurant->status == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="deactive"
                                                {{ $restaurant->status == 'deactive' ? 'selected' : '' }}>Deactive</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Adres</label>
                                        <input type="text" class="form-control" name="address"
                                            value="{{ $restaurant->address }}" placeholder="İşyeri Adresi">
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
