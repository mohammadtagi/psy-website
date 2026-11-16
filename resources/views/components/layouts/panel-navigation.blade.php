<header class="panel-navigation-header">
    <nav class="panel-navigation-links" aria-label="بخش‌های سامانه">
        @foreach($navigation as $item)
            <a href="{{ route($item['route']) }}" @if(request()->routeIs(...($item['patterns'] ?? [$item['route']]))) aria-current="page" @endif>
                <svg class="icon" aria-hidden="true"><use href="#{{ $item['icon'] }}"/></svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
    <div class="panel-header-actions">
        <a href="{{ route('home') }}">وب‌سایت</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">خروج از حساب</button></form>
    </div>
</header>
