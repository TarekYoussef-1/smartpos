<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Smart POS System">
<meta name="author" content="Tarek Youssef">
<meta name="keywords" content="smart pos, laravel, php">
<meta name="csrf-token" content="{{ csrf_token() }}">
@isset($order)
    <meta name="order-data" content="{{ $order->toJson() }}">
    <meta name="editing-order-id" content="{{ $order->id }}">
@endisset
<!-- Title Page (Dynamic) -->
<title>@yield('title', 'Smart POS')</title>
<!-- Fontfaces CSS-->
<link href="{{ asset('assets/css/font-face.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('assets/vendor/fontawesome-7.0.1/css/all.min.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('assets/vendor/mdi-font/css/material-design-iconic-font.min.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('assets/css/pos.css') }}" rel="stylesheet" media="all">
<!-- Bootstrap CSS-->
<link href="{{ asset('assets/vendor/bootstrap-5.3.8.min.css') }}" rel="stylesheet" media="all">
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> -->

<!-- Vendor CSS-->
<link href="{{ asset('assets/css/aos.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('assets/vendor/css-hamburgers/hamburgers.min.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('assets/css/swiper-bundle-11.2.10.min.css') }}" rel="stylesheet" media="all">
<link href="{{ asset('assets/vendor/perfect-scrollbar/perfect-scrollbar-1.5.6.css') }}" rel="stylesheet" media="all">
<!-- Main CSS-->
<link href="{{ asset('assets/css/theme.css') }}" rel="stylesheet" media="all">