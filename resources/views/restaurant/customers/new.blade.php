@extends('restaurant.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Müşteriler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/restaurant/customers">Müşteriler</a></li>
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
        @if (session()->has('test'))
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
                        <form method="post" class="repeater" action="{{ route('restaurant.customers.create') }}">
                            @csrf
                            <div class="row">
                                <!-- Müşteri Adı -->
                                <div class="mb-3 col-md-12">
                                    <label class="form-label text-dark">Müşteri Adı <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" name="name" placeholder="Müşteri Adı" required>
                                </div>

                                <!-- Telefon -->
                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-dark">Telefon Numarası <small class="text-danger">*</small></label>
                                    <input type="text" class="form-control" name="phone" placeholder="Telefon Numarası" required>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label class="form-label text-dark">Telefon Numarası (opsiyonel)</label>
                                    <input type="text" class="form-control" name="mobile" placeholder="Diğer Telefon Numarası">
                                </div>
                            </div>

                            <hr>
                            <!-- Adresler -->
                            <div class="p-3" style="background: #f4f0f0">
                                <div class="repeater-heading mb-3">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <h5>Adres Ekle <small class="text-danger">*</small></h5>
                                        </div>
                                        <div class="col-lg-2 text-end">
                                            <a id="new-add" class="special-ok-button-small btn-xs repeater-add-btn" data-repeater-create>+ Yeni Ekle
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div data-repeater-list="address">
                                    <div data-repeater-item class="item-content row border p-3 mb-4 rounded">
                                        <!-- Adres Başlığı -->
                                        <div class="mb-3 col-md-5">
                                            <input type="text" class="form-control" name="name" required placeholder="Adres Başlığı">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <select class="form-control" required name="sehir" id="">
                                                @foreach(App\Models\City::all() as $city)
                                                    <option value="{{$city->id}}">{{$city->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="mahalle" required placeholder="Mahalle">
                                        </div>
                                        <div class="mb-3 col-md-1 text-end">
                                            <a style="font-size: 20px; cursor: pointer" class="text-danger" data-repeater-delete>
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="sokak_cadde" required placeholder="Sokak/Cadde">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="bina_no" required placeholder="Bina No">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="kat" required placeholder="Kat">
                                        </div>

                                        <div class="mb-3 col-md-3">
                                            <input type="text" class="form-control" name="daire_no" required placeholder="Daire No">
                                        </div>



                                        <div class="mb-3 col-md-12">
                                            <input type="text" name="adres_tarifi" class="form-control" required placeholder="Adres Tarifi">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="special-button float-end mt-4">Kaydı Tamamla</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
