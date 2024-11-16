@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restaurant Ürünleri</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Restaurant Ürünleri</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Düzenle</a></li>
                </ol>
            </div>
        </div>
        @if(session()->has('message'))

            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                </button>
                <a href="#">   {{ session()->get('message') }}</a>
            </div>

        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Ürün Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('restaurant.products.update')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="id" value="{{$product->id}}">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ürün Adı</label>
                                        <input type="text" class="form-control" name="name" value="{{$product->name}}"
                                               placeholder="Ürün Adı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ürün Kodu</label>
                                        <input type="text" class="form-control" name="code" value="{{$product->code}}"
                                               placeholder="Ürün Kodu">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Kategorisi</label>
                                        <select class="me-sm-2 default-select form-control wide" name="categorie_id">
                                            @foreach($categories as $categorie)
                                                <option @if($categorie->id == $product->categorie_id) selected @endif value="{{$categorie->id}}">{{$categorie->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ürün Fiyatı</label>
                                        <input type="text" class="form-control" name="price" value="{{$product->price}}"
                                               placeholder="Ürün Fiyatı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Resim</label>
                                        <input type="file" class="form-control" name="image">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Hazırlanma Süresi</label>
                                        <input type="number" class="form-control" name="preparation_time"
                                               value="{{$product->preparation_time}}"
                                               placeholder="Hazırlanma Süresi (dk)">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Ürün Detayları</label>
                                        <textarea class="form-control" name="details"
                                                  rows="15">{{$product->details}}</textarea>
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


