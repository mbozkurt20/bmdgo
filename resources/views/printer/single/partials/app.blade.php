<!DOCTYPE html>
<html lang="tr">

@include('printer.single.partials.head')

<body onload="window.print()">
<div class="receipt-wrap">
    <div class="receipt">
        @include('printer.single.partials.header')

        <div class="paper">
            <div class="meta">
                <div class="text-bold">
                    <img class="logo" src="{{url('/theme/images/bmdGo.png')}}" alt="{{ env('APP_NAME') }}">
                </div>
                <div class="" style="float: right">
                    <div class="hourly">
                        <small>{{date('d-m-Y', strtotime($order->created_at))}}</small>
                    </div>
                    <div class="hourly" style="float: right">
                        <small class="block">{{date('H:i', strtotime($order->created_at))}}</small>
                    </div>
                </div>
            </div>

            <div class="divider"></div>
            <div class="line"></div>

            <div style="display:flex;justify-content:space-between;align-items:baseline">
                <div class="order-no">#{{$order->tracking_id}}</div>
                <div class="pill text-bold">

                </div>
            </div>

            <div class="line"></div>
            @if($order->notes)
                <div class="note">** {{$order->notes}}</div>
                <div class="line"></div>
            @endif

            @yield('content')

            <div class="sum">
                <div class="row grand"><div class="label">Toplam</div><div class="total">₺ {{$order->amount}}</div></div>
            </div>

            <div class="line"></div>
        </div>
    </div>
</div>
</body>
</html>
