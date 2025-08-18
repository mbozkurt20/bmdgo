
<!DOCTYPE html>
<html lang="tr">

@include('superadmin.layouts.partials.head')

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
    @include('superadmin.layouts.partials.header')

    @include('superadmin.layouts.partials.sidebar')

    <div id="app" class="content-body">
        @yield('content')
    </div>

    @include('superadmin.layouts.partials.footer')
</div>


@include('superadmin.layouts.partials.scripts')
</body>
</html>
