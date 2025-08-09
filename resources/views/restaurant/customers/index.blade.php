@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Müşteriler</h2>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Müşteriler</a></li>
                <li class="breadcrumb-item active">Liste</li>
            </ol>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search">
                <div class="input-group search-area">
                    <input type="text" id="custom-filter" class="form-control" placeholder="Müşteri ara..">
                    <span class="input-group-text"><i class="flaticon-381-search-2"></i></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{ route('restaurant.customers.new') }}" class="special-button me-3">
                    <i class="fas fa-user-plus me-2"></i> Yeni Ekle
                </a>
                <a href="javascript:void(0);" onclick="location.reload();" class="special-ok-button  mb-2">
                    <i class="fas fa-sync"></i>
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="customerTable" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Müşteri Adı <i class="fa fa-angle-down"></i></th>
                            <th>Telefon Numarası <i class="fa fa-angle-down"></i></th>
                            <th>Oluşturulma Tarihi <i class="fa fa-angle-down"></i></th>
                            <th>İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customers as $customer)
                            <tr id="data_{{ $customer->id }}">
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('restaurant.customers.edit', $customer->id) }}" class="btn btn-primary btn-sm me-1">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <button onclick="DeleteFunction({{ $customer->id }})" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables + SweetAlert -->
    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#customerTable').DataTable({
                order: [[2, "desc"]], // created_at sütununa göre sıralama
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

            $('#custom-filter').on('keyup', function () {
                table.search(this.value).draw();
            });
        });

        function DeleteFunction(id) {
            Swal.fire({
                title: 'Silmek istediğinizden emin misiniz?',
                text: "Bu işlemi geri alamazsınız!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d2646',
                cancelButtonColor: '#e7004d',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Silmek istiyorum!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/restaurant/customers/delete/' + id,
                        success: function (data) {
                            if (data === "OK") {
                                $('#data_' + id).fadeOut(300, function () {
                                    $(this).remove();
                                });
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                            } else {
                                Swal.fire("Uyarı!", "Bu müşteriyi silemezsiniz.", "warning");
                            }
                        },
                        error: function () {
                            Swal.fire("Hata!", "Bir hata oluştu, lütfen tekrar deneyin.", "error");
                        }
                    });
                }
            });
        }
    </script>
@endsection
