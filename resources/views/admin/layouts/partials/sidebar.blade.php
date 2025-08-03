<div class="deznav" style="background:  #0d2646">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li><a class="ai-icon" href="{{ url('/admin') }}" aria-expanded="false">
                    <i class="flaticon-025-dashboard text-white"></i>
                    <span class="nav-text text-white">Anasayfa</span>
                </a>
            </li>
            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-048-home text-white"></i>
                    <span class="nav-text text-white">Restaurantlar</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('admin.restaurants') }}">Restaurantlar</a></li>
                </ul>
            </li>

            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-user text-white"></i>
                    <span class="nav-text text-white">Kuryeler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('admin.couriers') }}">Kuryeler</a></li>
                    <li><a class="text-white" href="{{ route('admin.couriers.maps') }}">Kurye Takip</a></li>
                    <li><a class="text-white" href="{{ route('admin.couriers.new') }}"> Kurye Ekle</a></li>
                </ul>
            </li>

            <li><a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                    <i class="fa-solid fa-utensils text-white"></i>
                    <span class="nav-text text-white">Siparişler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('admin.deliveredOrders') }}">Teslim Edilen Siparişler</a></li>
                    <li><a class="text-white" href="{{ route('admin.deletedOrders') }}">İptal Edilen Siparişler</a></li>
                </ul>
            </li>

            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-040-graph text-white"></i>
                    <span class="nav-text text-white">Hakedişler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('admin.progress_payment.restaurant') }}">Restorant Hakediş</a></li>
                    <li><a class="text-white" href="{{ route('admin.progress_payment.courier') }}">Kurye Hakediş</a></li>
                </ul>
            </li>

            <li><a href="{{ route('admin.reports') }}" class="ai-icon" aria-expanded="false">
                    <i class="flaticon-041-graph text-white"></i>
                    <span class="nav-text text-white">Raporlar</span>
                </a>
            </li>
        </ul>
        <br>
    </div>
</div>
