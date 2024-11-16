@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Kuryeler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Kuryeler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Liste</a></li>
                </ol>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                <div class="input-group search-area">
                    <input type="text" class="form-control"  id="custom-filter"  placeholder="Kurye ara..">
                    <span class="input-group-text"><a href="javascript:void(0)"><i
                                class="flaticon-381-search-2"></i></a></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{route('restaurant.couriers.new')}}" class="btn btn-primary btn-rounded me-3 mb-2"><i
                        class="fas fa-user me-2"></i>+ Yeni Ekle</a>
                <a href="javascript:void(0);" class="btn bg-white btn-rounded me-2 mb-2 text-black shadow-sm"><i
                        class="fas fa-calendar-times me-3 scale3 text-primary"></i>Filtrele<i
                        class="fas fa-chevron-down ms-3 text-primary"></i></a>
                <a href="javascript:void(0);" class="btn btn-secondary btn-rounded mb-2"><i class="fas fa-sync"></i></a>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 card">
                <div class="table-responsive card-body">
                    <table id="example3" class="order-table shadow-hover card-table text-black" style="min-width: 845px">
                        <thead>
                        <tr>
                            <th style="width: 60%">Kurye Adı</th>
                            <th style="width: 15%">Telefon Numarası</th>
                            <th style="width: 15%">Paket Başı Fiyatı</th>
                            <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($couriers as $courier)
                            <tr id="data_{{$courier->id}}">
                                <td>{{$courier->name}}</td>
                                <td>{{$courier->phone}}</td>
                                <td class="text-ov">{{$courier->price}} ₺</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{route('restaurant.couriers.report', ['id' => $courier->id])}}" class="btn btn-secondary shadow btn-xs sharp me-1"><i class="fas fa-file-pdf"></i></a>
                                        <a href="{{route('restaurant.couriers.edit', ['id' => $courier->id])}}" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                        <a onclick="DeleteFunction({{$courier->id}})" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
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
                        url: '/restaurant/couriers/delete/' + event,
                        success: function (data) {
                            if (data == "OK") {
                                isConfirm.value && Swal.fire("Silindi!", "Silme işlemi başarılı.", "success");
                                $("#data_" + event).fadeOut($("#data_" + event).remove());
                            }
                            if (data == "NO") {
                                isConfirm.value && Swal.fire("Uyarı !!", "Bu Kuryeyi silemezsiniz..", "warning");
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
