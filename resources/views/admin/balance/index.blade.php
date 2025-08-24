@extends('admin.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/reports/index.css')}}">

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kontör Bakiyesi</h2>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="card card-body " >
                <div class="" style="max-width: 35vw">
                    <p class="fw-bold size-2 text-dark">Merhaba, {{\Illuminate\Support\Facades\Auth::guard('admin')->user()->name}}</p>

                    <p class="size-2 special-button mb-5 rounded-lg">Güncel Kontör Bakiyeniz: {{\Illuminate\Support\Facades\Auth::guard('admin')->user()->top_up_balance}}</p>

                    <p class="text-primary">
                        <strong> Bilgilendirme:: </strong> Kontör bakiyesini buradan ve üst alandan kontrol edebilirsiniz.
                    </p>

                    <p class="text-danger">
                        <strong>Dikkat:: Kontör bakiyeniz yetersiz olması durumunda siparişleriniz eklenmeyecektir, bu durumdan {{env('APP_NAME')}} olarak sorumluluk almadığımızı belirtmek isteriz.</strong>
                    </p>

                    <p class="text-success">
                        <strong> Satın Alım:: </strong> Paketlerimizi <a class="text-primary" style="text-decoration: underline" href="https://bmdgo.com/fiyat/">BmdGo </a>  adresimizden inceleyebilirsiniz.
                    </p>
                </div>

                {{--
                  <div class="border border-dark p-3 rounded-2" style="max-width: 25%">
                    <form method="GET" action="{{ route('payment.form') }}">
                        @csrf

                        <!-- Kullanıcıya tanımlı kontör fiyatı -->
                        <input type="hidden" id="top_up_price" value="{{ auth()->user()->top_up_price }}">

                        <div class="mb-3">
                            <label class="fw-bold text-black">Kontör Fiyatı</label>
                            <div class="form-control bg-secondary text-white fw-bold">
                                1 Kontör = <span class="text-white fw-bold">{{ auth()->user()->top_up_price }} ₺</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold text-black">Kontör Adet</label>
                            <input required class="form-control" type="number" min="1" placeholder="Adet giriniz" id="top_up" name="top_up">
                        </div>

                        <!-- Anlık hesaplanan tutar -->
                        <div class="alert alert-info" id="hesaplamaBox" style="display: none;">
                            <span class="fw-bold">Ödemeniz Gereken Tutar:</span>
                            <span id="toplamTutar" class="text-success"></span> ₺
                        </div>

                        <!-- Butonlar -->
                        <div class="d-flex gap-2">
                            <button class="special-button" style="display: none" type="button" id="hesaplaBtn">Hesapla</button>
                            <button class="special-button" type="submit">Ödeme Yap</button>
                        </div>
                    </form>
                </div>
                --}}
            </div>
        </div>

    </div>

    <script>
        const axios = require('axios');

        async function sendWhatsAppMessage() {
            const token = "BURAYA_META_ACCESS_TOKEN";
            const phone_number_id = "08503469503";

            await axios.post(`https://graph.facebook.com/v17.0/${phone_number_id}/messages`, {
                messaging_product: "whatsapp",
                to: "905xxxxxxxxx", // Numaranız (ülke kodu ile)
                type: "text",
                text: { body: "Merhaba! Bu bir test mesajıdır." }
            }, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            console.log("Mesaj gönderildi!");
        }
    </script>

    <script>
        const topUpInput = document.getElementById("top_up");
        const fiyat = parseFloat(document.getElementById("top_up_price").value);
        const hesaplamaBox = document.getElementById("hesaplamaBox");
        const toplamTutar = document.getElementById("toplamTutar");
        const hesaplaBtn = document.getElementById("hesaplaBtn");

        function hesapla() {
            let adet = parseInt(topUpInput.value);
            let toplam = adet * fiyat;

            if (!isNaN(toplam) && adet > 0) {
                toplamTutar.innerText = toplam.toFixed(2);
                hesaplamaBox.style.display = "block";
            } else {
                hesaplamaBox.style.display = "none";
            }
        }

        // Hesapla butonu ile
        hesaplaBtn.addEventListener("click", hesapla);

        // Kullanıcı yazdıkça otomatik hesapla
        topUpInput.addEventListener("input", hesapla);
    </script>
@endsection


