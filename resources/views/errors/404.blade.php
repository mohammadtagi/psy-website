<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <meta name="theme-color" content="#214736">
    <title>صفحه پیدا نشد | کریم محمدزاده</title>
    <link rel="stylesheet" href="{{ asset('website/style/care-error-404.css') }}?v=1">
</head>
<body class="ce404">
    @php
        $homeUrl = \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/');
        $bookingUrl = \Illuminate\Support\Facades\Route::has('booking.index') ? route('booking.index') : null;
    @endphp
    <header class="ce404-header">
        <a class="ce404-brand" href="{{ $homeUrl }}">
            <span class="ce404-mark" aria-hidden="true"><svg viewBox="0 0 40 40" fill="none"><path d="M20 31V17M20 25C8 25 8 15 8 12c10 0 12 6 12 13ZM20 20C20 10 27 8 33 8c0 9-5 13-13 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span><strong>کریم محمدزاده</strong><small>مشاوره و روان‌شناسی</small></span>
        </a>
        <a class="ce404-header-link" href="{{ $homeUrl }}">صفحهٔ اصلی <span aria-hidden="true">↖</span></a>
    </header>
    <main id="main-content" class="ce404-main">
        <div class="ce404-art" aria-hidden="true">
            <svg viewBox="0 0 440 260" fill="none" focusable="false">
                <ellipse cx="220" cy="132" rx="173" ry="113" fill="#e9eee5"/>
                <path d="M58 194C108 207 109 149 158 153s55 72 113 42 53-36 109-22" stroke="#b5c7b5" stroke-width="2" stroke-linecap="round" stroke-dasharray="5 8"/>
                <ellipse cx="220" cy="232" rx="111" ry="8" fill="#dbe4d7"/>
                <g class="ce404-float">
                    <rect x="139" y="49" width="163" height="166" rx="22" transform="rotate(-7 139 49)" fill="#fffdf7" stroke="#b8cbb9" stroke-width="2"/>
                    <path d="m164 77 65-8M168 95l40-5" stroke="#c5d2c2" stroke-width="6" stroke-linecap="round"/>
                    <circle cx="220" cy="140" r="39" fill="#edf3e9" stroke="#36634b" stroke-width="5"/>
                    <path d="m249 169 27 28" stroke="#36634b" stroke-width="11" stroke-linecap="round"/>
                    <path d="m207 132 8 8m0-8-8 8m19-10 8 8m0-8-8 8M211 156c5-5 11-5 16-1" stroke="#36634b" stroke-width="3" stroke-linecap="round"/>
                </g>
                <g class="ce404-leaf" stroke="#527960" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M92 192v-43"/><path d="M92 176c-22 0-25-16-25-25 17 0 25 11 25 25Z" fill="#cbdcc3"/><path d="M92 164c0-20 13-27 27-27 0 18-12 27-27 27Z" fill="#dce7cc"/>
                </g>
                <g stroke="#b6913e" stroke-width="2" stroke-linecap="round"><path d="M332 73v14m-7-7h14M113 75v10m-5-5h10"/></g>
                <circle cx="345" cy="146" r="5" fill="#d9bc6d"/>
                <circle cx="130" cy="208" r="3" fill="#87a28a"/>
            </svg>
            <span class="ce404-code" dir="ltr">404</span>
        </div>
        <p class="ce404-eyebrow">یک مسیر تازه، از همین‌جا</p>
        <h1>این صفحه پیدا نشد</h1>
        <p class="ce404-description">ممکن است آدرس صفحه تغییر کرده باشد یا لینک واردشده درست نباشد.<br>می‌توانید از صفحهٔ اصلی، مسیرتان را ادامه دهید.</p>
        <nav class="ce404-actions" aria-label="ادامهٔ مسیر">
            <a class="ce404-primary" href="{{ $homeUrl }}">بازگشت به صفحهٔ اصلی <span aria-hidden="true">←</span></a>
            @if ($bookingUrl)
                <a class="ce404-secondary" href="{{ $bookingUrl }}">رزرو نوبت</a>
            @endif
        </nav>
        <p class="ce404-hint">اگر آدرس را دستی وارد کرده‌اید، یک‌بار دیگر آن را بررسی کنید.</p>
    </main>
    <footer class="ce404-footer">کریم محمدزاده <span aria-hidden="true">·</span> مشاوره و روان‌شناسی</footer>
</body>
</html>
