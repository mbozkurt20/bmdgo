@extends('dealer.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/admin/home/index.css')}}">
    @include('dealer.home_table')
@endsection
