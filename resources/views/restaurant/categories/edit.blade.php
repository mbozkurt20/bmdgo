@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kategoriler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Kategoriler</a></li>
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
                        <h4 class="card-title">Kategori Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('restaurant.categories.update')}}">
                                @csrf
                                <div class="row">
                                    <input type="hidden" name="id" value="{{$categorie->id}}">
                                    <div class="mb-3 col-md-8">
                                        <label class="form-label">Kategori Adı</label>
                                        <input type="text" class="form-control" name="name" value="{{$categorie->name}}"
                                               placeholder="Kategori Adı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">POS Sırası</label>
                                        <input type="number" class="form-control" name="desk" value="{{$categorie->desk}}"
                                               placeholder="POS Görünüm Sırası">
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


