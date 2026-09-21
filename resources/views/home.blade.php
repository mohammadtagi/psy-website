<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>صفحه اصلی</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main>
    @auth
        <h1>ورود شما با موفقیت انجام شد.</h1>

        <p>
            شماره موبایل:
            {{ auth()->user()->mobile }}
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit">
                خروج
            </button>
        </form>
        <p>
            نقش کاربر:
            {{ auth()->user()->role ?? 'مهمان' }}
        </p>

    @else
        <h1>به وب‌سایت مشاوره خوش آمدید.</h1>

        <a href="{{ route('auth.login') }}">
            ورود
        </a>
    @endauth
</main>
</body>
</html>
