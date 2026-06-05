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
     <!-- Dynamic Breadcrumb Schema -->
    @if (Request::segment(1))
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            }
            @php
                $segments = Request::segments();
                $url = '';
                $position = 2;
            @endphp

            @foreach($segments as $segment)
                @php
                    $url .= '/' . $segment;

                    $name = ucwords(
                        str_replace(
                            ['-', '_'],
                            ' ',
                            urldecode($segment)
                        )
                    );
                @endphp
                ,
                {
                    "@type": "ListItem",
                    "position": {{ $position++ }},
                    "name": "{{ $name }}",
                    "item": "{{ url($url) }}"
                }
            @endforeach
        ]
    }
    </script>
    @endif
    @yield('schema')
</head>
<body class="@if (request()->is('login') || request()->is('register')) bg-body @endif">
    @include('frontend.partials.header')
    <main>
        {{-- Breadcrumb --}}
        @if (Request::segment(1))
        <section class="breadcrumb-section py-2 bg-light border-bottom">
            <div class="container text-center">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">

                        {{-- Home --}}
                        <li class="breadcrumb-item">
                            <a href="{{ url('/') }}">Home</a>
                        </li>

                        @php
                            $segments = Request::segments();
                            $url = '';
                            $displayCount = count($segments);
                        @endphp

                        @foreach ($segments as $index => $segment)

                            @php
                                $url .= '/' . $segment;
                                $name = ucwords(str_replace(['-', '_'], ' ', urldecode($segment)));
                                $isLast = $index === ($displayCount - 1);
                                $words = explode(' ', $name);
                                $mobileTitle = implode(' ', array_slice($words, 0, 1)) . '...';
                            @endphp

                            @if ($isLast)
                                <li class="breadcrumb-item active" aria-current="page">
                                    <span class="d-none d-md-inline">
                                        {{ $name }}
                                    </span>
                                    <span class="d-inline d-md-none">
                                        {{ $displayCount > 3 ? $mobileTitle : $name }}
                                    </span>
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ url($url) }}">
                                        {{ $name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            </div>
        </section>
        @endif
        @yield('content')
    </main>
    @include('frontend.partials.footer')
    <!-- JS -->
    <script src="{{ asset('frontend_assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('frontend_assets/js/script.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    {{-- Scripts --}}
    @stack('scripts')
    
</body>
</html>
