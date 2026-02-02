@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Giderler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Giderler</a></li>
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
                        <h4 class="card-title">Gider Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('expenses.update',$expense->id)}}">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Gider Başlığı</label>
                                        <input value="{{$expense->title}}" required type="text" class="form-control" name="title" placeholder="Gider Başlığı">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Tutar</label>
                                        <input value="{{$expense->amount}}" required type="number" class="form-control" name="amount" placeholder="Gider Tutarı">
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ödeme Tarihi</label>
                                        <input value="{{$expense->date}}" required type="datetime-local" class="form-control" name="date" >
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Ödeme Method</label>
                                        <select required class="form-control" name="payment_method" id="">
                                            <option {{$expense->payment_method == 'Nakit' ? 'selected' : null}} value="Nakit">Nakit</option>
                                            <option {{$expense->payment_method == 'Kredi Kartı' ? 'selected' : null}} value="Kredi Kartı">Kredi Kartı</option>
                                            <option {{$expense->payment_method == 'Eft/Havale' ? 'selected' : null}} value="Eft/Havale">Eft/Havale</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Gider Türü</label>
                                        <select required class="form-control" name="expense_type" id="">
                                            <option {{$expense->expense_type == 'Kira' ? 'selected' : null}} value="Kira">Kira</option>
                                            <option {{$expense->expense_type == 'Fatura' ? 'selected' : null}} selected value="Fatura">Fatura</option>
                                            <option {{$expense->expense_type == 'Personel' ? 'selected' : null}} value="Personel">Personel</option>
                                            <option {{$expense->expense_type == 'Malzeme' ? 'selected' : null}} value="Malzeme">Malzeme</option>
                                            <option {{$expense->expense_type == 'Diğer' ? 'selected' : null}} value="Diğer">Diğer</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Açıklama (opsiyonel)</label>
                                        <textarea class="form-control" name="description" placeholder="Eklemek istediğiniz detaylı açıklama">{{$expense->description}}</textarea>
                                    </div>
                                </div>

                                <button type="submit" class="special-button float-end mt-4">Kaydı Güncelle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


