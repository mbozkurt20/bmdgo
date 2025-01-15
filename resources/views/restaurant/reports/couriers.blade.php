@extends('restaurant.layouts.app')

@section('content')
    <style type="text/css">
        .tops {
            padding: 20px 10px;
            font-weight: bold;
            color: #fff;
        }

        .tops span {
            font-size: 15px;
        }

        .table thead tr {
            background: #ddd;
        }

        .table thead tr th {
            color: #000;
            font-size: 15px;
            height: 20px;
            overflow: hidden;
        }
    </style>
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
                <div class="col-lg-3">
                    <select class="form-control" id="courier">
                        <option value="-1">VerGelsin Kuryesi</option>
                        <option value="0">Restaurant Kuryeleri</option>
                        @foreach ($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="start_date">
                </div>
                <div class="col-lg-3">
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="end_date">
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-primary" onclick="ReportFilter()"> <i class="fa fa-filter"></i> Filtrele</button>
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
                                <th style="width:15%">Müsteri</th>
                                <th>Telefon</th>
                                <th>Ödeme Yön.</th>
                                <th>Tutar</th>
                                <th>Saat</th>
                            </tr>
                        </thead>
                        <tbody id="report">

                        </tbody>

                    </table>
                </div>

                <div class="row" style="background: #fd683e;">
                    <div class="col-md-2 tops">
                        Siparis Sayısı: <span id="topsiparis"></span>
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
                    <div class="col-md-2"></div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>

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

            doc.save('siparis_raporlari.pdf');
        }

        document.querySelector(".btn-danger").addEventListener("click", generatePDF);
    </script>

    <script type="text/javascript">
        function ReportFilter() {

            var courier = $('#courier').val();
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            $.ajax({
                type: 'POST',
                url: '/restaurant/reports/globalFilter' + '?_token=' + '{{ csrf_token() }}',
                data: {
                    courier: courier,
                    start: start,
                    end: end
                },
                success: function(response) {

                    const total = 0;

                    $('#report').html("");

                    response.data.forEach((element) => {

                        $('#report').append(
                            `<tr><td>${element.platform}</td><td>${element.tracking_id}</td><td>${element.courier}</td><td>${element.full_name}</td><td>${element.phone}</td><td>${element.payment}</td><td>${element.amount}</td><td>${element.time}</td></tr>`
                        );



                        $('#topnakit').html(element.kapida_nakit);
                        $('#topkkarti').html(element.kapida_k_karti);
                        $('#topticket').html(element.kapida_ticket);
                        $('#toponline').html(element.online);
                        $('#topsiparis').html(element.topsiparis);




                    });


                },
                error: function() {
                    console.log(response);
                }
            });

        }
    </script>
@endsection
