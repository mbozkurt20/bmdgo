@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">İptal Edilen Siparişler</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">Siparişler</li>
                <li class="breadcrumb-item active">Liste</li>
            </ol>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-3 mb-sm-0">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter-delete-admin" placeholder="Sipariş ara...">
                    <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="javascript:void(0);" onclick="location.reload();" class="special-ok-button  mb-2">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>

        <div class="card card-body">
            <div class="table-responsive">
                <table id="example56" class="table table-hover text-black" style="min-width: 845px;">
                    <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Telefon</th>
                        <th>Tutar</th>
                        <th>Ödeme Yön.</th>
                        <th>İptal Sebebi</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $order)
                        <tr id="data_{{ $order->id }}">
                            <td>
                                @switch($order->platform)
                                    @case('yemeksepeti')
                                        <img src="{{ asset('theme/images/yemeksepeti.png') }}" style="height: 18px;">
                                        @break
                                    @case('getir')
                                        <img src="{{ asset('theme/images/getiryemek.png') }}" style="height: 18px;">
                                        @break
                                    @case('trendyol')
                                        <img src="{{ asset('theme/images/trendyolyemek.png') }}" style="height: 18px;">
                                        @break
                                    @case('adisyo')
                                        <img src="{{ asset('theme/images/adisyoFull.png') }}" style="height: 22px;">
                                        @break
                                    @case('telefonsiparis')
                                        <span class="badge bg-warning text-dark">POS</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $order->tracking_id }}</td>
                            <td>{{ $order->full_name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ number_format($order->amount, 2) }} TL</td>
                            <td>{{ $order->payment_method === 'PAY_WITH_CARD' ? 'Kredi Kartı' : $order->payment_method }}</td>
                            <td>{{ $order->message ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $order->status }}</span>
                            </td>
                            <td>
                                <div class="d-flex">
                                    <button class="btn btn-secondary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#Orders{{ $order->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="printDiv({{ $order->id }})">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="Orders{{ $order->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $order->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content" id="Printed{{ $order->id }}">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel{{ $order->id }}">Sipariş Bilgileri</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2"><strong>Sipariş No:</strong> {{ $order->tracking_id }}</div>
                                                    <div class="col-md-6 mb-2"><strong>Müşteri:</strong> {{ $order->full_name }}</div>
                                                    <div class="col-md-4 mb-2"><strong>Telefon:</strong> {{ $order->phone }}</div>
                                                    <div class="col-md-4 mb-2"><strong>Tutar:</strong> {{ number_format($order->amount, 2) }} TL</div>
                                                    <div class="col-md-4 mb-2"><strong>Ödeme Yön.:</strong> {{ $order->payment_method === 'PAY_WITH_CARD' ? 'Kredi Kartı' : $order->payment_method }}</div>
                                                    <div class="col-md-12 mb-3"><strong>Adres:</strong> {{ $order->address }}</div>

                                                    <div class="col-md-12">
                                                        <table class="table table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th>Ürün</th>
                                                                <th>Adet</th>
                                                                <th>Fiyat</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach (json_decode($order->items) as $item)
                                                                <tr>
                                                                    <td>{{ $item->name }}</td>
                                                                    <td>1</td>
                                                                    <td>{{ number_format($item->price, 2) }} TL</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" onclick="printDiv({{ $order->id }})">
                                                    <i class="fa fa-print"></i> Yazdır
                                                </button>
                                                <button class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal End -->
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script>
        $(document).ready(function () {
            // Eğer daha önce DataTable başlatılmışsa önce yık
            if ($.fn.DataTable.isDataTable('#example56')) {
                $('#example56').DataTable().clear().destroy();
            }

            // DataTable başlat
            let table = $('#example56').DataTable({
                order: [[1, 'desc']],
                language: {
                    search: "Ara:",
                    url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json",
                    lengthMenu: "Sayfa başına _MENU_ kayıt",
                    info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
                    infoEmpty: "Gösterilecek kayıt yok",
                    paginate: {
                        next: "Sonraki",
                        previous: "Önceki"
                    }
                }
            });

            // Özel arama inputu bağla
            $('#custom-filter-delete-admin').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function printDiv(id) {
            const content = document.getElementById('Printed' + id).innerHTML;
            const printWindow = window.open('', '', 'width=900,height=700');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Yazdır</title>
                    <link rel="stylesheet" href="{{ asset('theme/css/style.css') }}">
                    <!-- Gerekirse diğer CSS dosyalarını ekleyin -->
                </head>
                <body>${content}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>
@endsection
