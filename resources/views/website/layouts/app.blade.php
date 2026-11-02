<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'کریم محمدزاده')</title>
    @hasSection('description')
        <meta name="description" content="@yield('description')">
    @endif
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="author" content="کریم محمدزاده">
    <meta name="theme-color" content="#075B3F">
    <link rel="canonical" href="@yield('canonical', url()->current())">

    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="کریم محمدزاده">
    <meta property="og:title" content="@yield('title', 'کریم محمدزاده')">
    @hasSection('description')
        <meta property="og:description" content="@yield('description')">
        <meta name="twitter:description" content="@yield('description')">
    @endif
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:image" content="@yield('social_image', asset('website/images/logo-mini.png'))">
    <meta property="og:image:alt" content="@yield('social_image_alt', 'کریم محمدزاده')">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'کریم محمدزاده')">
    <meta name="twitter:image" content="@yield('social_image', asset('website/images/logo-mini.png'))">
    <meta name="twitter:image:alt" content="@yield('social_image_alt', 'کریم محمدزاده')">

    <link rel="icon" href="{{ asset('website/images/favicon.ico') }}">

    {{-- Temporary preview setup: replace with an isolated compiled website stylesheet before production. --}}
    {{-- tw.js configures the Tailwind 3 browser runtime, not the project's Tailwind 4 pipeline. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('website/js/tw.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('website/style/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('website/style/style.css') }}">
    @stack('styles')

    <style>
        html, body {
            font-family: 'IRANYekan', 'IRANSansX', system-ui, sans-serif;
        }
        [x-cloak] { display: none !important; }
        body.menu-open { overflow: hidden; }
    </style>

    @stack('head')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
</head>
<body class="@yield('body_class')">
{{-- Each page places the shared header inside its own hero/background wrapper. --}}
@yield('content')

@include('website.partials.footer')

@stack('scripts')
</body>
</html>
