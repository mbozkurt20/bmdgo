@extends('superadmin.layouts.app')

@section('content')
    <div class="container">
        <h4>PayTR Entegrasyon Ayarları</h4>

        <form method="POST" action="{{ route('superadmin.payment.paytr.update') }}">
            @csrf

            <div class="mb-3">
                <label>Merchant ID</label>
                <input type="text" name="merchant_id"
                       class="form-control"
                       value="{{ old('merchant_id', config('payment.paytr.merchant_id')) }}">
            </div>

            <div class="mb-3">
                <label>Merchant Key</label>
                <input type="text" name="merchant_key"
                       class="form-control"
                       value="{{ old('merchant_key', config('payment.paytr.merchant_key')) }}">
            </div>

            <div class="mb-3">
                <label>Merchant Salt</label>
                <input type="text" name="merchant_salt"
                       class="form-control"
                       value="{{ old('merchant_salt', config('payment.paytr.merchant_salt')) }}">
            </div>

            <div class="mb-3">
                <label>Sandbox</label>
                <select name="sandbox" class="form-control">
                    <option value="0" {{ !config('payment.paytr.sandbox') ? 'selected' : '' }}>
                        Kapalı
                    </option>
                    <option value="1" {{ config('payment.paytr.sandbox') ? 'selected' : '' }}>
                        Açık
                    </option>
                </select>
            </div>

            <button class="btn btn-primary">
                Kaydet
            </button>
        </form>
    </div>
@endsection
