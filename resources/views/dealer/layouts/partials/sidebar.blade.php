<div class="deznav" style="background:  #0d2646">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li><a class="ai-icon" href="{{ route('dealer.dashboards') }}" aria-expanded="false">
                    <i class="flaticon-025-dashboard text-white"></i>
                    <span class="nav-text text-white">Dashboard</span>
                </a>
            </li>
            <li><a class="ai-icon" href="{{ route('dealer.orders') }}" aria-expanded="false">
                    <i class="flaticon-007-bulleye text-white"></i>
                    <span class="nav-text text-white">Siparişler</span>
                </a>
            </li>
            <li><a class="ai-icon" href="{{ route('dealer.reports') }}" aria-expanded="false">
                    <i class="flaticon-040-graph text-white"></i>
                    <span class="nav-text text-white">Raporlar</span>
                </a>
            </li>
            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-user text-white"></i>
                    <span class="nav-text text-white">Yöneticiler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('dealer.admin') }}">Yöneticiler</a></li>
                    <li><a class="text-white" href="{{ route('dealer.admin_create') }}">Yönetici Ekle</a></li>
                </ul>
            </li>
        </ul>
        <br>
    </div>
</div>
