<!DOCTYPE html>
<html lang="en">
<head>
    <!-- BASIC META TAGS -->
    @include('frontend.partials.head')

    <!-- PERFORMANCE: early connections + font preload -->
    <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://connect.facebook.net">
    <link rel="preload" href="{{ asset('frontend_assets/fonts/Inter/Inter-VariableFont_opsz,wght.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="{{ asset('frontend_assets/images/logo/favicon.jpg') }}">

    <!-- Critical CSS (render-blocking, same-origin) -->
    <link rel="stylesheet" href="{{ versionedAsset('frontend_assets/node_modules/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ versionedAsset('frontend_assets/css/style-v3.css') }}">

    {{-- Font Awesome: non-blocking (icons are not required for first paint / LCP) --}}
    <link rel="stylesheet" href="{{ versionedAsset('frontend_assets/vendor/fontawesome/css/all.min.css') }}" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="{{ versionedAsset('frontend_assets/vendor/fontawesome/css/all.min.css') }}"></noscript>

    <style>
        @media (max-width: 768px) {
            .breadcrumb-section {
                padding-top: .25rem !important;
                padding-bottom: .25rem !important;
            }

            .breadcrumb {
                --bs-breadcrumb-item-padding-x: .25rem;
                font-size: 12px;
                line-height: 1.25;
            }

            .breadcrumb-item.active {
                max-width: 110px;
            }
        }
    </style>

    @stack('extras')

    {{-- Analytics delayed until after window load to free the critical path --}}
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    window.addEventListener('load', function () {
        var ga = document.createElement('script');
        ga.async = true;
        ga.src = 'https://www.googletagmanager.com/gtag/js?id=G-PLEQEJBY8K';
        document.head.appendChild(ga);
        gtag('js', new Date());
        gtag('config', 'G-PLEQEJBY8K');

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
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=1228920345968692&ev=PageView&noscript=1"
    /></noscript>

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
                $isProductBreadcrumb = isset($productBreadcrumbName) && Request::segment(1) === 'shop' && count($segments) >= 4;
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
                    $schemaName = $isProductBreadcrumb && $loop->last ? $productBreadcrumbName : $name;
                @endphp
                ,
                {
                    "@type": "ListItem",
                    "position": {{ $position++ }},
                    "name": {!! json_encode($schemaName) !!},
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
                            $isProductBreadcrumb = isset($productBreadcrumbName) && Request::segment(1) === 'shop' && $displayCount >= 4;
                        @endphp

                        @foreach ($segments as $index => $segment)

                            @php
                                $url .= '/' . $segment;
                                $name = ucwords(str_replace(['-', '_'], ' ', urldecode($segment)));
                                $isLast = $index === ($displayCount - 1);
                                $isProductPageTitle = $isLast && $isProductBreadcrumb;
                                $desktopName = $isProductPageTitle
                                    ? \Illuminate\Support\Str::words($productBreadcrumbName, 2, '...')
                                    : $name;
                                $mobileName = $isProductPageTitle
                                    ? \Illuminate\Support\Str::words($productBreadcrumbName, 1, '...')
                                    : $name;
                                $words = explode(' ', $name);
                                $mobileTitle = implode(' ', array_slice($words, 0, 1)) . '...';
                            @endphp

                            @if ($isLast)
                                <li class="breadcrumb-item active" aria-current="page">
                                    <span class="d-none d-md-inline">
                                        {{ $desktopName }}
                                    </span>
                                    <span class="d-inline d-md-none">
                                        {{ $isProductPageTitle ? $mobileName : ($displayCount > 3 ? $mobileTitle : $name) }}
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
    <!-- JS: jQuery sync (required by page stacks), Bootstrap/site scripts deferred -->
    <script src="{{ versionedAsset('frontend_assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ versionedAsset('frontend_assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ versionedAsset('frontend_assets/js/script.js') }}" defer></script>
    <script>
    window.wishlistConfig = {
        addUrl: @json(route('wishlist.add')),
        removeUrl: @json(route('wishlist.remove')),
        csrfToken: @json(csrf_token()),
        isAuthenticated: @json(auth()->check() && auth()->user()->utype === 'USR')
    };
    window.authConfig = {
        isAuthenticated: window.wishlistConfig.isAuthenticated
    };
    window.wishlistOptions = window.wishlistOptions || {};
    </script>
    @stack('wishlist-options')
    <script src="{{ versionedAsset('frontend_assets/js/wishlist.js') }}" defer></script>
    {{-- Scripts --}}
    @stack('scripts')

    {{-- Google Merchant store widget: load after idle/load --}}
    <script>
      (function () {
        function startMerchantWidget() {
          var s = document.createElement('script');
          s.id = 'merchantWidgetScript';
          s.src = 'https://www.gstatic.com/shopping/merchant/merchantwidget.js';
          s.defer = true;
          s.addEventListener('load', function () {
            if (typeof merchantwidget === 'undefined') return;
            merchantwidget.start({
              merchant_id: {{ (int) config('services.google.merchant_id', 5791926177) }},
              position: 'RIGHT_BOTTOM',
              region: 'IN'
            });
          });
          document.body.appendChild(s);
        }
        if ('requestIdleCallback' in window) {
          requestIdleCallback(startMerchantWidget, { timeout: 4000 });
        } else {
          window.addEventListener('load', function () {
            setTimeout(startMerchantWidget, 1500);
          });
        }
      })();
    </script>
</body>
</html>
