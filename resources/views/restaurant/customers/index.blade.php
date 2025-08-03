@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Müşteriler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Müşteriler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Liste</a></li>
                </ol>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="customer-search mb-sm-0 mb-3">
                <div class="input-group search-area">
                    <input type="text" id="custom-filter" class="form-control" placeholder="Müşteri ara..">
                    <span class="input-group-text"><a href="javascript:void(0)"><i
                                class="flaticon-381-search-2"></i></a></span>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap">
                <a href="{{route('restaurant.customers.new')}}" class="special-button me-3"><i
                        class="fas fa-user-plus me-2"></i> Yeni Ekle</a>
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
                            <th style="width: 60%">Müşteri Adı</th>
                            <th style="width: 15%">Telefon Numarası</th>
                            <th style="width: 10%">İşlem</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customers as $customer)
                            <tr id="data_{{$customer->id}}">
                                <td>{{$customer->name}}</td>
                                <td>{{$customer->phone}}</td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{route('restaurant.customers.edit', ['id' => $customer->id])}}" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>
                                        <a onclick="DeleteFunction({{$customer->id}})" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                   @if(!count($customers))
                       <h4 class="text-center mt-4">Müşteri Bulunmamaktadır.</h4>
                   @endif
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
                confirmButtonColor: '#4927b3',
                cancelButtonColor: '#e7004d',
                cancelButtonText: 'Hayır',
                confirmButtonText: 'Evet, Silmek istiyorum!',

            }).then(function (isConfirm) {
                if (isConfirm.value) {
                    $.ajax({
                        type: 'GET', //THIS NEEDS TO BE GET
                        url: '/restaurant/customers/delete/' + event,
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
