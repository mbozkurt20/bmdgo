<!DOCTYPE html>
<html lang="tr">

@include('pages.printer.partials.head')

<body onload="window.print()">
<div class="receipt-wrap">
    <div class="receipt">
        @include('pages.printer.partials.header')

        <div class="paper">
            <div class="meta">
                <div class="text-bold">
                    <span class="icon check">✓</span>
                    {{count($order->tags) > 1 ? 'ONLINE ÖDENMİŞ' : 'KİPHOK ÖDENMİŞ'}}
                </div>
                <div class="hourly">
                    <strong>{{date('H:i', strtotime($order->created_at))}}</strong>
                </div>
            </div>

            <div class="divider"></div>
            <div class="line"></div>

            <div style="display:flex;justify-content:space-between;align-items:baseline">
                <div class="order-no">#3146</div>
                <div class="pill text-bold">
                    {{count($order->tags) > 1 ? ucfirst($order->tags[0]) : 'Kiphok - '.$order->tags[0]}}
                </div>
            </div>

            <div class="line"></div>
            <div class="note">** {{$order->customer_note?? 'Not Bulunmuyor'}}</div>
            <div class="note payment-type">Ödeme tipi: {{$order->delivery_method ?? '-'}}</div>
            <div class="line"></div>

            <div class="list">
                <div class="row">
                    <div>
                        <div class="item-title">
                            1 {{$orderItems['Ekmekler'][0]->name}} - {{$orderItems['Tavuk Gramaj'][0]->name}}.
                        </div>
                        @php unset($orderItems['Ekmekler'],$orderItems['Tavuk Gramaj']) @endphp
                        @foreach($orderItems as $category => $items)
                            @foreach($items as $item)
                                <div class="sub">{{$item->name}}</div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="sum">
                <div class="row"><div class="label">Ara toplam</div><div style="color: black">₺ {{$order->subtotal_amount}}</div></div>
                <div class="line"></div>
                <div class="row"><div class="label">Servis Ücreti</div><div style="color: black">₺ {{$order->service_fee}}</div></div>
                <div class="row grand"><div class="label">Toplam</div><div style="color: black">₺ {{$order->total_amount}}</div></div>
                <div class="vat">KDV (Dahil):</div>
            </div>

            @include('pages.printer.partials.footer')
        </div>
    </div>
</div>
</body>
</html>
