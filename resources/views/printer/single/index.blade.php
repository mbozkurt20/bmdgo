@extends('printer.single.partials.app')
@section('content')
    <div class="list">
        <div class="row">
            <div>
                @foreach($items as $item)
                    <div class="sub">
                        <span class="name">{{$item->name}} x {{$item->quantity}}</span>
                        <span class="price">{{$item->price}}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
