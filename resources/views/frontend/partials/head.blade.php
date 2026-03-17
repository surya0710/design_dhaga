<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<!-- PRIMARY SEO -->
<title>{{ trim($__env->yieldContent('title')) ?: 'Nothing' }}</title>


<meta name="description" content="@yield('meta_description')">

<meta name="keywords" content="@yield('meta_keywords')">

<!-- OPEN GRAPH (FACEBOOK / INSTAGRAM / WHATSAPP) -->
<meta property="og:title" content="@yield('og_title')">

<meta property="og:description" content="@yield('og_description')">

<meta property="og:image" content="@yield('og_image')">

<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">

<!-- CANONICAL URL -->
<link rel="canonical" href="{{ url()->current() }}">