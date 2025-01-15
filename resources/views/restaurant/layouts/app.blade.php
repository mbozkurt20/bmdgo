<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- PAGE TITLE HERE -->
    <title>{{ Auth::user()->restaurant_name }} Restaurant Paneli - VerGelsin</title>
    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('theme/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/chartist.min.css') }}">
    <link href="{{ asset('theme/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/css/nice-select.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme/css/select2.min.css') }}">
    <!-- Style css -->
    <link href="{{ asset('theme/css/style.css') }}" rel="stylesheet">
    <!-- Add The FontAwesome Icons CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link href="{{ asset('pos/assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ asset('pos/assets/css/ui.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css"
        integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link href="{{ asset('pos/assets/css/OverlayScrollbars.css') }}" type="text/css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    {{-- @if ($_SERVER['REQUEST_URI'] == '/restaurant')
        <script type="text/javascript">
            $(document).ready(function() {
                setTimeout(function() {
                    location.reload();
                }, 60000)
            });
        </script>
    @endif --}}
</head>

<body>



    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="gooey">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>








    <!--*******************
        Preloader end
    ********************-->
    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="{{ url('/restaurant') }}" class="brand-logo">
                <div class="logo-abbr" style="width: 56px; height:56px"> <img src="" style="height: 40px">
                </div>
                <div class="brand-title" style="width: 165px; height:35px"> <img
                        src="https://i.hizliresim.com/erh6qtm.png" style="height: 50px">
                </div>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <a href="{{ route('getOrders') }}"
                                style="font-family: 'Poppins', sans-serif;margin-left:15px;"
                                class="btn btn-primary btn-rounded">
                                {{ Auth::user()->restaurant_name }}
                            </a>


                        </div>


                        {{-- Yeni Siparis Start --}}

                        {{-- Yeni Siparis End --}}

                        <ul class="navbar-nav header-right">
                            <li class="nav-item recipe">

                                {{-- <a target="_blank" href="{{ route('restaurant.orders.new') }}"
                                    style="font-family: 'Poppins', sans-serif;" class="btn btn-secondary btn-rounded"><i
                                        class="fas fa-cash-register"></i> Yeni
                                    Sipariş Ekranı </a> --}}
                                <button type="button" style="font-family: 'Poppins', sans-serif;"
                                    class="btn btn-secondary btn-rounded" data-bs-toggle="modal"
                                    data-bs-target="#Orders">
                                    <i class="fas fa-cash-register"></i> Yeni Sipariş Ekle
                                </button>
                                <!-- TODO : Added Button For Get The Adisyo Siparis-->
                                {{-- <a href="{{ route('getOrders') }}"
                                    style="font-family: 'Poppins', sans-serif;margin-left:15px;"
                                    class="btn btn-warning btn-rounded"><i class="fa-solid fa-cart-shopping"></i>&nbsp
                                    Siparişleri Getir </a> --}}

                            </li>
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link ai-icon" id="notificationOrders" href="javascript:void(0);"
                                    role="button" data-bs-toggle="dropdown">
                                    <svg width="28" height="28" viewbox="0 0 28 28" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M22.75 15.8385V13.0463C22.7471 10.8855 21.9385 8.80353 20.4821 7.20735C19.0258 5.61116 17.0264 4.61555 14.875 4.41516V2.625C14.875 2.39294 14.7828 2.17038 14.6187 2.00628C14.4546 1.84219 14.2321 1.75 14 1.75C13.7679 1.75 13.5454 1.84219 13.3813 2.00628C13.2172 2.17038 13.125 2.39294 13.125 2.625V4.41534C10.9736 4.61572 8.97429 5.61131 7.51794 7.20746C6.06159 8.80361 5.25291 10.8855 5.25 13.0463V15.8383C4.26257 16.0412 3.37529 16.5784 2.73774 17.3593C2.10019 18.1401 1.75134 19.1169 1.75 20.125C1.75076 20.821 2.02757 21.4882 2.51969 21.9803C3.01181 22.4724 3.67904 22.7492 4.375 22.75H9.71346C9.91521 23.738 10.452 24.6259 11.2331 25.2636C12.0142 25.9013 12.9916 26.2497 14 26.2497C15.0084 26.2497 15.9858 25.9013 16.7669 25.2636C17.548 24.6259 18.0848 23.738 18.2865 22.75H23.625C24.321 22.7492 24.9882 22.4724 25.4803 21.9803C25.9724 21.4882 26.2492 20.821 26.25 20.125C26.2486 19.117 25.8998 18.1402 25.2622 17.3594C24.6247 16.5786 23.7374 16.0414 22.75 15.8385ZM7 13.0463C7.00232 11.2113 7.73226 9.45223 9.02974 8.15474C10.3272 6.85726 12.0863 6.12732 13.9212 6.125H14.0788C15.9137 6.12732 17.6728 6.85726 18.9703 8.15474C20.2677 9.45223 20.9977 11.2113 21 13.0463V15.75H7V13.0463ZM14 24.5C13.4589 24.4983 12.9316 24.3292 12.4905 24.0159C12.0493 23.7026 11.716 23.2604 11.5363 22.75H16.4637C16.284 23.2604 15.9507 23.7026 15.5095 24.0159C15.0684 24.3292 14.5411 24.4983 14 24.5ZM23.625 21H4.375C4.14298 20.9999 3.9205 20.9076 3.75644 20.7436C3.59237 20.5795 3.50014 20.357 3.5 20.125C3.50076 19.429 3.77757 18.7618 4.26969 18.2697C4.76181 17.7776 5.42904 17.5008 6.125 17.5H21.875C22.571 17.5008 23.2382 17.7776 23.7303 18.2697C24.2224 18.7618 24.4992 19.429 24.5 20.125C24.4999 20.357 24.4076 20.5795 24.2436 20.7436C24.0795 20.9076 23.857 20.9999 23.625 21Z"
                                            fill="#9B9B9B" />
                                    </svg>
                                    <span class="badge light text-white bg-primary rounded-circle">
                                        1
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3"
                                        style="height:380px;">
                                        <ul class="timeline" id="orderNotifications">


                                        </ul>
                                    </div>
                                    <a class="all-notification" href="javascript:void(0);">Tm Siparişler <i
                                            class="ti-arrow-end"></i></a>
                                </div>
                            </li>

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
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
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
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                            <polyline points="16 17 21 12 16 7" />
                                            <line x1="21" y1="12" x2="9" y2="12" />
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


        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->
        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="deznav">
            <div class="deznav-scroll">
                <ul class="metismenu" id="menu">
                    <li><a class="ai-icon" href="{{ url('/restaurant') }}" aria-expanded="false">
                            <i class="flaticon-381-news"></i>
                            <span class="nav-text">Anasayfa</span>
                        </a>
                    </li>

                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                            <i class="flaticon-381-user-9"></i>
                            <span class="nav-text">Müşteriler</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('restaurant.customers') }}">Müşteriler</a></li>
                        </ul>
                    </li>

                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                            <i class="flaticon-381-tab"></i>
                            <span class="nav-text">Menüler</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('restaurant.categories') }}">Kategoriler</a></li>
                            <li><a href="{{ route('restaurant.products') }}">Ürünler</a></li>
                        </ul>
                    </li>

                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                            <i class="flaticon-041-graph"></i>
                            <span class="nav-text">Kuryeler</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('restaurant.couriers') }}">Kuryeler</a></li>
                            <li><a href="#">Kurye Takip</a></li>

                        </ul>
                    </li>

                    {{-- Siparisler --}}
                    <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">
                            <i class="fa-solid fa-utensils"></i>
                            <span class="nav-text">Siparişler</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('restaurant.deliveredOrders') }}">Teslim Edilen Siparişler</a></li>
                            <li><a href="{{ route('restaurant.deletedOrders') }}">İptal Edilen Siparişler</a></li>
                        </ul>
                    </li>

                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                            <i class="flaticon-041-graph"></i>
                            <span class="nav-text">Raporlar</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('restaurant.reports.orders') }}">Sipariş Raporları</a></li>
                            <li><a href="{{ route('restaurant.reports.couriers') }}">Kurye Raporları</a></li>
                        </ul>
                    </li>

                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                            <i class="flaticon-381-settings-5"></i>
                            <span class="nav-text">Ayarlar</span>
                        </a>
                        <ul aria-expanded="false">

                            <li><a href="{{ route('restaurant.entegrations') }}">Entegrasyonlar</a></li>
                            <li><a href="https://VerGelsin.com.tr/download/printer.exe">Printer IO</a></li>
                            <li><a href="https://download.anydesk.com/AnyDesk.exe">Teknik Destek</a></li>
                        </ul>
                    </li>

                </ul>

                <div class="copyright">
                    <p>
                        <strong>VerGelsin</strong> 2024.
                    </p>
                </div>
            </div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
            @yield('content')
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>
                    Copyright {{ date('Y') }} VerGelsin


                </p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
    </div>


    <audio autoplay="true" src="{{ url('pos/audio/new_beep.mp3') }}" muted></audio>

    <script src="{{ asset('theme/js/global.min.js') }}"></script>
    <script src="{{ asset('theme/js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('theme/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('theme/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/js/datatables.init.js') }}"></script>
    <script src="{{ asset('theme/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('theme/js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('theme/js/form-repeater.int.js') }}"></script>
    <!-- Chart piety plugin files -->
    <script src="{{ asset('theme/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('theme/js/select2-init.js') }}"></script>
    <!-- Dashboard 1 -->
    <script src="{{ asset('theme/js/dashboard-1.js') }}"></script>
    <script src="{{ asset('theme/js/custom.min.js') }}"></script>
    <script src="{{ asset('theme/js/deznav-init.js') }}"></script>

    {{-- <script src="{{ asset('pos/assets/js/jquery-2.0.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('pos/assets/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('pos/assets/js/OverlayScrollbars.js') }}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://kuryenkapinda.com/theme/js/sweetalert2.all.min.js"></script>
--}}
    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>

</body>

</html>
