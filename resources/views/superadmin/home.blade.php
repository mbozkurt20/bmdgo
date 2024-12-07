@extends('superadmin.layouts.app')

@section('content')
    <style>
        th {
            font-weight: bold;
        }

        .orderTitle {
            font-size: 14px;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
        }

        .orderProde {
            font-size: 13px;
            font-weight: 500;
        }

        /* Courier Style */
        .courier-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .courier-item {
            flex: 1 1 200px;
            min-width: 200px;
        }

        .courier-box {
            padding: 15px;
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .courier-box p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-assign {
            padding: 5px 15px;
            font-size: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-assign:hover {
            background-color: #218838;
        }

        .courier-box {
            position: relative;
        }

        .courier-box::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 10px;
            border-top-right-radius: 2px;
            border-bottom-right-radius: 2px;
        }

        .red-box::after {
            background-color: #ff0a0a;
        }

        .purple-box::after {
            background-color: #624FD1;
        }

        .avatar {
            vertical-align: middle;
            width: 35px;
            height: 35px;
            border-radius: 0%;
        }

        .bg-default,
        .btn-default {
            background-color: #f2f3f8;
        }

        .btn-error {
            color: #ef5f5f;
        }

        .tabs {
            display: flex;
            flex-wrap: wrap; // make sure it wraps
        }

        .tabs label {
            order: 1;
            display: block;
            padding: 1rem 2rem;
            margin-right: 0.2rem;
            cursor: pointer;
            background: #5c5c5c;
            font-weight: bold;
            transition: background ease 0.2s;
            color: #fff;
        }

        .tabs .tab {
            order: 99;
            flex-grow: 1;
            width: 100%;
            display: none;
            padding: 1rem;
            background: #fff;
        }

        .tabs input[type="radio"] {
            display: none;
        }

        .tabs input[type="radio"]:checked+label {
            background: #edeff1;
            color: #000;
        }

        .tabs input[type="radio"]:checked+label+.tab {
            display: block;
        }

        @media (max-width: 45em) {

            .tabs .tab,
            .tabs label {
                order: initial;
            }

            .tabs label {
                width: 100%;
                margin-right: 0;
                margin-top: 0.8rem;
            }
        }

        .toplusil a i:hover {
            color: red;
        }

        .paymentRol {
            padding: 12px;
            font-size: 18px;
            color: #fff;
            text-align: center;
            font-weight: bold;
            height: 80px;
            border-radius: 10px;
            margin: 0px 2px;
            cursor: pointer;
        }

        .nakit {
            background: #008002;
        }

        .kkkarti {
            background: #f72b50;
        }

        .kkarti {
            background: #624FD1;
        }

        .kayit {
            background: #fd673e;
            padding: 25px;
            font-size: 22px;
        }

        .customer {
            padding: 0px 10px;
            border-left: 3px solid #5c5c5c;
            width: 100%;
            border-radius: 10px;
            border-right: 3px solid #5c5c5c;
            text-align: left;
        }

        .rightbtn {
            padding: 0 !important;
        }

        .rightbtn a {
            padding: 10px 15px;
            height: 50px;
            font-size: 18px;
            color: #fff !important;
            background: #5c5c5c;
        }

        .selectiki {
            height: 50px !important;
            padding: 10px;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            line-height: 40px;
        }

        .select2-container--default .select2-results__option--selected {
            background-color: #e7e7e7;
            line-height: 40px;
        }

        .select2-results__option--selectable {
            cursor: pointer;
            line-height: 40px;
        }


        #loader {
            position: absolute;
            padding: 0;
            width: 100%;
            height: 100vh;
            text-align: center;
            background: #fff;
            z-index: 999;
        }

        #loader2 {
            position: absolute;
            top: 50%;
            display: table-cell;
            vertical-align: middle;
        }

        #loader img {
            position: relative;
            top: 50%;
        }

        .select-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0;
            padding: 10px;
            background-color: #fff;
            border-radius: 10px;
            margin-left: 200px;
            margin-right: 25px;
        }

        .custom-select {
            width: 100%;
            padding: 14px 16px;
            font-size: 12px;
            font-weight: 500;
            color: #495057 !important;
            border: 2px solid #ced4da;
            border-radius: 8px;
            background-color: #ffffff;
            transition: border-color 0.3s ease, background-color 0.3s ease;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII, %3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"%3E%3Cpath fill="%23999" d="M2 0L0 2h4z"/%3E%3C/svg%3E') !important;
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 12px;
        }

        .custom-select:focus {
            border-color: #80bdff !important;
            background-color: #f0f8ff !important;
            outline: none !important;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5) !important;
        }

        .custom-select:hover {
            border-color: #5a6268;
            background-color: #e9ecef;
        }

        .custom-select option {
            padding: 10px;
            font-size: 16px;
            font-weight: 500;
        }

        .custom-select option:hover {
            background-color: #007bff !important;
            color: white;
        }

        .custom-select option:checked {
            background-color: #007bff !important;
            color: white;
        }

        .select-container::after {
            content: '⏷';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #999;
            pointer-events: none;
        }

        .order-card {
            border-radius: 10px;
            color: white;
            padding: 5px;
            text-align: center;
            border: none;
        }

        .order-card span {
            font-size: 1rem;
        }

        .order-btn {
            font-size: 0.9rem;
        }

        .orders-section,
        .performance-section {
            background-color: #efefef;
            padding: 20px;
            border-radius: 10px;
        }

        .order-number {
            font-size: 1.5rem;
            font-weight: bold;
        }



        .btn-group-custom {
            border-radius: 10px;
            padding: 10px 20px;
        }

        .date-filter span {
            font-size: 14px;
        }

        .date-filter:hover {
            padding: 1.5px 10px;
            background-color: #FD683E;
            border: #fff;
            border-radius: 15px;
            transition: .5s all;
        }

        .date-filter:hover span {
            color: #fff !important;
            padding-left: 5px;
        }

        .date-filter:hover .fas {
            color: #fff !important;
            transition: .8s all;
        }

        #modal-header-newOrde {
            padding: 0.2rem 1.5rem !important;
        }

        .custom-input {
            border-radius: 12px;
            padding: 8px 12px;
            font-size: .8rem;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
            width: 200px;
            background-color: #f9f9f9;
        }

        .custom-input:focus {
            border-color: #fd683e;
            box-shadow: 0 0 8px rgba(253, 104, 62, 0.4);
            background-color: white;
        }

        .custom-input::placeholder {
            color: #b0b0b0;
            font-style: italic;
        }

        .form-label {
            font-weight: 400;
            color: #555;
        }

        .custom-btn:hover {
            background: #fff;
            color: #fd683e;
            box-shadow: 0 4px 12px rgba(253, 104, 62, 0.3);
            transition: .8s all;
        }

        .custom-link {
            display: flex;
            align-items: center;
            color: #000;
            font-weight: 600;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
            transition: .8s all;
        }

        .custom-link i {
            margin-right: 8px;
            color: #FD683E;
        }

        .custom-link:hover {
            padding: 10px 15px;
            background-color: #FD683E;
            color: white;
            box-shadow: 0 4px 8px rgba(253, 104, 62, 0.3);
        }

        .date-filters {
            margin-left: 20px;
        }

        .custom-dropdown {
            position: relative;
            width: 100%;
        }

        .select-box {
            background-color: #fff;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .select-box::after {
            content: "▼";
            font-size: 12px;
            color: #333;
        }

        .options-container {
            display: none;
            position: absolute;
            top: 50px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            z-index: 10;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .option {
            padding: 12px;
            font-size: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            color: #333;
        }

        .option:hover {
            background-color: #f0f0f0;
        }

        .color-circle {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .option[data-type="restaurant"] .color-circle {
            background-color: #00bcd4;
        }

        .option[data-type="general"] .color-circle {
            background-color: #ff9800;
        }

        th {
            font-weight: bold;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="orders-section" style="margin-bottom: 10px">
                    <form method="GET" action="{{ route('superadmin.filterByDate') }}"
                          class="d-flex  gap-3 align-items-center">
                        <div>
                            <span class="mb-1 text-dark">{{isset($startDate) ? date('d-m-Y H:i',strtotime($startDate)):null}}</span>
                            <input  type="date" class="form-control custom-input" id="start_date" name="start_date"
                                   required>
                        </div>
                        <div>
                            <span class="mb-2 text-dark">  {{isset($endDate) ? date('d-m-Y H:i',strtotime($endDate)):null}}</span>
                            <input type="date" class="form-control custom-input" id="end_date" name="end_date" required>
                        </div>
                        <div class="d-flex align-items-end">
                            <button style="background: #fd683e;color:#fff;font-size: 0.8rem" type="submit"
                                    class="btn custom-btn">
                                <i class="fas fa-calendar-day" style="padding-right:5px"></i>
                                Filtrele</button>
                        </div>
                    </form>

                    <h4 class="mt-4 mb-2">Siparişler</h4>
                    <div class="row g-3">
                        <!-- All Orders Button -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom bg-warning order-btn d-flex justify-content-between align-items-center w-100">

                                <span>
                                    <i class="fa-solid fa-box" style="color: #fff;font-size:18px;padding-right:10px"></i>
                                    Tüm Siparişler</span>
                                <span class="badge bg-light text-dark order-number">{{ count($tumu) }}</span>
                            </button>
                        </div>
                        <!-- Getir Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom bg-secondary order-btn d-flex justify-content-between align-items-center w-100">
                                <img src="{{ asset('theme/images/GetirYemek_Logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="77px"
                                     height="14px" alt="">
                                <span>Getir Siparişleri</span>
                                <span class="badge bg-light text-dark order-number">{{ count($getiryemek) }}</span>
                            </button>
                        </div>
                        <!-- Trendyol Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom bg-primary order-btn d-flex justify-content-between align-items-center w-100">
                                <img src="{{ asset('theme/images/trendyolyemek.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="71px"
                                     height="14px" alt="">
                                <span>Trendyol Siparişleri</span>
                                <span class="badge bg-light text-dark order-number">{{ count($trendyol) }}</span>
                            </button>
                        </div>

                        <!-- Yemeksepeti Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #F90050">
                                <img src="{{ asset('theme/images/Yemeksepeti_Logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="79px"
                                     height="15px" alt="">
                                <span>Yemeksepeti Siparişleri</span>
                                <span class="badge bg-light text-dark order-number">{{ count($yemeksepeti) }}</span>
                            </button>
                        </div>
                        <!-- Migros Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #363A86">
                                <img src="{{ asset('theme/images/MigrosYemek_logo.png') }}"
                                     style="background-repeat: no-repeat; background-position:center" width="69px"
                                     height="28px" alt="">
                                <span>Migros Yemek Siparişleri</span>
                                <span class="badge bg-light text-dark order-number">{{ $migros }}</span>
                            </button>
                        </div>
                        <!-- Phone Orders -->
                        <div class="col-md-6">
                            <button
                                class="order-card btn-group-custom order-btn d-flex justify-content-between align-items-center w-100"
                                style="background: #1da713">
                                <i class="fa-solid fa-phone" style="color: #fff;font-size:18px;padding-left:10px"></i>
                                <span>Telefonla Gelen Sipariş</span>
                                <span class="badge bg-light text-dark order-number">{{ count($telefonsiparis) }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div id="loading"></div>

                    <div class="card-body">
                        <form id="dashboardForm">
                            <div class="row">
                                <div class="col">
                                    <label for="">Başlangıç Tarihi</label>
                                    <input type="date" class="form-control" name="start_date" value="{{date("Y-m-d")}}">
                                </div>
                                <div class="col">
                                    <label for="">Bitiş Tarihi</label>
                                    <input type="date" class="form-control" name="end_date" value="{{date("Y-m-d")}}">
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Bayi Durumları</h2>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Sipariş Sağlayıcı</h2>
                    </div>
                    <div class="card-body">
                        <canvas id="chart-2"></canvas>
                    </div>
                </div>

            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Sipariş Durumları</h2>
                    </div>
                    <div class="card-body">
                        <canvas id="chart-3"></canvas>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="preccess-loading">

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/peity/3.2.1/jquery.peity.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Grafikler için global değişkenler
            let myChart, chart2, chart3;

            function capitalizeFirstLetter(val) {
                return String(val).charAt(0).toUpperCase() + String(val).slice(1).toLowerCase();
            }
            let bgColors = [
                'rgb(255, 99, 132)', // Kırmızı ton
                'rgb(54, 162, 235)', // Mavi ton
                'rgb(255, 205, 86)', // Sarı ton
                'rgb(75, 192, 192)', // Yeşil ton
                'rgb(153, 102, 255)', // Mor ton
                'rgb(255, 159, 64)', // Turuncu ton
                'rgb(201, 203, 207)', // Gri ton
                'rgb(105, 129, 239)', // Açık mavi ton
                'rgb(255, 99, 71)', // Koyu kırmızı ton
                'rgb(0, 204, 102)' // Açık yeşil ton
            ];
            // Grafiklerin oluşturulması
            function initializeCharts(dealerLabels, dealerData, providerLabels, providerData, orderStatusLabels,
                orderStatusData) {
                const ctx = document.getElementById('myChart');
                const chart2Ctx = document.getElementById('chart-2');
                const chart3Ctx = document.getElementById('chart-3');

                // Gelen veri ile grafikleri oluştur
                myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: dealerLabels,
                        datasets: [{
                            label: '# Toplam Sipariş',
                            data: dealerData,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                chart2 = new Chart(chart2Ctx, {
                    type: 'doughnut',
                    data: {
                        labels: providerLabels,
                        datasets: [{
                            label: 'Sipariş Sağlayıcı',
                            data: providerData,
                            backgroundColor: bgColors,
                            hoverOffset: 4
                        }]
                    }
                });

                chart3 = new Chart(chart3Ctx, {
                    type: 'doughnut',
                    data: {
                        labels: orderStatusLabels,
                        datasets: [{
                            label: 'Sipariş Durumları',
                            data: orderStatusData,
                            backgroundColor: bgColors,
                            hoverOffset: 4
                        }]
                    }
                });
            }

            // Grafiklerin verilerini güncelleme ve yeniden çizdirme fonksiyonu
            function updateCharts(dealerLabels, dealerData, providerLabels, providerData, orderStatusLabels,
                orderStatusData) {
                myChart.data.labels = dealerLabels;
                myChart.data.datasets[0].data = dealerData;

                chart2.data.labels = providerLabels;
                chart2.data.datasets[0].data = providerData;

                chart3.data.labels = orderStatusLabels;
                chart3.data.datasets[0].data = orderStatusData;

                myChart.update();
                chart2.update();
                chart3.update();
            }

            function fetchDashboardData() {
                var formData = $("#dashboardForm").serialize(); // Form verilerini al

                $.ajax({
                    url: '{{ route('superadmin.dashboards.get_data') }}', // Sunucudaki veri endpoint'i
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        // Grafikler için veriyi doğrudan düzenle
                        const dealerLabels = response.dealerStatus.map(item => item.title);
                        const dealerData = response.dealerStatus.map(item => item.orderCount);

                        const providerLabels = response.orderProvider.map(item => capitalizeFirstLetter(
                            item.platform));
                        const providerData = response.orderProvider.map(item => item.order_count);

                        const orderStatusLabels = response.orderStatus.map(item =>
                            capitalizeFirstLetter(item.status));
                        const orderStatusData = response.orderStatus.map(item => item.order_count);

                        // Grafikleri başlat veya güncelle
                        if (!myChart || !chart2 || !chart3) {
                            initializeCharts(dealerLabels, dealerData, providerLabels, providerData,
                                orderStatusLabels, orderStatusData);
                        } else {
                            updateCharts(dealerLabels, dealerData, providerLabels, providerData,
                                orderStatusLabels, orderStatusData);
                        }
                        $(".preccess-loading").html("Veriler Güncellendi");
                        setTimeout(() => {
                            $(".preccess-loading").hide(500);
                        }, 2000);
                    },
                    error: function() {
                        $(".preccess-loading").html("Veriler Güncellenemedi!");
                        setTimeout(() => {
                            $(".preccess-loading").hide(500);
                        }, 2000);
                        alert("Veriler alınırken bir hata oluştu.");
                    }
                });
            }

            fetchDashboardData();

            $('#dashboardForm input').on('change', function() {
                $(".preccess-loading").html("Veriler Güncelleniyor...");
                $(".preccess-loading").fadeIn(500);
                fetchDashboardData();
            });
        });
    </script>
@endsection
