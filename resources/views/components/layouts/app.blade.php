@php
    $role = auth()->user()?->role;
    $navigation = match ($role) {
        'psychologist' => [
            ['route'=>'psychologist.dashboard', 'label'=>'داشبورد', 'icon'=>'home', 'patterns'=>['psychologist.dashboard']],
            ['route'=>'psychologist.appointments.index', 'label'=>'نوبت‌ها', 'icon'=>'calendar', 'patterns'=>['psychologist.appointments.*']],
            ['route'=>'psychologist.clients.index', 'label'=>'مراجعان', 'icon'=>'users', 'patterns'=>['psychologist.clients.*','psychologist.clinical-records.*','psychologist.clinical-notes.*','psychologist.clinical-attachments.*','psychologist.treatment-plans.*']],
            ['route'=>'psychologist.availabilities.index', 'label'=>'زمان‌های حضور', 'icon'=>'clock', 'patterns'=>['psychologist.availabilities.*']],
            ['route'=>'psychologist.discounts.index', 'label'=>'کدهای تخفیف', 'icon'=>'ticket', 'patterns'=>['psychologist.discounts.*']],
        ],
        'client' => [
            ['route'=>'client.dashboard', 'label'=>'داشبورد', 'icon'=>'home', 'patterns'=>['client.dashboard']],
            ['route'=>'client.appointments.index', 'label'=>'نوبت‌های من', 'icon'=>'calendar', 'patterns'=>['client.appointments.*']],
            ['route'=>'booking.index', 'label'=>'رزرو جلسه', 'icon'=>'clock', 'patterns'=>['booking.*']],
            ['route'=>'client.profile.show', 'label'=>'پروفایل', 'icon'=>'users', 'patterns'=>['client.profile.*']],
        ],
        'admin' => [
            ['route'=>'admin.dashboard', 'label'=>'داشبورد مدیر', 'icon'=>'home', 'patterns'=>['admin.*']],
        ],
        default => [
            ['route'=>'home', 'label'=>'وب‌سایت', 'icon'=>'home', 'patterns'=>['home']],
            ['route'=>'booking.index', 'label'=>'رزرو جلسه', 'icon'=>'calendar', 'patterns'=>['booking.*']],
            ['route'=>'auth.login', 'label'=>'ورود', 'icon'=>'users', 'patterns'=>['auth.*']],
        ],
    };
    $roleLabel = match ($role) { 'psychologist'=>'پنل مشاور', 'client'=>'پنل مراجع', 'admin'=>'پنل مدیر', default=>'سامانه مشاوره' };
    $pageTitle = isset($title) && is_string($title) ? $title : $roleLabel;
@endphp
    <!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $pageTitle) | کریم محمدزاده</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('website/style/care-shell.css') }}">
    @stack('styles')
</head>
<body class="care-shell">
<a class="care-skip" href="#care-content">رفتن به محتوای اصلی</a>
@include('components.layouts.care-icons')
<aside class="care-sidebar" aria-label="منوی اصلی">
    <a class="care-brand" href="{{ route($navigation[0]['route']) }}"><span class="care-brand-mark"><img src="{{ asset('website/images/logo-mini.png')  }}"></span><span>کریم محمدزاده<small>سامانه مدیریت مشاوره</small></span></a>
    <p class="care-caption">{{ $roleLabel }}</p>
    <nav aria-label="بخش‌های سامانه">
        @foreach($navigation as $item)
            <a class="care-nav-item" href="{{ route($item['route']) }}" @if(request()->routeIs(...$item['patterns'])) aria-current="page" @endif><svg class="icon" aria-hidden="true"><use href="#{{ $item['icon'] }}"/></svg>{{ $item['label'] }}</a>
        @endforeach
    </nav>
    <div class="care-sidebar-bottom"><p>فضایی برای تمرکز بر مراقبت</p><a href="{{ route('home') }}">بازگشت به وب‌سایت</a>@auth<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="care-logout">خروج از حساب</button></form>@endauth</div>
</aside>
<div class="care-workspace">
    <header class="care-topbar">
        <div><small>{{ $roleLabel }}</small><strong>@yield('title', $pageTitle)</strong></div>
        <div class="care-date">{{ \App\Support\PersianDate::format(now('Asia/Tehran'), 'EEEE، d MMMM yyyy') }}<small>ساعت‌ها به وقت تهران</small></div>
        <details class="care-mobile-menu"><summary><svg class="icon" aria-hidden="true"><use href="#menu"/></svg>منو</summary><nav aria-label="منوی موبایل">
                @foreach($navigation as $item)<a href="{{ route($item['route']) }}" @if(request()->routeIs(...$item['patterns'])) aria-current="page" @endif>{{ $item['label'] }}</a>@endforeach
                <a href="{{ route('home') }}">وب‌سایت</a>
                @auth<form method="POST" action="{{ route('logout') }}">@csrf<button class="care-logout" type="submit">خروج از حساب</button></form>@endauth
            </nav></details>
    </header>
    <div id="care-content" class="care-content" tabindex="-1">
        @if(isset($slot) && !is_array($slot)){{ $slot }}@endif
        @yield('content')
    </div>
    <footer class="care-footer">سامانه اختصاصی مدیریت خدمات مشاوره · کریم محمدزاده</footer>
</div>
<nav class="care-bottom-nav" style="--care-nav-count: {{ min(count($navigation), 4) }}" aria-label="دسترسی سریع موبایل">
    @foreach(array_slice($navigation, 0, 4) as $item)<a href="{{ route($item['route']) }}" @if(request()->routeIs(...$item['patterns'])) aria-current="page" @endif><svg class="icon" aria-hidden="true"><use href="#{{ $item['icon'] }}"/></svg><span>{{ $item['label'] }}</span></a>@endforeach
</nav>
@stack('scripts')
</body>
</html>
