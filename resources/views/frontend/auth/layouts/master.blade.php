<!DOCTYPE html>
<html lang="fa">

@include('frontend.layouts.head')

<body>
<div class="wrapper">

    @include('frontend.layouts.loader')
    <!-- Start main-content -->
    @yield('content')
    <!-- End main-content -->
</div>
<!-- Core JS Files -->
@include('frontend.layouts.js_frontend')
</body>
</html>
