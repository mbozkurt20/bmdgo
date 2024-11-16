@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Müşteriler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Müşteriler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
                </ol>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                </button>
                <a href="#"> {{ session()->get('message') }}</a>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Yeni Müşteri Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" class="repeater" action="{{ route('restaurant.customers.create') }}">
                                @csrf
                                <div class="row">
                                    <!-- Customer Name -->
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Müşteri Adı</label>
                                        <input type="text" class="form-control" name="name" placeholder="Müşteri Adı"
                                            required>
                                    </div>
                                    <!-- Customer Phone -->
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon Numarası</label>
                                        <input type="text" class="form-control" name="phone"
                                            placeholder="Telefon Numarası" required>
                                    </div>
                                    <!-- Customer Mobile (Secondary Phone) -->
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Telefon Numarası 2</label>
                                        <input type="text" class="form-control" name="mobile"
                                            placeholder="Diğer Telefon Numarası">
                                    </div>
                                </div>

                                <!-- Address Section -->
                                <div class="card-body" style="border-top:1px solid #ddd">
                                    <div>
                                        <!-- Repeater Heading -->
                                        <div class="repeater-heading">
                                            <div class="row">
                                                <div class="col-lg-10">
                                                    <h5 class="pull-left">Adres Ekle</h5>
                                                </div>
                                                <div class="col-lg-2" style="text-align: right">
                                                    <a class="btn btn-primary repeater-add-btn"
                                                        data-repeater-create>Ekle</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>

                                        <!-- Repeater Items -->
                                        <div data-repeater-list="address">
                                            <!-- Repeater Content -->
                                            <div data-repeater-item class="item-content row"
                                                style="background: #f4f4f4;margin: 15px 0px 10px;padding:10px 0px;border-radius: 10px">
                                                <!-- Address Name -->
                                                <div class="mb-3 col-md-5">
                                                    <input type="text" class="form-control" name="name"
                                                        placeholder="Adres Başlığı">
                                                </div>
                                                <!-- Sokak/Cadde -->
                                                <div class="mb-3 col-md-6">
                                                    <input type="text" class="form-control" name="sokak_cadde"
                                                        placeholder="Sokak/Cadde">
                                                </div>
                                                <!-- Remove Button -->
                                                <div class="mb-3 col-md-1">
                                                    <div class="pull-right repeater-remove-btn">
                                                        <a id="remove-btn" style="font-size: 20px;cursor: pointer"
                                                            class="text-danger" data-repeater-delete>
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!-- Bina No -->
                                                <div class="mb-3 col-md-3">
                                                    <input type="text" class="form-control" name="bina_no"
                                                        placeholder="Bina No">
                                                </div>
                                                <!-- Kat -->
                                                <div class="mb-3 col-md-3">
                                                    <input type="text" class="form-control" name="kat"
                                                        placeholder="Kat">
                                                </div>
                                                <!-- Daire No -->
                                                <div class="mb-3 col-md-3">
                                                    <input type="text" class="form-control" name="daire_no"
                                                        placeholder="Daire No">
                                                </div>
                                                <!-- Mahalle -->
                                                <div class="mb-3 col-md-3">
                                                    <input type="text" class="form-control" name="mahalle"
                                                        placeholder="Mahalle">
                                                </div>
                                                <!-- Adres Tarifi -->
                                                <div class="mb-3 col-md-12">
                                                    <input type="text" name="adres_tarifi" class="form-control"
                                                        placeholder="Adres Tarifi">
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary">Kaydı Tamamla</button>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
