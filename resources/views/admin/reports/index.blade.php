@extends('admin.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/reports/index.css')}}">

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Raporlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Raporlar</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Filtrele</a></li>
                </ol>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="row" style="width: 60%;">
                <div class="col-lg-2">
                    <select class="form-control" id="courier">
                        <option value="0">Kurye Seçiniz</option>
                        @foreach ($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-control" id="restaurant">
                        <option value="0">Restaurant Seçiniz</option>
                        @foreach ($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->restaurant_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="start_date">
                </div>
                <div class="col-lg-2">
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="end_date">
                </div>

                <div class="col-lg-4">
                    <button class="btn btn-primary" onclick="ReportFilter()"> <i class="fa fa-filter"></i> Raporla</button>
                </div>
            </div>
            <div>
                <button class="btn btn-danger">PDF</button>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12" id="reportList">
                <div class="table-responsive">
                    <table class="table table-responsive-sm">
                        <thead>
                            <tr>
                                <th style="width:15%">Platform</th>
                                <th>Siparis No</th>
                                <th>Kurye Adi</th>
                                <th>Müsteri Adi</th>
                                <th>Telefon</th>
                                <th>Ödeme Yön.</th>
                                <th>Tutar</th>
                                <th>Tarih</th>
                                <th>Mesafe Hesaplama</th>
                            </tr>
                        </thead>
                        <tbody id="report">

                        </tbody>
                    </table>
                </div>

                <div class="container-fluid">
                    <div class="row mt-3 pt-3 border-top text-white" style="background: #fd683e;">
                        <div class="col-md-2 tops">
                            Sipariş Sayısı: <span id="topsiparis"></span>
                        </div>
                        <div class="col-md-2 tops">
                            Top. Nakit: <span id="topnakit"></span>
                        </div>
                        <div class="col-md-2 tops">
                            Top. Kredi Kartı: <span id="topkkarti"></span>
                        </div>
                        <div class="col-md-2 tops">
                            Top. Ticket: <span id="topticket"></span>
                        </div>
                        <div class="col-md-2 tops">
                            Top. Online : <span id="toponline"></span>
                        </div>
                        <div class="col-md-2 tops">
                            Top. Ciro : <span id="topciro"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

<script type="text/javascript">
    function generatePDF() {
        const {
            jsPDF
        } = window.jspdf;

        const doc = new jsPDF();

        doc.setFont('Roboto');

        doc.autoTable({
            html: '#reportList table',
            theme: 'grid',
            styles: {
                fontSize: 6,
                textColor: [0, 0, 0],
                lineColor: [255, 255, 255],
                cellPadding: 4,
            },
            headStyles: {
                fillColor: [253, 104, 62],
                textColor: [255, 255, 255],
                fontSize: 7,
                fontStyle: 'bold',
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245],
            },
        });

        doc.save('admin_raporlari.pdf');
    }

    document.querySelector(".btn-danger").addEventListener("click", generatePDF);
</script>

<script>
    // Pusher'ı başlat
    var pusher = new Pusher('c68579a24eeaebfd6487', {
        cluster: 'eu'
    });

    // 'orders' kanalını dinle
    var channel = pusher.subscribe('sweet-garden-70');
    channel.bind('App\\Events\\NewOrderAdded', function(data) {
        // Yeni sipariş geldiğinde ekrana verileri ekle
        console.log('Yeni sipariş:', data.order);
        // Burada DOM manipülasyonu yaparak yeni siparişi sayfaya ekleyebilirsiniz.
    });
</script>
<script type="text/javascript">
    function ReportFilter() {
        var courier = $('#courier').val();
        var restaurant = $('#restaurant').val();
        var start = $('#start_date').val();
        var end = $('#end_date').val();

        $.ajax({
            type: 'POST',
            url: '/admin/reports/globalFilter' + '?_token=' + '{{ csrf_token() }}',
            data: {
                courier: courier,
                restaurant: restaurant,
                start: start,
                end: end
            },
            success: function(response) {

                const total = 0;

                $('#report').html("");

                response.data.forEach((element) => {


                    $('#report').append(
                        `<tr><td>${element.platform}</td><td>${element.tracking_id}</td><td>${element.courier}</td><td>${element.full_name}</td><td>${element.phone}</td><td>${element.payment}</td><td>${element.amount}</td><td>${element.time}</td><td>${(element.message == null ? "Belirtilmemiş" : element.message)}<br>Km</td></tr>`
                    );

                    $('#topnakit').html(element.kapida_nakit);
                    $('#topkkarti').html(element.kapida_k_karti);
                    $('#topticket').html(element.kapida_ticket);
                    $('#toponline').html(element.online);
                    $('#topsiparis').html(element.topsiparis);

                });
                // HTML elementlerinden içerikleri al
                var nakit = parseFloat($('#topnakit').html()) || 0;
                var kkarti = parseFloat($('#topkkarti').html()) || 0;
                var ticket = parseFloat($('#topticket').html()) || 0;
                var online = parseFloat($('#toponline').html()) || 0;

                // Toplamı hesapla
                var topciro = nakit + kkarti + ticket + online;

                // Hesaplanan toplamı yeni bir elemente yazdır
                $('#topciro').html(topciro.toFixed(
                    2
                )); // İsteğe bağlı olarak iki ondalık basamak göstermek için toFixed(2) kullanabilirsiniz




            },
            error: function() {
                console.log(response);
            }
        });

    }
</script>
