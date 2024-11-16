@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restaurantlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Restaurantlar</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
                </ol>
            </div>
        </div>
        @if(session()->has('message'))

        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
            </button> <a href="#">   {{ session()->get('message') }}</a>
        </div>

        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni İşyeri Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('admin.restaurants.create')}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">İşyeri Adı</label>
                                        <input type="text" class="form-control" name="restaurant_name" placeholder="İşyeri Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Yetkili Adı</label>
                                        <input type="text" class="form-control" name="name" placeholder="Yetkili Adı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">E-posta Adresi</label>
                                        <input type="email" class="form-control" name="email" placeholder="E-posta Adresi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon</label>
                                        <input type="text" class="form-control" name="phone" placeholder="Telefon">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Şifre</label>
                                        <input type="password" class="form-control" name="password"  placeholder="Şifre">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Vergi Dairesi</label>
                                        <input type="text" class="form-control" name="tax_name" placeholder="Vergi Dairesi">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Vergi Numarası</label>
                                        <input type="text" class="form-control" name="tax_number" placeholder="Vergi Numarası">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Entegra ID</label>
                                        <input type="text" class="form-control" name="entegra_id"   placeholder="Entegra Id">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Entegra Token</label>
                                        <input type="text" class="form-control" name="entegra_token"   placeholder="Entegra Token">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Paket Fiyatı</label>
                                        <input type="text" class="form-control" name="package_price"  value="1.00" placeholder="Paket Fiyatı">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Adres</label>
                                        <input type="text" class="form-control" name="address" placeholder="İşyeri Adresi">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Kaydı Tamamla</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


