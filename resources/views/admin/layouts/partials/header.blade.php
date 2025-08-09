<div class="nav-header " style="background:  #0d2646">
<a href="{{ url('/admin') }}" class="d-flex justify-content-center align-items-center mt-3">
        <div class="brand-title" style="width: 185px; height:50px">
            <img src="{{ config('site.logo') }}" alt="Logo" style="height: 100px;">
        </div>
    </a>
    <!--div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div-->
</div>

<div class="header">
    @if(env('TEST_MODE') === true)
        <div class="row" style="background-color: #f3eded; color: #e7004d;">
            <div class="text-center fw-bold py-2" >
                <strong>BmdGo</strong> Test Modu Hesabı Kullanmaktasınız.
            </div>
        </div>
    @endif

    <div class="container-fluid py-2 px-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center">

            <!-- Otomatik Kurye Atama -->
            <div class="form-check form-switch me-3">
                @php
                    $admin = \App\Models\Admin::find(auth()->id());
                @endphp

                <label class="form-check-label text-dark fw-bold px-3 mt-3" for="auto_order">Otomatik Kurye Atama</label>
                <input class="form-check-input" type="checkbox" id="auto_order" onclick="AutoOrders()"
                       role="switch" style="height: 40px; width: 80px;"
                       @if($admin->auto_orders == 1) checked @endif>
            </div>

            <!-- Yeni Sipariş Uyarısı -->
            <div class="text-success fw-bold d-none" id="new-order">Yeni Bir Siparişiniz Var !!!</div>

            <p class="{{ \Illuminate\Support\Facades\Auth::guard('admin')->user()->top_up_balance > 0 ? 'special-button ' : 'special-ok-button'}}">
                Kalan Kontör:

                <strong>{{\Illuminate\Support\Facades\Auth::guard('admin')->user()->top_up_balance}}</strong>
            </p>
            <!-- Profil -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                    <img src="/theme/images/avatar.jpg" class="rounded-circle" style="height: 45px; width: 45px;" alt="Avatar">
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-person-circle text-primary me-2"></i> Profil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap
                        </a>
                    </li>
                </ul>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <!-- Ses Uyarısı -->
    <audio id="audioPlayer" class="d-none" controls>
        <source src="{{ asset('upload/arrived.mp3') }}" type="audio/mp3">
        Tarayıcınız ses öğesini desteklemiyor.
    </audio>
</div>
