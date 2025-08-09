@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restoran Ürünleri</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Ürünler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Liste</a></li>
                </ol>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                <div class="input-group search-area">
                    <input type="text" class="form-control" id="custom-filter-product" placeholder="Ürün ara..">
                    <span class="input-group-text"><a href="javascript:void(0)"><i
                                class="flaticon-381-search-2"></i></a></span>
                </div>
            </div>
            <a href="{{route('restaurant.products.new')}}" class="special-button"><i class="fas fa-shopping-bag me-2"></i> Yeni Ekle</a>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="exampleProduct" class="table table-bordered text-black" style="min-width: 845px">
                        <thead>
                        <tr>
                            <th style="width: 15%">Resim</th>
                            <th style="width: 15%">Ürün Kodu</th>
                            <th style="width: 30%">Ürün Adı</th>
                            <th style="width: 15%">Satış Fiyatı</th>
                            <th style="width: 15%">Hazırlanma Süresi</th>
                            <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr id="data_{{$product->id}}-product">
                                <td><img alt="" src="{{$product->image}}" style="height: 60px;"></td>
                                <td class="wspace-no">{{$product->code}}</td>
                                <td>{{$product->name}}</td>
                                <td class="text-ov">{{$product->price}} ₺</td>
                                <td class="text-ov">{{$product->preparation_time}} dk.</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{route('restaurant.products.edit', ['id' => $product->id])}}" class="btn btn-primary btn-sm me-1 shadow sharp"><i class="fas fa-pencil-alt"></i></a>
                                        <button onclick="DeleteFunction({{$product->id}})" class="btn btn-danger btn-sm shadow sharp"><i class="fa fa-trash"></i></button>
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

    <script type="text/javascript">
        $(document).ready(function(){
            var table = $('#exampleProduct').DataTable({
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

            $('#custom-filter-product').keyup(function() {
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
                confirmButtonText: 'Evet, Silmek istiyorum!',
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'GET',
                        url: '/restaurant/products/delete/' + id,
                        success: function(data) {
                            if (data == "OK") {
                                Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + id + "-product").fadeOut(function() { $(this).remove(); });
                            } else if (data == "NO") {
                                Swal.fire("Uyarı!", "Bu Ürünü silemezsiniz. Bu Ürüne ait sipariş bulunmaktadır.", "warning");
                            }
                        },
                        error: function() {
                            Swal.fire("Hata!", "Silme işlemi başarısız oldu.", "error");
                        }
                    });
                }
            });
        }
    </script>
@endsection
