<!DOCTYPE html>
<html lang="tr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content>
    <meta name="author" content>
    <meta name="robots" content>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="ParsKurye :  Admin Ekranı">
    <meta property="og:title" content="ParsKurye :  Admin Ekranı">
    <meta property="og:description" content="ParsKurye :  Admin Ekranı">
    <meta property="og:image" content="images/social-image.png">
    <meta name="format-detection" content="telephone=no">
    <!-- PAGE TITLE HERE -->
    <title>Admin - ParsKurye</title>
    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('theme/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/chartist.min.css') }}">
    <link href="{{ asset('theme/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme/css/nice-select.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme/css/select2.min.css') }}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <!-- Style css -->
    <link href="{{ asset('theme/css/style.css') }}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add The FontAwesome Icons CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    @if ($_SERVER['REQUEST_URI'] == '/admin')
        <script type="text/javascript">
            $(document).ready(function() {
                setTimeout(function() {
                    location.reload();
                }, 60000)
            });
        </script>
    @endif

    <script>
        window.onload = function() {
            var audio = document.getElementById('audioPlayer');

            window.playAudio = function() {
                audio.play();
            };

            window.pauseAudio = function() {
                audio.pause();
            };
        }

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('a293a751fd7a10f5a071', {
            cluster: 'mt1'
        });


        var channel = pusher.subscribe('new-order-channel');
        channel.bind('new-order-event', function(data) {
            var newOrderMessage = document.getElementById('new-order');
            newOrderMessage.style.display = 'block';

            setTimeout(function() {
                newOrderMessage.style.display = 'none';
            }, 3000);

            playAudio()
            console.log({da: data})
        });
    </script>

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
                <div class="brand-title" style="width: 165px; height:35px"> <img
                        src="https://i.hizliresim.com/oefzi6j.png" style="height: 50px"> </div>
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
                            <h5 id="new-order" style="display: none" class="text-success">Yeni Bir Sipariş Geldi!!</h5>

                            <audio style="display: none" id="audioPlayer" controls>
                                <source src="{{asset('upload/brass-new-level-151765.mp3')}}" type="audio/mp3">
                            </audio>

                            <div class="nav-item">
                                <div class="input-group search-area">
                                    <div class="form-check form-switch">
                                        @php
                                            $admin = \App\Models\Admin::find(1);

                                        @endphp

                                    </div>
                                </div>
                            </div>
                        </div>

                        <ul class="navbar-nav header-right">


                            </li>
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link ai-icon" href="javascript:void(0);" role="button"
                                    data-bs-toggle="dropdown">
                                    <svg width="28" height="28" viewbox="0 0 28 28" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M22.75 15.8385V13.0463C22.7471 10.8855 21.9385 8.80353 20.4821 7.20735C19.0258 5.61116 17.0264 4.61555 14.875 4.41516V2.625C14.875 2.39294 14.7828 2.17038 14.6187 2.00628C14.4546 1.84219 14.2321 1.75 14 1.75C13.7679 1.75 13.5454 1.84219 13.3813 2.00628C13.2172 2.17038 13.125 2.39294 13.125 2.625V4.41534C10.9736 4.61572 8.97429 5.61131 7.51794 7.20746C6.06159 8.80361 5.25291 10.8855 5.25 13.0463V15.8383C4.26257 16.0412 3.37529 16.5784 2.73774 17.3593C2.10019 18.1401 1.75134 19.1169 1.75 20.125C1.75076 20.821 2.02757 21.4882 2.51969 21.9803C3.01181 22.4724 3.67904 22.7492 4.375 22.75H9.71346C9.91521 23.738 10.452 24.6259 11.2331 25.2636C12.0142 25.9013 12.9916 26.2497 14 26.2497C15.0084 26.2497 15.9858 25.9013 16.7669 25.2636C17.548 24.6259 18.0848 23.738 18.2865 22.75H23.625C24.321 22.7492 24.9882 22.4724 25.4803 21.9803C25.9724 21.4882 26.2492 20.821 26.25 20.125C26.2486 19.117 25.8998 18.1402 25.2622 17.3594C24.6247 16.5786 23.7374 16.0414 22.75 15.8385ZM7 13.0463C7.00232 11.2113 7.73226 9.45223 9.02974 8.15474C10.3272 6.85726 12.0863 6.12732 13.9212 6.125H14.0788C15.9137 6.12732 17.6728 6.85726 18.9703 8.15474C20.2677 9.45223 20.9977 11.2113 21 13.0463V15.75H7V13.0463ZM14 24.5C13.4589 24.4983 12.9316 24.3292 12.4905 24.0159C12.0493 23.7026 11.716 23.2604 11.5363 22.75H16.4637C16.284 23.2604 15.9507 23.7026 15.5095 24.0159C15.0684 24.3292 14.5411 24.4983 14 24.5ZM23.625 21H4.375C4.14298 20.9999 3.9205 20.9076 3.75644 20.7436C3.59237 20.5795 3.50014 20.357 3.5 20.125C3.50076 19.429 3.77757 18.7618 4.26969 18.2697C4.76181 17.7776 5.42904 17.5008 6.125 17.5H21.875C22.571 17.5008 23.2382 17.7776 23.7303 18.2697C24.2224 18.7618 24.4992 19.429 24.5 20.125C24.4999 20.357 24.4076 20.5795 24.2436 20.7436C24.0795 20.9076 23.857 20.9999 23.625 21Z"
                                            fill="#9B9B9B" />
                                    </svg>
                                    <span class="badge light text-white bg-primary rounded-circle" id="totalOrders">
                                        @php
                                            $totalOrder = count(\App\Models\Order::where('courier_id', -1)->get());
                                            $totalAdd = count(\App\Models\CourierOrder::all());
                                            $totals = $totalOrder - $totalAdd;
                                            echo $totals;
                                        @endphp
                                    </span>
                                </a>
                            </li>

                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    <img src="https://cdn-icons-png.flaticon.com/512/2942/2942813.png" width="56"
                                        alt>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="#" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary"
                                            width="18" height="18" viewbox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        <span class="ms-2">Profil </span>
                                    </a>

                                    <a class="dropdown-item ai-icon" href="{{ route('superadmin.logout') }}"
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

                                    <form id="logout-form" action="{{ route('superadmin.logout') }}" method="POST"
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
                    <li><a class="ai-icon" href="{{ route('superadmin.dashboards') }}" aria-expanded="false">
                            <i class="flaticon-025-dashboard"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li><a class="ai-icon" href="{{ route('superadmin.orders') }}" aria-expanded="false">
                        <i class="flaticon-007-bulleye"></i>
                        <span class="nav-text">Siparişler</span>
                    </a>
                </li>
                <li><a class="ai-icon" href="{{ route('superadmin.reports') }}" aria-expanded="false">
                    <i class="flaticon-040-graph"></i>
                    <span class="nav-text">Raporlar</span>
                </a>
            </li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                            <i class="flaticon-381-user"></i>
                            <span class="nav-text">Bayiler</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="{{ route('superadmin.dealer') }}">Bayiler</a></li>
                            <li><a href="{{ route('superadmin.dealer_create') }}">Bayi Ekle</a></li>
                        </ul>
                    </li>

                </ul>
                <br>

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








        <div class="footer text-center">

            <p>
                Copyright © Developed by <a href="#" target="_blank">ParsKurye</a> 2024
            </p>

        </div>
        <!--**********************************
            Footer end
        ***********************************-->
        <!--**********************************
           Support ticket button start
        ***********************************-->
        <!--**********************************
           Support ticket button end
        ***********************************-->
    </div>



    <!--**********************************
        Main wrapper end
    ***********************************-->
    <!--**********************************
        Scripts
    ***********************************-->

    <!-- Required vendors -->
    <script src="{{ asset('theme/js/global.min.js') }}"></script>
    <script src="{{ asset('theme/js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('theme/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('theme/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('theme/js/datatables.init.js') }}"></script>
    <script src="{{ asset('theme/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('theme/js/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('theme/js/form-repeater.int.js') }}"></script>
    <script src="{{ asset('theme/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('theme/js/select2-init.js') }}"></script>
    <!-- Chart piety plugin files -->
    <!-- Dashboard 1 -->
    <script src="{{ asset('theme/js/dashboard-1.js') }}"></script>
    <script src="{{ asset('theme/js/custom.min.js') }}"></script>
    <script src="{{ asset('theme/js/deznav-init.js') }}"></script>


    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>

    <script type="text/javascript">
        const socket = io('https://kuryenkapinda.com:5000');

        socket.on('orderCourierNotification', (data) => {

            $('#totalOrders').html(data.total);

            const bildirim = document.getElementById('orderNotice').checked;
            if (bildirim === true) {

                let src = '{{ url('pos/audio/new_beep.mp3') }}';
                let audio = new Audio(src);
                audio.play();

            }

        });


        function AutoOrders(e) {

            const durum = document.getElementById('auto_order').checked;
            if (durum === true) {
                var status = 1;
            } else {
                var status = 0;
            }
            console.log(status)
            $.ajax({
                type: 'GET', //THIS NEEDS TO BE GET
                url: '/admin/setting/auto_order/' + status,
                success: function(data) {
                    if (data == "OK") {
                        Swal.fire('Otomatik Sipariş Aktif!!');
                    }
                    if (data == "ERR") {
                        Swal.fire('Otomatik Sipari Kapal!!');
                    }

                },
                error: function() {
                    console.log(data);
                }
            });
        }
    </script>


</body>

</html>
