{{-- resources/views/layouts/app.blade.php --}}
    <!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'جلسات روان‌درمانی آنلاین با دکتر کریم محمدزاده — فضایی امن برای اضطراب، حال بد، تروما و استرس‌های روزمره.')">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <title>@yield('title', 'دکتر کریم محمدزاده | روان‌شناس بالینی')</title>

    {{-- Open Graph ساده --}}
    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', 'دکتر کریم محمدزاده | روان‌شناس بالینی')">
    <meta property="og:description" content="@yield('meta_description', 'جلسات روان‌درمانی آنلاین با رویکرد علمی و رابطه‌ای محترمانه.')">
    <meta property="og:url" content="{{ url()->current() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-white text-primary-dark">
@include('partials.header')

<main id="main-content">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
