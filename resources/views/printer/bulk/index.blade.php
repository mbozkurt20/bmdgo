@extends('pages.printer.partials.app')
@section('content')
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
@endsection
