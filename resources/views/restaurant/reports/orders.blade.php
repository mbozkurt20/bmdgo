@extends('restaurant.layouts.app')

@section('content')
    <style>
        /* Kurumsal Light Kart Stilleri */
        .summary-box {
            padding: 1.25rem;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 1rem;
            border: 1px solid rgba(0,0,0,0.05); /* Çok hafif sınır çizgisi */
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .summary-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        /* Rakamlar için koyu gri/füme */
        .summary-box h3 {
            font-size: 1.5rem;
            margin: 0.2rem 0 0;
            font-weight: 800;
            color: #2d3748;
        }

        /* Başlıklar için orta ton gri */
        .summary-box span {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            display: block;
            color: #718096;
        }

        /* Light Renk Paleti (Soft Arka Planlar) */
        .bg-nakit  { background-color: #ffffff !important; border-bottom: 3px solid #c1d3d2 !important; } /* Su Yeşili */
        .bg-kkarti { background-color: #ffffff !important; border-bottom: 3px solid #b6bcc1 !important; } /* Buz Mavisi */
        .bg-ticket { background-color: #ffffff !important; border-bottom: 3px solid #d5d2d0 !important; } /* Krem Turuncu */
        .bg-online { background-color: #edf2ff !important; border-bottom: 3px solid #f3f3f3 !important; } /* Lavanta Mavi */
        .bg-other  { background-color: #f7fafc !important; border-bottom: 3px solid #a0aec0 !important; } /* Bulut Gri */
        .bg-total  { background-color: #f1f5f9 !important; border-bottom: 3px solid #1e293b !important; } /* Slate (Toplam) */
        .bg-cancel { background-color: #fff5f5 !important; border-bottom: 3px solid #ffffff !important; } /* Pudra Kırmızı */
    </style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Sipariş Raporları</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Raporlar</a></li>
                <li class="breadcrumb-item active">Filtrele</li>
            </ol>
        </div>

        <div class="card p-4 mb-4 shadow-sm border-0" style="border-radius: 15px;">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="small fw-bold">Platform</label>
                    <select class="form-select" id="platform">
                        <option value="0">Tüm Platformlar</option>
                        <option value="gpsyemek">GpsYemek</option>
                        <option value="getir">GetirYemek</option>
                        <option value="trendyol">TrendyolYemek</option>
                        <option value="yemeksepeti">Yemeksepeti</option>
                        <option value="migros">MigrosYemek</option>
                        <option value="telefonsiparis">Telefon Sipariş</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold">Durum</label>
                    <select class="form-select" id="status_filter">
                        <option value="delivered">Teslim Edilenler</option>
                        <option value="cancelled">İptal Edilenler</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Başlangıç Tarihi</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="start_date">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Bitiş Tarihi</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="form-control" id="end_date">
                </div>
                <div class="col-md-2 gap-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100 fw-bold shadow-sm" onclick="ReportFilter()">
                        <i class="fas fa-search"></i> Listele
                    </button>
                    <button class="btn btn-danger w-100 fw-bold shadow-sm" id="downloadPDF">
                        <i class="fas fa-file-pdf"></i> PDF Rapor
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-2 mb-4">
            <div class="col-md-3 col-6">
                <div class="summary-box bg-total"><span>Sipariş Adedi</span><h3 id="res-count">0</h3></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-box bg-nakit"><span>Nakit Toplam</span><h3 id="res-nakit">0.00 TL</h3></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-box bg-kkarti"><span>Kredi Kartı</span><h3 id="res-kkarti">0.00 TL</h3></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-box bg-online"><span>Online Ödeme</span><h3 id="res-online">0.00 TL</h3></div>
            </div>
            <div class="col-md-2 col-4">
                <div class="summary-box bg-ticket"><span>Ticket</span><h3 id="res-ticket">0.00 TL</h3></div>
            </div>
            <div class="col-md-2 col-4">
                <div class="summary-box bg-other"><span>Sodexo</span><h3 id="res-sodexo">0.00 TL</h3></div>
            </div>
            <div class="col-md-2 col-4">
                <div class="summary-box bg-other"><span>Multinet/Pluxee</span><h3 id="res-multi_plux">0.00 TL</h3></div>
            </div>
            <div class="col-md-6 col-12">
                <div class="summary-box" style="background: linear-gradient(45deg, #1c1c1c, #000); border: 1px solid #gold;">
                    <span class="text-warning">GENEL TOPLAM CİRO</span>
                    <h2 class="text-warning fw-bold m-0" id="res-grand_total">0.00 TL</h2>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="table-responsive table-secondary">
                <table class="table table-hover align-middle mb-0" id="reportTable">
                    <thead class="bg-light text-muted small uppercase">
                    <tr><th>Platform</th><th>No</th><th>Müşteri</th><th>Ödeme Tipi</th><th class="text-end">Tutar</th><th>Saat</th></tr>
                    </thead>
                    <tbody id="report" class="small"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PDF & Toast Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.16/jspdf.plugin.autotable.min.js"></script>

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

        document.getElementById("downloadPDF").addEventListener("click", function () {
            const tableRows = document.querySelectorAll("#report tr");
            if (tableRows.length === 0) {
                showAlert("PDF oluşturmak için önce rapor filtreleyin.", "warning");
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.autoTable({
                html: '#reportTable',
                theme: 'grid',
                styles: {
                    fontSize: 8,
                    textColor: [0, 0, 0],
                    cellPadding: 4,
                },
                headStyles: {
                    fillColor: [253, 104, 62],
                    textColor: [255, 255, 255],
                    fontSize: 9,
                    fontStyle: 'bold',
                },
                alternateRowStyles: { fillColor: [245, 245, 245] },
            });

            doc.save('siparis-raporları.pdf');
        });
    </script>
    <script>
        function ReportFilter() {
            const params = {
                start: $('#start_date').val(),
                end: $('#end_date').val(),
                platform: $('#platform').val(),
                status: $('#status_filter').val() // İptal parametresi
            };

            $.post('/restaurant/reports/globalFilterOrder?_token={{ csrf_token() }}', params, function (res) {
                let html = '';
                res.data.forEach(order => {
                    html += `<tr>
                    <td><span class="badge bg-soft-primary text-primary">${order.platform}</span></td>
                    <td>#${order.tracking_id}</td>
                    <td class="fw-bold">${order.full_name}</td>
                    <td>${order.payment}</td>
                    <td class="text-end fw-bold">${order.amount}</td>
                    <td>${order.time}</td>
                </tr>`;
                });
                $('#report').html(html || '<tr><td colspan="6" class="text-center py-4">Sonuç bulunamadı.</td></tr>');

                // Tüm ID'leri ve Grand Total'i otomatik güncelle
                Object.keys(res.totals).forEach(key => {
                    let val = res.totals[key];
                    // Multinet ve Pluxee'yi tek kutuda toplamak istersen diye (opsiyonel):
                    if(key === 'multinet' || key === 'pluxee') {
                        // Burayı ihtiyacına göre JS ile toplatabilirsin.
                    }
                    $(`#res-${key}`).text(key === 'count' ? val : new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2 }).format(val) + " TL");
                });
            });
        }

        // Multinet ve Pluxee'yi ayrı ayrı basmak için HTML'e res-multinet ve res-pluxee ekleyebilirsin.
    </script>
@endsection
