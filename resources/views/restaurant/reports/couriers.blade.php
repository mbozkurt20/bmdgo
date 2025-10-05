@extends('restaurant.layouts.app')

@section('content')
    <style>
        .tops {
            padding: 15px 10px;
            font-weight: bold;
            color: #fff;
            background: #259a38;
        }
        .tops span {
            font-size: 15px;
        }
        .table thead tr {
            background: #ec691e; /* bg-primary rengi */
        }
        .table thead tr th {
            color: #fff;
            font-size: 14px;
        }
        #alertBox {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 250px;
            max-width: 400px;
            opacity: 0.95;
            border-radius: 5px;
            padding: 10px;
        }
        .no-data {
            text-align: center;
            color: #1a1414;
            font-weight: bold;
            padding: 30px 0;
            background: #ede3e6;
        }
    </style>

    <div id="alertBox" class="alert d-none" role="alert"></div>

    <div class="container-fluid">
        <div class="mb-4 d-flex flex-wrap align-items-center text-head">
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
                <div class="col-lg-3 mb-2 gap-2 d-flex">
                    <button class="special-button" onclick="ReportFilter()">
                        <i class="fa fa-filter"></i> Filtrele
                    </button>

                    <button class="btn btn-danger" id="downloadPDF">PDF İndir</button>
                    <button class="btn btn-success" id="downloadExcel">Excel İndir</button>
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
                        <tbody id="report">
                        <tr class="no-data">
                            <td colspan="8">Veri bulunamadı</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3 tops">Sipariş Sayısı: <span id="topsiparis">0</span></div>
                    <div class="col-md-3 tops">Top. Nakit: <span id="topnakit">0</span></div>
                    <div class="col-md-2 tops">Top. Kredi Kartı: <span id="topkkarti">0</span></div>
                    <div class="col-md-2 tops">Top. Ticket: <span id="topticket">0</span></div>
                    <div class="col-md-2 tops">Top. Online: <span id="toponline">0</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- PDF & Excel --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        function showAlert(message, type='info') {
            const alertBox = document.getElementById('alertBox');
            alertBox.className = `alert alert-${type}`;
            alertBox.innerText = message;
            alertBox.classList.remove('d-none');
            setTimeout(() => alertBox.classList.add('d-none'), 3000);
        }

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
                data: { courier, start, end },
                success: function(response) {
                    $('#report').empty();

                    if (!response.data || response.data.length === 0) {
                        $('#report').html('<tr class="no-data"><td colspan="8">Veri bulunamadı</td></tr>');
                        showAlert("Seçilen kriterlere göre veri bulunamadı.", "danger");
                        return;
                    }

                    response.data.forEach((el) => {
                        $('#report').append(`
                            <tr>
                                <td class="text-black font-weight-bold">${el.platform}</td>
                                <td class="text-black font-weight-bold">${el.tracking_id}</td>
                                <td class="text-black font-weight-bold">${el.courier}</td>
                                <td class="text-black font-weight-bold">${el.full_name}</td>
                                <td class="text-black font-weight-bold">${el.phone}</td>
                                <td class="text-black font-weight-bold">${el.payment}</td>
                                <td class="text-black font-weight-bold">${el.amount}</td>
                                <td class="text-black font-weight-bold">${el.time}</td>
                            </tr>
                        `);
                    });

                    console.log({response21:response})
                    $('#topnakit').text(response.totals.kapida_nakit || 0);
                    $('#topkkarti').text(response.totals.kapida_k_karti || 0);
                    $('#topticket').text(response.totals.kapida_ticket || 0);
                    $('#toponline').text(response.totals.online || 0);
                    $('#topsiparis').text(response.totals.topsiparis || 0);
                },
                error: function(err) {
                    showAlert("Raporlar yüklenirken bir hata oluştu.", "danger");
                    console.error(err);
                }
            });
        }

        // PDF
        document.getElementById("downloadPDF").addEventListener("click", function () {
            const tableRows = document.querySelectorAll("#report tr");
            if (!tableRows.length || tableRows[0].classList.contains('no-data')) {
                showAlert("PDF oluşturmak için önce rapor filtreleyin.", "danger");
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.autoTable({
                html: '#reportTable',
                theme: 'grid',
                styles: { fontSize: 6, cellPadding: 4 },
                headStyles: { fillColor: [231, 0, 77], textColor: [255,255,255], fontSize:7, fontStyle:'bold' },
                alternateRowStyles: { fillColor: [245,245,245] },
            });

            doc.save('siparis_raporlari.pdf');
        });

        // Excel
        document.getElementById("downloadExcel").addEventListener("click", function () {
            const tableRows = document.querySelectorAll("#report tr");
            if (!tableRows.length || tableRows[0].classList.contains('no-data')) {
                showAlert("Excel oluşturmak için önce rapor filtreleyin.", "danger");
                return;
            }

            const wb = XLSX.utils.table_to_book(document.getElementById('reportTable'), {sheet:"Rapor"});
            XLSX.writeFile(wb, "siparis_raporlari.xlsx");
        });
    </script>
@endsection
