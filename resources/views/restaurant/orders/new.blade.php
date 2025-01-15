

<!DOCTYPE HTML>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Bootstrap-ecommerce by Vosidiy">
    <title>Sipariş Ekranı - VerGelsin</title>

    <!-- Bootstrap4 files-->
    <link href="{{asset('pos/assets/css/bootstrap.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('pos/assets/css/ui.css')}}" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <link href="{{asset('pos/assets/css/OverlayScrollbars.css')}}" type="text/css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Font awesome 5 -->
    <style>
        .avatar {
            vertical-align: middle;
            width: 35px;
            height: 35px;
            border-radius: 0%;
        }
        .bg-default, .btn-default{
            background-color: #f2f3f8;
        }
        .btn-error{
            color: #ef5f5f;
        }

        .tabs {
            display: flex;
            flex-wrap: wrap; // make sure it wraps
        }
        .tabs label {
            order: 1;
            display: block;
            padding: 1rem 2rem;
            margin-right: 0.2rem;
            cursor: pointer;
            background: #5c5c5c;
            font-weight: bold;
            transition: background ease 0.2s;
            color:#fff;
        }
        .tabs .tab {
            order: 99;
            flex-grow: 1;
            width: 100%;
            display: none;
            padding: 1rem;
            background: #fff;
        }
        .tabs input[type="radio"] {
            display: none;
        }
        .tabs input[type="radio"]:checked + label {
            background: #edeff1;
            color:#000;
        }
        .tabs input[type="radio"]:checked + label + .tab {
            display: block;
        }

        @media (max-width: 45em) {
            .tabs .tab,
            .tabs label {
                order: initial;
            }
            .tabs label {
                width: 100%;
                margin-right: 0;
                margin-top: 0.8rem;
            }
        }
        .toplusil a i:hover{
            color:red;
        }
        .paymentRol{
            padding: 12px;
            font-size: 18px;
            color: #fff;
            text-align: center;
            font-weight: bold;
            height: 80px;
            border-radius: 10px;
            margin: 0px 2px;
            cursor: pointer;
        }
        .nakit{
            background: #008002;
        }
        .kkkarti{
            background: #f72b50;
        }
        .kkarti{
            background: #624FD1;
        }
        .kayit{
            background: #fd673e;
            padding: 25px;
            font-size: 22px;
        }
        .customer{
            padding: 0px 10px;
            border-left: 3px solid #5c5c5c;
            width: 100%;
            border-radius: 10px;
            border-right: 3px solid #5c5c5c;
            text-align: left;
        }
        .rightbtn{
            padding: 0 !important;
        }
        .rightbtn a {
            padding: 10px 15px;
            height: 50px;
            font-size:18px;
            color:#fff !important;
            background: #5c5c5c;
        }
        .selectiki{
            height: 50px !important;
            padding: 10px;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable{
            line-height: 40px;
        }
        .select2-container--default .select2-results__option--selected {
            background-color: #e7e7e7;
            line-height: 40px;
        }
        .select2-results__option--selectable {
            cursor: pointer;
            line-height: 40px;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #aaa;
            height: 45px;
        }
        #loader{
            position: absolute;
            padding: 0;
            width: 100%;
            height: 100vh;
            text-align: center;
            background: #fff;
            z-index: 999;
        }
        #loader2{
            position: absolute;
             top: 50%; 
             display: table-cell; 
             vertical-align: middle;
        }
        #loader img{ 
            position: relative; 
            top: 50%; 
        }

    </style>
    <!-- custom style -->
</head>
<body>
    <div id="loader"style=" display: none;" >
        <div>
            <img src="https://wpamelia.com/wp-content/uploads/2018/11/ezgif-2-6d0b072c3d3f.gif" style="height:190px">
        </div>
    </div>
