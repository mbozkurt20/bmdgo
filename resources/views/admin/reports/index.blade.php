@extends('admin.layouts.app')
@section('content')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        .page-header {
            background: #07004d;
            color: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(7,0,77,0.2);
        }

        .page-header h2 {
            font-weight: 700;
            margin: 0;
        }

        .card-custom {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 24px;
        }

        .card-custom h5 {
            font-weight: 600;
            color: #07004d;
        }

        .btn-primary {
            background-color: #ec691e;
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #ca3768;
        }

        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead {
            background: #259a38;
            color: white;
        }

        .summary-box {
            background: #ec691e;
            color: white;
            gap: 5px;
            padding: 16px;
            text-align: center;
            font-weight: 600;
            margin-top: 16px;
        }

        .filter-row .form-label {
            font-weight: 600;
        }
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Raporlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Raporlar</a></li>
                    <li class="breadcrumb-item active">Filtrele</li>
                </ol>
            </div>
        </div>

        <!-- Filtre alanı -->
        <div class="card card-custom mb-4">
            <div class="card-body">
                <div class="row g-3 filter-row align-items-end">
                    <div class="col-md-3">
                        <label for="courier" class="form-label">Kurye Seçiniz</label>
                        <select class="form-control" id="courier">
                            <option value="0">Kurye Seçiniz</option>
                            @foreach ($couriers as $courier)
                                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="restaurant" class="form-label">Restaurant Seçiniz</label>
                        <select class="form-control" id="restaurant">
                            <option value="0">Restaurant Seçiniz</option>
                            @foreach ($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}">{{ $restaurant->restaurant_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="start_date">
                    </div>

                    <div class="col-md-2">
                        <label for="end_date" class="form-label">Bitiş Tarihi</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="end_date">
                    </div>

                    <div class="col-md-2">
                        <button class="special-ok-button w-100" onclick="ReportFilter()">
                            <i class="fa fa-filter"></i> Raporla
                        </button>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-danger me-2" id="pdfBtn">PDF İndir</button>
                    <button class="btn btn-success" id="excelBtn">Excel İndir</button>
                </div>
            </div>
        </div>

        <!-- Rapor tablosu -->
        <div class="card card-custom">
            <div class="card-body" id="reportList">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-bordered">
                        <tr>
                            <th>Platform</th>
                            <th>Sipariş No</th>
                            <th>Kurye Adı</th>
                            <th>Müşteri Adı</th>
                            <th>Telefon</th>
                            <th>Ödeme Yöntemi</th>
                            <th>Tutar</th>
                            <th>Tarih</th>
                            <th>Mesafe</th>
                        </tr>
                        </thead>
                        <tbody id="report">
                        <tr id="no-data" style="display: table-row;">
                            <td colspan="9" class="text-center text-muted font-weight-bold">Veri Bulunmamaktadır</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!--div class="row mt-3 g-3">
                    <div class="col-md-2 summary-box">Sipariş Sayısı: <span id="topsiparis">0</span></div>
                    <div class="col-md-2 summary-box">Top. Nakit: <span id="topnakit">0</span></div>
                    <div class="col-md-2 summary-box">Top. Kredi Kartı: <span id="topkkarti">0</span></div>
                    <div class="col-md-2 summary-box">Top. Ticket: <span id="topticket">0</span></div>
                    <div class="col-md-2 summary-box">Top. Online: <span id="toponline">0</span></div>
                    <div class="col-md-2 summary-box">Top. Ciro: <span id="topciro">0</span></div>
                </div-->
            </div>
        </div>
    </div>

    <!-- PDF ve Excel kütüphaneleri -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

    <script type="text/javascript">
        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.autoTable({
                html: '#reportList table',
                theme: 'grid',
                styles: { fontSize: 6, textColor: [0,0,0], cellPadding: 4 },
                headStyles: { fillColor: [13,38,70], textColor: [255,255,255], fontStyle: 'bold' },
                alternateRowStyles: { fillColor: [245,245,245] },
            });
            doc.save('raporlar.pdf');
        }

        document.getElementById("pdfBtn").addEventListener("click", generatePDF);

        function generateExcel() {
            var table = document.querySelector("#reportList table");
            var wb = XLSX.utils.table_to_book(table, { sheet: "Raporlar" });
            XLSX.writeFile(wb, "raporlar.xlsx");
        }

        document.getElementById("excelBtn").addEventListener("click", generateExcel);

        function ReportFilter() {
            let courier    = $('#courier').val();
            let restaurant = $('#restaurant').val();
            let start      = $('#start_date').val();
            let end        = $('#end_date').val();

            if (courier == 0 || restaurant == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Eksik Seçim!',
                    text: 'Lütfen hem kurye hem de restoran seçiniz.',
                    confirmButtonColor: '#ec691e'
                });
                return;
            }

            $.ajax({
                type: 'POST',
                url: '/admin/reports/globalFilter',
                data: {
                    _token: '{{ csrf_token() }}',
                    courier: courier,
                    restaurant: restaurant,
                    start: start,
                    end: end
                },
                beforeSend: function () {
                    $('#report').html('');
                    $('#no-data').show();
                },
                success: function (response) {

                    // TABLO
                    if (response.data.length === 0) {
                        $('#no-data').show();
                    } else {
                        $('#no-data').hide();

                        response.data.forEach((item) => {
                            $('#report').append(`
                            <tr>
                                <td>${item.platform}</td>
                                <td>${item.tracking_id}</td>
                                <td>${item.courier}</td>
                                <td>${item.full_name}</td>
                                <td>${item.phone}</td>
                                <td>${item.payment}</td>
                                <td>${item.amount}</td>
                                <td>${item.time}</td>
                                <td>${item.distance ?? 'Belirtilmemiş'} mt</td>
                            </tr>
                        `);
                        });
                    }

                    // ÖZET ALANI (TURUNCU KUTULAR)
                    let t = response.totals;

                    $('#topsiparis').html(t.topsiparis);

                    $('#topnakit').html(
                        Number(t.kapida_nakit).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' TL'
                    );

                    $('#topkkarti').html(
                        Number(t.kapida_k_karti).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' TL'
                    );

                    $('#topticket').html(
                        Number(t.kapida_ticket).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' TL'
                    );

                    $('#toponline').html(
                        Number(t.online).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' TL'
                    );

                    $('#topciro').html(
                        Number(t.topciro).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' TL'
                    );

                    Swal.fire({
                        icon: 'success',
                        title: 'Rapor Hazır',
                        text: 'Filtreleme başarıyla tamamlandı',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Veriler alınırken bir hata oluştu.',
                        confirmButtonColor: '#ec691e'
                    });
                }
            });
        }
    </script>
@endsection
