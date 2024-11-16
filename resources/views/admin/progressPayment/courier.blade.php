@extends('admin.layouts.app')

@section('content')
  <style type="text/css">
    .tops{
        padding: 20px 10px;
        font-weight: bold;
        color: #fff;
    }
    .tops span{
        font-size: 15px;
    }
       .table thead tr{
        background: #ddd;
       }
    .table thead tr th{
        color:#000;
        font-size: 15px;
        height: 20px;
        overflow: hidden;
    }
</style>

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Hakedişler</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Restorant Hakediş</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Filtrele</a></li>
                </ol>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
             <div class="row" style="width: 60%;">
             <div class="col-lg-2">
                     <select class="form-control" id="courier">
                        <option value="0">Kurye Seçiniz</option>
                        @foreach($couriers as $courier)
                         <option value="{{$courier->id}}">{{$courier->name}}</option>
                         @endforeach
                     </select>
                 </div>
                  <div class="col-lg-2">
                     <input type="date" value="{{date('Y-m-d')}}" class="form-control" id="start_date">
                 </div>
                  <div class="col-lg-2">
                     <input type="date" value="{{date('Y-m-d')}}" class="form-control" id="end_date">
                 </div>
                 <div class="col-lg-4">
                     <button class="btn btn-primary"  onclick="ReportFilter()"> <i class="fa fa-filter"></i> Filtrele</button>
                 </div>
             </div>
        </div>
        <div class="row">
            <div class="col-xl-12" id="reportList">
            <div class="card border-success mb-3" style="max-width: 18rem;">
                <div class="card-header bg-transparent border-success" id="selected-resturant" style="font-weight: bold; text-align: center; color: #fd683e;"></div>
                <div class="card-body text-success">
                <h5 class="card-title">Paket Sayısı: <span id="order-count"></span></h5>
                <h5 class="card-title">Toplam Hakediş: <span id="total-progress-payment"></span> </h5>
                </div>
                </div>
            </div>
        </div>

    </div>


    <script type="text/javascript">

        function ReportFilter() {

            var courier = $('#courier').val();
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            $.ajax({
            type : 'POST',
            url : '/admin/progress-payment/courier' + '?_token=' + '{{ csrf_token() }}',
            data : {  courier: courier, start: start, end: end},
            success: function (response) {
                
                const total = 0;

                $('#report').html("");
                $("#selected-resturant").text(response.courier_name);

                $("#order-count").html(response.order_count);
                $("#total-progress-payment").html(response.total_progress_payment);


            },
            error: function () {
                console.log(response);
            }
        });

        }
        
    </script>

@endsection
