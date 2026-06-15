<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<!-- PRIMARY SEO -->
<title>{!! $__env->yieldContent('title') !!}</title>


<meta name="description" content="@yield('meta_description')">

<meta name="keywords" content="@yield('meta_keywords')">

<!-- OPEN GRAPH (FACEBOOK / INSTAGRAM / WHATSAPP) -->
<meta property="og:title" content="@yield('og_title')">

<meta property="og:description" content="@yield('og_description')">

<meta property="og:image" content="@yield('og_image')">

<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">

@php
    $twitterCard = trim($__env->yieldContent('twitter_card'));
    $twitterTitle = trim($__env->yieldContent('twitter_title'));
    $twitterSite = trim($__env->yieldContent('twitter_site'));
    $twitterDescription = trim($__env->yieldContent('twitter_description'));
    $twitterImage = trim($__env->yieldContent('twitter_image'));
    $twitterImageAlt = trim($__env->yieldContent('twitter_image_alt'));
@endphp

@if($twitterCard && $twitterTitle && $twitterSite && $twitterDescription && $twitterImage && $twitterImageAlt)
<!-- TWITTER CARD -->
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:title" content="{{ $twitterTitle }}">
<meta name="twitter:site" content="{{ $twitterSite }}">
<meta name="twitter:description" content="{{ $twitterDescription }}">
<meta name="twitter:image" content="{{ $twitterImage }}">
<meta name="twitter:image:alt" content="{{ $twitterImageAlt }}">
@endif

<!-- CANONICAL URL -->
<link rel="canonical" href="{{ $pageContent->canonical_url ?? url()->current() }}">