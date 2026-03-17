<!DOCTYPE html>
<html lang="en">
<head>
    <!-- BASIC META TAGS -->
    @include('frontend.partials.head')

    <!-- PERFORMANCE OPTIMIZATION -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="preload" href="{{ asset('frontend_assets/css/style.css') }}" as="style">
    <link rel="preload" href="{{ asset('frontend_assets/js/script.js') }}" as="script">

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="{{ asset('frontend_assets/images/logo/favicon.jpg') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('frontend_assets/node_modules/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend_assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    @stack('extras')
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1228920345968692');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1228920345968692&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
</head>
<body class="@if (request()->is('login') || request()->is('register')) bg-body @endif">
    @include('frontend.partials.header')
    <main>
        @yield('content')
    </main>
    @include('frontend.partials.footer')
    <!-- JS -->
    <script src="{{ asset('frontend_assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('frontend_assets/js/script.js') }}" defer></script>
    {{-- Scripts --}}
    @stack('scripts')

</body>
</html>
