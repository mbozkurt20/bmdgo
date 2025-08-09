<div class="nav-header" style="background:  #0d2646">
    <a href="{{ url('/restaurant') }}" class="brand-logo">
        <div class="logo-abbr" style="width: 56px; height:56px">
            <img alt="" src="" style="height: 40px">
        </div>
        <div class="brand-title">
            <img src="{{config('site.logo')}}" style="height: 100px" alt="">
        </div>
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>

<div class="deznav" style="background:  #0d2646">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li><a class="ai-icon text-white" href="{{ url('/restaurant') }}" aria-expanded="false">
                    <i class="flaticon-381-news text-white"></i>
                    <span class="nav-text text-white">Anasayfa</span>
                </a>
            </li>

            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-user-9 text-white"></i>
                    <span class="nav-text text-white">Müşteriler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('restaurant.customers') }}">Müşteriler</a></li>
                </ul>
            </li>

            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-tab text-white"></i>
                    <span class="nav-text text-white">Menüler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('restaurant.categories') }}">Kategoriler</a></li>
                    <li><a class="text-white" href="{{ route('restaurant.products') }}">Ürünler</a></li>
                </ul>
            </li>


            {{-- Siparisler --}}
            <li><a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa-solid fa-utensils text-white"></i>
                    <span class="nav-text text-white">Siparişler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('restaurant.deliveredOrders') }}">Teslim Edilen Siparişler</a></li>
                    <li><a class="text-white" href="{{ route('restaurant.deletedOrders') }}">İptal Edilen Siparişler</a></li>
                </ul>
            </li>

            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-041-graph text-white"></i>
                    <span class="nav-text text-white">Raporlar</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('restaurant.reports.orders') }}">Sipariş Raporları</a></li>
                    <li><a class="text-white" href="{{ route('restaurant.reports.couriers') }}">Kurye Raporları</a></li>
                </ul>
            </li>

            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-settings-5 text-white"></i>
                    <span class="nav-text text-white">Entegrasyonlar</span>
                </a>
                <ul aria-expanded="false">

                    <li><a class="text-white" href="{{ route('restaurant.entegrations') }}">Platformlar</a></li>
                    <li><a  class="text-white" href="{{ route('restaurant.sms.entegrations') }}">Sms</a></li>
                </ul>
            </li>

            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-settings-5 text-white"></i>
                    <span class="nav-text text-white">Ayarlar</span>
                </a>
                <ul aria-expanded="false">
                    <li><a  class="text-white" href="https://download.anydesk.com/AnyDesk.exe">Teknik Destek</a></li>
                </ul>
            </li>

        </ul>
    </div>
</div>
