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
            <h2 class="mb-3 me-auto">Raporlar</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Raporlar</a></li>
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
                     <select class="form-control" id="restaurant">
                        <option value="0">Restaurant Seçiniz</option>
                        @foreach($restaurants as $restaurant)
                         <option value="{{$restaurant->id}}">{{$restaurant->restaurant_name}}</option>
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
                  <div class="table-responsive">
                    <table class="table table-responsive-sm">
                        <thead>
                        <tr>
                              <th style="width:15%">Platform</th>
                            <th>Sipariş No</th>
                            <th>Kurye Adı</th>
                            <th style="width:15%">Müşteri</th>
                            <th>Telefon</th>
                            <th>Ödeme Yön.</th>
                            <th>Tutar</th>
                            <th>Saat</th>
                        </tr>
                        </thead>
                        <tbody id="report"> 
                             
                        </tbody>
                         
                    </table>
                </div>
                 <div class="row" style="background: #fd683e;">
                    <div class="col-md-2 tops">
                        Siparis Sayısı: <span id="topsiparis"></span>
                    </div>
                    <div class="col-md-2 tops">
                        Top. Nakit: <span id="topnakit"></span>
                    </div>
                    <div class="col-md-2 tops">
                        Top. Kredi Kartı: <span id="topkkarti"></span>
                    </div>
                    <div class="col-md-2 tops">
                        Top. Ticket: <span id="topticket"></span>
                    </div>
                    <div class="col-md-2 tops">
                        Top. Online : <span id="toponline"></span>
                    </div>
                    <div class="col-md-2 tops">
                        Top. Ciro : <span id="topciro"></span>
                    </div>
                    <div class="col-md-2"></div> 
                </div>
            </div>
        </div>

    </div>


    <script type="text/javascript">

        function ReportFilter() {

            var courier = $('#courier').val();
            var restaurant = $('#restaurant').val();
            var start = $('#start_date').val();
            var end = $('#end_date').val();

            $.ajax({
            type : 'POST',
            url : '/admin/reports/globalFilter' + '?_token=' + '{{ csrf_token() }}',
            data : { courier: courier, restaurant: restaurant, start: start, end: end},
            success: function (response) {
                
                const total = 0;

                $('#report').html("");

                response.data.forEach((element) => {
                    

                $('#report').append(`<tr><td>${element.platform}</td><td>${element.tracking_id}</td><td>${element.courier}</td><td>${element.full_name}</td><td>${element.phone}</td><td>${element.payment}</td><td>${element.amount}</td><td>${element.time}</td></tr>`); 
             
                $('#topnakit').html(element.kapida_nakit);
                $('#topkkarti').html(element.kapida_k_karti);
                $('#topticket').html(element.kapida_ticket);
                $('#toponline').html(element.online);
                $('#topsiparis').html(element.topsiparis);
               
                });
// HTML elementlerinden içerikleri al
var nakit = parseFloat($('#topnakit').html()) || 0;
var kkarti = parseFloat($('#topkkarti').html()) || 0;
var ticket = parseFloat($('#topticket').html()) || 0;
var online = parseFloat($('#toponline').html()) || 0;

// Toplamı hesapla
var topciro = nakit + kkarti + ticket + online;

// Hesaplanan toplamı yeni bir elemente yazdır
$('#topciro').html(topciro.toFixed(2)); // İsteğe bağlı olarak iki ondalık basamak göstermek için toFixed(2) kullanabilirsiniz




            },
            error: function () {
                console.log(response);
            }
        });

        }
        
    </script>

@endsection
