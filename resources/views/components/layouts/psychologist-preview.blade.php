@props(['title' => 'داشبورد مشاور'])
@php
    $navigation = [
        ['route' => 'psychologist.dashboard', 'label' => 'داشبورد', 'icon' => 'home'],
        ['route' => 'psychologist.appointments.index', 'label' => 'نوبت‌ها', 'icon' => 'calendar'],
        ['route' => 'psychologist.clients.index', 'label' => 'مراجعان', 'icon' => 'users'],
        ['route' => 'psychologist.availabilities.index', 'label' => 'زمان‌های حضور', 'icon' => 'clock'],
        ['route' => 'psychologist.discounts.index', 'label' => 'کدهای تخفیف', 'icon' => 'ticket'],
    ];
@endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | کریم محمدزاده</title>
    <link rel="stylesheet" href="{{ asset('website/style/psychologist-dashboard.css') }}">
</head>
<body class="therapy-ui">
<a class="skip-link" href="#main-content">رفتن به محتوای اصلی</a>
<svg xmlns="http://www.w3.org/2000/svg" class="icon-library" aria-hidden="true"><defs>
    <symbol id="home" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10H3Z M9 20v-7h6v7"/></symbol>
    <symbol id="calendar" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v5m10-5v5M3 11h18m-14 5h3m4 0h3"/></symbol>
    <symbol id="users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 5"/></symbol>
    <symbol id="clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/></symbol>
    <symbol id="ticket" viewBox="0 0 24 24"><path d="M3 6h18v4a2 2 0 0 0 0 4v4H3v-4a2 2 0 0 0 0-4Zm12 0v12"/></symbol>
    <symbol id="arrow" viewBox="0 0 24 24"><path d="M20 12H4m6-6-6 6 6 6"/></symbol>
    <symbol id="menu" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></symbol>
    <symbol id="leaf" viewBox="0 0 24 24"><path d="M20 3C8 2 2 8 5 15s16 5 15-12ZM4 21 16 9"/></symbol>
</defs></svg>
<aside class="sidebar" aria-label="منوی اصلی">
    <a class="brand" href="{{ route('psychologist.dashboard') }}"><span class="brand-mark"><svg class="icon"><use href="#leaf"/></svg></span><span>کریم محمدزاده<small>سامانه مدیریت مشاوره</small></span></a>
    <p class="nav-caption">فضای کاری مشاور</p>
    <nav aria-label="بخش‌های سامانه">
        @foreach ($navigation as $item)
            <a class="nav-item {{ request()->routeIs($item['route']) ? 'is-active' : '' }}" href="{{ route($item['route']) }}" @if(request()->routeIs($item['route'])) aria-current="page" @endif><svg class="icon" aria-hidden="true"><use href="#{{ $item['icon'] }}"/></svg>{{ $item['label'] }}</a>
        @endforeach
    </nav>
    <div class="sidebar-bottom"><p>فضایی برای تمرکز بر مراقبت</p><a href="{{ route('home') }}">بازگشت به وب‌سایت</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">خروج از حساب</button></form></div>
</aside>
<div class="workspace">
    <header class="topbar">
        <div class="topbar-title"><span class="eyebrow">پنل مشاور</span><strong>{{ $title }}</strong></div>
        <div class="header-date">{{ \App\Support\PersianDate::format(now('Asia/Tehran'), 'EEEE، d MMMM yyyy') }}<small>ساعت‌ها به وقت تهران</small></div>
        <details class="mobile-menu"><summary aria-label="باز کردن منوی سامانه"><svg class="icon" aria-hidden="true"><use href="#menu"/></svg><span>منو</span></summary><nav aria-label="منوی موبایل">
            @foreach ($navigation as $item)<a href="{{ route($item['route']) }}" @if(request()->routeIs($item['route'])) aria-current="page" @endif>{{ $item['label'] }}</a>@endforeach
            <a href="{{ route('home') }}">وب‌سایت</a><form method="POST" action="{{ route('logout') }}">@csrf<button class="logout" type="submit">خروج از حساب</button></form>
        </nav></details>
    </header>
    <main id="main-content" tabindex="-1">{{ $slot }}</main>
    <footer class="page-footer">سامانه اختصاصی مدیریت خدمات مشاوره · کریم محمدزاده</footer>
</div>
<nav class="bottom-nav" aria-label="دسترسی سریع موبایل">
    @foreach (array_slice($navigation, 0, 4) as $item)
        <a href="{{ route($item['route']) }}" @if(request()->routeIs($item['route'])) aria-current="page" @endif><svg class="icon" aria-hidden="true"><use href="#{{ $item['icon'] }}"/></svg><span>{{ $item['label'] }}</span></a>
    @endforeach
</nav>
</body>
</html>
