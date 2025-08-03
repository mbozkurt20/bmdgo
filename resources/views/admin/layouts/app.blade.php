
<!DOCTYPE html>
<html lang="tr">

@include('admin.layouts.partials.head')

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
    @include('admin.layouts.partials.header')

    @include('admin.layouts.partials.sidebar')

    <div class="content-body">
        @yield('content')
    </div>

    @include('admin.layouts.partials.footer')
</div>


@include('admin.layouts.partials.scripts')
</body>
</html>
