@extends('restaurant.layouts.app')

@section('content')
    <style>
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
        }

        #alertBox {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 250px;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            opacity: 0.95;
        }
    </style>

    <div id="alertBox" class="alert d-none" role="alert"></div>


    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kurye Raporları</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Kurye Raporlar</a></li>
                <li class="breadcrumb-item active">Filtrele</li>
            </ol>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="row w-100 w-md-60">
                <div class="col-lg-3 mb-2">
                    <select class="form-control" id="courier">
                        <option value="-1">{{ env('APP_NAME') }} Kuryesi</option>
                        <option value="0">Restaurant Kuryeleri</option>
                        @foreach ($couriers as $courier)
                            <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 mb-2">
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="start_date">
                </div>
                <div class="col-lg-3 mb-2">
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="end_date">
                </div>
                <div class="col-lg-3 flex mb-2">
                    <button class="special-button w-50" onclick="ReportFilter()">
                        <i class="fa fa-filter"></i> Filtrele</button>
                    <button class="special-ok-button" id="downloadPDF">PDF İndir</button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12" id="reportList">
                <div class="table-responsive">
                    <table class="table table-sm" id="reportTable">
                        <thead>
                        <tr>
                            <th style="width:15%">Platform</th>
                            <th>Sipariş No</th>
                            <th>Kurye Adı</th>
                            <th style="width:15%">Müşteri</th>
                            <th>Telefon</th>
                            <th>Ödeme Yön.</th>
                            <th>Tutar</th>
                            <th>Saat</th>
                        </tr>
                        </thead>
                        <tbody id="report"></tbody>
                    </table>
                </div>

                <div class="row bg-danger text-white">
                    <div class="col-md-2 tops">Sipariş Sayısı: <span id="topsiparis">0</span></div>
                    <div class="col-md-2 tops">Top. Nakit: <span id="topnakit">0</span></div>
                    <div class="col-md-2 tops">Top. Kredi Kartı: <span id="topkkarti">0</span></div>
                    <div class="col-md-2 tops">Top. Ticket: <span id="topticket">0</span></div>
                    <div class="col-md-2 tops">Top. Online: <span id="toponline">0</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF & Table Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>

    <script>
        document.getElementById("downloadPDF").addEventListener("click", function () {
            const tableRows = document.querySelectorAll("#report tr");
            if (tableRows.length === 0) {
                showAlert("PDF oluşturmak için önce rapor filtreleyin.", "danger");
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.autoTable({
                html: '#reportTable',
                theme: 'grid',
                styles: {
                    fontSize: 6,
                    textColor: [0, 0, 0],
                    cellPadding: 4,
                },
                headStyles: {
                    fillColor: [253, 104, 62],
                    textColor: [255, 255, 255],
                    fontSize: 7,
                    fontStyle: 'bold',
                },
                alternateRowStyles: { fillColor: [245, 245, 245] },
            });

            doc.save('siparis_raporlari.pdf');
        });

        function ReportFilter() {
            const courier = $('#courier').val();
            const start = $('#start_date').val();
            const end = $('#end_date').val();

            if (!start || !end) {
                showAlert("Lütfen geçerli bir başlangıç ve bitiş tarihi girin.", "danger");
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/restaurant/reports/globalFilter?_token={{ csrf_token() }}',
                data: {
                    courier: courier,
                    start: start,
                    end: end
                },
                success: function(response) {
                    $('#report').empty();

                    if (!response.data || response.data.length === 0) {
                        showAlert("Seçilen kriterlere göre veri bulunamadı.", "danger");
                        return;
                    }

                    response.data.forEach((el) => {
                        $('#report').append(`
                        <tr>
                            <td>${el.platform}</td>
                            <td>${el.tracking_id}</td>
                            <td>${el.courier}</td>
                            <td>${el.full_name}</td>
                            <td>${el.phone}</td>
                            <td>${el.payment}</td>
                            <td>${el.amount}</td>
                            <td>${el.time}</td>
                        </tr>
                    `);

                        $('#topnakit').text(el.kapida_nakit || 0);
                        $('#topkkarti').text(el.kapida_k_karti || 0);
                        $('#topticket').text(el.kapida_ticket || 0);
                        $('#toponline').text(el.online || 0);
                        $('#topsiparis').text(el.topsiparis || 0);
                    });
                },
                error: function(err) {
                    alert("Raporlar yüklenirken bir hata oluştu.");
                    console.error(err);
                }
            });
        }
    </script>

    <script>
        function showAlert(message, type = 'info') {
            const alertBox = document.getElementById('alertBox');
            alertBox.className = `alert alert-${type}`;
            alertBox.innerText = message;
            alertBox.classList.remove('d-none');

            setTimeout(() => {
                alertBox.classList.add('d-none');
            }, 3000);
        }
    </script>
@endsection
