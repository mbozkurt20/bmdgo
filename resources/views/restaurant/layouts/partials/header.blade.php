<div class="header">
    @if(env('TEST_MODE') === true)
        <div class="row relative" style="background-color: #f3eded; color: #e7004d;">
            <div class="text-lg absolute text-center fw-bold py-2" >
                <strong>BmdGo</strong> Test Modu Hesabı Kullanmaktasınız !!
            </div>
        </div>
    @endif


    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <!--a href="{{ route('getOrders') }}"
                       style="font-family: 'Poppins', sans-serif;margin-left:15px;"
                       class="btn btn-primary btn-rounded">
                        {{ Auth::user()->restaurant_name }}
                    </a-->
                </div>

                <ul class="navbar-nav header-right">
                    <li class="nav-item recipe">
                        <button id="openModalBtn" class="special-button ">
                            Hızlı Sipariş
                        </button>
                    </li>


                    @include('restaurant.layouts.partials.quick_order_modal')


                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button"
                           data-bs-toggle="dropdown">
                            <img src="https://cdn-icons-png.flaticon.com/512/433/433087.png">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="https://download.anydesk.com/AnyDesk.exe" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary"
                                     width="18" height="18" viewbox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span class="ms-2">Teknik Destek</span>
                            </a>

                            <a class="dropdown-item ai-icon" href="{{ route('restaurant.logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger"
                                     width="18" height="18" viewbox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                <span class="ms-2">Çıkış Yap </span>
                            </a>

                            <form id="logout-form" action="{{ route('restaurant.logout') }}" method="POST"
                                  class="d-none">
                                @csrf
                            </form>

                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
