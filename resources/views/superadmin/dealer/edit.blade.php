@extends('superadmin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Bayii</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Bayii</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Güncelle</a></li>
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
        @if($errors->any())
            {{ implode('', $errors->all('<div>:message</div>')) }}
        @endif
        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Bayii Güncelle</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('superadmin.dealer_update', $dealer->id)}}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Bayii Adı</label>
                                        <input type="text" class="form-control" name="name" placeholder="Bayii Adı" value="{{ $dealer->name }}">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control" name="email"
                                               placeholder="Email" value="{{ $dealer->email }}">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Parola</label>
                                        <input type="password" class="form-control" name="password"
                                               placeholder="Parola">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Lokasyon (Latitude)</label>
                                        <input type="text" class="form-control" name="lat" placeholder="546932" value="{{ $dealer->default_locations_lat }}" >
                                    </div>
									<div class="mb-3 col-md-4">
                                        <label class="form-label">Lokasyon (Longitude)</label>
                                        <input type="text" class="form-control" name="lon" placeholder="546932" value="{{ $dealer->default_locations_lon }}">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


