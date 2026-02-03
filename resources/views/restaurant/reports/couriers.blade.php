@extends('restaurant.layouts.app')

@section('content')
    <style>
        /* Modern Summary Box */
        .report-summary {
            padding: 1.25rem;
            border-radius: 12px;
            text-align: center;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-bottom: 4px solid #cbd5e1;
            transition: all 0.2s ease;
        }
        .report-summary h3 { font-size: 1.4rem; margin: 5px 0 0; font-weight: 800; color: #1e293b; }
        .report-summary span { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #64748b; }

        /* Renk Vurguları */
        .border-nakit { border-bottom-color: #38b2ac !important; }
        .border-kkarti { border-bottom-color: #4299e1 !important; }
        .border-ticket { border-bottom-color: #ed8936 !important; }
        .border-online { border-bottom-color: #5a67d8 !important; }
        .border-total { border-bottom-color: #1e293b !important; }

        /* Tablo Stili */
        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 12px;
            border-bottom: 2px solid #e2e8f0;
        }
        .no-data-row { padding: 50px !important; color: #94a3b8; font-style: italic; }
    </style>

    <div id="alertBox" class="alert d-none" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <div class="container-fluid">
        <div class="mb-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kurye Raporları</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Kurye Raporlar</a></li>
                <li class="breadcrumb-item active">Filtrele</li>
            </ol>
        </div>

        <div class="mb-4 d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger btn-sm fw-bold" id="downloadPDF"><i class="fas fa-file-pdf"></i> PDF</button>
                <button class="btn btn-outline-success btn-sm fw-bold" id="downloadExcel"><i class="fas fa-file-excel"></i> EXCEL</button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-lg-3">
                        <label class="small fw-bold text-muted">Kurye Seç</label>
                        <select class="form-select" id="courier">
                            <option value="-1">Tüm Kuryeler</option>
                            <option value="0">Restaurant Kuryeleri</option>
                            @foreach ($couriers as $courier)
                                <option value="{{ $courier->id }}">{{ $courier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="small fw-bold text-muted">Başlangıç</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="start_date">
                    </div>
                    <div class="col-lg-2">
                        <label class="small fw-bold text-muted">Bitiş</label>
                        <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="end_date">
                    </div>
                    <div class="col-lg-2">
                        <label class="small fw-bold text-muted">Durum</label>
                        <select class="form-select" id="report_status">
                            <option value="delivered">Teslim Edilenler</option>
                            <option value="cancelled">İptal Edilenler</option>
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100 fw-bold" onclick="ReportFilter()">
                            <i class="fa fa-search me-1"></i> RAPORU GETİR
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col">
                <div class="report-summary border-total">
                    <span>Sipariş</span>
                    <h3 id="topsiparis">0</h3>
                </div>
            </div>
            <div class="col">
                <div class="report-summary border-nakit">
                    <span>Nakit</span>
                    <h3 id="topnakit">0.00 ₺</h3>
                </div>
            </div>
            <div class="col">
                <div class="report-summary border-kkarti">
                    <span>K. Kartı</span>
                    <h3 id="topkkarti">0.00 ₺</h3>
                </div>
            </div>
            <div class="col">
                <div class="report-summary border-ticket">
                    <span>Ticket / Kart</span>
                    <h3 id="topticket">0.00 ₺</h3>
                </div>
            </div>
            <div class="col">
                <div class="report-summary border-online">
                    <span>Online</span>
                    <h3 id="toponline">0.00 ₺</h3>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle" id="reportTable">
                    <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Sipariş No</th>
                        <th>Kurye</th>
                        <th>Müşteri / Telefon</th>
                        <th>Ödeme Yöntemi</th>
                        <th class="text-end">Tutar</th>
                        <th class="text-center">Saat</th>
                    </tr>
                    </thead>
                    <tbody id="report">
                    <tr class="no-data-row text-center">
                        <td colspan="7">Sorgulama yapmak için filtreleri kullanın.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function ReportFilter() {
            const courier = $('#courier').val();
            const start = $('#start_date').val();
            const end = $('#end_date').val();
            const status = $('#report_status').val();

            $.ajax({
                type: 'POST',
                url: '/restaurant/reports/globalFilter?_token={{ csrf_token() }}',
                data: { courier, start, end, status },
                beforeSend: function() {
                    $('#report').html('<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>');
                },
                success: function(response) {
                    $('#report').empty();

                    if (!response.data || response.data.length === 0) {
                        $('#report').html('<tr class="no-data-row text-center"><td colspan="7">Seçilen kriterlerde kayıt bulunamadı.</td></tr>');
                        return;
                    }

                    response.data.forEach((el) => {
                        $('#report').append(`
                            <tr>
                                <td class="fw-bold small">${el.platform}</td>
                                <td class="text-muted small">${el.tracking_id}</td>
                                <td><span class="badge bg-primary text-white fw-large">${el.courier}</span></td>
                                <td class="small">${el.full_name}<br><span class="text-muted">${el.phone}</span></td>
                                <td class="small">${el.payment}</td>
                                <td class="text-end fw-bold">${el.amount}</td>
                                <td class="text-center small text-muted">${el.time}</td>
                            </tr>
                        `);
                    });

                    $('#topnakit').text(response.totals.kapida_nakit + ' ₺');
                    $('#topkkarti').text(response.totals.kapida_k_karti + ' ₺');
                    $('#topticket').text(response.totals.kapida_ticket + ' ₺');
                    $('#toponline').text(response.totals.online + ' ₺');
                    $('#topsiparis').text(response.totals.topsiparis);
                }
            });
        }
    </script>


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
