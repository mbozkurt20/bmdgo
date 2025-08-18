@extends('superadmin.layouts.app')
@section('content')
    <link rel="stylesheet" href="{{asset('css/pages/admin/home/index.css')}}">
    @include('superadmin.home_table')
@endsection