<section class="header-main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="brand-wrap">
                    <h2 class="logo-text">Sipariş Ekranı</h2>
                    <br>
                    {{ Auth::user()->restaurant_name }}
                </div>
                <!-- brand-wrap.// -->
            </div>
            <div class="col-md-4 col-sm-6 text-left">
                <form action="#" class="search-wrap">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Ürün ara..." style="height: 50px">
                        <div class="input-group-append">
                            <button class="btn kkarti" type="submit" style="color:#fff;">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <!-- search-wrap .end// -->
            </div>
            <div class="col-md-3 col-sm-6 row text-left">
                <div class="brand-wrap customer">
                    <div style="text-align: center;padding: 15px">Müşteri Seçin</div>
                </div>
            </div>
            <div class="col-md-1 col-sm-6">
                
            </div>
            <div class="col-md-2 col-sm-6 rightbtn">
               <a class="btn" data-toggle="modal" data-target="#musteriAta" style=""> <i class="fas fa-user-plus"></i> Müşteri</a>
               <a class="btn btn-danger" onclick="ExitPos()" style="background:#c02e3b"> <i class="fas fa-times"></i></a>
            </div>
            <!-- col.// -->
        </div>
        <!-- row.// -->
    </div>
    <!-- container.// -->
</section>

<!-- ========================= SECTION CONTENT ========================= -->
<section class="section-content padding-y-sm bg-default ">
    <form method="" name="formPos">
    <div class="container-fluid">
        <div class="row">
            <input type="hidden" name="payment_control" id="payment_control" value="0">
            <input type="hidden" name="user_id" id="customer_id" value="0">
            <input type="hidden" name="courier_id" id="courier_id" value="">
            <input type="hidden" name="total" id="totalPrice" value="">
            <div class="col-md-9 card padding-y-sm card " style="border-radius: 10px">
                <div class="tabs">
                    @php $s = 0; @endphp
                    @foreach($categories as $cat)
                        @php $s++; @endphp
                    <input type="radio" name="tabs" id="tabProduct_{{$cat->id}}" @if($s == 1) checked="checked" @endif>
                    <label for="tabProduct_{{$cat->id}}">{{$cat->name}}</label>
                    <div class="tab">
                         <div class="row">
                            @foreach(\App\Models\Product::where('categorie_id', $cat->id)->where('status','active')->where('restaurant_id',\Illuminate\Support\Facades\Auth::user()->id)->get() as $pro)
                             <div class="col-md-2" style="max-width: 16.3%;border-radius:5px; margin:4px 2px;padding: 0px;height:150px;background:#fd673e !important;color:#fff">
                                 <a onclick="productAdd({{$pro->id}})" style="cursor: pointer">
                                 <figure class="card card-product" style="background:#fd673e"> 
                                     <figcaption class="info-wrap" style="padding:3px 5px">
                                         <div class="row">
                                             <div class="col-lg-12" style="height:90px;    text-align: center;padding: 13px 10px;">
                                                 <span  style="font-size: 24px;color:#fff;font-weight: 500;" class="title">{{$pro->name}}</span>
                                             </div>
                                             <div class="col-lg-12 text-center" style="height:40px;padding-bottom:10px">
                                                 <span style="font-size: 30px;font-weight:bold" class="price-new">{{number_format($pro->price, 2, ',', '.')}} ₺</span>
                                             </div>
                                         </div>
                                     </figcaption>
                                 </figure> <!-- card // -->
                                 </a>
                             </div> <!-- col // -->
                             @endforeach
                         </div>
                    </div>
                    @endforeach


                </div>
            </div>
            <!-- SAĞ SEPET -->
            <div class="col-md-3">
                <div class="card" style="border-radius: 10px">
                    <div id="sepetim" class="row">
                        <div class="col-lg-6 text-left">
                           <div class="sepet" style="padding: 10px 30px;">
                               <div style="font-size: 22px">
                                   <i class="fa fa-shopping-bag"></i>
                                   <span style="font-size: 15px" id="posTotalItem">{{\Cart::session(\Illuminate\Support\Facades\Auth::user()->id)->getTotalQuantity()}}</span>
                               </div>
                           </div>
                        </div>
                        <div class="col-lg-6 text-right">
                            <div class="toplusil" style="padding: 10px 30px;">
                                <a onclick="removePos(1)" style="font-size: 20px;cursor:pointer;"><i class="fa fa-trash-alt"></i> </a>
                            </div>
                        </div>
                    </div>
                    <div class="productItems row" style="min-height: 500px;">
                        <div class="col-lg-12" id="productItemListp" style="padding: 20px;height: 460px;overflow-y: scroll">
                            @foreach(\Cart::session(\Illuminate\Support\Facades\Auth::user()->id)->getContent() as $basket)
                                <div id="posItem_{{$basket->id}}" class="item select-none mb-3 bg-blue-gray-50 rounded-lg w-full text-blue-gray-700 py-2 px-2 flex justify-center" style="background: #e7e7e7;border-radius: 10px">
                                    <div class="row">
                                        <input type="hidden" name="product_id[]" value="{{$basket->id}}">
                                        <div class="col-md-2" >
                                            <img src="{{asset($basket->associatedModel["image"])}}" style="height: 45px;width:65px;border-radius: 10px">
                                        </div>
                                        <div class="col-md-6 text-left" style="width: 100%;">
                                            <span style="width: 100%;"> {{$basket->name}}</span><br>
                                            <span style="width: 100%;">{{number_format($basket->price, 2, ',', '.')}} </span>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <div class="m-btn-group m-btn-group--pill btn-group mr-2" role="group" aria-label="..." style="padding: 3px">
                                                <button type="button" onclick="updateMinus({{$basket->id}})" class="m-btn btn btn-default" style="background: #5c5c5c;color:#fff"><i class="fa fa-minus"></i></button>
                                                <input type="button" class="m-btn btn btn-default" name="quantity[]" id="quantity_{{$basket->id}}" value="{{$basket->quantity}}" style="background: #fff;color:#000;font-weight: bold" disabled>
                                                <button type="button"  onclick="updatePlus({{$basket->id}})" class="m-btn btn btn-default" style="background: #5c5c5c;color:#fff"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                        <div class="col-lg-12" id="productItemLista" style="padding: 20px;height: 460px;overflow-y: scroll;display: none">

                        </div>
                    </div>
                    <div style="padding: 1rem 1.2rem;" class="">

                        <dl class="dlist-align">
                            <dt>Toplam: </dt>
                            <dd class="text-right h4 b" id="posTotal"> {{number_format(\Cart::session(\Illuminate\Support\Facades\Auth::user()->id)->getTotal(), 2, ',', '.')}} TL</dd>
                        </dl>
                        <div class="row" style="margin:0px;">
                            <div class="col-md-4" style="padding: 10px 0px">
                               <div class="paymentRol nakit" onclick="PaymentMethodSave('Kapıda Nakit ile Ödeme')" style="font-size: 14px;">
                                   <i class="fas fa-lira-sign"></i><br>
                                   Nakit
                               </div>
                            </div>
                            <div class="col-md-4" style="padding: 10px 0px">
                                <div class="paymentRol kkarti" onclick="PaymentMethodSave('Kapıda Ticket ile Ödeme')" style="font-size: 14px;">
                                <i class="fas fa-credit-card"></i><br>
                                    Ticket
                                </div>
                            </div>
                            <div class="col-md-4" style="padding: 10px 0px">
                                <div class="paymentRol kkkarti" onclick="PaymentMethodSave('Kapıda Kredi Kartı ile Ödeme')" style="font-size: 14px;">
                                    <i class="fas fa-credit-card"></i><br>
                                    Kredi Kartı
                                </div>
                            </div>
                            
                            <div class="col-md-12" style="padding: 10px 0px">
                                <div class="paymentRol kayit" onclick="CreateOrder()">
                                    <i class="fas fa-check"></i>
                                    Kaydet
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- card.// -->

                <!-- box.// -->
            </div>
        </div>
    </div>
    </form>
    <!-- container //  -->
