@extends('superadmin.layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
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
