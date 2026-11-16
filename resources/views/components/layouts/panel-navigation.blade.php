<header class="panel-navigation-header">
    <a class="panel-header-brand" href="{{ route('home') }}" aria-label="کریم محمدزاده؛ صفحهٔ اصلی" title="صفحهٔ اصلی">
        <img src="{{ asset('website/images/logo-mini.png') }}" alt="" width="44" height="44">
        <span class="panel-brand-copy"><strong>کریم محمدزاده</strong><small>سامانهٔ خدمات مشاوره</small></span>
    </a>
    <nav class="panel-navigation-links" aria-label="بخش‌های سامانه">
        @foreach($navigation as $item)
            <a href="{{ route($item['route']) }}" @if(request()->routeIs(...($item['patterns'] ?? [$item['route']]))) aria-current="page" @endif>
                <svg class="icon" aria-hidden="true"><use href="#{{ $item['icon'] }}"/></svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
    <div class="panel-header-actions">
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="panel-logout-icon" type="submit" aria-label="خروج از حساب" title="خروج از حساب"><svg viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10 4H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h5M14 8l4 4-4 4M8 12h10"/></svg></button></form>
    </div>
</header>
