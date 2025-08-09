@extends('layouts.app')

@section('content')


    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <div class="flex gap-2">
            <div class="card card-body bg-success-gradient">
                <h5>  Çevrimiçi: {{$couriers->where('online',true)->count()}} Kurye</h5>
            </div>
            <div class="card card-body bg-danger-gradient">
                <h5>  Çevrimdışı: {{$couriers->where('online',false)->count()}} Kurye</h5>
            </div>
        </div>
    </div>

    <!-- End::page-header -->

    <!-- Start::row-1 -->
    <div class="row">
        <div class="col-lg-12">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif

            <form method="POST" action="{{route('couriers.store')}}">
                @csrf

                <div class="card custom-card">
                    <div class="card-body">
                        <div id="map" style="width: 100%; height: 75vh;"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        var map = L.map('map').setView([37.969074, 37.1329995], 7);

        L.tileLayer('https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
            attribution: ''
        }).addTo(map);

        var markers = []; // Tüm marker'ları burada tutacağız

        function addMarkers(locations) {
            // Mevcut marker'ları haritadan sil
            markers.forEach(function(marker) {
                map.removeLayer(marker);
            });
            markers = [];

            // Yeni marker'ları oluştur
            locations.forEach(function(location) {
                var icon = L.icon({
                    iconUrl: '/backend/images/kurye.png',
                    iconSize: [48, 48],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -42]
                });

                var marker = L.marker([location.latitude, location.longitude], { icon: icon }).addTo(map);
                marker.bindPopup('<b>' + location.name + '</b><br>' + location.price);

                marker.bindTooltip(location.name + ' - Tel: ' + location.phone +
                    '<br> Paket Başı: ' + location.price + '₺' +
                    '<br> Uzaklık: ' + location.distance, {
                    permanent: false,
                    direction: 'top'
                });

                markers.push(marker); // Yeni marker'ı diziye ekle
            });
        }

        // İlk yüklemede kurye verilerini ekle
        var initialData = {!! $couriers !!};
        addMarkers(initialData);

        // Pusher kurulumu
        Pusher.logToConsole = false;

        var courierPusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
            cluster: '{{env('PUSHER_APP_CLUSTER')}}'
        });

        var channelT = courierPusher.subscribe('courier-channel');
        channelT.bind('courier-{{ $restaurant->code }}', function(data) {
            addMarkers(data); // Gelen verilerle marker'ları güncelle
        });
    </script>
@endsection