</section>

<div class="modal fade" id="kuryeAta"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kurye Seçiniz</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <select class="js-example-basic-single" onchange="CourierSet(event)" style="width: 100%;">
                            <option value="0" >Kurye Ata</option>
                            <option value="-1">VerGelsin Kuryesi</option>
                            @foreach($couriers as $courier)
                                <option value="{{$courier->id}}">{{$courier->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tamam</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="musteriAta"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Müşteri Seçiniz</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <select class="js-example-basic-single" onchange="customerSelect(event)" style="height: 60px;width: 100%;" >
                                <option value="0" >Müşteri Seçiniz</option>
                            @foreach($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->name}} - {{$customer->phone}} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-toggle="modal" data-target="#yeniMusteri" class="btn btn-success"><i class="fas fa-plus"></i> Müşteri Ekle</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tamam</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="yeniMusteri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Müşteri Ekle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" style="padding: 20px">
                    <div class="basic-form">
                        <form method="post" id="customerForm">
                            <div class="row">
                                <div class="mb-3 col-md-12">
                                    <label class="form-label">Müşteri Adı</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Müşteri Adı"
                                           required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">Telefon Numarası</label>
                                    <input type="text" class="form-control" name="phone"
                                           placeholder="Telefon Numarası" id="phoneNumber" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label">2.Telefon Numarası</label>
                                    <input type="text" class="form-control" name="mobile"
                                           placeholder="2.Telefon Numarası">
                                </div> 

                            </div>

                            <div class="card-body" style="border-top:1px solid #ddd;padding: 0px 0px">
                                <div class="clearfix"></div>
                                <div class="repeater-heading">
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <h5 class="pull-left">Adres Ekle</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="item-content row"
                                     style="background: #f4f4f4;margin: 15px 0px  10px;padding:10px 0px;border-radius: 10px">
                                    <div class="mb-3 col-md-6">
                                        <input type="text" class="form-control" id="adres_name" name="adres_name" value="Adres" value="Adres" 
                                               placeholder="Adres Başlğı">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <input type="text" class="form-control" id="sokak_cadde" name="sokak_cadde" value="" 
                                               placeholder="Sokak/Cadde">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <input type="text" class="form-control" id="bina_no" name="bina_no" value="" 
                                               placeholder="Bina No">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <input type="text" class="form-control" id="kat" name="kat" value="" 
                                               placeholder="Kat">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <input type="text" class="form-control" id="daire_no" name="daire_no" value="" 
                                               placeholder="Daire No">
                                    </div>
									<div class="mb-3 col-md-6">
                                        <input type="text" class="form-control" id="mahalle" name="mahalle" value="" 
                                               placeholder="Mahalle">
                                    </div>
   

                                    <div class="mb-3 col-md-12">
                                        <input type="text" name="adres_tarifi" id="adres_tarifi" class="form-control"
                                               placeholder="Adres Tarifi">
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"  class="btn btn-secondary" onclick="CreateCustomer()">Kaydet</button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" value="{{Auth::user()->id}}" id="restaurant">

<!-- ========================= SECTION CONTENT END// ========================= -->
<script src="{{asset('pos/assets/js/jquery-2.0.0.min.js')}}" type="text/javascript"></script>
<script src="{{asset('pos/assets/js/bootstrap.bundle.min.js')}}" type="text/javascript"></script>
<script src="{{asset('pos/assets/js/OverlayScrollbars.js')}}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://kuryenkapinda.com/theme/js/sweetalert2.all.min.js"></script>

<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script> 
<script type="text/javascript">

    /*
    const socket = io('https://kuryenkapinda.com:5000'); 

    const restaurant = $('#restaurant').val();

   socket.on('getPhoneNumber_'+restaurant, (data) => {

          

          if(data.type === 1){
 
            $('.customer').html('<h2 class="logo-text">'+data.name+'</h2> '+data.phone+' <br><span>'+data.mahalle+'. '+data.sokak_cadde+'. No: '+data.bina_no+'. Kat:'+data.kat+'. Daire: '+data.daire_no+' </span>');

            $('#customer_id').val(data.id);

          }else{

            $('#yeniMusteri').modal('show');
            $('#phoneNumber').val(data.phone);
          }

          


    });
    
    */


    $(document).ready(function() {
        $('.js-example-basic-single').select2({ 
            selectionCssClass: 'selectiki',
            placeholder: 'Müşteri Arayınız..',
        });

    });

    function productAdd(e) {
        let quant =  $('#quantity_'+e).val();

        $('#loader').css('display','block');

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/addPOS/' + e,
            success: function (data) {
                let src = '{{url('pos/audio/dot.mp3')}}';
                let audio = new Audio(src);
                audio.play();


                $('#productItemListp').css('display','none');
                $('#productItemLista').css('display','block');

                if(data.durum ===  "yok"){
                    $('#productItemLista').append(data.items);
                }else{
                    let newquant =  parseInt(quant) + 1;
                    $('#quantity_'+e).val(newquant);
                }


                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                 $('#totalPrice').val(data.total);

                    $('#loader').css('display','none');




            },
            error: function () {
                console.log(data);
            }
        });
    }

    function updatePlus(id){

            $('#loader').css('display','block');

        let quant =  $('#quantity_'+id).val();

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/updatePlusPOS/' + id,
            success: function (data) {
                let src = '{{url('pos/audio/dot.mp3')}}';
                let audio = new Audio(src);
                audio.play();

                let newquant =  parseInt(quant) + 1;
                $('#quantity_'+id).val(newquant);

                $('#posTotalItem').html(data.posTotalItem);
                $('#posTotal').html(data.posTotal);
                $('#totalPrice').val(data.total);

                    $('#loader').css('display','none');


            },
            error: function () {
                console.log(data);
            }
        });


    }

    function updateMinus(id){

            $('#loader').css('display','block');

        let qty= document.getElementById("quantity_"+id).value;

        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/updateMinusPOS/' + id +'/'+ qty,
            success: function (data) {
                let src = '{{url('pos/audio/dot.mp3')}}';
                let audio = new Audio(src);
                audio.play();

                if(qty<=1){
                    $("#posItem_"+id).remove();
                    $('#posTotalItem').html(data.posTotalItem);
                    $('#posTotal').html(data.posTotal);
                     $('#totalPrice').val(data.total);
                }else{
                    let newquant =  parseInt(qty) - 1;
                    $('#quantity_'+id).val(newquant);
                    $('#posTotalItem').html(data.posTotalItem);
                    $('#posTotal').html(data.posTotal);
                     $('#totalPrice').val(data.total);
                }

                    $('#loader').css('display','none');


            },
            error: function () {
                console.log(data);
            }
        });


    }

    function removePos(e){
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/removePOS',
            success: function (data) {
                let src = '{{url('pos/audio/trash.mp3')}}';
                let audio = new Audio(src);
                audio.play();
                $('#productItemListp').html("");
                $('#productItemLista').html("");
                $('.customer').html('<div style="text-align: center;padding: 15px">Müşteri Seçin</div>');
                $('#posTotalItem').html("0");
                $('#posTotal').html("0,00 TL");
                location.reload();

            },
            error: function () {
                console.log(data);
            }
        });
    }

    function PaymentMethodSave(e){
        $('#payment_control').val(e);

       if(e === "Kapıda Nakit ile Ödeme"){
           let src = '{{url('pos/audio/beep.mp3')}}';
           let audio = new Audio(src);
           audio.play();
            $('.nakit').css('background','#5c5c5c');
            $('.kkarti').css('background','#624FD1');
            $('.kkkarti').css('background','#f72b50');
       }
       if(e === "Kapıda Ticket ile Ödeme"){
           let src = '{{url('pos/audio/beep.mp3')}}';
           let audio = new Audio(src);
           audio.play();
            $('.nakit').css('background','#008002');
            $('.kkarti').css('background','#5c5c5c');
            $('.kkkarti').css('background','#f72b50');
       }
        if(e === "Kapıda Kredi Kartı ile Ödeme"){
            let src = '{{url('pos/audio/beep.mp3')}}';
            let audio = new Audio(src);
            audio.play();
            $('.nakit').css('background','#008002');
            $('.kkarti').css('background','#624FD1');
            $('.kkkarti').css('background','#5c5c5c');
        }
    }

    function CourierSet(e){
        $('#courier_id').val(e.target.value);
        $('#kuryeAta').modal('hide');

    }


    function ExitPos(){

         window.location.href = 'https://panel.VerGelsin.com.tr';

    }

    function customerSelect(e){
        let customerId =  e.target.value;
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/restaurant/orders/customerpos/' + customerId,
            success: function (data) {
                $('#musteriAta').modal('hide');
                $('.customer').html(data.customer);
                $('#customer_id').val(e.target.value);

            },
            error: function () {
                console.log(data);
            }
        });
    }

    function CreateCustomer(){
        $.ajax({
            type : 'POST',
            url : '/restaurant/orders/customeradd' + '?_token=' + '{{ csrf_token() }}',
            data : $('#customerForm').serialize(),
            success: function (response) {
                $('.customer').html(response.customer);
                $('#customer_id').val(response.customerid);
                $('#yeniMusteri').modal('hide');
                $('#musteriAta').modal('hide');
            },
            error: function () {
                console.log(response);
            }
        });
    }

    function CreateOrder() {
        this.disabled = true; 

        var payment_control = $('#payment_control').val();
        var customer_id = $('#customer_id').val();
        var courier_id = $('#courier_id').val();
        var total =  $('#totalPrice').val();

        let products = [];

        $('.item').each(function() {
          products.push({
            product_id: $(this).find('input[name="product_id"]').val(),
            quantity: $(this).find('input[name="quantity"]').val()
          })
        }) 
        
        if(payment_control != 0){

            if(customer_id>0){

                if(products.length > 0){

                    //İşlemleriburada yapacağız 
                    $.ajax({
                        type: 'POST', 
                        url : '/restaurant/orders/addOrder' + '?_token=' + '{{ csrf_token() }}',
                        data: { customer_id: customer_id, payment_method: payment_control, courier_id: courier_id, products:products, total:total},
                        success: function (response) {
                            if(response.status == "OK"){

                                var divToPrint = response.printed; 
                                var mywindow = window.open('', 'PRINT', 'height=600,width=800');
                                mywindow.document.write('<html><head><title>' + document.title  + '</title>');
                                mywindow.document.write('</head><body >'); 
                                mywindow.document.write(divToPrint);
                                mywindow.document.write('</body></html>');
                                mywindow.document.close(); // necessary for IE >= 10
                                mywindow.focus(); // necessary for IE >= 10*/
                                mywindow.print();

                                $.ajax({
                                type: 'GET', //THIS NEEDS TO BE GET
                                url: '/restaurant/orders/removePOS',
                                success: function (data) {  

                                    let src = '{{url('pos/audio/trash.mp3')}}';
                                    let audio = new Audio(src);
                                    audio.play();
                                    $('#productItemListp').html("");
                                    $('#productItemLista').html("");
                                    $('#posTotalItem').html("0");
                                    $('.customer').html('<div style="text-align: center;padding: 15px">Müşteri Seçin</div>');
                                    $('#posTotal').html("0,00 TL"); 

                                    $('.nakit').css('background','#008002');
                                    $('.kkarti').css('background','#624FD1');
                                    $('.kkkarti').css('background','#f72b50');

                                    Swal.fire('Sipariş Tamamlandı.');
                                    this.disabled = false; 
                                    location.reload();
                                },
                                error: function () {
                                    console.log(data);
                                }
                            });

                                
                            }
                        },
                        error: function (response) {
         
                        }
                    });

                }else{
                     Swal.fire('Ürün eklemeden sipariş oluşturamazsınız!!')
                }

            }else{
                 Swal.fire('Müşteri seçmeden devam edemezsiniz!!')
                 $('#musteriAta').modal('show');
            }

        }else{
            Swal.fire('Ödeme Methodu seçmelisiniz!!')
        }
    }

</script>
</body>
</html>
