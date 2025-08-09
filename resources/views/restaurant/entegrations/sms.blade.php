@extends('restaurant.layouts.app')
@section('content')
    <style>
        #phone-input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #30d760;
            border-radius: 5px;
            width: 200px;
        }

        #phone-input:invalid {
            border-color: #242323;
        }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Sms Entegrasyon</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Sms Entegrasyon</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Güncelle</a></li>
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
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-header" style="background: #e7004d;color:#fff">
                        <h4 class="card-title text-white">Vatan Sms Bilgileri</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="repeater" action="{{ route('restaurant.sms.entegrations.update') }}">
                            @csrf

                            <div class="basic-form">
                                <div class="row">
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Customer (45***)</label>
                                        <input type="text" class="form-control" name="vatan_sms_customer"
                                               placeholder="Müşteri Giriniz"
                                               value="{{ $restaurant->vatan_sms_customer }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Username (905***5**8**)</label>
                                        <input type="text" class="form-control" name="vatan_sms_username"
                                               placeholder="Kullanıcı Adı Giriniz"
                                               value="{{ $restaurant->vatan_sms_username }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Password</label>
                                        <input type="text" class="form-control" name="vatan_sms_password"
                                               placeholder="Şifre Giriniz"
                                               value="{{ $restaurant->vatan_sms_password }}">
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Orginator (8*0**3*3**)</label>
                                        <input type="text" class="form-control" name="vatan_sms_orginator"
                                               placeholder="Başlatıcı Giriniz"
                                               value="{{ $restaurant->vatan_sms_orginator }}">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="special-button">Bilgileri Güncelle</button>
                        </form>
                    </div>
                </div>

            </div>
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-header" style="background: #e7004d;color:#fff">
                        <h4 class="card-title text-white">Vatan Sms Test</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <div class="row">
                                <p>Sol tarafta <strong>VatanSms</strong> bilgilerinizi giriniz.</p>
                                <p>Doğru olduğunu düşünüyorsanız Telefon No girip test messajı gönderiniz.</p>

                                <p class="bg-danger-light text-danger">Uyarı:: Test mesajı size ulaşmadan sms aktif etmeyiniz!!!</p>
                                <form method="post" action="{{route('restaurant.sms.entegrations.test')}}">
                                    @csrf
                                    <input required name="phone" type="tel" id="phone-input" maxlength="10" placeholder="5xx xxx xx xx">
                                    <button class="special-button" type="submit">Gönder</button>
                                </form>


                                <div class="absolute bottom-0 mb-4">
                                    <p class="size-3 py-2  text-dark fw-bold rounded-xl">
                                        Test mesajımız size iletildi ise Aktif Et diyerek siparişlerinizin kurye teslimini sms doğrulama ile yapabilirsiniz...</p>

                                    <a id="toggle-button"
                                       onclick="DeleteFunction()"
                                       class="{{ \Illuminate\Support\Facades\Auth::guard('restaurant')->user()->is_sms ? 'special-ok-button' : 'special-button' }}"
                                       data-status="{{ \Illuminate\Support\Facades\Auth::guard('restaurant')->user()->is_sms ? 'active' : 'passive' }}">
                                        {{ \Illuminate\Support\Facades\Auth::guard('restaurant')->user()->is_sms ? 'Pasif Et' : 'Aktif Et' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function DeleteFunction() {
            const button = document.getElementById('toggle-button');
            const currentStatus = button.getAttribute('data-status'); // "active" or "passive"
            const willActivate = currentStatus === 'passive';

            const newText = willActivate ? 'Pasif Et' : 'Aktif Et';
            const newClass = willActivate ? 'special-ok-button' : 'special-button';
            const oldClass = willActivate ? 'special-button' : 'special-ok-button';

            Swal.fire({
                title: `${willActivate ? 'Aktif' : 'Pasif'} etmek istediğinizden emin misiniz?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d2646',
                cancelButtonColor: '#3057d7',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet İstiyorum!',
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'GET',
                        url: '/restaurant/update/entegrations/status',
                        success: function (data) {
                            if (data === "OK") {
                                Swal.fire("Güncellendi!", "Güncelleme İşlemi Başarılı.", "success");

                                // 🔄 Butonun text, class ve data-status'unu değiştir
                                button.textContent = newText;
                                button.classList.remove(oldClass);
                                button.classList.add(newClass);
                                button.setAttribute('data-status', willActivate ? 'active' : 'passive');

                            } else {
                                Swal.fire("Uyarı !!", "Üzgünüz, Güncelleme Yapılamadı.", "warning");
                            }
                        },
                        error: function () {
                            Swal.fire("Hata!", "Sunucuya bağlanılamadı.", "error");
                        }
                    });
                }
            });
        }

        const phoneInput = document.getElementById('phone-input');

        phoneInput.addEventListener('input', function (e) {
            // Sadece rakam girilmesine izin ver
            this.value = this.value.replace(/\D/g, '');

            // Max 10 hane olacak şekilde sınırla
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });

        // Form gönderilmeden önce validasyon
        document.querySelector('form').addEventListener('submit', function (e) {
            const phone = phoneInput.value;

            if (!/^5\d{9}$/.test(phone)) {
                alert("Lütfen geçerli bir telefon numarası girin (5453455125 formatında).");
                e.preventDefault();
            }
        });
    </script>
@endsection
