@extends('admin.layouts.app')

@section('content')
    <div class="min-vh-100 ">
        <iframe src="https://www.paytr.com/odeme/guvenli/{{ urlencode($token) }}"
                frameborder="0"
                scrolling="no"
                style="width: 100%; height: 1600px;">
        </iframe>
    </div>
@endsection
