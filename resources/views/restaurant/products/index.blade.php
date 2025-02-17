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
                    <input type="text" class="form-control" id="custom-filter" placeholder="Ürün ara..">
                    <span class="input-group-text"><a href="javascript:void(0)"><i
                                class="flaticon-381-search-2"></i></a></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{route('restaurant.products.new')}}" class="btn btn-primary btn-rounded me-3 mb-2"><i
                        class="fas fa-shopping-bag me-2"></i>+ Yeni Ekle</a>
                <a href="javascript:void(0);" class="btn bg-white btn-rounded me-2 mb-2 text-black shadow-sm"><i
                        class="fas fa-calendar-times me-3 scale3 text-primary"></i>Filtrele<i
                        class="fas fa-chevron-down ms-3 text-primary"></i></a>
                <a href="javascript:void(0);" class="btn btn-secondary btn-rounded mb-2"><i class="fas fa-sync"></i></a>
            </div>
        </div>
        <div class="row card">
            <div class="col-xl-12 card-body">
                <div class="table-responsive">
                    <table id="example3" class="order-table shadow-hover card-table text-black" style="min-width: 845px">
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
                            <tr id="data_{{$product->id}}">
                                <td><img src="https://i.hizliresim.com/oefzi6j.png" style="height: 30px;"></td>
                                <td class="wspace-no">{{$product->code}}</td>
                                <td>{{$product->name}}</td>
                                <td class="text-ov">{{$product->price}} ₺</td>
                                <td class="text-ov">{{$product->preparation_time}} dk.</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{route('restaurant.products.edit', ['id' => $product->id])}}" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                        <a onclick="DeleteFunction({{$product->id}})" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
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
            var table = $('#example3').DataTable();
            //DataTable custom search field
            $('#custom-filter').keyup( function() {
                table.search( this.value ).draw();
            } );
        });

        function DeleteFunction(event) {


            Swal.fire({
                title: 'Silmek istediğinizden emin misiniz ?',
                text: "Bu işlemi geri alamazsınız!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#007c00',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Silmek istiyorum!',

            }).then(function (isConfirm) {
                if (isConfirm.value) {
                    $.ajax({
                        type: 'GET', //THIS NEEDS TO BE GET
                        url: '/restaurant/products/delete/' + event,
                        success: function (data) {
                            if (data == "OK") {
                                isConfirm.value && Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + event).fadeOut($("#data_" + event).remove());
                            }
                            if (data == "NO") {
                                isConfirm.value && Swal.fire("Uyarı !!", "Bu Ürünü silemezsiniz. Bu Ürüne ait sipariş bulunmaktadır.", "warning");
                            }

                        },
                        error: function () {
                            console.log(data);
                        }
                    });
                }


            })


        }
    </script>
@endsection
