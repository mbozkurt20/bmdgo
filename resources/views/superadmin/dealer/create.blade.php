@extends('superadmin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Bayii</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Bayii</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
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
                        <h4 class="card-title">Yeni Bayii Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('superadmin.dealer_create_request')}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Bayii Adı</label>
                                        <input type="text" class="form-control" name="name" placeholder="Bayii Adı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control" name="email"
                                               placeholder="Email">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Parola</label>
                                        <input type="password" class="form-control" name="password"
                                               placeholder="Parola">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Lokasyon (Latitude)</label>
                                        <input type="text" class="form-control" name="lat" placeholder="546932">
                                    </div>
									<div class="mb-3 col-md-4">
                                        <label class="form-label">Lokasyon (Longitude)</label>
                                        <input type="text" class="form-control" name="lon" placeholder="546932">
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


