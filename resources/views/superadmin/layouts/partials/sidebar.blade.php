<div class="deznav" style="background: #259a38">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            <li><a class="ai-icon" href="{{ route('superadmin.dashboards') }}" aria-expanded="false">
                    <i class="flaticon-025-dashboard text-white"></i>
                    <span class="nav-text text-white">Dashboard</span>
                </a>
            </li>
            <li><a class="ai-icon" href="{{ route('superadmin.orders') }}" aria-expanded="false">
                    <i class="flaticon-007-bulleye text-white"></i>
                    <span class="nav-text text-white">Siparişler</span>
                </a>
            </li>
            <li><a class="ai-icon" href="{{ route('superadmin.reports') }}" aria-expanded="false">
                    <i class="flaticon-040-graph text-white"></i>
                    <span class="nav-text text-white">Raporlar</span>
                </a>
            </li>
            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-settings-5 text-white"></i>
                    <span class="nav-text text-white">Entegrasyonlar</span>
                </a>
                <ul aria-expanded="false">
                    <li><a  class="text-white" href="{{ route('superadmin.payment.entegrations') }}">Sanal Pos</a></li>
                </ul>
            </li>
            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-user text-white"></i>
                    <span class="nav-text text-white">Yöneticiler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('superadmin.admin') }}">Yöneticiler</a></li>
                    <li><a class="text-white" href="{{ route('superadmin.admin_create') }}">Yönetici Ekle</a></li>
                </ul>
            </li>
            <li><a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <i class="flaticon-381-user text-white"></i>
                    <span class="nav-text text-white">Partnerler</span>
                </a>
                <ul aria-expanded="false">
                    <li><a class="text-white" href="{{ route('superadmin.dealer') }}">Partnerler</a></li>
                    <li><a class="text-white" href="{{ route('superadmin.dealer_create') }}">Partner Ekle</a></li>
                </ul>
            </li>
        </ul>
        <br>
    </div>
</div>
