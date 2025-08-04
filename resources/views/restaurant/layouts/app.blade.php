<!DOCTYPE html>
<html lang="tr">

@include('restaurant.layouts.partials.head')

<body>
<div id="preloader">
    <div class="gooey">
        <span class="dot"></span>
        <div class="dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>

<div id="main-wrapper">
    @include('restaurant.layouts.partials.header')

    @include('restaurant.layouts.partials.sidebar')

    <button onclick="toggleDrawer()" class="special-ok-button"
            style="position: fixed; top: 50%; right: 20px; transform: translateY(-50%); z-index: 60;">
        + Sipariş Ekle
    </button>

    @include('restaurant.orders.new')


    <div class="content-body" >
        @yield('content')
    </div>

    @include('restaurant.layouts.partials.footer')
</div>


<audio autoplay="true" src="{{ url('pos/audio/new_beep.mp3') }}" muted></audio>

@include('restaurant.layouts.partials.scripts')
</body>

</html>
