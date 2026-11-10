@php
    $publicLinks = [
        ['home', 'خانه'], ['website.services', 'خدمات'], ['website.about', 'درباره من'],
        ['website.contact', 'تماس با ما'], ['website.faq', 'سؤالات متداول'],
    ];
    $accountRoute = auth()->check() ? match(auth()->user()->role) {
        'admin' => 'admin.dashboard', 'psychologist' => 'psychologist.dashboard',
        'client' => 'client.dashboard', default => 'home',
    } : 'auth.login';
@endphp
<header class="cps-header">
    <div class="cps-bar">
        <a class="cps-brand" href="{{ route('home') }}" aria-label="کریم محمدزاده؛ صفحه اصلی">
            <img src="{{ asset('website/images/logo-mini.webp') }}" width="52" height="52" alt="">
            <span><strong>کریم محمدزاده</strong><small>روان‌شناس و روان‌درمانگر</small></span>
        </a>
        <nav class="cps-desktop" aria-label="منوی اصلی">
            @foreach($publicLinks as [$routeName, $label])
                <a href="{{ route($routeName) }}" @if(request()->routeIs($routeName)) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
        <div class="cps-actions">
            <a class="cps-account" href="{{ route($accountRoute) }}"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 21v-2a7 7 0 0 1 14 0v2"/></svg><span>{{ auth()->check() ? 'حساب کاربری' : 'ورود به حساب' }}</span></a>
            <a class="cps-button" href="{{ route('booking.index') }}">رزرو نوبت <span aria-hidden="true">←</span></a>
        </div>
    <details class="cps-mobile">
        <summary aria-label="منوی سایت"><svg class="cps-menu-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></summary>
        <nav aria-label="منوی موبایل">
            @foreach($publicLinks as [$routeName, $label])
                <a href="{{ route($routeName) }}" @if(request()->routeIs($routeName)) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
            <a class="cps-mobile-account" href="{{ route($accountRoute) }}">
                <svg class="cps-user-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8" r="3.5"/><path d="M5 21v-2a7 7 0 0 1 14 0v2"/></svg>
                <span class="cps-account-copy"><strong>{{ auth()->check() ? 'حساب کاربری' : 'ورود به حساب' }}</strong><small>{{ auth()->check() ? 'مشاهده و مدیریت حساب' : 'برای دسترسی به نوبت‌ها و حساب شما' }}</small></span>
                <svg class="cps-account-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m14 6-6 6 6 6"/></svg>
            </a>
        </nav>
    </details>
    </div>
</header>
